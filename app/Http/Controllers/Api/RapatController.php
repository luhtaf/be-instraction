<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Rapat;
use App\Http\Resources\RapatResource;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // For logging exceptions
use App\Exceptions\InvalidSortFieldException; // Create a custom exception (optional)

class RapatController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $query = Rapat::query();

            // 1. Searching (Filter by Keyword)
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%$search%") // Assuming these are your fields
                    //   ->orWhere('tempat_rapat', 'like', "%$search%")
                    //   ->orWhere('tanggal_rapat', 'like', "%$search%")
                      // Add more fields as needed
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

            $rapat = $query->latest()->paginate($perPage);
            return RapatResource::collection($rapat);

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
    public function show($id)
    {
        //find post by ID

        $rapat = Rapat::find($id);
        if (!$rapat) {
            return response()->json(['message' => 'Rapat not found'], 404);
        }
        //return single post as a resource
        return new RapatResource($rapat);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'     => 'required|string',
            'kategori' => 'nullable|string',
            'tanggal' => 'nullable',
            'urgensi' => 'nullable|string',
            'waktu' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'metode' => 'nullable|string',
            'penyelenggara' => 'nullable|string',
            'pimpinan' => 'nullable|string',
            'jenis' => 'nullable|string',
            'pemapar' => 'nullable|string',
            'tautan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'tema'=> 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Rapat::create([
            'nama'     => $request->nama,
            'kategori' => $request->kategori,
            'tanggal' => $request->tanggal,
            'urgensi' => $request->urgensi,
            'waktu' => $request->waktu,
            'lokasi' => $request->lokasi,
            'metode' => $request->metode,
            'penyelenggara' => $request->penyelenggara,
            'pimpinan' => $request->pimpinan,
            'jenis' => $request->jenis,
            'pemapar' => $request->pemapar,
            'tautan' => $request->tautan,
            'catatan' => $request->catatan,
            'keterangan' => $request->keterangan,
            'tema'=>$request->tema
        ]);

        return response()->json(['message' => 'Sukses Membuat Rapat Baru'], 201);

    }

    public function update(Request $request, $id)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'nama'     => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //find post by ID
        $rapat = Rapat::find($id);
        if (!$rapat) {
            return response()->json(['message' => 'Rapat not found'], 404);
        }

        $rapat->update([
            'nama'     => $request->nama,
            'kategori' => $request->kategori,
            'tanggal' => $request->tanggal,
            'urgensi' => $request->urgensi,
            'waktu' => $request->waktu,
            'lokasi' => $request->lokasi,
            'metode' => $request->metode,
            'penyelenggara' => $request->penyelenggara,
            'pimpinan' => $request->pimpinan,
            'jenis' => $request->jenis,
            'pemapar' => $request->pemapar,
            'tautan' => $request->tautan,
            'catatan' => $request->catatan,
            'keterangan' => $request->keterangan,
            'tema'=>$request->tema
        ]);

        return response()->json(['message' =>'Sukses update rapat'], 202);
    }
    public function destroy($id)
    {
        $rapat = Rapat::find($id);
        if (!$rapat) {
            return response()->json(['message' => 'Rapat not found'], 404);
        }
        //delete post
        $rapat->delete();
        return response()->json(['message' =>'Sukses Hapus Rapat'], 202);
    }

    public function getTema()
    {
        $temaValues = Rapat::getUniqueTemas(); // Call the static method

        return response()->json(['tema' => $temaValues]);
    }
}
