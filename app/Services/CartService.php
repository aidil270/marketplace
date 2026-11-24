<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    /**
     * Dapatkan atau buat keranjang belanja untuk user login.
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Hitung total kuantitas item keranjang milik user.
     */
    public function getCartCount(User $user): int
    {
        $cart = Cart::where('user_id', $user->id)->first();
        if (! $cart) {
            return 0;
        }

        return (int) $cart->items()->sum('quantity');
    }

    /**
     * Tambah item ke keranjang belanja dengan validasi stok & harga server-side.
     */
    public function addItem(User $user, int $productId, ?int $variantId = null, int $quantity = 1): CartItem
    {
        $quantity = max(1, $quantity);

        $product = Product::with('store')->findOrFail($productId);

        if (! $product->is_active || ! $product->store || ! $product->store->is_active) {
            throw new Exception('Produk atau toko saat ini tidak aktif.');
        }

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::where('product_id', $product->id)->findOrFail($variantId);
            $availableStock = $variant->stock;
        } else {
            $availableStock = $product->stock;
        }

        if ($availableStock <= 0) {
            throw new Exception('Maaf, stok produk ini sedang habis.');
        }

        $cart = $this->getOrCreateCart($user);

        // Cek apakah item sudah ada di keranjang
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        $newQuantity = $cartItem ? ($cartItem->quantity + $quantity) : $quantity;

        if ($newQuantity > $availableStock) {
            throw new Exception("Jumlah melebihi stok yang tersedia (tersisa {$availableStock}).");
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQuantity]);
            return $cartItem;
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => $variantId,
            'quantity' => $newQuantity,
            'is_selected' => true,
        ]);
    }

    /**
     * Ubah kuantitas item keranjang.
     */
    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        $quantity = max(1, $quantity);

        $product = $item->product;
        $variant = $item->variant;
        $availableStock = $variant ? $variant->stock : $product->stock;

        if ($quantity > $availableStock) {
            throw new Exception("Jumlah melebihi stok yang tersedia (tersisa {$availableStock}).");
        }

        $item->update(['quantity' => $quantity]);
        return $item;
    }

    /**
     * Hapus item dari keranjang.
     */
    public function removeItem(CartItem $item): bool
    {
        return (bool) $item->delete();
    }

    /**
     * Toggle status pilihan item.
     */
    public function toggleSelect(CartItem $item, ?bool $isSelected = null): CartItem
    {
        $item->update([
            'is_selected' => $isSelected !== null ? $isSelected : ! $item->is_selected,
        ]);
        return $item;
    }

    /**
     * Pilih atau batal pilih semua item dalam keranjang.
     */
    public function selectAll(User $user, bool $isSelected): void
    {
        $cart = $this->getOrCreateCart($user);
        $cart->items()->update(['is_selected' => $isSelected]);
    }

    /**
     * Hitung harga item per unit di server (harga dasar + harga varian).
     */
    public static function getItemUnitPrice(CartItem $item): float
    {
        $base = (float) ($item->product->price ?? 0);
        $addon = (float) ($item->variant->additional_price ?? 0);
        return $base + $addon;
    }

    /**
     * Hitung subtotal item terpilih di keranjang.
     */
    public function getSelectedSubtotal(Cart $cart): float
    {
        return (float) $cart->items()
            ->where('is_selected', true)
            ->with(['product', 'variant'])
            ->get()
            ->sum(fn ($item) => self::getItemUnitPrice($item) * $item->quantity);
    }
}
