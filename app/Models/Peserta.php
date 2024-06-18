<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Peserta extends Model
{
    protected $table = 'peserta';
    use HasFactory;
    use HasUuids;
    protected $fillable = [
        'rapat_id',
        'nama',
        'keterangan',
        'perwakilan',
        'jenis'
    ];
}
