<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Services\CartService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Tampilkan halaman keranjang belanja pembeli.
     */
    public function index(): View
    {
        $cart = $this->cartService->getOrCreateCart(auth()->user());

        $items = $cart->items()
            ->with(['product.store', 'product.primaryImage', 'variant'])
            ->get();

        // Kelompokkan item per toko
        $groupedItems = $items->groupBy(fn ($item) => $item->product->store->id ?? 0);

        // Hitung subtotal item terpilih
        $subtotal = $this->cartService->getSelectedSubtotal($cart);
        $selectedCount = $items->where('is_selected', true)->sum('quantity');

        return view('cart.index', compact('cart', 'items', 'groupedItems', 'subtotal', 'selectedCount'));
    }

    /**
     * Tambah item ke keranjang atau "Beli Sekarang".
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'is_buy_now' => ['nullable', 'boolean'],
        ]);

        try {
            $user = auth()->user();
            $cartItem = $this->cartService->addItem(
                $user,
                (int) $validated['product_id'],
                ! empty($validated['variant_id']) ? (int) $validated['variant_id'] : null,
                (int) $validated['quantity']
            );

            // Jika "Beli Sekarang", jadikan item ini satu-satunya yang terpilih dan redirect langsung ke checkout
            if ($request->boolean('is_buy_now')) {
                $cart = $cartItem->cart;
                $cart->items()->update(['is_selected' => false]);
                $cartItem->update(['is_selected' => true]);

                return redirect()->route('checkout.index');
            }

            return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang belanja!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Ubah kuantitas item di keranjang.
     */
    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        // Otorisasi kepemilikan keranjang
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->cartService->updateQuantity($cartItem, (int) $validated['quantity']);
            return redirect()->back()->with('success', 'Jumlah produk berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Hapus item dari keranjang.
     */
    public function destroy(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $this->cartService->removeItem($cartItem);

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }

    /**
     * Toggle checklist item terpilih.
     */
    public function toggleSelect(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $this->cartService->toggleSelect($cartItem);

        return redirect()->back();
    }

    /**
     * Pilih semua atau batal pilih semua item di keranjang.
     */
    public function selectAll(Request $request): RedirectResponse
    {
        $isSelected = $request->boolean('select', true);
        $this->cartService->selectAll(auth()->user(), $isSelected);

        return redirect()->back();
    }
}
