<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class KelengkapanPost extends Model
{
    protected $table = 'kelengkapan_post';
    use HasFactory;
    use HasUuids;
    protected $fillable = [
        'rapat_id',
        'undangan',
        'rekaman',
        'risalah',
        'bahan',
        'absen',
        'laporan',
        'dokumentasi'
    ];
}
