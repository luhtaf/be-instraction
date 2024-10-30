<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArahanPimpinan;
use Illuminate\Support\Facades\Log; // Import Log facade

class EncryptAllArahanInDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:encrypt-all-arahan-in-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enkrip semua arahan pimpinan di database';
    protected $arahanPimpinanModel;

    public function __construct(ArahanPimpinan $arahanPimpinanModel) {
        $this->arahanPimpinanModel = $arahanPimpinanModel;
        parent::__construct();
    }
    public function handle()
    {
        $arahan=$this->arahanPimpinanModel::all()->toArray();
        $encryptedData=$this->sealData($arahan);
        if ($encryptedData) {
            $hasilGabungan = array_map(function ($ori,$encrypt) {
                $ori['arahan']=$encryp['text'];
                return $ori;
            }, $arahan,$encryptedData);
            foreach ($hasilGabungan as $item) {
                $this->arahanPimpinanModel::where('id', $item['id'])
                    ->update(['arahan' => $item['arahan']]);
            }
            $this->info('Semua arahan pimpinan telah berhasil dienkripsi dan diperbarui di database.');
        }
        else {
            $this->error('Gagal mendapatkan data terenkripsi.');
        }
    }

    private function sealData($plainTexts)
    {
        $certFile = storage_path('app/crt/client.crt');
        $keyFile = storage_path('app/crt/client.key');
        $url = config('app.base_api_url') . '/seal'; // URL untuk enkripsi

        $payload = ['Plaintext' => array_map(function ($text) {
            return ['Text' => $text['arahan']];
        }, $plainTexts)];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $keyFile);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            // Log the error message
            Log::error('CURL error: ' . curl_error($ch));
            curl_close($ch);
            return null; // Return null on error
        }

        curl_close($ch);
        $parsedResponse = json_decode($response, true);
        return $parsedResponse['Ciphertext'] ?? null;
    }
}
