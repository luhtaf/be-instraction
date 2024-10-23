<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;

class UnitController extends Controller
{
    public function getAllUnits(): JsonResponse
    {
        $units = Unit::all();

        return response()->json($units);
    }
}
