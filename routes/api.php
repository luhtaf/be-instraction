<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PesertaController;
use App\Http\Controllers\Api\ArahanPimpinanController;
use App\Http\Controllers\Api\RapatController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('arahan_pimpinan', [ArahanPimpinanController::class,'all_arahan']);
Route::get('total_arahan_belum_tl', [ArahanPimpinanController::class,'get_belum_tindak_lanjut']);
Route::get('tema', [RapatController::class,'getTema']);
Route::resource('/rapat', RapatController::class)->only(['show','index','store', 'update', 'destroy']);
Route::prefix('rapat/{rapat}')->group(function () {
    Route::apiResource('penanggung_jawab', App\Http\Controllers\Api\PenanggungJawabController::class)->only(['index','show','store', 'update', 'destroy']);
    Route::apiResource('arahan_pimpinan', ArahanPimpinanController::class)->only(['index','show','store', 'update', 'destroy']);

    Route::apiResource('kelengkapan_post', App\Http\Controllers\Api\KelengkapanPostController::class)->only(['index','show','store', 'update', 'destroy']);

    Route::get('/peserta', [PesertaController::class, 'index']);
    Route::post('/peserta', [PesertaController::class, 'store']);
    Route::get('/peserta/{peserta}', [PesertaController::class, 'show']);
    Route::patch('/peserta/{peserta}', [PesertaController::class, 'update']);
    Route::delete('/peserta/all', [PesertaController::class, 'destroy_all']);
    Route::delete('/peserta/{peserta}', [PesertaController::class, 'destroy']);

});
