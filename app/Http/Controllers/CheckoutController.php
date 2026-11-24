<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentGroup;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\Voucher;
use App\Services\CartService;
use App\Services\DokuService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Halaman checkout belanja.
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $cart = $this->cartService->getOrCreateCart($user);

        // Ambil hanya item yang dipilih
        $selectedItems = $cart->items()
            ->where('is_selected', true)
            ->with(['product.store', 'product.primaryImage', 'variant'])
            ->get();

        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Silakan pilih minimal satu produk untuk melanjutkan checkout.');
        }

        // Kelompokkan per toko
        $itemsByStore = $selectedItems->groupBy(fn ($item) => $item->product->store->id);

        // Alamat pembeli
        $addresses = $user->addresses()->orderByDesc('is_primary')->latest()->get();
        $selectedAddress = $addresses->firstWhere('is_primary', true) ?: $addresses->first();

        // Daftar ekspedisi pengiriman dari konfigurasi
        $couriers = config('shipping.couriers', []);
        $defaultCourierKey = config('shipping.default_courier', 'jnt');
        $selectedCourier = $couriers[$defaultCourierKey] ?? reset($couriers);

        // Hitung Subtotal barang
        $subtotal = $selectedItems->sum(fn ($item) => CartService::getItemUnitPrice($item) * $item->quantity);

        // Hitung Ongkir Total (tarif flat per toko)
        $shippingCostPerStore = $selectedCourier['cost'] ?? 14000;
        $totalShippingCost = $shippingCostPerStore * $itemsByStore->count();

        // Validasi Voucher dari Session
        $voucherCode = session('checkout_voucher');
        $voucher = null;
        $discountAmount = 0;

        if ($voucherCode) {
            $voucher = Voucher::where('code', $voucherCode)->first();
            if ($voucher && $voucher->isValidFor($subtotal)) {
                $discountAmount = $voucher->calculateDiscount($subtotal);
            } else {
                session()->forget('checkout_voucher');
                $voucher = null;
            }
        }

        $finalAmount = max(0, $subtotal + $totalShippingCost - $discountAmount);

        return view('checkout.index', compact(
            'selectedItems',
            'itemsByStore',
            'addresses',
            'selectedAddress',
            'couriers',
            'selectedCourier',
            'subtotal',
            'totalShippingCost',
            'shippingCostPerStore',
            'voucher',
            'discountAmount',
            'finalAmount'
        ));
    }

    /**
     * Terapkan kode voucher diskon di sesi checkout.
     */
    public function applyVoucher(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = strtoupper(trim($request->input('code')));
        $voucher = Voucher::where('code', $code)->first();

        if (! $voucher) {
            return redirect()->back()->with('error', "Kode voucher '{$code}' tidak ditemukan.");
        }

        if (! $voucher->is_active) {
            return redirect()->back()->with('error', "Voucher '{$code}' sedang tidak aktif.");
        }

        if (now()->lt($voucher->start_date) || now()->gt($voucher->end_date)) {
            return redirect()->back()->with('error', "Voucher '{$code}' telah kedaluwarsa.");
        }

        if ($voucher->quota > 0 && $voucher->used_count >= $voucher->quota) {
            return redirect()->back()->with('error', "Kuota penggunaan voucher '{$code}' sudah habis.");
        }

        // Cek subtotal saat ini
        $user = auth()->user();
        $cart = $this->cartService->getOrCreateCart($user);
        $subtotal = $this->cartService->getSelectedSubtotal($cart);

        if ($subtotal < (float) $voucher->min_spend) {
            $minFormatted = 'Rp ' . number_format($voucher->min_spend, 0, ',', '.');
            return redirect()->back()->with('error', "Minimal belanja untuk voucher ini adalah {$minFormatted}.");
        }

        session(['checkout_voucher' => $code]);

        return redirect()->back()->with('success', "Voucher '{$voucher->name}' berhasil diterapkan!");
    }

    /**
     * Hapus voucher dari sesi checkout.
     */
    public function removeVoucher(): RedirectResponse
    {
        session()->forget('checkout_voucher');
        return redirect()->back()->with('success', 'Voucher berhasil dibatalkan.');
    }

    /**
     * Proses pembuatan Order dalam Database Transaction.
     */
    public function process(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // 1. Validasi Alamat
        $addressId = $request->input('address_id');
        if (! $addressId) {
            // Jika pembeli mengisi form alamat baru cepat
            $validatedAddress = $request->validate([
                'recipient_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:20'],
                'full_address' => ['required', 'string'],
                'province' => ['required', 'string'],
                'city' => ['required', 'string'],
                'postal_code' => ['required', 'string', 'max:10'],
            ]);

            $address = Address::create(array_merge($validatedAddress, [
                'user_id' => $user->id,
                'is_primary' => ! $user->addresses()->exists(),
            ]));
        } else {
            $address = Address::where('user_id', $user->id)->findOrFail($addressId);
        }

        // 2. Ekspedisi Kurir
        $courierKey = $request->input('courier', config('shipping.default_courier', 'jnt'));
        $couriers = config('shipping.couriers', []);
        $courier = $couriers[$courierKey] ?? reset($couriers);
        $shippingCostPerStore = (float) ($courier['cost'] ?? 14000);

        // 3. Ambil Item Terpilih di Keranjang
        $cart = $this->cartService->getOrCreateCart($user);
        $selectedItems = $cart->items()
            ->where('is_selected', true)
            ->with(['product.store', 'variant'])
            ->get();

        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong atau belum ada produk yang dipilih.');
        }

        // 4. Validasi Stok Terkini Tepat Sebelum Transaksi
        foreach ($selectedItems as $item) {
            $product = $item->product;
            $variant = $item->variant;
            $availableStock = $variant ? $variant->stock : $product->stock;

            if ($item->quantity > $availableStock) {
                return redirect()->route('cart.index')->with(
                    'error',
                    "Stok untuk {$product->name} tidak mencukupi (tersisa {$availableStock}). Silakan sesuaikan jumlah pembelian."
                );
            }
        }

        // 5. Kelompokkan per Toko
        $itemsByStore = $selectedItems->groupBy(fn ($item) => $item->product->store->id);
        $totalSubtotal = $selectedItems->sum(fn ($item) => CartService::getItemUnitPrice($item) * $item->quantity);

        // 6. Voucher
        $voucherCode = session('checkout_voucher');
        $voucher = null;
        $totalDiscount = 0;
        if ($voucherCode) {
            $v = Voucher::where('code', $voucherCode)->first();
            if ($v && $v->isValidFor($totalSubtotal)) {
                $voucher = $v;
                $totalDiscount = $voucher->calculateDiscount($totalSubtotal);
            }
        }

        // 7. Jalankan Pembuatan Order dalam DB Transaction
        $createdOrders = [];
        try {
            $paymentGroup = DB::transaction(function () use (
                $user,
                $itemsByStore,
                $address,
                $courier,
                $shippingCostPerStore,
                $voucher,
                $totalSubtotal,
                $totalDiscount,
                $request,
                &$createdOrders
            ) {
                $storeCount = $itemsByStore->count();
                $discountDistributed = 0;
                $currentIndex = 0;

                foreach ($itemsByStore as $storeId => $storeItems) {
                    $currentIndex++;

                    // Subtotal produk toko ini
                    $storeSubtotal = $storeItems->sum(fn ($item) => CartService::getItemUnitPrice($item) * $item->quantity);

                    // Alokasikan diskon voucher secara proporsional ke tiap toko
                    if ($voucher && $totalSubtotal > 0) {
                        if ($currentIndex === $storeCount) {
                            $storeDiscount = $totalDiscount - $discountDistributed;
                        } else {
                            $storeDiscount = round(($storeSubtotal / $totalSubtotal) * $totalDiscount, 2);
                            $discountDistributed += $storeDiscount;
                        }
                    } else {
                        $storeDiscount = 0;
                    }

                    $storeFinal = max(0, $storeSubtotal + $shippingCostPerStore - $storeDiscount);

                    // Generate Invoice Number Unik (contoh: INV/20261124/8K2LM1)
                    $invoiceNumber = 'INV/' . date('Ymd') . '/' . strtoupper(Str::random(6));

                    $recipientAddressText = "{$address->recipient_name} ({$address->phone_number})\n{$address->full_address}, {$address->city}, {$address->province} {$address->postal_code}";

                    // Buat Record Order
                    $order = Order::create([
                        'invoice_number' => $invoiceNumber,
                        'user_id' => $user->id,
                        'store_id' => $storeId,
                        'voucher_id' => $voucher?->id,
                        'total_amount' => $storeSubtotal,
                        'shipping_cost' => $shippingCostPerStore,
                        'discount_amount' => $storeDiscount,
                        'final_amount' => $storeFinal,
                        'status' => 'pending',
                        'shipping_address' => $recipientAddressText,
                        'notes' => $request->input('notes'),
                    ]);

                    // Simpan Order Items (Snapshot harga & nama produk)
                    foreach ($storeItems as $item) {
                        $unitPrice = CartService::getItemUnitPrice($item);
                        $variantName = $item->variant ? " ({$item->variant->name}: {$item->variant->value})" : "";

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'product_name' => $item->product->name . $variantName,
                            'product_price' => $unitPrice,
                            'quantity' => $item->quantity,
                            'subtotal' => $unitPrice * $item->quantity,
                        ]);
                    }

                    // Buat Record Payment (status: pending, gateway: doku)
                    Payment::create([
                        'order_id' => $order->id,
                        'gateway' => 'doku',
                        'payment_method' => null,
                        'reference_number' => null,
                        'payment_url' => null,
                        'amount' => $storeFinal,
                        'status' => 'pending',
                        'paid_at' => null,
                        'expired_at' => now()->addDay(),
                        'gateway_response' => null,
                    ]);

                    // Buat Record Shipment (status: pending)
                    $recipientAddressText = "{$address->recipient_name} ({$address->phone_number})\n{$address->full_address}, {$address->city}, {$address->province} {$address->postal_code}";
                    Shipment::create([
                        'order_id' => $order->id,
                        'courier_name' => $courier['name'] ?? 'Kurir Reguler',
                        'service_type' => $courier['service'] ?? 'REG',
                        'tracking_number' => null,
                        'shipment_status' => 'pending',
                        'recipient_address' => $recipientAddressText,
                    ]);

                    $createdOrders[] = $order;
                }

                // Buat SATU payment_group untuk menaungi seluruh order checkout ini
                $grandTotal = array_sum(array_map(fn ($o) => (float) $o->final_amount, $createdOrders));
                $paymentGroup = PaymentGroup::create([
                    'user_id' => $user->id,
                    'invoice_number' => 'PAY/' . date('Ymd') . '/' . strtoupper(Str::random(6)),
                    'total_amount' => $grandTotal,
                    'status' => 'pending',
                    'expired_at' => now()->addMinutes(1440),
                ]);

                foreach ($createdOrders as $createdOrder) {
                    $createdOrder->update(['payment_group_id' => $paymentGroup->id]);
                }

                // Tambahkan penggunaan voucher jika voucher valid digunakan
                if ($voucher) {
                    $voucher->increment('used_count');
                }

                // Hapus item-item yang sudah dipesan dari keranjang
                $cart = $this->cartService->getOrCreateCart($user);
                $cart->items()->where('is_selected', true)->delete();

                // Bersihkan session voucher
                session()->forget('checkout_voucher');

                return $paymentGroup;
            });

            // Buat sesi pembayaran DOKU Checkout Hosted Page
            $dokuService = app(DokuService::class);
            $dokuResult = $dokuService->createCheckout($paymentGroup);

            return redirect()->route('payment-groups.show', $paymentGroup->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}
