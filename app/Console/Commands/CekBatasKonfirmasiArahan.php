<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // Import Log facade
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ArahanPimpinan;

class CekBatasKonfirmasiArahan extends Command
{

    protected $signature = 'app:cek-batas-konfirmasi-arahan';
    protected $description = 'Cek Arahan yang melewati Batas Konfirmasi';

    protected $arahanPimpinanModel;

    public function __construct(ArahanPimpinan $arahanPimpinanModel) {
        $this->arahanPimpinanModel = $arahanPimpinanModel;
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $today = now();
        $today = Carbon::now();
        $this->info($today); // Output to console
        $expiredArahan = $this->arahanPimpinanModel::whereIn('status', ['Menunggu Konfirmasi Perbaikan', 'Dalam Proses'])
        // ->whereNotNull('batas_konfirmasi')
        // ->where('batas_konfirmasi', '<', $today)
        ->get();

        foreach ($expiredArahan as $arahan) {
            // $arahan->status = 'Tidak Ada Tindak Lanjut dari Unit Kerja';
            // $arahan->save();
            if($arahan->batas_konfirmasi < $today){
                $this->info("lewat");
            }
            $message = "Arahan dengan arahan {$arahan->arahan} dan id {$arahan->id} sudah melewati Batas Waktu, dan belum ada tindak lanjut. {$arahan->batas_konfirmasi}";
            $this->info($message); // Output to console
            Log::info($message); // Log to file
        }
    }
}
