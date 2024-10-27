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
        $today = Carbon::now('UTC')->toIso8601String();
        $today=$this->reformatDateString($today);
        // $expiredArahan = $this->arahanPimpinanModel::whereIn('status', ['Menunggu Konfirmasi Perbaikan', 'Dalam Proses'])
        $expiredArahan = $this->arahanPimpinanModel::where(function ($query) {
            $query->where('status', 'Menunggu Konfirmasi Perbaikan')
                  ->orWhereNull('status');
        })
        ->where('batas_konfirmasi', '<', $today)
        ->get();
        foreach ($expiredArahan as $arahan) {
            $arahan->status = 'Belum Ada Tindak Lanjut dari Unit Kerja';
            $arahan->save();
            $message = "Arahan dengan arahan {$arahan->arahan} dan id {$arahan->id} sudah melewati Batas Waktu, dan belum ada tindak lanjut. {$arahan->batas_konfirmasi}";
            $this->info($message); // Output to console
            Log::info($message); // Log to file
        }
    }

    private function reformatDateString($dateString) {
        // Ambil tanggal dan waktu dari string input menggunakan explode
        [$date, $time] = explode('T', $dateString);

        // Ambil bagian waktu dan hapus offsetnya (+07:00)
        $timeWithoutOffset = substr($time, 0, 8); // Mengambil 'HH:MM:SS' saja

        // Gabungkan kembali dengan format baru, menambahkan ".000Z"
        return "{$date}T{$timeWithoutOffset}.000Z";
    }
}
