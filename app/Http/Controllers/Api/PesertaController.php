<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Rapat;
use App\Models\Peserta;

use App\Http\Resources\PesertaResource;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class PesertaController extends Controller
{
    public function index(Rapat $rapat)
    {
        $peserta = $rapat->peserta;
        // Return the data as part of the message
        return response()->json(['message' => 'Peserta Rapat ditemukan', 'data' => $peserta], 200);
    }

    // public function show(Rapat $rapat, Peserta $peserta)
    // {
    //     try {
    //         // Check if the arahanPimpinan belongs to the specified Rapat
    //         if ($peserta->rapat_id !== $rapat->id) {
    //             return response()->json(['error' => 'Peserta Rapat tidak terkait dengan Rapat ini'], 404);
    //         }

    //         return new PesertaResource($peserta);

    //     } catch (ModelNotFoundException $e) {
    //         Log::warning('Peserta Rapat not found:', ['rapat_id' => $rapat->id]);
    //         return response()->json(['error' => 'Peserta Rapat tidak ditemukan'], 404);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching Peserta Rapat:', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => 'Gagal Mendapatkan Data Peserta Rapat'], 500);
    //     }
    // }

    public function show(Rapat $rapat, Peserta $peserta)
    {
        // Ensure $peserta belongs to $rapat
        if ($peserta->rapat_id !== $rapat->id) {
            return response()->json([
                'success' => false,
                'error' => 'Peserta Rapat tidak terkait dengan Rapat ini'
            ], 404);
        }

        try {
            return new PesertaResource($peserta);
        } catch (\Exception $e) {
            // Log the specific error with more details for debugging
            Log::error('Error fetching Peserta Rapat:', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'rapat_id' => $rapat->id,
                'peserta_id' => $peserta->id // Assuming $peserta is valid here
            ]);

            // More specific error message based on exception type
            $errorMessage = 'Gagal Mendapatkan Data Peserta Rapat';
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $errorMessage = 'Peserta Rapat tidak ditemukan';
            }

            return response()->json([
                'success' => false,
                'error' => $errorMessage
            ], 500);
        }
    }

    public function store(Request $request, Rapat $rapat)
    {
        try {
            if (!$rapat) {
                return response()->json(['message' => 'Rapat not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string',
                'keterangan' => 'nullable|string',
                'perwakilan'=> 'nullable|string',
                'jenis'=>'nullable|string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $peserta = new Peserta($request->all());
            $rapat->peserta()->save($peserta);

            return response()->json(['message' => 'Sukses Assign Peserta Rapat', 'data' => new PesertaResource($peserta)], 201);

        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating Kelengkapan Peserta:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Assign Peserta Rapat'], 500);
        }
    }

    public function update(Request $request, Rapat $rapat, Peserta $peserta)
    {
        try {
            if ($peserta->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Peserta Rapat tidak terkait dengan Rapat ini'], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string',
                'keterangan' => 'nullable|string',
                'perwakilan'=> 'nullable|string',
                'jenis'=>'nullable|string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $peserta->update($request->all());

            return response()->json(['message' => 'Sukses Update Peserta Rapat', 'data' => new PesertaResource($peserta)], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Peserta not found for updating:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Peserta tidak ditemukan'], 404);
        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating Peserta:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Update Peserta', 'detail'=>$e->getMessage()], 500);
        }
    }


    // public function store(Request $request, Rapat $rapat)
    // {
    //     try {
    //         // Validate the incoming data (similar to your existing validation)
    //         $validator = Validator::make($request->all(), [
    //             // 'peserta' => 'required|array',
    //             'peserta.*.nama' => 'required|string',
    //             'peserta.*.keterangan' => 'string',
    //             'peserta.*.perwakilan' => 'string',
    //             'peserta.*.jenis' => 'string',
    //         ]);

    //         if ($validator->fails()) {
    //             throw new ValidationException($validator);
    //         }

    //         $pesertaData = $request->input('peserta');

    //         // Use a database transaction for atomicity
    //         DB::transaction(function () use ($rapat, $pesertaData) {
    //             // 1. Delete existing Peserta associated with the Rapat
    //             // $rapat->peserta()->delete();

    //             // 2. Create new Peserta records
    //             $rapat->peserta()->createMany($pesertaData);
    //         });

    //         // Retrieve the updated Peserta records after the transaction
    //         $updatedPeserta = $rapat->peserta;

    //         return response()->json(['message' => 'Sukses Update Peserta Rapat', 'data' => PesertaResource::collection($updatedPeserta)], 200);

    //     } catch (ValidationException $e) {
    //         Log::info('Validation error:', $e->errors());
    //         return response()->json(['errors' => $e->errors()], 422);
    //     } catch (\Exception $e) {
    //         Log::error('Error updating Peserta:', ['error' => $e->getMessage()]);
    //         return response()->json(['error' => 'Gagal Update Peserta', 'detail' => $e->getMessage()], 500);
    //     }
    // }


    public function destroy(Rapat $rapat, Peserta $peserta)
    {
        try {
            if ($peserta->rapat_id !== $rapat->id) {
                print_r($peserta);
                echo $peserta->rapat_id;
                echo '<br>';
                echo $rapat->id;
                return response()->json(['error' => 'Peserta Rapat tidak terkait dengan Rapat ini'], 400);
            }

            $peserta->delete();
            return response()->json(['message' => 'Sukses Hapus Peserta Rapat'], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Peserta Rapat not found for deleting:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Peserta Rapat tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Peserta Rapat:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Menghapus Peserta Rapat'], 500);
        }
    }

    public function destroy_all(Rapat $rapat)
    {
        try {
            // 1. Authorization Check (Optional but recommended)
            // $this->authorize('deletePeserta', $rapat); // Use a policy for fine-grained authorization

            // 2. Delete Peserta Records
            $deletedCount = Peserta::where('rapat_id', $rapat->id)->delete();

            // 3. Logging (Optional)
            Log::info("Deleted $deletedCount peserta associated with rapat ID: {$rapat->id}");

            // 4. Response
            return response()->json([
                'success' => true,
                'message' => "Sukses menghapus $deletedCount Undangan"
            ]);

        } catch (\Exception $e) {
            Log::error("Error deleting peserta:", ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete peserta from rapat.'
            ], 500);
        }
    }
}
