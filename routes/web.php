<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/home', function ()
// {
//     $user=[
//         'name'=>'boy'
//     ];
//     return Inertia::render('Show', [
//     'user' => $user
//     ]);
// });
