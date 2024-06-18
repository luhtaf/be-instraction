<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ArahanPimpinan extends Model
{
    protected $table = 'arahan_pimpinan';
    use HasFactory;
    use HasUuids;
    protected $fillable = [
        'rapat_id',
        'arahan',
        'deadline',
        'pelaksana',
        'status',
        'penyelesaian',
        'data_dukung',
        'keterangan'
    ];


    public function pelaksana_arahan_pimpinan(): HasMany
    {
        return $this->hasMany(PelaksanaArahanPimpinan::class);
    }
}
