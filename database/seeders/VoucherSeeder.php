<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'DISKONHEMAT',
                'name' => 'Diskon Hemat Rp 20.000',
                'type' => 'fixed',
                'discount_amount' => 20000,
                'min_spend' => 100000,
                'max_discount' => null,
                'quota' => 100,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'GAJIANSERU',
                'name' => 'Cashback Spesial Gajian 15%',
                'type' => 'percent',
                'discount_amount' => 15, // 15%
                'min_spend' => 150000,
                'max_discount' => 50000,
                'quota' => 200,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'GRATISONGKIR',
                'name' => 'Voucher Potongan Ongkir Rp 15.000',
                'type' => 'fixed',
                'discount_amount' => 15000,
                'min_spend' => 50000,
                'max_discount' => null,
                'quota' => 500,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(6),
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $v) {
            Voucher::firstOrCreate(['code' => $v['code']], $v);
        }
    }
}
