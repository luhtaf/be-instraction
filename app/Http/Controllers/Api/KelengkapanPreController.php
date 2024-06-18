<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Rapat;
use App\Models\KelengkapanPre;

use App\Http\Resources\KelengkapanPreResource;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class KelengkapanPreController extends Controller
{

    public function index(Rapat $rapat)
    {
        $kelengkapanPre = $rapat->kelengkapan_pre;
        // Return the data as part of the message
        return response()->json(['message' => 'Kelengkapan Pre Rapat ditemukan', 'data' => $kelengkapanPre], 200);
    }

    public function show(Rapat $rapat, KelengkapanPre $kelengkapanPre)
    {
        try {
            // Check if the arahanPimpinan belongs to the specified Rapat
            if ($kelengkapanPre->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Kelengkapan Pre Rapat tidak terkait dengan Rapat ini'], 404);
            }

            return new KelengkapanPreResource($kelengkapanPre);

        } catch (ModelNotFoundException $e) {
            Log::warning('Kelengkapan Pre Rapat not found:', ['rapat_id' => $kelengkapanPre->id]);
            return response()->json(['error' => 'Kelengkapan Pre Rapat tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching Kelengkapan Pre Rapat:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Mendapatkan Data Kelengkapan Pre Rapat'], 500);
        }
    }

    public function store(Request $request, Rapat $rapat)
    {
        try {
            if (!$rapat) {
                return response()->json(['message' => 'Rapat not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'poin' => 'required|string',
                'keterangan' => 'string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $kelengkapanPre = new KelengkapanPre($request->all());
            $rapat->kelengkapan_pre()->save($kelengkapanPre);

            return response()->json(['message' => 'Sukses Assign Kelengkapan Pre Rapat', 'data' => new KelengkapanPreResource($kelengkapanPre)], 201);

        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating Kelengkapan Pre Rapat:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Assign Kelengkapan Pre Rapat'], 500);
        }
    }

    public function update(Request $request, Rapat $rapat, KelengkapanPre $kelengkapanPre)
    {
        try {
            if ($kelengkapanPre->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Kelengkapan Pre Rapat tidak terkait dengan Rapat ini'], 404);
            }

            $validator = Validator::make($request->all(), [
                'poin' => 'required|string',
                'keterangan' => 'string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $kelengkapanPre->update($request->all());

            return response()->json(['message' => 'Sukses Update Kelengkapan Pre Rapat', 'data' => new KelengkapanPreResource($kelengkapanPre)], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Kelengkapan Pre not found for updating:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Kelengkapan Pre tidak ditemukan'], 404);
        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating Kelengkapan Pre:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Update Kelengkapan Pre', 'detail'=>$e->getMessage()], 500);
        }
    }

    public function destroy(Rapat $rapat, KelengkapanPre $kelengkapanPre)
    {
        try {
            if ($kelengkapanPre->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Kelengkapan Pre Rapat tidak terkait dengan Rapat ini'], 404);
            }

            $kelengkapanPre->delete();
            return response()->json(['message' => 'Sukses Hapus Kelengkapan Pre Rapat'], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Kelengkapan Pre Rapat not found for deleting:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Kelengkapan Pre Rapat tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Kelengkapan Pre Rapat:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Menghapus Kelengkapan Pre Rapat'], 500);
        }
    }

}
