<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indikator extends Model
{
    protected $table = 'indikator';

    protected $fillable = [
        'parent_id',
        'nama_indikator',
        'satuan',
        'jenis',
        'deskripsi',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Indikator::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Indikator::class, 'parent_id');
    }

    public function capaians(): HasMany
    {
        return $this->hasMany(Capaian::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        if (is_numeric($value)) {
            return $this->where('id', $value)->first();
        }

        $normalized = strtolower(trim((string) $value));
        if ($normalized === 'ipm') {
            return $this->where(function ($query) {
                $query->whereRaw('LOWER(nama_indikator) = ?', ['ipm'])
                    ->orWhereRaw('LOWER(nama_indikator) LIKE ?', ['%ipm%'])
                    ->orWhereRaw('LOWER(nama_indikator) LIKE ?', ['%indeks pembangunan manusia%']);
            })->first();
        }

        return $this->where('nama_indikator', 'like', "%{$value}%")->first();
    }
}
