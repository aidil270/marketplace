<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Daftar Ekspedisi & Tarif Statis Pengiriman
    |--------------------------------------------------------------------------
    |
    | Digunakan sementara sebelum integrasi API RajaOngkir / Kurir langsung.
    |
    */

    'couriers' => [
        'jnt' => [
            'code' => 'jnt',
            'name' => 'J&T Express',
            'service' => 'EZ (Reguler)',
            'cost' => 14000,
            'etd' => '1-2 hari',
        ],
        'jne' => [
            'code' => 'jne',
            'name' => 'JNE Express',
            'service' => 'REG (Reguler)',
            'cost' => 15000,
            'etd' => '2-3 hari',
        ],
        'sicepat' => [
            'code' => 'sicepat',
            'name' => 'SiCepat Ekspres',
            'service' => 'SIUNTUNG',
            'cost' => 13000,
            'etd' => '2-3 hari',
        ],
    ],

    'default_courier' => 'jnt',
];
