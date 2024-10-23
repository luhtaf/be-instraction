<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'uker';
    use HasFactory;

    protected $fillable = [
        'nama',
        'parent'
    ];

}
