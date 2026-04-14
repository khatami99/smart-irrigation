<?php

namespace App\Console\Commands;

use App\Models\AiPrediction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PrediksiAI extends Command
{
    protected $signature   = 'ai:prediksi';
    protected $description = 'Jalankan training Linear Regression dan simpan prediksi ke DB';

    public function handle(): int
    {
        $this->info('Menjalankan predict.py...');

        $pythonPath = base_path('predict.py');
        $output     = shell_exec("python $pythonPath");

        if (!$output) {
            AiPrediction::create([
                'prediksi'   => 0,
                'status'     => 'error',
                'pesan'      => 'Tidak ada output dari Python',
                'trained_at' => now(),
            ]);
            $this->error('Gagal: tidak ada output dari Python.');
            return self::FAILURE;
        }

        preg_match('/Prediksi Kebutuhan Air Besok: ([\d.]+)/', $output, $m1);
        preg_match('/Akurasi Model R2: (-?[\d.]+)/', $output, $m2);
        preg_match('/RMSE: (-?[\d.]+)/',             $output, $m3);

        $prediksi = (float) ($m1[1] ?? 0);
        $r2       = (float) ($m2[1] ?? 0);
        $rmse     = (float) ($m3[1] ?? 0);

        $status = $prediksi > 0 ? 'ok' : 'insufficient_data';

        AiPrediction::create([
            'prediksi'   => $prediksi,
            'r2'         => $r2,
            'rmse'       => $rmse,
            'status'     => $status,
            'pesan'      => $output,
            'trained_at' => now(),
        ]);

        Log::info('AI Prediction saved', compact('prediksi', 'r2', 'rmse'));
        $this->info("Prediksi: {$prediksi} mm | R²: {$r2} | RMSE: {$rmse}");

        return self::SUCCESS;
    }
}
