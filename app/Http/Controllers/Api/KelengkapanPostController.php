<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Rapat;
use App\Models\KelengkapanPost;

use App\Http\Resources\KelengkapanPostResource;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class KelengkapanPostController extends Controller
{
    public function index(Rapat $rapat)
    {
        $kelengkapanPost = $rapat->kelengkapan_post;
        // Return the data as part of the message
        return response()->json(['message' => 'Kelengkapan Post Rapat ditemukan', 'data' => $kelengkapanPost], 200);
    }

    public function show(Rapat $rapat, KelengkapanPost $kelengkapanPost)
    {
        try {
            // Check if the arahanPimpinan belongs to the specified Rapat
            if ($kelengkapanPost->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Kelengkapan Post Rapat tidak terkait dengan Rapat ini'], 404);
            }

            return new KelengkapanPostResource($kelengkapanPost);

        } catch (ModelNotFoundException $e) {
            Log::warning('Kelengkapan Post Rapat not found:', ['rapat_id' => $kelengkapanPost->id]);
            return response()->json(['error' => 'Kelengkapan Post Rapat tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching Kelengkapan Post Rapat:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Mendapatkan Data Kelengkapan Post Rapat'], 500);
        }
    }

    public function store(Request $request, Rapat $rapat)
    {
        try {
            if (!$rapat) {
                return response()->json(['message' => 'Rapat not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'undangan' => 'nullable|string',
                'rekaman' => 'nullable|string',
                'risalah' => 'nullable|string',
                'bahan' => 'nullable|string',
                'absen' => 'nullable|string',
                'laporan' => 'nullable|string',
                'dokumentasi' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $kelengkapanPost = new KelengkapanPost($request->all());
            $rapat->kelengkapan_post()->save($kelengkapanPost);

            return response()->json(['message' => 'Sukses Assign Kelengkapan Post Rapat', 'data' => new KelengkapanPostResource($kelengkapanPost)], 201);

        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating Kelengkapan Post Rapat:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Assign Kelengkapan Post Rapat'], 500);
        }
    }

    public function update(Request $request, Rapat $rapat, KelengkapanPost $kelengkapanPost)
    {
        try {
            if ($kelengkapanPost->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Kelengkapan Post Rapat tidak terkait dengan Rapat ini'], 404);
            }

            $validator = Validator::make($request->all(), [
                'undangan' => 'nullable|string',
                'rekaman' => 'nullable|string',
                'risalah' => 'nullable|string',
                'bahan' => 'nullable|string',
                'absen' => 'nullable|string',
                'laporan' => 'nullable|string',
                'dokumentasi' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $kelengkapanPost->update($request->all());

            return response()->json(['message' => 'Sukses Update Kelengkapan Post Rapat', 'data' => new KelengkapanPostResource($kelengkapanPost)], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Kelengkapan Post not found for updating:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Kelengkapan Post tidak ditemukan'], 404);
        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating Kelengkapan Post:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Update Kelengkapan Post', 'detail'=>$e->getMessage()], 500);
        }
    }

    public function destroy(Rapat $rapat, KelengkapanPost $kelengkapanPost)
    {
        try {
            if ($kelengkapanPost->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Kelengkapan Post Rapat tidak terkait dengan Rapat ini'], 404);
            }

            $kelengkapanPost->delete();
            return response()->json(['message' => 'Sukses Hapus Kelengkapan Post Rapat'], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Kelengkapan Post Rapat not found for deleting:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Kelengkapan Post Rapat tidak ditemukan'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting Kelengkapan Post Rapat:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Menghapus Kelengkapan Post Rapat'], 500);
        }
    }
}
