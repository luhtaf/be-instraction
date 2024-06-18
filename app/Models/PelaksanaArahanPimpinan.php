<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PelaksanaArahanPimpinan extends Model
{
    protected $table = 'pelaksana_arahan_pimpinan';
    use HasFactory;
    use HasUuids;
    protected $fillable = [
        'arahan_pimpinan_id',
        'target_arahan',
        'keterangan'
    ];
}
