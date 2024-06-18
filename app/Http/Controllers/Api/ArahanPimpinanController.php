<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Rapat;
use App\Models\ArahanPimpinan;
use App\Http\Resources\ArahanPimpinanResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ArahanPimpinanController extends Controller
{
    public function index(Rapat $rapat)
    {
        $arahanPimpinan = $rapat->arahan_pimpinan;
        // Return the data as part of the message
        return response()->json(['message' => 'Arahan Pimpinan ditemukan', 'data' => $arahanPimpinan], 200);

    }

    public function show(Rapat $rapat, ArahanPimpinan $arahanPimpinan)
    {
        try {
            // Check if the arahanPimpinan belongs to the specified Rapat
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            return new ArahanPimpinanResource($arahanPimpinan);

        } catch (ModelNotFoundException $e) {
            Log::warning('Arahan Pimpinan not found:', ['arahan_pimpinan_id' => $arahanPimpinan->id]);
            return response()->json(['error' => 'Arahan Pimpinan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Mendapatkan Data Arahan Pimpinan'], 500);
        }
    }

    public function store(Request $request, Rapat $rapat)
    {
        try {
            if (!$rapat) {
                return response()->json(['message' => 'Rapat not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'arahan' => 'required|string',
                'deadline' => 'nullable|string',
                'pelaksana' => 'nullable|string',
                'status' => 'nullable|string',
                'penyelesaian' => 'nullable|string',
                'data_dukung' => 'nullable|string',
                'keterangan' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $arahanPimpinan = new ArahanPimpinan($request->all());
            $rapat->arahan_pimpinan()->save($arahanPimpinan);

            return response()->json(['message' => 'Sukses Assign Arahan Pimpinan Rapat', 'data' => new ArahanPimpinanResource($arahanPimpinan)], 201);

        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Assign Arahan Pimpinan Rapat', 'detail'=>$e->getMessage()], 500);
        }
    }

    public function update(Request $request, Rapat $rapat, ArahanPimpinan $arahanPimpinan)
    {
        try {
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            $validator = Validator::make($request->all(), [
                'arahan' => 'required|string',
                'deadline' => 'nullable|string',
                'pelaksana' => 'nullable|string',
                'status' => 'nullable|string',
                'penyelesaian' => 'nullable|string',
                'data_dukung' => 'nullable|string',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $arahanPimpinan->update($request->all());

            return response()->json(['message' => 'Sukses Update Arahan Pimpinan', 'data' => new ArahanPimpinanResource($arahanPimpinan)], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Arahan Pimpinan not found for updating:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Arahan Pimpinan tidak ditemukan'], 404);
        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Update Arahan Pimpinan', 'detail'=>$e->getMessage()], 500);
        }
    }

    public function destroy(Rapat $rapat, ArahanPimpinan $arahanPimpinan)
    {
        try {
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            $arahanPimpinan->delete();
            return response()->json(['message' => 'Sukses Hapus Arahan Pimpinan Rapat'], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Arahan Pimpinan not found for deleting:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Arahan Pimpinan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Menghapus Arahan Pimpinan Rapat'], 500);
        }
    }

    public function all_arahan(Request $request)
    {
        try
        {
            $query = ArahanPimpinan::query();

            // 1. Searching (Filter by Keyword)
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('arahan', 'like', "%$search%")
                      ;
                });
            }

            // 2. Sorting (Order by a Specific Field)
            if ($request->has('sort_by') && $request->has('order')) {
                $sortBy = $request->input('sort_by');
                $order = $request->input('order'); // 'asc' or 'desc'

                // Ensure the sortBy field is valid to prevent potential security risks
                $allowedSortFields = ['judul_rapat', 'tempat_rapat', 'tanggal_rapat']; // Add more fields as needed
                if (in_array($sortBy, $allowedSortFields)) {
                    $query->orderBy($sortBy, $order);
                }
            }

            // 3. Pagination (Still using Laravel's built-in pagination)

            $allowedPageSizes = [5, 10, 25, 50, 100];
            $perPage = $request->has('size')
                ? (in_array($request->input('size'), $allowedPageSizes) ? $request->input('size') : 5)
                : 5; // Default to 5 if invalid or not provided

            $arahan_pimpinan = $query->latest()->paginate($perPage);
            return $arahan_pimpinan;

        }
        catch (\Illuminate\Database\QueryException $e)
        {
            Log::error("Database Error: " . $e->getMessage()); // Log the detailed error
            return response()->json(['error' => 'Internal Server Error'], 500);

        }
        catch (InvalidSortFieldException $e)
        { // Handle custom exception if used
            Log::warning("Invalid Sort Field: " . $e->getMessage());
            return response()->json(['error' => 'Invalid sort field'], 400);

        }
        catch (\Exception $e)
        { // Catch-all for unexpected errors
            Log::error("Unexpected Error: " . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function destroy_all(Rapat $rapat)
    {
        try {
            // 1. Authorization Check (Optional but recommended)
            // $this->authorize('deletePeserta', $rapat); // Use a policy for fine-grained authorization

            // 2. Delete Peserta Records
            $deletedCount = ArahanPimpinan::where('rapat_id', $rapat->id)->delete();

            // 3. Logging (Optional)
            Log::info("Deleted $deletedCount arahan pimpinan associated with rapat ID: {$rapat->id}");

            // 4. Response
            return response()->json([
                'success' => true,
                'message' => "Sukses menghapus $deletedCount arahan pimpinan"
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
