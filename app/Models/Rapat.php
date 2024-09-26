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
        'tanggal_mulai',
        'tanggal_selesai',
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
        'tema',
        'link_rsvp'
    ];

    // public static function getUniqueTemasWithCounts($tanggalMulai=NULL,$tanggalSelesai=NULL)
    // {
    //     $query= self::query()
    //         ->select('tema', DB::raw('count(*) as count'))
    //         ->groupBy('tema');

    //     if ($tanggalMulai!=NULL && $tanggalSelesai!=NULL) {
    //         $query->whereBetween('deadline', [$tanggalMulai, $tanggalSelesai]);
    //     }

    //     $query->limit(5)
    //         ->get();

    //     return $query;
    // }

    public static function getUniqueTemasWithCounts($tanggalMulai = null, $tanggalSelesai = null)
    {
        $query = self::query()
            ->select('tema', DB::raw('count(*) as count'))
            ->groupBy('tema');

        if ($tanggalMulai != null && $tanggalSelesai != null) {
            $query->whereBetween('deadline', [$tanggalMulai, $tanggalSelesai]);
        }

        $result = $query->orderByDesc('count') // Order by count in descending order
            ->limit(5)
            ->get();

        return $result; // Return the result of the query
    }

    public static function getUniqueTemas()
    {
        return self::query()
            ->select(DB::raw('tema AS lower_tema'))
            ->distinct()
            ->pluck('lower_tema');
            // ->map(function ($tema) {
            //     return Str::title($tema); // Capitalize each word
            // });
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

    public function getJumlahArahanAttribute()
    {
        return $this->arahan_pimpinan->count();
    }

    public function getAllDataAttribute()
    {
        return array_merge($this->attributes, [
            'arahan_pimpinan' => $this->arahan_pimpinan->toArray(),
            'jumlah_arahan' => $this->jumlah_arahan,

        ]);
    }
}
