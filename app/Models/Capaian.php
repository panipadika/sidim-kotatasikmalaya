<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Capaian extends Model
{
    use HasFactory;

    protected $table = 'capaian';

    protected $fillable = [
        'indikator_id',
        'tahun',
        'nilai',
        'sumber',
        'keterangan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'nilai' => 'decimal:6',
    ];

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }
}
