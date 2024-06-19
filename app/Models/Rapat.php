<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Rapat extends Model
{
    protected $table = 'rapat';
    use HasFactory;
    use HasUuids;
    protected $fillable = [
        'nama',
        'kategori',
        'tanggal',
        'urgensi',
        'waktu',
        'lokasi',
        'metode',
        'penyelenggara',
        'pimpinan',
        'jenis',
        'pemapar',
        'tautan',
        'catatan',
        'keterangan',
        'tema'
    ];

    public static function getUniqueTemas()
    {
        return self::query()
            ->select(DB::raw('LOWER(tema) AS lower_tema'))
            ->distinct()
            ->pluck('lower_tema')
            ->map(function ($tema) {
                return Str::title($tema); // Capitalize each word
            });
    }

    public function kelengkapan_pre(): HasMany
    {
        return $this->hasMany(KelengkapanPre::class);
    }

    public function kelengkapan_post(): HasOne
    {
        return $this->hasOne(KelengkapanPost::class);
    }

    public function penanggung_jawab(): HasMany
    {
        return $this->hasMany(PenanggungJawab::class);
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(Peserta::class);
    }

    public function arahan_pimpinan(): HasMany
    {
        return $this->hasMany(ArahanPimpinan::class);
    }
}
