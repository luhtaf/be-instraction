<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class KelengkapanPre extends Model
{
    protected $table = 'kelengkapan_pre';
    use HasFactory;
    use HasUuids;
    protected $fillable = [
        'rapat_id',
        'poin',
        'keterangan'
    ];

}
