<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function __invoke(Request $request) {
        $units = Karyawan::all();

        return response()->json($units);
    }
}
