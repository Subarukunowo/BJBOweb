<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\Message;
use App\Models\Pesanan;
use App\Models\Postingan;
use App\Models\Promo;
use App\Models\Store;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private const DEFAULT_TEST_USER_EMAIL = 'risethunder7@gmail.com';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedAdminAndUsers();
        $this->call(PostinganSeeder::class);
        $this->seedStores();
        $this->seedPromos();
        $this->seedSampleOrders();
        $this->seedChatsAndNotifications();
    }

    protected function seedAdminAndUsers(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'username' => 'Admin',
                'password' => Hash::make('123456789'),
            ]
        );

        // Berikan saldo awal BJPay untuk akun uji coba
        User::whereIn('email', [
            self::DEFAULT_TEST_USER_EMAIL,
            'budi.santoso@gmail.com',
            'siti.rahma@gmail.com',
            'ahmad.zaki@gmail.com',
        ])->update(['saldo_bjpay' => 5000000]);
    }

    protected function seedStores(): void
    {
        collect([
            [
                'email' => self::DEFAULT_TEST_USER_EMAIL,
                'name' => 'Sedagho Tech Bengkalis',
                'slug' => 'sedagho-tech-bengkalis',
                'phone' => '081275001101',
                'address' => 'Jl. Sudirman No. 18, Bengkalis Kota',
                'desc' => 'Menjual laptop, elektronik, dan perlengkapan kerja bergaransi di Bengkalis.',
            ],
            [
                'email' => 'siti.rahma@gmail.com',
                'name' => 'Toko Rahma Elektronik',
                'slug' => 'toko-rahma-elektronik',
                'phone' => '081275002202',
                'address' => 'Jl. Ahmad Yani, Bengkalis Kota',
                'desc' => 'Pusat TV, audio rumah, dan elektronik keluarga terpercaya di Bengkalis.',
            ],
            [
                'email' => 'budi.santoso@gmail.com',
                'name' => 'Mandau Karya Mandiri',
                'slug' => 'mandau-karya-mandiri',
                'phone' => '081275003303',
                'address' => 'Jl. Jend. Sudirman, Mandau, Duri',
                'desc' => 'Penjual perangkat rumah tangga, AC, dan kebutuhan warga Mandau.',
            ],
        ])->each(function (array $storeData) {
            $owner = User::where('email', $storeData['email'])->first();
            if (! $owner) {
                return;
            }

            Store::updateOrCreate(
                ['user_id' => $owner->id],
                [
                    'store_name' => $storeData['name'],
                    'store_slug' => $storeData['slug'],
                    'store_type' => 'perorangan',
                    'phone_number' => $storeData['phone'],
                    'email' => $owner->email,
                    'description' => $storeData['desc'],
                    'address' => $storeData['address'],
                    'status' => 'aktif',
                    'verified_at' => now()->subDays(5),
                ]
            );
        });
    }

    protected function seedPromos(): void
    {
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
    }

    protected function seedSampleOrders(): void
    {
        $posts = Postingan::all();
        $buyer = User::where('email', self::DEFAULT_TEST_USER_EMAIL)->first() ?: User::first();

        if ($posts->isEmpty() || ! $buyer) {
            return;
        }

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

            Pesanan::updateOrCreate(
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

        $budi = User::where('email', 'budi.santoso@gmail.com')->first();
        $siti = User::where('email', 'siti.rahma@gmail.com')->first();
        $zaki = User::where('email', 'ahmad.zaki@gmail.com')->first();
        $tvPost = Postingan::where('name', 'like', '%Smart TV Sony%')->first() ?: $posts->first();
        $acPost = Postingan::where('name', 'like', '%AC Sharp%')->first() ?: $posts->last();

        if ($budi && $siti && $tvPost) {
            collect([
                [
                    'order_number' => 'BJBO-2POV-PAY-001',
                    'user_id' => $budi->id,
                    'postingan_id' => $tvPost->id,
                    'penjual_id' => $siti->id,
                    'total_price' => (int) $tvPost->price,
                    'final_price' => (int) $tvPost->price + 10000,
                    'ongkir' => 10000,
                    'diskon_ongkir' => 0,
                    'status' => 'pending',
                    'shipping_status' => 'menunggu_konfirmasi',
                    'kurir_name' => 'Kurir Lokal Sedagho Bengkalis',
                    'nomor_resi' => 'BKS-PAY2601',
                    'metode_pembayaran' => 'transfer',
                    'payment_type' => 'qris',
                    'last_checkpoint' => 'Menunggu pembayaran Midtrans / QRIS oleh Budi Santoso.',
                ],
                [
                    'order_number' => 'BJBO-2POV-SHIP-002',
                    'user_id' => $budi->id,
                    'postingan_id' => $tvPost->id,
                    'penjual_id' => $siti->id,
                    'total_price' => (int) $tvPost->price,
                    'final_price' => (int) $tvPost->price + 5000,
                    'ongkir' => 10000,
                    'diskon_ongkir' => 5000,
                    'status' => 'paid',
                    'shipping_status' => 'dikirim',
                    'kurir_name' => 'Kurir Lokal Sedagho Bengkalis',
                    'nomor_resi' => 'BKS-8842109',
                    'metode_pembayaran' => 'bjpay',
                    'payment_type' => 'bjpay',
                    'last_checkpoint' => 'Kurir Lokal Sedagho berada di Jl. Sudirman Bengkalis Kota menuju alamat pembeli.',
                    'origin_lat' => 1.4877,
                    'origin_lng' => 102.1334,
                    'origin_address' => 'Toko Rahma Elektronik - Bengkalis Kota',
                    'destination_lat' => 1.4725,
                    'destination_lng' => 102.1520,
                    'destination_address' => 'Jl. Jenderal Sudirman No. 45, Mandau',
                    'current_lat' => 1.4820,
                    'current_lng' => 102.1410,
                ],
                [
                    'order_number' => 'BJBO-2POV-PROC-003',
                    'user_id' => $zaki ? $zaki->id : $buyer->id,
                    'postingan_id' => $tvPost->id,
                    'penjual_id' => $siti->id,
                    'total_price' => (int) $tvPost->price,
                    'final_price' => (int) $tvPost->price + 18000,
                    'ongkir' => 18000,
                    'diskon_ongkir' => 0,
                    'status' => 'paid',
                    'shipping_status' => 'diproses',
                    'kurir_name' => 'J&T Express Reguler',
                    'nomor_resi' => 'JT-9921048',
                    'metode_pembayaran' => 'transfer',
                    'payment_type' => 'midtrans_inapp',
                    'last_checkpoint' => 'Pembayaran terverifikasi Midtrans. Toko Rahma sedang mengemas pesanan.',
                    'origin_lat' => 1.4877,
                    'origin_lng' => 102.1334,
                    'origin_address' => 'Toko Rahma Elektronik - Bengkalis Kota',
                    'destination_lat' => 1.4685,
                    'destination_lng' => 102.1480,
                    'destination_address' => 'Kecamatan Bantan, Bengkalis',
                    'current_lat' => 1.4860,
                    'current_lng' => 102.1355,
                ],
                [
                    'order_number' => 'BJBO-2POV-SELLER-004',
                    'user_id' => $zaki ? $zaki->id : $buyer->id,
                    'postingan_id' => $acPost->id,
                    'penjual_id' => $budi->id,
                    'total_price' => (int) $acPost->price,
                    'final_price' => (int) $acPost->price + 10000,
                    'ongkir' => 10000,
                    'diskon_ongkir' => 0,
                    'status' => 'paid',
                    'shipping_status' => 'menunggu_konfirmasi',
                    'kurir_name' => 'Kurir Lokal Sedagho Bengkalis',
                    'nomor_resi' => 'BKS-7731902',
                    'metode_pembayaran' => 'transfer',
                    'payment_type' => 'qris',
                    'last_checkpoint' => 'Pembayaran QRIS diterima, menunggu konfirmasi penjual Budi Santoso.',
                ],
            ])->each(function (array $data) {
                $pesanan = Pesanan::updateOrCreate(
                    ['order_number' => $data['order_number']],
                    array_merge($data, [
                        'settlement_time' => $data['status'] === 'paid' ? now()->subHours(2) : null,
                    ])
                );
                app(InvoiceService::class)->generateInvoice($pesanan);
            });
        }
    }

    protected function seedChatsAndNotifications(): void
    {
        $sandy = User::where('email', self::DEFAULT_TEST_USER_EMAIL)->first();
        $budi = User::where('email', 'budi.santoso@gmail.com')->first();
        $siti = User::where('email', 'siti.rahma@gmail.com')->first();
        $tvPost = Postingan::where('name', 'like', '%Smart TV Sony%')->first() ?: Postingan::first();
        $laptopPost = Postingan::where('name', 'like', '%Laptop Lenovo%')->first() ?: Postingan::first();

        if ($budi && $siti && $tvPost) {
            $pairHasMessages = Message::where(function ($q) use ($budi, $siti) {
                $q->where('sender_id', $budi->id)->where('receiver_id', $siti->id);
            })->orWhere(function ($q) use ($budi, $siti) {
                $q->where('sender_id', $siti->id)->where('receiver_id', $budi->id);
            })->exists();

            if (! $pairHasMessages) {
                collect([
                    ['from' => $budi->id, 'to' => $siti->id, 'mins' => 45, 'text' => 'Halo Kak Siti, apakah Smart TV Sony Bravia 43 Inch masih tersedia dan layarnya mulus?'],
                    ['from' => $siti->id, 'to' => $budi->id, 'mins' => 40, 'text' => 'Halo Kak Budi! Masih tersedia, layar mulus tanpa dead pixel, remote & WiFi normal semua.'],
                    ['from' => $budi->id, 'to' => $siti->id, 'mins' => 32, 'text' => 'Baik Kak, saya sudah bayar via Midtrans pakai Kurir Lokal Sedagho ya. Tolong dipacking rapi.'],
                    ['from' => $siti->id, 'to' => $budi->id, 'mins' => 24, 'text' => 'Siap Kak Budi, pembayaran sudah masuk ke sistem. Paket sudah dijemput Kurir Lokal Sedagho (Resi: BKS-8842109).'],
                ])->each(function (array $row) use ($tvPost) {
                    Message::create([
                        'sender_id' => $row['from'],
                        'receiver_id' => $row['to'],
                        'postingan_id' => $tvPost->id,
                        'message' => Crypt::encryptString($row['text']),
                        'is_reported' => false,
                        'is_read' => false,
                        'created_at' => now()->subMinutes($row['mins']),
                        'updated_at' => now()->subMinutes($row['mins']),
                    ]);
                });
            }
        }

        if ($budi && $sandy && $laptopPost) {
            $pairHasLaptop = Message::where(function ($q) use ($budi, $sandy) {
                $q->where('sender_id', $budi->id)->where('receiver_id', $sandy->id);
            })->orWhere(function ($q) use ($budi, $sandy) {
                $q->where('sender_id', $sandy->id)->where('receiver_id', $budi->id);
            })->exists();

            if (! $pairHasLaptop) {
                collect([
                    ['from' => $budi->id, 'to' => $sandy->id, 'mins' => 18, 'text' => 'Halo Kak Sandy, Laptop Lenovo ThinkPad X270 SSD-nya bisa kirim ke Mandau hari ini?'],
                    ['from' => $sandy->id, 'to' => $budi->id, 'mins' => 10, 'text' => 'Bisa Kak, charger original lengkap dan siap kirim lewat J&T Express atau Kurir Sedagho.'],
                ])->each(function (array $row) use ($laptopPost) {
                    Message::create([
                        'sender_id' => $row['from'],
                        'receiver_id' => $row['to'],
                        'postingan_id' => $laptopPost->id,
                        'message' => Crypt::encryptString($row['text']),
                        'is_reported' => false,
                        'is_read' => false,
                        'created_at' => now()->subMinutes($row['mins']),
                        'updated_at' => now()->subMinutes($row['mins']),
                    ]);
                });
            }
        }

        if ($budi && $siti) {
            collect([
                // POV Pembeli (Budi Santoso)
                [
                    'user_id' => $budi->id,
                    'type' => 'pembayaran',
                    'title' => 'Pembayaran Midtrans Terverifikasi',
                    'message' => 'Pembayaran pesanan #BJBO-2POV-SHIP-002 (Smart TV Sony Bravia 43 Inch) senilai Rp2.405.000 telah berhasil diverifikasi.',
                    'data' => ['order_number' => 'BJBO-2POV-SHIP-002'],
                ],
                [
                    'user_id' => $budi->id,
                    'type' => 'pesanan',
                    'title' => 'Pesanan Sedang Dikirim Kurir',
                    'message' => 'Penjual Siti Rahmawati mengirim paket via Kurir Lokal Sedagho Bengkalis (Resi: BKS-8842109). Pantau kurir di peta.',
                    'data' => ['kurir' => 'Kurir Lokal Sedagho Bengkalis', 'resi' => 'BKS-8842109'],
                ],
                [
                    'user_id' => $budi->id,
                    'type' => 'chat',
                    'title' => 'Pesan Baru dari Siti Rahmawati',
                    'message' => 'Siap Kak Budi, pembayaran sudah masuk ke sistem. Paket sudah dijemput Kurir Lokal Sedagho.',
                    'data' => ['sender_name' => 'Siti Rahmawati'],
                ],
                // POV Penjual (Siti Rahmawati)
                [
                    'user_id' => $siti->id,
                    'type' => 'pembayaran',
                    'title' => 'Pembayaran Diterima - #BJBO-2POV-PROC-003',
                    'message' => 'Pembeli Ahmad Zaki telah menyelesaikan pembayaran via Midtrans In-App sebesar Rp2.418.000. Segera proses & input resi pengiriman.',
                    'data' => ['order_number' => 'BJBO-2POV-PROC-003'],
                ],
                [
                    'user_id' => $siti->id,
                    'type' => 'rekber',
                    'title' => 'Dana Escrow Rekber Terkunci Aman',
                    'message' => 'Dana penjualan Smart TV Sony Bravia sebesar Rp2.400.000 diamankan di Rekber BJBO dan cair ke saldo penjual setelah barang diterima pembeli.',
                    'data' => ['amount' => 2400000],
                ],
                [
                    'user_id' => $siti->id,
                    'type' => 'chat',
                    'title' => 'Pesan Baru dari Pembeli Budi Santoso',
                    'message' => 'Baik Kak, saya sudah bayar via Midtrans pakai Kurir Lokal Sedagho ya. Tolong dipacking rapi.',
                    'data' => ['sender_name' => 'Budi Santoso'],
                ],
            ])->each(function (array $notif) {
                AppNotification::updateOrCreate(
                    ['user_id' => $notif['user_id'], 'title' => $notif['title']],
                    [
                        'message' => $notif['message'],
                        'type' => $notif['type'],
                        'data' => $notif['data'],
                        'is_read' => false,
                    ]
                );
            });
        }
    }
}
