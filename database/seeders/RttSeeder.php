<?php
// database/seeders/RttSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Rtt;
use App\Models\Petak;
use App\Models\MusimTanam;
use App\Models\User;
use Carbon\Carbon;

class RttSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Rtt::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $mtAktif = MusimTanam::where('status', 'berjalan')->first();
        $userId  = User::where('email', 'juru@irigasi.id')->value('id') ?? 1;
        $petaks  = Petak::where('status', 'aktif')->orderBy('kode_petak')->get();

        // Jadwal rotasi — tiap petak mulai tanam beda 10 hari (giliran air)
        $mulaiMT = Carbon::parse($mtAktif->tanggal_mulai);

        $statuses = ['selesai','selesai','berjalan','berjalan','rencana','rencana'];

        foreach ($petaks as $i => $petak) {
            $mulai   = $mulaiMT->copy()->addDays($i * 10);
            $selesai = $mulai->copy()->addDays(119); // 120 hari 1 siklus padi

            $status = $statuses[$i] ?? 'rencana';

            $realisasiMulai   = null;
            $realisasiSelesai = null;
            $realisasiLuas    = null;

            if (in_array($status, ['selesai', 'berjalan'])) {
                $realisasiMulai = $mulai->copy()->addDays(rand(0, 3))->format('Y-m-d');
                if ($status === 'selesai') {
                    $realisasiSelesai = $selesai->copy()->addDays(rand(-2, 5))->format('Y-m-d');
                    $realisasiLuas    = round($petak->luas_area * (rand(85, 100) / 100), 2);
                }
            }

            Rtt::create([
                'petak_id'               => $petak->id,
                'musim_tanam_id'         => $mtAktif->id,
                'user_id'                => $userId,
                'rencana_mulai_tanam'    => $mulai->format('Y-m-d'),
                'rencana_selesai_tanam'  => $selesai->format('Y-m-d'),
                'realisasi_mulai_tanam'  => $realisasiMulai,
                'realisasi_selesai_tanam'=> $realisasiSelesai,
                'target_luas'            => $petak->luas_area,
                'realisasi_luas'         => $realisasiLuas,
                'urutan_rotasi'          => $i + 1,
                'durasi_pemberian_air'   => 10,
                'status'                 => $status,
                'jadwal_fase'            => Rtt::generateJadwalFase($mulai->format('Y-m-d'), $selesai->format('Y-m-d')),
                'keterangan'             => null,
            ]);
        }

        $this->command->info('✅ ' . Rtt::count() . ' RTT berhasil dibuat');
    }
}
