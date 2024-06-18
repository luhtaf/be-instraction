<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function show()
    {
        $user=[
            'name'=>'boy'
        ];
        return Inertia::render('Show', [
        'user' => $user
        ]);
    }
}
