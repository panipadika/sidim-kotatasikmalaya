<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    use HasFactory;

    protected $table = 'target';

    protected $fillable = [
        'indikator_id',
        'jenis_target',
        'periode',
        'tahun',
        'nilai_target',
        'nilai_target_range',
        'sumber',
        'keterangan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'nilai_target' => 'decimal:6',
    ];

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(Indikator::class);
    }
}
