<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class PenanggungJawab extends Model
{
    protected $table = 'penanggung_jawab';
    use HasFactory;
    use HasUuids;
    protected $fillable = [
        'rapat_id',
        'nama_personil',
        'role'
    ];
}
