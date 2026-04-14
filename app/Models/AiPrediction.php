<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPrediction extends Model
{
    protected $fillable = [
        'prediksi', 'r2', 'rmse', 'status', 'pesan', 'trained_at'
    ];

    protected $casts = [
        'trained_at' => 'datetime',
    ];
}
