# TokoKu - E-Commerce Marketplace Platform

Aplikasi marketplace modern berbasis Laravel 12, Blade, Tailwind CSS, Alpine.js, dan MySQL (`ecommerce_db`) dengan arsitektur multi-toko (multi-store), integrasi pembayaran DOKU Checkout Hosted Payment Gateway (Mode Sandbox), sistem ulasan, alamat bertingkat, dan wishlist.

---

## Akun Contoh Demo (Seeder)
Semua akun menggunakan password: `password`

| Peran | Nama Akun | Email | Keterangan |
|---|---|---|---|
| **Admin** | Administrator | `admin@tokoku.com` | Akses penuh manajemen sistem |
| **Seller 1** | Budi Santoso | `seller1@tokoku.com` | Toko: TechOfficial Store |
| **Seller 2** | Siti Aminah | `seller2@tokoku.com` | Toko: FashionBoutique ID |
| **Buyer 1** | Pembeli Satu | `buyer1@tokoku.com` | Akun pembeli aktif |
| **Buyer 2** | Pembeli Dua | `buyer2@tokoku.com` | Akun pembeli aktif |
| **Buyer 3** | Pembeli Tiga | `buyer3@tokoku.com` | Akun pembeli aktif |

---

## Panduan Pengujian Pembayaran DOKU Sandbox

### 1. Konfigurasi Kredensial di `.env`
Buka file `.env` di direktori proyek dan masukkan kredensial akun Sandbox DOKU Anda (didapatkan dari DOKU Back Office Sandbox):

```dotenv
DOKU_ENV=sandbox
DOKU_CLIENT_ID=MCH-XXXX-XXXX-XXXX
DOKU_SECRET_KEY=SK-XXXX-XXXX-XXXX
DOKU_BASE_URL=https://api-sandbox.doku.com
```

> **Perhatian**: Jangan pernah membagikan atau mempublikasikan `DOKU_SECRET_KEY`. File `.env` sudah dimasukkan ke dalam `.gitignore` demi keamanan.

---

### 2. Menjalankan Tunnel Publik untuk Menerima Webhook / Notifikasi
Karena server lokal berjalan di `localhost:8000`, DOKU memerlukan URL publik berbasis HTTPS agar dapat mengirimkan notifikasi status pembayaran (webhook).

Anda dapat menggunakan salah satu tool tunnel berikut:

#### Opsi A: Cloudflare Tunnel (Gratis, Tanpa Akun)
Jalankan perintah berikut di terminal:
```bash
cloudflared tunnel --url http://localhost:8000
```
Cloudflare akan memberikan URL publik acak sementara, misalnya:
`https://example-random-subdomain.trycloudflare.com`

#### Opsi B: Ngrok
Jalankan perintah berikut di terminal:
```bash
ngrok http 8000
```
Ngrok akan memberikan URL publik HTTPS, misalnya:
`https://xxxx-xx-xx.ngrok-free.app`

> **Catatan Penting**: URL tunnel gratis berubah setiap kali proses tunnel di-restart. Jika Anda me-restart tunnel, perbarui Notification URL di DOKU Back Office dengan domain baru.

---

### 3. Mendaftarkan Notification URL di DOKU Back Office Sandbox
1. Login ke DOKU Back Office Sandbox: [https://sandbox.doku.com/bo](https://sandbox.doku.com/bo).
2. Masuk ke menu **Settings** &rarr; **Integration** / **Notification URL**.
3. Daftarkan endpoint webhook TokoKu:
   ```text
   https://<domain-tunnel-anda>/api/doku/notification
   ```
4. Simpan konfigurasi.

---

### 4. Simulasi Alur Pembayaran di Halaman DOKU Sandbox
1. Jalankan aplikasi web lokal:
   ```bash
   php artisan serve
   ```
2. Login sebagai pembeli (misal `buyer1@tokoku.com` / `password`).
3. Pilih produk dari satu atau beberapa toko berbeda, masukkan ke keranjang, lalu klik **Checkout**.
4. Di halaman checkout, pilih alamat pengiriman dan kurir, lalu klik **Buat Pesanan**.
5. Sistem secara otomatis membuat pesanan per toko dan satu grup pembayaran (`payment_groups`), lalu mengarahkan ke halaman **Selesaikan Pembayaran** (`/pembayaran/{id}`).
6. Klik tombol **Bayar Sekarang (DOKU Checkout)**:
   - Anda akan diarahkan ke halaman hosted DOKU Checkout Sandbox.
   - Pilih metode pembayaran simulasi (misal Virtual Account BCA, Mandiri, Permata, atau QRIS).
   - Di halaman simulator DOKU Sandbox, klik tombol bayar simulasi.
7. Setelah berhasil, DOKU akan mengirimkan webhook HTTP POST ke `/api/doku/notification`:
   - Sistem TokoKu memverifikasi HMAC-SHA256 Signature.
   - Status tagihan grup berubah menjadi `paid`.
   - Semua pesanan dalam grup berubah status menjadi `processing`.
   - Stok produk dan variasi berkurang secara otomatis dan atomik.
8. Klik tombol **Cek Status Pembayaran** di aplikasi untuk melihat pembaruan status instan.

---

## Menjalankan Automated Tests
Sistem pengujian TokoKu menggunakan database khusus `ecommerce_db_testing` sehingga database utama `ecommerce_db` tetap aman dan tidak terhapus:

```bash
php artisan test
```

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
