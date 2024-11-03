<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginDwsController extends Controller
{
    // Function to decode the base64 URL-encoded JWT parts without verifying the signature
    private function base64UrlDecode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $addlen = 4 - $remainder;
            $input .= str_repeat('=', $addlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    // Function to decode the JWT without verification
    private function decodeJwtWithoutVerification($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid JWT token structure.');
        }

        // Decode header and payload
        $header = json_decode($this->base64UrlDecode($parts[0]), true);
        $payload = json_decode($this->base64UrlDecode($parts[1]), true);

        return [
            'header' => $header,
            'payload' => $payload
        ];
    }

    public function loginDws(Request $request)
    {
        // Get the JWT from the request body
        $jwt = $request->input('token');
        if (!$jwt) {
            return response()->json(['error' => 'JWT not provided'], 400);
        }

        try {
            // Decode the JWT to extract the 'sub' claim without verifying the signature
            $decoded = $this->decodeJwtWithoutVerification($jwt);
            $sub = $decoded['payload']['sub'];

            // Find a Karyawan record with the matching guid_dws
            $karyawan = Karyawan::where('guid_dws', $sub)->first();
            if (!$karyawan) {
                return response()->json(['error' => 'No matching karyawan found'], 404);
            }

            // Check if a user already exists with the Karyawan's guid_dws
            $user = User::where('guid_dws', $karyawan->guid_dws)->first();
            if (!$user) {
                $role = 'staff'; // Default role
                if (str_contains($karyawan->jabatan, 'Kepala Biro') || str_contains($karyawan->jabatan, 'Direktur')) {
                    $role = 'eselon 2';
                } elseif (str_contains($karyawan->jabatan, 'Deputi')) {
                    $role = 'eselon 1';
                } elseif ($karyawan->unit_kerja === 'Bagian Dukungan Strategis dan Tata Usaha Pimpinan, Biro Hukum dan Komunikasi Publik, Sekretariat Utama') {
                    $role = 'admin';
                }
                // Create a new user based on the karyawan data
                $user = User::create([
                    'guid_dws' => $karyawan->guid_dws,
                    'name' => $karyawan->nama,
                    'email' => $karyawan->email,
                    'role' => $role, // Assign a default role or set dynamically as needed
                    // 'password' => bcrypt('default-password'), // Set a default password or random one
                    'unit_kerja' => $karyawan->unit_kerja,
                ]);
            }

            // Auto-login the user
            Auth::login($user);

            // Generate a JWT token for the logged-in user
            // $token = Auth::login($user);
            $token = Auth::guard('api')->login($user);

            // Return a success response with the user data and JWT token
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid JWT or server error', 'details' => $e->getMessage()], 500);
        }
    }
}
