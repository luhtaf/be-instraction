<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Import Log facade
class DecryptMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (is_array($response->original)) {
            $responseArray=$response->original;
            $originalData=$responseArray['data'];
            $decryptedData=$this->unsealData($originalData);
            if ($decryptedData) {
                $hasilGabungan = array_map(function ($text,$text2) {
                    $text['arahan']=$text2['text'];
                    return $text;
                }, $originalData,$decryptedData);
                $responseArray['data']=$hasilGabungan;
                $response->setContent(json_encode( $responseArray));
            }
        }
        else{
            if ($response->original && isset($response->original['arahan'])) {
                $encryptedTexts = array(["arahan"=>$response->original['arahan']]);
                $decryptedData = $this->unsealData($encryptedTexts)[0]['text']?? null;
                if ($decryptedData) {
                    $responseData = $response->original;
                    $responseData['arahan'] = "decryptedData"; // Set the decrypted value
                    $response->setContent(json_encode(['data' => $responseData]));
                }

            }
        }
        return $response;
    }

    private function unsealData($encryptedTexts)
    {
        // Membaca sertifikat dan kunci dari direktori storage
        $certFile = storage_path('app/crt/client.crt');
        $keyFile = storage_path('app/crt/client.key');
        $url = config('app.base_api_url') . '/unseal'; // URL untuk dekripsi

        $payload = ['Ciphertext' => array_map(function ($text) {
            return ['text' => $text['arahan']];
        }, $encryptedTexts)];

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
        return $parsedResponse['Plaintext'] ?? null;
    }
}
