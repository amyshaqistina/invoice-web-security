<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('directories')->truncate();
        DB::table('subdirectories')->truncate();

        $directories = [
            [
                'name' => 'Kenderaan',
                'subcategories' => [
                    'Kereta',
                    'Motosikal',
                    'Aksesori Kereta',
                    'Aksesori Motosikal',
                    'Lain-lain'
                ],
            ],
            [
                'name' => 'Perkhidmatan/Pekerjaan',
                'subcategories' => [
                    'Pekerjaan',
                    'Perkhidmatan/Servis',
                    'Kelas/Seminar/Workshop',
                    'Acara/Event Planner, Perkahwinan, Katering'
                ],
            ],
            [
                'name' => 'Hobi/Riadah/Aktiviti',
                'subcategories' => [
                    'Buku / Majalah',
                    'Peralatan Sukan & Riadah',
                    'Hobi & Barangan Koleksi',
                    'Muzik, Filem, Seni',
                    'Haiwan Peliharaan',
                    'Tiket & Voucer',
                    'Lain-lain'
                ],
            ],
            [
                'name' => 'Hartanah',
                'subcategories' => [
                    'Untuk Dijual',
                    'Untuk Disewa',
                    'Pembangunan Baru'
                ],
            ],
            [
                'name' => 'Percutian',
                'subcategories' => [
                    'Hotel, Pengginapan & Homestay',
                    'Pakej Percutian'
                ],
            ],
            [
                'name' => 'Pakaian/Aksesori/Perhiasan',
                'subcategories' => [
                    'Pakaian Wanita, Aksesori',
                    'Pakaian Lelaki, Aksesori',
                    'Tudung dan Aksesori',
                    'Perhiasan Diri, Kecantikan',
                    'Kesihatan',
                    'Ibu & Anak',
                    'Kasut, Sandal, Stokin',
                    'Beg, Wallet, Tali Pinggang',
                    'Jam',
                    'Lain-lain'
                ],
            ],
            [
                'name' => 'Perhiasan Rumah, Dapur, Kebun',
                'subcategories' => [
                    'Perhiasan Dalaman',
                    'Luaran Rumah',
                    'Kebun',
                    'Peralatan Dapur',
                    'Lain-lain'
                ],
            ],
            [
                'name' => 'Elektronik/Gadget',
                'subcategories' => [
                    'Telefon Pintar, Tablet',
                    'Aksesori Telefon',
                    'Komputer, Laptop, Desktop',
                    'Aksesori Komputer, Rangkaian, Gajet USB, & Perkakasan',
                    'Barangan Elektrik, TV/Audio/Video, Penghawa Dingin, Mesin Basuh, dan lain-lain',
                    'Kamera & Fotografi, Aksesori',
                    'Permainan/Konsol, Hiburan',
                    'Lain-lain'
                ],
            ],
            [
                'name' => 'Makanan/Minuman',
                'subcategories' => [
                    'Makanan/Minuman',
                    'Produk Sejuk Beku',
                    'Bahan Mentah Segar, Ikan, Daging, Sayur, etc',
                    'Kuih Muih, Biskut, Kek, etc',
                    'Lain-lain'
                ],
            ],
            [
                'name' => 'Digital',
                'subcategories' => [
                    'E-book, Audio/Video, Podcast, Tutorial/Panduan',
                    'Perisian, Aplikasi, Tool, Sistem Maklumat',
                    'Logo, Rekabentuk Grafik, Animasi, Montaj, Iklan, etc',
                    'Lain-lain'
                ],
            ],
            [
                'name' => 'Lain-lain',
                'subcategories' => [
                    'Simcard, Prepaid, Postpaid, Tambah Nilai',
                    'Lain-lain'
                ],
            ],
        ];

        foreach ($directories as $directory) {
            $directoryId = DB::table('directories')->insertGetId([
                'name' => $directory['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($directory['subcategories'] as $subcategory) {
                DB::table('subdirectories')->insert([
                    'directory_id' => $directoryId,
                    'name' => $subcategory,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
