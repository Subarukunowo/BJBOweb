<?php

namespace Database\Seeders;

use App\Models\Postingan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class PostinganSeeder extends Seeder
{
    public function run(): void
    {
        $storagePostinganDir = storage_path('app/public/postingan');
        if (! File::exists($storagePostinganDir)) {
            File::makeDirectory($storagePostinganDir, 0755, true);
        }

        $imageMappings = [
            'ac.jpg' => 'ac.jpg',
            'lenovo.jpg' => 'lenovo.jpg',
            'tv.jpg' => 'tv.jpg',
            'sony.jpg' => 'sony.jpg',
            'iklan1.jpg' => 'iklan1.jpg',
            'iklan2.jpg' => 'iklan2.jpg',
            'iklan3.jpg' => 'iklan3.jpg',
            'iklan4.jpg' => 'iklan4.jpg',
            'iklan5.jpg' => 'iklan5.jpg',
            'iklan6.jpg' => 'iklan6.jpg',
            'iklan7.jpg' => 'iklan7.jpg',
            'iklan8.jpg' => 'iklan8.jpg',
        ];

        foreach ($imageMappings as $src => $dest) {
            $srcPath = public_path('images/'.$src);
            $destPath = $storagePostinganDir.'/'.$dest;
            if (File::exists($srcPath) && ! File::exists($destPath)) {
                File::copy($srcPath, $destPath);
            }
        }

        $user1 = User::firstOrCreate(
            ['email' => 'risethunder7@gmail.com'],
            [
                'name' => 'Sandy mulia Kesuma',
                'password' => Hash::make('password123'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'budi.santoso@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
            ]
        );

        $user3 = User::firstOrCreate(
            ['email' => 'siti.rahma@gmail.com'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('password123'),
            ]
        );

        $user4 = User::firstOrCreate(
            ['email' => 'ahmad.zaki@gmail.com'],
            [
                'name' => 'Ahmad Zaki',
                'password' => Hash::make('password123'),
            ]
        );

        $postingsData = [
            [
                'name' => 'Laptop Lenovo ThinkPad X270 Core i5 Ram 8GB SSD',
                'price' => 2850000,
                'category' => 'Elektronik',
                'description' => 'Kondisi mulus 92 persen, baterai masih awet 3-4 jam. Cocok untuk kerja kantor, kuliah, dan olah data. Lengkap charger original.',
                'image' => 'postingan/lenovo.jpg',
                'lokasi' => 'Bengkalis Kota',
                'user_id' => $user1->id,
                'username' => $user1->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'AC Sharp 1 PK Plasmacluster Dingin Normal',
                'price' => 1450000,
                'category' => 'Elektronik',
                'description' => 'AC Sharp 1 PK second terawat, baru dicuci dan diisi freon penuh. Dingin menusuk, pipa 3 meter include remote original.',
                'image' => 'postingan/ac.jpg',
                'lokasi' => 'Mandau, Duri',
                'user_id' => $user2->id,
                'username' => $user2->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Smart TV Sony Bravia 43 Inch Full HD Audio Jernih',
                'price' => 2400000,
                'category' => 'Elektronik',
                'description' => 'Layar bening tanpa dead pixel, koneksi WiFi lancar untuk YouTube dan Netflix. Dijual karena mau upgrade ukuran lebih besar.',
                'image' => 'postingan/tv.jpg',
                'lokasi' => 'Pinggir',
                'user_id' => $user3->id,
                'username' => $user3->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Headphone Sony Wireless Extra Bass Masih Segel',
                'price' => 450000,
                'category' => 'Elektronik',
                'description' => 'Koleksi audio bass mantap, batre tahan 30 jam pemakaian. Ada kabel aux cadangan dan box lengkap.',
                'image' => 'postingan/sony.jpg',
                'lokasi' => 'Bantan',
                'user_id' => $user4->id,
                'username' => $user4->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Jaket Parasut Windbreaker Outdoor Waterproof',
                'price' => 165000,
                'category' => 'Fashion',
                'description' => 'Bahan tebal anti angin dan gerimis ringan, cocok untuk pengendara motor harian di wilayah Bengkalis dan sekitarnya.',
                'image' => 'https://images.unsplash.com/photo-1544923246-77307dd654cb?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Bengkalis Kota',
                'user_id' => $user1->id,
                'username' => $user1->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Sepatu Sneakers Pria Casual Size 42 Ringan Nyaman',
                'price' => 220000,
                'category' => 'Fashion',
                'description' => 'Kondisi 95 persen baru dipakai 2 kali ke acara kampus. Sol empuk tidak bikin pegal, box masih ada.',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Mandau, Duri',
                'user_id' => $user2->id,
                'username' => $user2->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Tas Ransel Laptop Anti Maling USB Port Charger',
                'price' => 135000,
                'category' => 'Fashion',
                'description' => 'Kapasitas muat laptop 15.6 inch, banyak kompartemen rahasia, busa punggung tebal dan adem.',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Bukit Batu',
                'user_id' => $user3->id,
                'username' => $user3->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Helm Full Face KYT Falcon FR Size L Busa Padat',
                'price' => 580000,
                'category' => 'Otomotif',
                'description' => 'Visor bening anti gores, sistem ventilasi lancar, busa dalam masih wangi dan terawat.',
                'image' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Mandau, Duri',
                'user_id' => $user4->id,
                'username' => $user4->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Cover Motor Outdoor All Weather Anti Panas Hujan',
                'price' => 85000,
                'category' => 'Otomotif',
                'description' => 'Bahan parasut tebal dilapisi coating perak penolak panas matahari, pas untuk motor matic besar seperti NMAX atau PCX.',
                'image' => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Bengkalis Kota',
                'user_id' => $user1->id,
                'username' => $user1->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Knalpot Racing Bobokan Suara Adem Ngebass',
                'price' => 310000,
                'category' => 'Otomotif',
                'description' => 'Tarikan lebih bertenaga, suara ramah lingkungan tidak bising, leher stainless anti karat.',
                'image' => 'https://images.unsplash.com/photo-1508974239320-0a029497e820?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Rupat',
                'user_id' => $user2->id,
                'username' => $user2->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Meja Belajar Minimalis Kayu Jati Belanda Kokoh',
                'price' => 420000,
                'category' => 'Perabot',
                'description' => 'Finishing vernis natural semi gloss, kaki besi hollow cat hitam doff anti karat, cocok untuk ruangan santai.',
                'image' => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Bengkalis Kota',
                'user_id' => $user3->id,
                'username' => $user3->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Kursi Kerja Ergonomis Hidrolik Sandaran Jaring',
                'price' => 520000,
                'category' => 'Perabot',
                'description' => 'Ketinggian bisa diatur dengan hidrolik lancar, sandaran bisa rebah sedikit, roda putar 360 derajat mulus.',
                'image' => 'https://images.unsplash.com/photo-1580481077195-c328a37db71a?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Mandau, Duri',
                'user_id' => $user4->id,
                'username' => $user4->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Rak Susun Kayu Dapur Serbaguna 4 Tingkat',
                'price' => 195000,
                'category' => 'Perabot',
                'description' => 'Praktis untuk bumbu dapur, microwave, dispenser atau buku hiasan. Bahan kayu solid kuat menahan beban.',
                'image' => 'https://images.unsplash.com/photo-1595428774223-ef52624120d2?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Bantan',
                'user_id' => $user1->id,
                'username' => $user1->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Gitar Akustik Pemula Suara Nyaring Senar String',
                'price' => 290000,
                'category' => 'Lainnya',
                'description' => 'Neck lurus, action senar rendah tidak sakit di jari, bonus tas gitar dan pick.',
                'image' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Bengkalis Kota',
                'user_id' => $user2->id,
                'username' => $user2->name,
                'status' => 'disetujui',
            ],
            [
                'name' => 'Raket Badminton Carbon Flex Plus Grip Karet',
                'price' => 210000,
                'category' => 'Lainnya',
                'description' => 'Tarikan senar 28 lbs suara garing, frame ringan untuk smash dan manuver cepat di lapangan.',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&w=600&q=80',
                'lokasi' => 'Pinggir',
                'user_id' => $user3->id,
                'username' => $user3->name,
                'status' => 'disetujui',
            ],
        ];

        foreach ($postingsData as $post) {
            Postingan::firstOrCreate(
                ['name' => $post['name']],
                $post
            );
        }
    }
}
