<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Rapat;
use App\Models\PenanggungJawab;
use App\Http\Resources\PenanggungJawabResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PenanggungJawabController extends Controller
{
    public function show(Rapat $rapat, PenanggungJawab $penanggungJawab)
    {
        try {
            // Check if the PenanggungJawab belongs to the specified Rapat
            if ($penanggungJawab->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Penanggung Jawab tidak terkait dengan Rapat ini'], 404);
            }

            return new PenanggungJawabResource($penanggungJawab);

        } catch (ModelNotFoundException $e) {
            Log::warning('Penanggung Jawab not found:', ['penanggung_jawab_id' => $penanggungJawab->id]);
            return response()->json(['error' => 'Penanggung Jawab tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching Penanggung Jawab:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Mendapatkan Data Penanggung Jawab'], 500);
        }
    }

    public function index(Rapat $rapat)
    {
        $penanggungJawab = $rapat->penanggung_jawab;
        // Return the data as part of the message
        return response()->json(['message' => 'Penanggung Jawab ditemukan', 'data' => $penanggungJawab], 200);

    }

    public function store(Request $request, Rapat $rapat)
    {
        try {
            if (!$rapat) {
                return response()->json(['message' => 'Rapat not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_personil' => 'required|string',
                'role' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $penanggungJawab = new PenanggungJawab($request->all());
            $rapat->penanggung_jawab()->save($penanggungJawab);

            return response()->json(['message' => 'Sukses Assign PJ Rapat', 'data' => new PenanggungJawabResource($penanggungJawab)], 201);

        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating Penanggung Jawab:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Assign PJ Rapat'], 500);
        }
    }

    public function update(Request $request, Rapat $rapat, PenanggungJawab $penanggungJawab)
    {
        try {
            if ($penanggungJawab->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Penanggung Jawab tidak terkait dengan Rapat ini'], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_personil' => 'required|string',
                'role' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $penanggungJawab->update($request->all());

            return response()->json(['message' => 'Sukses Update PJ Rapat', 'data' => new PenanggungJawabResource($penanggungJawab)], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Penanggung Jawab not found for updating:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Penanggung Jawab tidak ditemukan'], 404);
        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating Penanggung Jawab:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Update PJ Rapat'], 500);
        }
    }

    public function destroy(Rapat $rapat, PenanggungJawab $penanggungJawab)
    {
        try {
            if ($penanggungJawab->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Penanggung Jawab tidak terkait dengan Rapat ini'], 404);
            }

            $penanggungJawab->delete();
            return response()->json(['message' => 'Sukses Hapus Penanggung Jawab Rapat'], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Penanggung Jawab not found for deleting:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Penanggung Jawab tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Penanggung Jawab:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Menghapus Penanggung Jawab Rapat'], 500);
        }
    }
}
