<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_renders_successfully_with_products(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Kategori Pilihan');
        $response->assertSee('Rekomendasi Untukmu');
    }

    public function test_product_catalog_page_can_be_rendered(): void
    {
        $response = $this->get('/produk');

        $response->assertStatus(200);
        $response->assertSee('Filter Pencarian');
        $response->assertSee('Batas Harga');
    }

    public function test_product_can_be_filtered_by_category(): void
    {
        $category = Category::where('slug', 'elektronik')->first();

        $response = $this->get('/kategori/' . $category->slug);

        $response->assertStatus(200);
        $response->assertSee($category->name);
    }

    public function test_product_can_be_searched_by_keyword(): void
    {
        $product = Product::first();

        $response = $this->get('/produk?q=' . urlencode(substr($product->name, 0, 5)));

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_product_detail_page_renders_successfully(): void
    {
        $product = Product::first();

        $response = $this->get('/produk/' . $product->slug);

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Spesifikasi Produk');
        $response->assertSee('Garansi TokoKu');
    }

    public function test_invalid_product_slug_returns_404(): void
    {
        $response = $this->get('/produk/non-existent-product-slug');

        $response->assertStatus(404);
    }
}
