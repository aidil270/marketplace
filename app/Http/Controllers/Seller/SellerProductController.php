<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerProductController extends Controller
{
    /**
     * Tampilkan daftar produk milik toko penjual dengan pencarian & filter.
     */
    public function index(Request $request): View
    {
        $store = auth()->user()->store;

        $categories = Category::orderBy('name')->get();

        $query = $store->products()
            ->with(['primaryImage', 'category'])
            ->withCount('orderItems');

        // Filter nama produk
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        // Filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter status aktif/nonaktif
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('seller.products.index', compact('products', 'categories'));
    }

    /**
     * Tampilkan form penambahan produk baru.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('seller.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru beserta foto dan variasi.
     */
    public function store(Request $request): RedirectResponse
    {
        $store = auth()->user()->store;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.value' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.additional_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
        ], [
            'images.max' => 'Maksimal upload 8 foto produk.',
            'images.*.max' => 'Ukuran setiap foto produk maksimal 2MB.',
        ]);

        DB::transaction(function () use ($store, $validated, $request) {
            // Generate unique slug
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::withTrashed()->where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            // Buat produk
            $product = $store->products()->create([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'weight' => $validated['weight'] ?? 1000,
                'is_active' => $request->boolean('is_active', true),
                'is_featured' => false,
            ]);

            // Simpan foto-foto produk (maksimal 8)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store('products', 'public');
                    $product->images()->create([
                        'image_path' => '/storage/'.$path,
                        'is_primary' => ($index === 0),
                        'sort_order' => $index,
                    ]);
                }
            }

            // Simpan variasi produk jika ada
            if (! empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    if (! empty($v['name']) && ! empty($v['value'])) {
                        $product->variants()->create([
                            'name' => trim($v['name']),
                            'value' => trim($v['value']),
                            'additional_price' => ! empty($v['additional_price']) ? $v['additional_price'] : 0,
                            'stock' => ! empty($v['stock']) ? $v['stock'] : $product->stock,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk baru berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit(Product $product): View
    {
        $store = auth()->user()->store;
        if ($product->store_id !== $store->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit produk toko lain.');
        }

        $product->load(['images', 'variants', 'category']);
        $categories = Category::orderBy('name')->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Perbarui data produk, foto baru, dan variasi.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $store = auth()->user()->store;
        if ($product->store_id !== $store->id) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah produk toko lain.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'new_images' => ['nullable', 'array', 'max:8'],
            'new_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.name' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.value' => ['required_with:variants', 'string', 'max:100'],
            'variants.*.additional_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($product, $validated, $request) {
            // Update data utama produk
            $product->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'weight' => $validated['weight'] ?? 1000,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Tambah foto baru jika ada
            if ($request->hasFile('new_images')) {
                $currentCount = $product->images()->count();
                $hasPrimary = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('new_images') as $i => $img) {
                    if ($currentCount >= 8) {
                        break;
                    }
                    $path = $img->store('products', 'public');
                    $isPrimary = (! $hasPrimary && $i === 0);
                    $product->images()->create([
                        'image_path' => '/storage/'.$path,
                        'is_primary' => $isPrimary,
                        'sort_order' => $currentCount + $i,
                    ]);
                    $currentCount++;
                }
            }

            // Sinkronisasi variasi produk
            $submittedVariantIds = [];
            if (! empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    if (empty($v['name']) || empty($v['value'])) {
                        continue;
                    }

                    $variantData = [
                        'name' => trim($v['name']),
                        'value' => trim($v['value']),
                        'additional_price' => ! empty($v['additional_price']) ? $v['additional_price'] : 0,
                        'stock' => ! empty($v['stock']) ? $v['stock'] : $product->stock,
                    ];

                    if (! empty($v['id'])) {
                        $existingVariant = ProductVariant::where('product_id', $product->id)
                            ->where('id', $v['id'])
                            ->first();

                        if ($existingVariant) {
                            $existingVariant->update($variantData);
                            $submittedVariantIds[] = $existingVariant->id;
                            continue;
                        }
                    }

                    // Buat variasi baru
                    $newVariant = $product->variants()->create($variantData);
                    $submittedVariantIds[] = $newVariant->id;
                }
            }

            // Hapus variasi lama yang tidak ada lagi di submit jika belum pernah dipesan
            $product->variants()
                ->whereNotIn('id', $submittedVariantIds)
                ->each(function ($variant) {
                    if (! $variant->orderItems()->exists()) {
                        $variant->delete();
                    }
                });
        });

        return redirect()->route('seller.products.edit', $product->id)
            ->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Ubah status aktif/nonaktif produk secara cepat.
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        $store = auth()->user()->store;
        if ($product->store_id !== $store->id) {
            abort(403);
        }

        $newStatus = ! $product->is_active;
        $product->update(['is_active' => $newStatus]);

        $message = $newStatus ? 'Produk berhasil diaktifkan kembali.' : 'Produk berhasil dinonaktifkan dari katalog.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Hapus satu foto dari produk.
     */
    public function deleteImage(Product $product, ProductImage $image): RedirectResponse
    {
        $store = auth()->user()->store;
        if ($product->store_id !== $store->id || $image->product_id !== $product->id) {
            abort(403);
        }

        $wasPrimary = $image->is_primary;

        // Hapus file fisik dari storage jika ada
        if ($image->image_path && Str::startsWith($image->image_path, '/storage/')) {
            $relativePath = Str::after($image->image_path, '/storage/');
            Storage::disk('public')->delete($relativePath);
        }

        $image->delete();

        // Jika foto yang dihapus adalah foto utama, jadikan foto pertama yang tersisa sebagai utama
        if ($wasPrimary) {
            $nextImage = $product->images()->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return redirect()->back()->with('success', 'Foto produk berhasil dihapus.');
    }

    /**
     * Jadikan foto terpilih sebagai foto utama produk.
     */
    public function setPrimaryImage(Product $product, ProductImage $image): RedirectResponse
    {
        $store = auth()->user()->store;
        if ($product->store_id !== $store->id || $image->product_id !== $product->id) {
            abort(403);
        }

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Foto utama produk berhasil diubah.');
    }

    /**
     * Hapus produk (Soft Delete jika sudah ada order, Hard Delete jika belum ada order).
     */
    public function destroy(Product $product): RedirectResponse
    {
        $store = auth()->user()->store;
        if ($product->store_id !== $store->id) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus produk ini.');
        }

        // Cek apakah produk sudah pernah dipesan
        $hasOrders = $product->orderItems()->exists();

        if ($hasOrders) {
            // Jangan hapus permanen: nonaktifkan dan soft delete
            $product->update(['is_active' => false]);
            $product->delete();

            return redirect()->route('seller.products.index')
                ->with('info', 'Produk ini sudah memiliki riwayat transaksi, sehingga diarsipkan (soft delete) dan dinonaktifkan demi menjaga integritas data pesanan.');
        }

        // Belum pernah dipesan: hapus permanen beserta fotonya
        foreach ($product->images as $img) {
            if ($img->image_path && Str::startsWith($img->image_path, '/storage/')) {
                $relativePath = Str::after($img->image_path, '/storage/');
                Storage::disk('public')->delete($relativePath);
            }
            $img->delete();
        }

        $product->variants()->delete();
        $product->forceDelete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Produk berhasil dihapus secara permanen.');
    }
}
