<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Rapat;
use App\Models\Unit;
use App\Models\ArahanPimpinan;
use App\Http\Resources\ArahanPimpinanResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ArahanPimpinanRequest;
use App\Http\Requests\LaporanArahanPimpinanRequest;

class ArahanPimpinanController extends Controller
{
    // public function index(Rapat $rapat)
    // {
    //     $arahanPimpinan = $rapat->arahan_pimpinan;
    //     // Return the data as part of the message
    //     return response()->json(['message' => 'Arahan Pimpinan ditemukan', 'data' => $arahanPimpinan], 200);
    // }

    public function index(Rapat $rapat, Request $request)
    {
        // Apply filtering, sorting, and pagination (optional)
        $query = $rapat->arahan_pimpinan(); // Get the relationship query builder

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('arahan', 'like', "%$search%")
                  ->orWhereHas('rapat', function ($rapatQuery) use ($search) {
                      $rapatQuery->where('nama', 'like', "%$search%");
                  });
            });
            $query->orWhere('pelaksana', 'like', "%$search%");
            $query->orWhere('status', 'like', "%$search%");
            $query->orWhere('penyelesaian', 'like', "%$search%");
            $query->orWhere('data_dukung', 'like', "%$search%");
            $query->orWhere('keterangan', 'like', "%$search%");
        }

        if ($request->has('nama_rapat')) {
            $search = $request->input('nama_rapat');
            $query->where(function ($q) use ($search) {
                $q->whereHas('rapat', function ($rapatQuery) use ($search) {
                      $rapatQuery->where('nama', 'like', "%$search%");
                  });
            });


        }

        if ($request->has('arahan')) {
            $search = $request->input('arahan');
            $query->where(function ($q) use ($search) {
                $q->where('arahan', 'like', "%$search%");
            });
        }

        if ($request->has('pelaksana')) {
            $search = $request->input('pelaksana');
            $query->where(function ($q) use ($search) {
                $q->where('pelaksana', 'like', "%$search%");
            });
        }

        if ($request->has('deadline')) {
            $search = $request->input('deadline');
            $query->where(function ($q) use ($search) {
                $q->where('deadline', '<', $search);
            });
        }

        if ($request->has('status')) {
            $search = $request->input('status');
            $query->where('status', $search); // Exact match
        }

        if ($request->has('penyelesaian')) {
            $search = $request->input('penyelesaian');
            $query->where(function ($q) use ($search) {
                $q->where('penyelesaian', 'like', "%$search%");
            });
        }

        if ($request->has('data_dukung')) {
            $search = $request->input('data_dukung');
            $query->where(function ($q) use ($search) {
                $q->where('data_dukung', 'like', "%$search%");
            });
        }

        if ($request->has('keterangan')) {
            $search = $request->input('keterangan');
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%$search%");
            });
        }

        $allowedPageSizes = [5, 10, 25, 50, 100];
        $perPage = $request->has('size')
            ? (in_array($request->input('size'), $allowedPageSizes) ? $request->input('size') : 5)
            : 5; // Default to 5 if invalid or not provided

        $arahanPimpinan = $query->latest()->paginate($perPage)->toArray();

        // Optionally transform the results to include the rapat name
        $arahanPimpinan['data'] = collect($arahanPimpinan['data'])->map(function ($item) use ($rapat) {
            $item['nama_rapat'] = $rapat->nama; // Assuming you have a 'nama' field in your Rapat model
            return $item;
        })->all();

        return response()->json($arahanPimpinan, 200);
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

    public function store(ArahanPimpinanRequest $request, Rapat $rapat)
    {
        try {
            if (!$rapat) {
                return response()->json(['message' => 'Rapat not found'], 404);
            }

            $data=$request->validated();
            $arahanPimpinan = new ArahanPimpinan($data);
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

    public function update(ArahanPimpinanRequest $request, Rapat $rapat, ArahanPimpinan $arahanPimpinan)
    {
        try {
            // Memeriksa apakah arahanPimpinan terkait dengan rapat yang dimaksud
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }

            // Validasi data dari request
            $data = $request->validated();

            // Cek kondisi status dan batas_konfirmasi
            if ($data['status']=="Dalam Proses" && $data['batas_konfirmasi']!=$arahanPimpinan->batas_konfirmasi) {
                // Mengubah status menjadi 'Menunggu Konfirmasi Perbaikan' jika kondisi terpenuhi
                $data['status'] = 'Menunggu Konfirmasi Perbaikan';
                $data['revisi']=$arahanPimpinan->revisi+1;
            }

            // Update arahanPimpinan dengan data yang telah disesuaikan
            $arahanPimpinan->update($data);

            return response()->json(['message' => 'Sukses Update Arahan Pimpinan', 'data' => new ArahanPimpinanResource($arahanPimpinan)], 200);

        } catch (ModelNotFoundException $e) {
            Log::warning('Arahan Pimpinan not found for updating:', ['rapat_id' => $rapat->id]);
            return response()->json(['error' => 'Arahan Pimpinan tidak ditemukan'], 404);
        } catch (ValidationException $e) {
            Log::info('Validation error:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating Arahan Pimpinan:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal Update Arahan Pimpinan', 'detail' => $e->getMessage()], 500);
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
            $user=Auth::user();
            // $query = ArahanPimpinan::query();
            $query = ArahanPimpinan::with('rapat:id,nama');

            // 1. Searching (Filter by Keyword)

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('arahan', 'like', "%$search%")
                      ->orWhereHas('rapat', function ($rapatQuery) use ($search) {
                          $rapatQuery->where('nama', 'like', "%$search%");
                      });
                });
                $query->orWhere('pelaksana', 'like', "%$search%");
                $query->orWhere('status', 'like', "%$search%");
                $query->orWhere('penyelesaian', 'like', "%$search%");
                $query->orWhere('data_dukung', 'like', "%$search%");
                $query->orWhere('keterangan', 'like', "%$search%");
            }

            if ($request->has('nama_rapat')) {
                $search = $request->input('nama_rapat');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('rapat', function ($rapatQuery) use ($search) {
                          $rapatQuery->where('nama', 'like', "%$search%");
                      });
                });
            }

            if ($request->has('arahan')) {
                $search = $request->input('arahan');
                $query->where(function ($q) use ($search) {
                    $q->where('arahan', 'like', "%$search%");
                });
            }

            if ($request->has('pelaksana')) {
                $search = $request->input('pelaksana');
                $query->where(function ($q) use ($search) {
                    $q->where('pelaksana', 'like', "%$search%");
                });
            }

            if ($request->has('deadline')) {
                $search = $request->input('deadline');
                $query->where(function ($q) use ($search) {
                    $q->where('deadline', '<', $search);
                });
            }

            if ($request->has('status')) {
                $search = $request->input('status');
                $query->where('status', $search); // Exact match
            }

            if ($request->has('penyelesaian')) {
                $search = $request->input('penyelesaian');
                $query->where(function ($q) use ($search) {
                    $q->where('penyelesaian', 'like', "%$search%");
                });
            }

            if ($request->has('data_dukung')) {
                $search = $request->input('data_dukung');
                $query->where(function ($q) use ($search) {
                    $q->where('data_dukung', 'like', "%$search%");
                });
            }

            if ($request->has('keterangan')) {
                $search = $request->input('keterangan');
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%$search%");
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

            if ($user->role=="staff" || $user->role=="direktur") {
                $query->where('pelaksana',$user->unit_kerja);
            }
            else if($user->role=="deputi"){
                $childUnits = Unit::where('parent', $user->unit_kerja)->pluck('nama');

                // Tambahkan "Deputi 2" ke dalam daftar pelaksana
                $pelaksanaList = $childUnits->push($user->unit_kerja);
                $query->whereIn('pelaksana', $pelaksanaList);
            }

            // 3. Pagination (Still using Laravel's built-in pagination)

            $allowedPageSizes = [5, 10, 25, 50, 100];
            $perPage = $request->has('size')
                ? (in_array($request->input('size'), $allowedPageSizes) ? $request->input('size') : 5)
                : 5; // Default to 5 if invalid or not provided

            // $arahan_pimpinan = $query->latest()->paginate($perPage);
            $arahan_pimpinan = $query->latest()->paginate($perPage);

        // Optionally transform the results to include the rapat name
            $arahan_pimpinan->getCollection()->transform(function ($item) {
                $item->nama_rapat = $item->rapat->nama;
                unset($item->rapat); // Remove the entire rapat relationship object
                return $item;
            });

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

    public function get_belum_tindak_lanjut()
    {
        $count = ArahanPimpinan::whereNull('status')->count();

        return response()->json([
            'jumlah' => $count
        ]);
    }

    public function get_top_status_values(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'sort_by' => 'required|in:total,selesai,gagal,dalam_proses,tidak_ada_tindak_lanjut,tanpa_keterangan,persentase_selesai,pelaksana',
                'order' => 'required|in:asc,desc',
            ]);

            $sortBy = $validatedData['sort_by'];
            $order = $validatedData['order'];

            $query = ArahanPimpinan::select('pelaksana',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai'),
                DB::raw('ROUND(100 * SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) / COUNT(*), 2) as persentase_selesai'),
                DB::raw('SUM(CASE WHEN status = "Gagal" THEN 1 ELSE 0 END) as gagal'),
                DB::raw('SUM(CASE WHEN status = "Dalam Proses" THEN 1 ELSE 0 END) as dalam_proses'),
                DB::raw('SUM(CASE WHEN status = "Tidak Ada Tindak Lanjut" THEN 1 ELSE 0 END) as tidak_ada_tindak_lanjut'),
                DB::raw('SUM(CASE WHEN status IS NULL THEN 1 ELSE 0 END) as tanpa_keterangan')
            )
                ->groupBy('pelaksana');

            if ($request->has('sort_by') && $request->has('order')) {
                $query->orderBy($sortBy, $order);
            } else {
                $query->orderByDesc('total');
            }

            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $tanggalMulai = $request->input('tanggal_mulai');
                $tanggalSelesai = $request->input('tanggal_selesai');
                $query->whereBetween('deadline', [$tanggalMulai, $tanggalSelesai]);
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


    public function get_top_penyelesaian_values(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'sort_by' => 'required|in:total,terlambat,tepat_waktu,persentase_tepat_waktu,pelaksana,belum_tl',
                'order' => 'required|in:asc,desc',
            ]);

            $sortBy = $validatedData['sort_by'];
            $order = $validatedData['order'];

            $query = ArahanPimpinan::select('pelaksana',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN penyelesaian = "Terlambat" THEN 1 ELSE 0 END) as terlambat'),
                DB::raw('ROUND(100 * SUM(CASE WHEN penyelesaian = "Tepat Waktu" THEN 1 ELSE 0 END) / COUNT(*), 2) as persentase_tepat_waktu'),
                DB::raw('SUM(CASE WHEN penyelesaian = "Tepat Waktu" THEN 1 ELSE 0 END) as tepat_waktu'),
                DB::raw('SUM(CASE WHEN penyelesaian IS NULL THEN 1 ELSE 0 END) as belum_tl')
            )
                ->groupBy('pelaksana');

            if ($request->has('sort_by') && $request->has('order')) {
                $query->orderBy($sortBy, $order);
            } else {
                $query->orderByDesc('total');
            }

            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $tanggalMulai = $request->input('tanggal_mulai');
                $tanggalSelesai = $request->input('tanggal_selesai');
                $query->whereBetween('deadline', [$tanggalMulai, $tanggalSelesai]);
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


    public function statistic_arahan(Request $request)
    {
        try {
            $nama = $request->input('nama'); // Get 'nama' from the request body

            $query = ArahanPimpinan::select(
                'pelaksana',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai'),
                DB::raw('ROUND(100 * SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) / COUNT(*), 2) as persentase_selesai'),
                DB::raw('SUM(CASE WHEN status = "Gagal" THEN 1 ELSE 0 END) as gagal'),
                DB::raw('SUM(CASE WHEN status = "Dalam Proses" THEN 1 ELSE 0 END) as dalam_proses'),
                DB::raw('SUM(CASE WHEN status = "Tidak Ada Tindak Lanjut" THEN 1 ELSE 0 END) as tidak_ada_tindak_lanjut'),
                DB::raw('SUM(CASE WHEN status IS NULL THEN 1 ELSE 0 END) as tanpa_keterangan')
            )
                ->groupBy('pelaksana');

            // Apply filtering by 'nama' (executor)
            $query->where('pelaksana', $nama);

            // Optional date filtering (if 'tanggal_mulai' and 'tanggal_selesai' are present)
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $tanggalMulai = $request->input('tanggal_mulai');
                $tanggalSelesai = $request->input('tanggal_selesai');
                $query->whereBetween('deadline', [$tanggalMulai, $tanggalSelesai]);
            }

            $results = $query->first(); // Fetch the first matching record


            return response()->json($results);
        } catch (\Exception $e) {
            Log::error("Unexpected Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function statistic_penyelesaian(Request $request)
    {
        try {
            $nama = $request->input('nama'); // Get 'nama' from the request body

            $query = ArahanPimpinan::select(
                'pelaksana',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN penyelesaian = "Terlambat" THEN 1 ELSE 0 END) as terlambat'),
                DB::raw('ROUND(100 * SUM(CASE WHEN penyelesaian = "Tepat Waktu" THEN 1 ELSE 0 END) / COUNT(*), 2) as persentase_tepat_waktu'),
                DB::raw('SUM(CASE WHEN penyelesaian = "Tepat Waktu" THEN 1 ELSE 0 END) as tepat_waktu'),
                DB::raw('SUM(CASE WHEN penyelesaian IS NULL THEN 1 ELSE 0 END) as belum_tl')
            )
                ->groupBy('pelaksana');

            // Apply filtering by 'nama' (executor)
            $query->where('pelaksana', $nama);

            // Optional date filtering (if 'tanggal_mulai' and 'tanggal_selesai' are present)
            if ($request->has('tanggal_mulai') && $request->has('tanggal_selesai')) {
                $tanggalMulai = $request->input('tanggal_mulai');
                $tanggalSelesai = $request->input('tanggal_selesai');
                $query->whereBetween('deadline', [$tanggalMulai, $tanggalSelesai]);
            }

            $results = $query->first(); // Fetch the first matching record


            return response()->json($results);
        } catch (\Exception $e) {
            Log::error("Unexpected Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateUnitkerja(LaporanArahanPimpinanRequest $request, Rapat $rapat, ArahanPimpinan $arahanPimpinan)
    {
        try {
            $user=Auth::user();
            if ($arahanPimpinan->rapat_id !== $rapat->id) {
                return response()->json(['error' => 'Arahan Pimpinan tidak terkait dengan Rapat ini'], 404);
            }
            if (in_array($user->role, ['staff', 'direktur'])) {
                if ($arahanPimpinan->pelaksana !== $user->unit_kerja) {
                    return response()->json(['error' => 'Anda tidak memiliki izin untuk memperbarui Arahan Pimpinan ini'], 403);
                }
            }

            $data=$request->validated();
            if (empty($arahanPimpinan->status) || $arahanPimpinan->status="Menunggu Konfirmasi Perbaikan") {
                $data['status'] = 'Dalam Proses';
            }
            $arahanPimpinan->update($data);

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


}
