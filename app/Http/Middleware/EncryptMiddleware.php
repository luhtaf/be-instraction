<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;
use Closure;
use Illuminate\Http\Request;
class EncryptMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('arahan')) {
            $plainTexts = $request->input('arahan');
            $encryptedData = $this->sealData($plainTexts);
            if ($encryptedData) {
                $request->merge(['arahan' => $encryptedData]);
            }
        }

        return $next($request);
    }

    private function sealData($plainTexts)
    {
        $certFile = storage_path('app/crt/client.crt');
        $keyFile = storage_path('app/crt/client.key');
        $url = config('app.base_api_url') . '/seal'; // URL untuk enkripsi
        $payload = ['Plaintext' =>
            array(['Text' => $plainTexts])
        ];

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
        return $parsedResponse['Ciphertext'][0]['text'] ?? null;
    }
}
