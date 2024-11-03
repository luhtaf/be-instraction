<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PesertaController;
use App\Http\Controllers\Api\ArahanPimpinanController;
use App\Http\Controllers\Api\RapatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LoginDwsController;

Route::post('login-dws', [LoginDwsController::class, 'loginDws']);
Route::post('register',[AuthController::class,'register']);
Route::post('login', [AuthController::class,'login']);
Route::get('send', [NotifController::class,'send']);
Route::get('show', [NotifController::class,'show']);
Route::post('refresh', [AuthController::class,'refresh'])->middleware('AUTH');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('AUTH:admin')->group(function () {
    Route::get('total_arahan_belum_tl', [ArahanPimpinanController::class,'get_belum_tindak_lanjut']);
    Route::get('top5_arahan', [ArahanPimpinanController::class,'get_top_status_values']);
    Route::get('top5_penyelesaian', [ArahanPimpinanController::class,'get_top_penyelesaian_values']);
    Route::get('top5_kehadiran', [PesertaController::class,'get_top_undangan_values']);
    Route::post('statistik_kehadiran', [PesertaController::class,'statistic_peserta']);
    Route::post('statistik_arahan', [ArahanPimpinanController::class,'statistic_arahan']);
    Route::post('statistik_penyelesaian', [ArahanPimpinanController::class,'statistic_penyelesaian']);
    Route::get('coba_relationship/{id}', [RapatController::class,'rapat_relationship']);
    Route::get('tema', [RapatController::class,'getTema']);
    Route::get('top5_tema', [RapatController::class,'getTop5Tema']);
    Route::resource('/rapat', RapatController::class)->only(['show','index','store', 'update', 'destroy']);
    Route::prefix('rapat/{rapat}')->group(function () {
        Route::apiResource('penanggung_jawab', App\Http\Controllers\Api\PenanggungJawabController::class)->only(['index','show','store', 'update', 'destroy']);
        // Route::apiResource('arahan_pimpinan', ArahanPimpinanController::class)->only(['index','show','store', 'update', 'destroy']);

        Route::middleware('dec')->group(function(){
            Route::get('arahan_pimpinan', [ArahanPimpinanController::class,'index']);
            Route::get('arahan_pimpinan/{arahan_pimpinan}', [ArahanPimpinanController::class,'show']);
        });

        Route::middleware('enc')->group(function(){
            Route::post('arahan_pimpinan', [ArahanPimpinanController::class,'store']);
            Route::put('arahan_pimpinan/{arahan_pimpinan}', [ArahanPimpinanController::class,'update']);
        });


        Route::delete('arahan_pimpinan/{arahan_pimpinan}', [ArahanPimpinanController::class,'destroy']);


        Route::apiResource('kelengkapan_post', App\Http\Controllers\Api\KelengkapanPostController::class)->only(['index','show','store', 'update', 'destroy']);
        Route::get('/peserta', [PesertaController::class, 'index']);
        Route::post('/peserta', [PesertaController::class, 'store']);
        Route::get('/peserta/{peserta}', [PesertaController::class, 'show']);
        Route::patch('/peserta/{peserta}', [PesertaController::class, 'update']);
        Route::delete('/peserta/all', [PesertaController::class, 'destroy_all']);
        Route::delete('/peserta/{peserta}', [PesertaController::class, 'destroy']);
    });

});


Route::middleware('AUTH')->group(function () {
    Route::get('arahan_pimpinan', [ArahanPimpinanController::class,'all_arahan'])->middleware('dec');
    Route::get('units', [UnitController::class, 'getAllUnits']);
    Route::patch('/rapat/{rapat}/laporan_arahan_pimpinan/{arahan_pimpinan}', [ArahanPimpinanController::class, 'updateUnitkerja']);
    Route::get('/karyawan', [KaryawanController::class,'__invoke']);
});
// Route::get('/karyawan', [KaryawanController::class],'__invoke');


