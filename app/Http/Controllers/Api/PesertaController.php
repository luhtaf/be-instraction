<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

public function get_top_undangan_values(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'sort_by' => 'required|in:nama,total,hadir,persentase_hadir',
                'order' => 'required|in:asc,desc',
            ]);

            $sortBy = $validatedData['sort_by'];
            $order = $validatedData['order'];


            $query = Peserta::select('peserta.nama',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN peserta.keterangan LIKE "Hadir%" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('ROUND(100 * SUM(CASE WHEN peserta.keterangan LIKE "Hadir%" THEN 1 ELSE 0 END) / COUNT(*), 2) as persentase_hadir'),
            )
            ->join('rapat', 'peserta.rapat_id', '=', 'rapat.id')
            ->groupBy('peserta.nama');

            if ($request->has('sort_by') && $request->has('order')) {
                $query->orderBy($sortBy, $order);
            } else {
                $query->orderByDesc('total');
            }

            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $tanggalMulai = $request->input('tanggal_mulai');
                $tanggalSelesai = $request->input('tanggal_selesai');
                $query->whereBetween('rapat.tanggal_mulai', [$tanggalMulai, $tanggalSelesai]);
            }
            elseif ($request->has('tanggal_mulai')) {
                $tanggalMulai=$request->input('tanggal_mulai');
                $query->whereDate('rapat.tanggal_mulai', '>=', $tanggalMulai);
            }
            elseif ($request->has('tanggal_selesai')) {
                $tanggalSelesai=$request->input('tanggal_selesai');
                $query->whereDate('rapat.tanggal_selesai', '<=', $tanggalSelesai);
            }

            $results = $query->limit(5)->get();

            return response()->json($results);
        }
        catch (\Exception $e)
        { // Catch-all for unexpected errors
            Log::error("Unexpected Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function statistic_peserta(Request $request)
    {
        try{
            $nama = $request->input('nama'); // Get the 'nama' from the request body

            $query = Peserta::where('peserta.nama', $nama)
                ->select('peserta.nama',
                    DB::raw('count(*) as count'),
                    DB::raw('SUM(CASE WHEN peserta.keterangan LIKE "Hadir%" THEN 1 ELSE 0 END) as hadir'),
                    DB::raw('SUM(CASE WHEN peserta.keterangan = "Diwakilkan" THEN 1 ELSE 0 END) as diwakilkan'),
                    DB::raw('SUM(CASE WHEN peserta.keterangan = "Tidak Hadir" THEN 1 ELSE 0 END) as tidak_hadir'),
                    )
                ->join('rapat', 'peserta.rapat_id', '=', 'rapat.id')
                ->groupBy('nama');


                            // Optional date filtering (if 'tanggal_mulai' and 'tanggal_selesai' are present)
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $tanggalMulai = $request->input('tanggal_mulai');
                $tanggalSelesai = $request->input('tanggal_selesai');
                $query->whereBetween('rapat.tanggal_mulai', [$tanggalMulai, $tanggalSelesai]);
            }
            $results=$query->first();
            return response()->json($results);
        }
        catch (\Exception $e)
        { // Catch-all for unexpected errors
            Log::error("Unexpected Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
