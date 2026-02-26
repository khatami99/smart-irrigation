<?php
// database/seeders/MasterDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Petak;
use App\Models\MusimTanam;
use App\Models\BlangkoOp;
use App\Models\User;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks dulu biar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Petak::truncate();
        MusimTanam::truncate();
        BlangkoOp::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── 1. PETAK ──
        $petaks = [
            ['kode_petak'=>'P-01','nama_petak'=>'Petak Sawah Blok A','luas_area'=>25.50,'lokasi_wilayah'=>'Desa Makmur, Kec. Mentaya Hilir','pintu_air'=>'PA-01','penanggung_jawab'=>'Ahmad Suryadi','status'=>'aktif'],
            ['kode_petak'=>'P-02','nama_petak'=>'Petak Sawah Blok B','luas_area'=>18.75,'lokasi_wilayah'=>'Desa Makmur, Kec. Mentaya Hilir','pintu_air'=>'PA-02','penanggung_jawab'=>'Ahmad Suryadi','status'=>'aktif'],
            ['kode_petak'=>'P-03','nama_petak'=>'Petak Sawah Blok C','luas_area'=>32.00,'lokasi_wilayah'=>'Desa Suka Maju, Kec. Mentaya Hilir','pintu_air'=>'PA-03','penanggung_jawab'=>'Budi Santoso','status'=>'aktif'],
            ['kode_petak'=>'P-04','nama_petak'=>'Petak Sawah Blok D','luas_area'=>15.25,'lokasi_wilayah'=>'Desa Suka Maju, Kec. Mentaya Hilir','pintu_air'=>'PA-04','penanggung_jawab'=>'Budi Santoso','status'=>'aktif'],
            ['kode_petak'=>'P-05','nama_petak'=>'Petak Sawah Blok E','luas_area'=>28.00,'lokasi_wilayah'=>'Desa Harapan, Kec. Seruyan Hilir','pintu_air'=>'PA-05','penanggung_jawab'=>'Slamet Riyadi','status'=>'aktif'],
            ['kode_petak'=>'P-06','nama_petak'=>'Petak Sawah Blok F','luas_area'=>20.50,'lokasi_wilayah'=>'Desa Harapan, Kec. Seruyan Hilir','pintu_air'=>'PA-06','penanggung_jawab'=>'Slamet Riyadi','status'=>'aktif'],
            ['kode_petak'=>'P-07','nama_petak'=>'Petak Sawah Blok G','luas_area'=>12.00,'lokasi_wilayah'=>'Desa Mulia, Kec. Seruyan Hilir','pintu_air'=>'PA-07','penanggung_jawab'=>'Hendra Wijaya','status'=>'nonaktif','keterangan'=>'Sedang dalam perbaikan saluran'],
        ];

        foreach ($petaks as $p) Petak::create($p);
        $this->command->info('✅ 7 petak berhasil dibuat');

        // ── 2. MUSIM TANAM ──
        $musims = [
            [
                'nama_mt'           => 'MT2 2024/2025',
                'jenis_mt'          => 'MT2',
                'tanggal_mulai'     => '2024-05-01',
                'tanggal_selesai'   => '2024-09-30',
                'target_luas_tanam' => 130.00,
                'jenis_tanaman'     => 'Padi',
                'status'            => 'selesai',
                'keterangan'        => 'MT selesai, hasil panen baik',
            ],
            [
                'nama_mt'           => 'MT3 2024/2025',
                'jenis_mt'          => 'MT3',
                'tanggal_mulai'     => '2024-10-01',
                'tanggal_selesai'   => '2025-01-31',
                'target_luas_tanam' => 100.00,
                'jenis_tanaman'     => 'Palawija',
                'status'            => 'selesai',
                'keterangan'        => 'Musim palawija, curah hujan cukup',
            ],
            [
                'nama_mt'           => 'MT1 2025/2026',
                'jenis_mt'          => 'MT1',
                'tanggal_mulai'     => '2025-11-01',
                'tanggal_selesai'   => '2026-03-31',
                'target_luas_tanam' => 152.00,
                'jenis_tanaman'     => 'Padi',
                'status'            => 'berjalan',
                'keterangan'        => 'Musim tanam utama sedang berjalan',
            ],
        ];

        foreach ($musims as $mt) MusimTanam::create($mt);
        $this->command->info('✅ 3 musim tanam berhasil dibuat');

        // ── 3. BLANGKO OP ──
        $juruId  = User::where('email', 'juru@irigasi.id')->value('id') ?? 1;
        $mtAktif = MusimTanam::where('status', 'berjalan')->first();
        $mtId    = $mtAktif->id;

        $petakAktif = Petak::where('status', 'aktif')->get();

        $periode = [
            ['tahun'=>2025,'bulan'=>11,'dekade'=>'I'],
            ['tahun'=>2025,'bulan'=>11,'dekade'=>'II'],
            ['tahun'=>2025,'bulan'=>11,'dekade'=>'III'],
            ['tahun'=>2025,'bulan'=>12,'dekade'=>'I'],
            ['tahun'=>2025,'bulan'=>12,'dekade'=>'II'],
            ['tahun'=>2025,'bulan'=>12,'dekade'=>'III'],
            ['tahun'=>2026,'bulan'=>1, 'dekade'=>'I'],
            ['tahun'=>2026,'bulan'=>1, 'dekade'=>'II'],
            ['tahun'=>2026,'bulan'=>1, 'dekade'=>'III'],
            ['tahun'=>2026,'bulan'=>2, 'dekade'=>'I'],
            ['tahun'=>2026,'bulan'=>2, 'dekade'=>'II'],
        ];

        $faseUrutan = [
            'pengolahan_tanah','tanam','vegetatif','vegetatif',
            'generatif','generatif','panen','bero',
            'pengolahan_tanah','tanam','vegetatif',
        ];

        foreach ($petakAktif as $petak) {
            foreach ($periode as $idx => $p) {
                $baseDebit = round($petak->luas_area * 3.5, 2);
                $baseLuas  = $petak->luas_area;
                $efisiensi = rand(80, 100) / 100;
                $chBase    = in_array($p['bulan'], [11, 12, 1]) ? rand(80, 180) : rand(20, 60);

                BlangkoOp::create([
                    'petak_id'         => $petak->id,
                    'musim_tanam_id'   => $mtId,
                    'user_id'          => $juruId,
                    'tahun'            => $p['tahun'],
                    'bulan'            => $p['bulan'],
                    'dekade'           => $p['dekade'],
                    'debit_rencana'    => $baseDebit,
                    'debit_realisasi'  => round($baseDebit * $efisiensi, 2),
                    'tinggi_muka_air'  => round(rand(350, 650) / 10, 1),
                    'luas_rencana'     => round($baseLuas, 2),
                    'luas_realisasi'   => round($baseLuas * $efisiensi, 2),
                    'fase_pertumbuhan' => $faseUrutan[$idx],
                    'kondisi_saluran'  => ['baik','baik','baik','rusak_ringan'][rand(0,3)],
                    'kondisi_bangunan' => ['baik','baik','rusak_ringan'][rand(0,2)],
                    'curah_hujan'      => $chBase,
                    'keterangan'       => null,
                ]);
            }
        }

        $this->command->info('✅ ' . BlangkoOp::count() . ' data blangko OP berhasil dibuat');
        $this->command->info('');
        $this->command->info('🌾 Summary:');
        $this->command->table(
            ['Data', 'Jumlah'],
            [
                ['Petak',       Petak::count()],
                ['Musim Tanam', MusimTanam::count()],
                ['Blangko OP',  BlangkoOp::count()],
            ]
        );
    }
}
