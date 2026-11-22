<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'username' => 'Admin',
                'password' => Hash::make('123456789'),
            ]
        );

        $this->call(PostinganSeeder::class);

        // Berikan saldo awal BJPay untuk akun uji coba
        User::whereIn('email', [
            'risethunder7@gmail.com',
            'budi.santoso@gmail.com',
            'siti.rahma@gmail.com',
            'ahmad.zaki@gmail.com',
        ])->update(['saldo_bjpay' => 500000]);

        // Seed data promo dan voucher diskon
        collect([
            [
                'title' => 'Voucher Diskon 10% Spesial Pengguna BJBO',
                'slug' => 'voucher-diskon-10-persen',
                'type' => 'voucher',
                'code' => 'BJBODISKON10',
                'discount_percentage' => 10,
                'discount_amount' => null,
                'min_purchase' => 50000,
                'banner_image' => null,
                'description' => 'Diskon 10% untuk transaksi minimal Rp 50.000 di seluruh kategori produk sedagho.',
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'title' => 'Potongan Langsung Rp 20.000 Sedagho Hemat',
                'slug' => 'potongan-langsung-20000-sedagho-hemat',
                'type' => 'voucher',
                'code' => 'SEDAGHOHEMAT',
                'discount_percentage' => null,
                'discount_amount' => 20000,
                'min_purchase' => 100000,
                'banner_image' => null,
                'description' => 'Potongan langsung Rp 20.000 untuk transaksi minimal Rp 100.000.',
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'title' => 'Promo Akbar Elektronik & Gadget Bengkalis',
                'slug' => 'promo-akbar-elektronik-gadget-bengkalis',
                'type' => 'banner',
                'code' => null,
                'discount_percentage' => 15,
                'discount_amount' => null,
                'min_purchase' => 0,
                'banner_image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Pesta diskon gadget dan produk elektronik seken berkualitas di Bengkalis dan sekitarnya.',
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(2),
                'is_active' => true,
            ],
        ])->each(function ($promoData) {
            Promo::updateOrCreate(
                ['slug' => $promoData['slug']],
                $promoData
            );
        });

        // Seed pesanan uji coba untuk analisis diagram kategori barang laku (1 minggu, 1 bulan, 1 tahun, all time)
        $posts = \App\Models\Postingan::all();
        $buyer = User::where('email', 'risethunder7@gmail.com')->first() ?: User::first();

        if ($posts->isNotEmpty() && $buyer) {
            $sampleOrders = collect([
                // 1 Minggu Terakhir (1-6 hari lalu)
                ['cat' => 'Elektronik', 'sub_days' => 2, 'price' => 2850000, 'status' => 'paid'],
                ['cat' => 'Elektronik', 'sub_days' => 3, 'price' => 1450000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 1, 'price' => 350000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 4, 'price' => 220000, 'status' => 'paid'],
                ['cat' => 'Otomotif', 'sub_days' => 2, 'price' => 310000, 'status' => 'paid'],
                ['cat' => 'Perabot', 'sub_days' => 5, 'price' => 420000, 'status' => 'paid'],
                ['cat' => 'Lainnya', 'sub_days' => 3, 'price' => 195000, 'status' => 'paid'],

                // 1 Bulan Terakhir (8-28 hari lalu)
                ['cat' => 'Elektronik', 'sub_days' => 12, 'price' => 2400000, 'status' => 'paid'],
                ['cat' => 'Elektronik', 'sub_days' => 18, 'price' => 450000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 14, 'price' => 400000, 'status' => 'paid'],
                ['cat' => 'Perabot', 'sub_days' => 19, 'price' => 520000, 'status' => 'paid'],
                ['cat' => 'Otomotif', 'sub_days' => 22, 'price' => 1200000, 'status' => 'paid'],

                // 1 Tahun Terakhir (45-180 hari lalu)
                ['cat' => 'Elektronik', 'sub_days' => 50, 'price' => 3100000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 80, 'price' => 500000, 'status' => 'paid'],
                ['cat' => 'Otomotif', 'sub_days' => 120, 'price' => 2500000, 'status' => 'paid'],
                ['cat' => 'Perabot', 'sub_days' => 150, 'price' => 750000, 'status' => 'paid'],
                ['cat' => 'Lainnya', 'sub_days' => 160, 'price' => 290000, 'status' => 'paid'],
            ]);

            $sampleOrders->each(function ($item, $idx) use ($posts, $buyer) {
                $post = $posts->firstWhere('category', $item['cat']) ?: $posts->first();
                $orderNum = 'BJBO-TEST-'.str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT);

                \App\Models\Pesanan::updateOrCreate(
                    ['order_number' => $orderNum],
                    [
                        'user_id' => $buyer->id,
                        'postingan_id' => $post->id,
                        'penjual_id' => $post->user_id,
                        'total_price' => $item['price'],
                        'final_price' => $item['price'],
                        'ongkir' => 10000,
                        'diskon_ongkir' => 0,
                        'status' => $item['status'],
                        'shipping_status' => $item['status'] === 'delivered' ? 'diterima_pembeli' : 'diproses',
                        'metode_pembayaran' => 'bjpay',
                        'payment_type' => 'bjpay',
                        'created_at' => now()->subDays($item['sub_days']),
                        'updated_at' => now()->subDays($item['sub_days']),
                    ]
                );
            });
        }

        // Seed pesanan uji coba untuk analisis diagram kategori barang laku (1 minggu, 1 bulan, 1 tahun, all time)
        $posts = \App\Models\Postingan::all();
        $buyer = User::where('email', 'risethunder7@gmail.com')->first() ?: User::first();

        if ($posts->isNotEmpty() && $buyer) {
            $sampleOrders = collect([
                // 1 Minggu Terakhir (1-6 hari lalu)
                ['cat' => 'Elektronik', 'sub_days' => 2, 'price' => 2850000, 'status' => 'paid'],
                ['cat' => 'Elektronik', 'sub_days' => 3, 'price' => 1450000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 1, 'price' => 350000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 4, 'price' => 220000, 'status' => 'paid'],
                ['cat' => 'Otomotif', 'sub_days' => 2, 'price' => 310000, 'status' => 'paid'],
                ['cat' => 'Perabot', 'sub_days' => 5, 'price' => 420000, 'status' => 'paid'],
                ['cat' => 'Lainnya', 'sub_days' => 3, 'price' => 195000, 'status' => 'paid'],

                // 1 Bulan Terakhir (8-28 hari lalu)
                ['cat' => 'Elektronik', 'sub_days' => 12, 'price' => 2400000, 'status' => 'paid'],
                ['cat' => 'Elektronik', 'sub_days' => 18, 'price' => 450000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 14, 'price' => 400000, 'status' => 'paid'],
                ['cat' => 'Perabot', 'sub_days' => 19, 'price' => 520000, 'status' => 'paid'],
                ['cat' => 'Otomotif', 'sub_days' => 22, 'price' => 1200000, 'status' => 'paid'],

                // 1 Tahun Terakhir (45-180 hari lalu)
                ['cat' => 'Elektronik', 'sub_days' => 50, 'price' => 3100000, 'status' => 'paid'],
                ['cat' => 'Fashion', 'sub_days' => 80, 'price' => 500000, 'status' => 'paid'],
                ['cat' => 'Otomotif', 'sub_days' => 120, 'price' => 2500000, 'status' => 'paid'],
                ['cat' => 'Perabot', 'sub_days' => 150, 'price' => 750000, 'status' => 'paid'],
                ['cat' => 'Lainnya', 'sub_days' => 160, 'price' => 290000, 'status' => 'paid'],
            ]);

            $sampleOrders->each(function ($item, $idx) use ($posts, $buyer) {
                $post = $posts->firstWhere('category', $item['cat']) ?: $posts->first();
                $orderNum = 'BJBO-TEST-' . str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT);

                \App\Models\Pesanan::updateOrCreate(
                    ['order_number' => $orderNum],
                    [
                        'user_id' => $buyer->id,
                        'postingan_id' => $post->id,
                        'penjual_id' => $post->user_id,
                        'total_price' => $item['price'],
                        'final_price' => $item['price'],
                        'ongkir' => 10000,
                        'diskon_ongkir' => 0,
                        'status' => $item['status'],
                        'shipping_status' => $item['status'] === 'delivered' ? 'diterima_pembeli' : 'diproses',
                        'metode_pembayaran' => 'bjpay',
                        'payment_type' => 'bjpay',
                        'created_at' => now()->subDays($item['sub_days']),
                        'updated_at' => now()->subDays($item['sub_days']),
                    ]
                );
            });
        }
    }
}
