<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KcKoefisienSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kc_koefisien')->truncate();

        $data = [];

        // ──────────────────────────────────────────────
        // PADI UNGGUL (90 hari = 9 dekade)
        // ──────────────────────────────────────────────
        $padiUnggul = [
            [1, 'Pengolahan Lahan',      1.100, 1],
            [2, 'Awal Tanam',            1.100, 1],
            [3, 'Pembentukan Anakan',    1.100, 1],
            [4, 'Pembentukan Anakan',    1.100, 1],
            [5, 'Pemanjangan Batang',    1.050, 1],
            [6, 'Pemanjangan Batang',    1.050, 1],
            [7, 'Pembungaan',            1.000, 1],
            [8, 'Pemasakan',             0.950, 1],
            [9, 'Panen',                 0.000, 1],
        ];
        foreach ($padiUnggul as [$fase, $nama, $kc, $durasi]) {
            $data[] = ['komoditas' => 'padi_unggul', 'fase_ke' => $fase, 'nama_fase' => $nama, 'kc' => $kc, 'durasi_dekade' => $durasi];
        }

        // ──────────────────────────────────────────────
        // PADI BIASA (120 hari = 12 dekade)
        // ──────────────────────────────────────────────
        $padiBiasa = [
            [1,  'Pengolahan Lahan',      1.100, 1],
            [2,  'Awal Tanam',            1.100, 1],
            [3,  'Pembentukan Anakan',    1.100, 1],
            [4,  'Pembentukan Anakan',    1.100, 1],
            [5,  'Pembentukan Anakan',    1.050, 1],
            [6,  'Pemanjangan Batang',    1.050, 1],
            [7,  'Pemanjangan Batang',    1.050, 1],
            [8,  'Pemanjangan Batang',    1.000, 1],
            [9,  'Pembungaan',            1.000, 1],
            [10, 'Pembungaan',            0.950, 1],
            [11, 'Pemasakan',             0.950, 1],
            [12, 'Panen',                 0.000, 1],
        ];
        foreach ($padiBiasa as [$fase, $nama, $kc, $durasi]) {
            $data[] = ['komoditas' => 'padi_biasa', 'fase_ke' => $fase, 'nama_fase' => $nama, 'kc' => $kc, 'durasi_dekade' => $durasi];
        }

        // ──────────────────────────────────────────────
        // PALAWIJA (60 hari = 6 dekade)
        // Kc rata-rata 0.75 sesuai KP-01
        // ──────────────────────────────────────────────
        $palawija = [
            [1, 'Awal Tanam',     0.500, 1],
            [2, 'Vegetatif',      0.750, 1],
            [3, 'Vegetatif',      0.850, 1],
            [4, 'Pembungaan',     0.850, 1],
            [5, 'Pemasakan',      0.750, 1],
            [6, 'Panen',          0.000, 1],
        ];
        foreach ($palawija as [$fase, $nama, $kc, $durasi]) {
            $data[] = ['komoditas' => 'palawija', 'fase_ke' => $fase, 'nama_fase' => $nama, 'kc' => $kc, 'durasi_dekade' => $durasi];
        }

        // ──────────────────────────────────────────────
        // TEBU (12 bulan = 36 dekade)
        // Kc rata-rata 0.85 sesuai KP-01
        // ──────────────────────────────────────────────
        $tebu = [
            [1,  'Perkecambahan',     0.400, 1],
            [2,  'Perkecambahan',     0.500, 1],
            [3,  'Anakan Awal',       0.650, 1],
            [4,  'Anakan Awal',       0.750, 1],
            [5,  'Anakan Awal',       0.800, 1],
            [6,  'Anakan Awal',       0.850, 1],
            [7,  'Pemanjangan',       0.900, 1],
            [8,  'Pemanjangan',       0.950, 1],
            [9,  'Pemanjangan',       1.000, 1],
            [10, 'Pemanjangan',       1.000, 1],
            [11, 'Pemanjangan',       1.000, 1],
            [12, 'Pemanjangan',       1.000, 1],
            [13, 'Pemanjangan',       1.000, 1],
            [14, 'Pemanjangan',       1.000, 1],
            [15, 'Pemanjangan',       0.950, 1],
            [16, 'Pemanjangan',       0.950, 1],
            [17, 'Pemanjangan',       0.900, 1],
            [18, 'Pemanjangan',       0.900, 1],
            [19, 'Pematangan',        0.850, 1],
            [20, 'Pematangan',        0.850, 1],
            [21, 'Pematangan',        0.800, 1],
            [22, 'Pematangan',        0.800, 1],
            [23, 'Pematangan',        0.750, 1],
            [24, 'Pematangan',        0.700, 1],
        ];
        foreach ($tebu as [$fase, $nama, $kc, $durasi]) {
            $data[] = ['komoditas' => 'tebu', 'fase_ke' => $fase, 'nama_fase' => $nama, 'kc' => $kc, 'durasi_dekade' => $durasi];
        }

        DB::table('kc_koefisien')->insert($data);

        $this->command->info('KcKoefisien seeded: ' . count($data) . ' records.');
    }
}
