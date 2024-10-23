<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Karyawan extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'karyawan';
    protected $fillable = [
        'guid_dws',
        'nama',
        'email',
        'jabatan',
        'unit_kerja',
    ];
}
