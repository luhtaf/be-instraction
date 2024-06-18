<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Rapat;
use App\Models\ArahanPimpinan;
use App\Models\PelaksanaArahanPimpinan;
use App\Http\Resources\PelaksanaArahanPimpinanResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
class PelaksanaArahanPimpinanController extends Controller
{
    public function index(Rapat $rapat, ArahanPimpinan $arahanPimpinan)
    {
        if ($arahanPimpinan->rapat_id !== $rapat->id) {
            return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
        }

        $pelaksanaArahanPimpinan = $arahanPimpinan->pelaksana_arahan_pimpinan;
        // Return the data as part of the message
        return response()->json(['message' => 'Pelaksana Arahan Pimpinan ditemukan', 'data' => $pelaksanaArahanPimpinan], 200);
    }

    public function show(Rapat $rapat, ArahanPimpinan $arahanPimpinan, PelaksanaArahanPimpinan $pelaksanaArahanPimpinan)
    {
        try {
            // Check if the arahanPimpinan belongs to the specified Rapat
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            if ($arahanPimpinan->id !== $pelaksanaArahanPimpinan->arahan_pimpinan_id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan PJ Arahan Pimpinan ini'], 404);
            }

            return new PelaksanaArahanPimpinanResource($pelaksanaArahanPimpinan);

        } catch (ModelNotFoundException $e) {
            Log::warning('Pelaksana Arahan Pimpinan not found:', ['pelaksana_arahan_pimpinan_id' => $pelaksanaArahanPimpinan->id]);
            return response()->json(['error' => 'Arahan Pimpinan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching Pelakasnaa Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Mendapatkan Data Arahan Pimpinan'], 500);
        }
    }

    public function store(Request $request, Rapat $rapat, ArahanPimpinan $arahanPimpinan)
    {
        try {
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            $validator = Validator::make($request->all(), [
                'target_arahan' => 'required|string',
                'keterangan' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $pelaksanaArahanPimpinan = new PelaksanaArahanPimpinan($request->all());
            $arahanPimpinan->pelaksana_arahan_pimpinan()->save($pelaksanaArahanPimpinan);

            return response()->json(['message' => 'Sukses Assign PJ Arahan Pimpinan Rapat', 'data' => new PelaksanaArahanPimpinanResource($arahanPimpinan)], 201);

        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error Asssign PJ Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Assign PJ Arahan Pimpinan Rapat','detail'=>$e->getMessage()], 500);
        }
    }

    public function update(Request $request, Rapat $rapat, ArahanPimpinan $arahanPimpinan, PelaksanaArahanPimpinan $pelaksanaArahanPimpinan)
    {
        try {
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            if ($arahanPimpinan->id !== $pelaksanaArahanPimpinan->arahan_pimpinan_id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan PJ Arahan Pimpinan ini'], 404);
            }

            $validator = Validator::make($request->all(), [
                'target_arahan' => 'required|string',
                'keterangan' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $pelaksanaArahanPimpinan->update($request->all());

            return response()->json(['message' => 'Sukses Update Arahan Pimpinan', 'data' => new PelaksanaArahanPimpinanResource($pelaksanaArahanPimpinan)], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Arahan Pimpinan not found for updating:', ['arahan_pimpinan_id' => $arahanPimpinan->id]);
            return response()->json(['error' => 'PJ Arahan Pimpinan tidak ditemukan'], 404);
        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating PJ Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Update PJ Arahan Pimpinan', 'detail'=>$e->getMessage()], 500);
        }
    }

    public function destroy(Rapat $rapat, ArahanPimpinan $arahanPimpinan, PelaksanaArahanPimpinan $pelaksanaArahanPimpinan)
    {
        try {
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            if ($arahanPimpinan->id !== $pelaksanaArahanPimpinan->arahan_pimpinan_id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan PJ Arahan Pimpinan ini'], 404);
            }

            $pelaksanaArahanPimpinan->delete();
            return response()->json(['message' => 'Sukses Hapus PJ Arahan Pimpinan Rapat'], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('PJ Arahan Pimpinan not found for deleting:', ['arahan_pimpinan_id' => $arahanPimpinan->id]);
            return response()->json(['error' => 'PJ Arahan Pimpinan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting PJ Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Menghapus PJ Arahan Pimpinan Rapat'], 500);
        }
    }
}
