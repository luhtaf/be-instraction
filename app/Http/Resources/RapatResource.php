<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// use App\Http\Resources\PenanggungJawabResource;

class RapatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
    // public function toArray($request)
    // {
    //     // Conditional check if it's a single post or a collection
    //     $isCollection = $this->resource instanceof Illuminate\Pagination\LengthAwarePaginator;
    //     return [
    //         'id' => $isCollection ? $this->id : $this->resource->id,
    //         'nama'     => $isCollection ? $this->id : $this->resource->nama,
    //         'kategori' => $isCollection ? $this->id : $this->resource->kategori,
    //         'tanggal' => $isCollection ? $this->id : $this->resource->tanggal,
    //         'urgensi' => $isCollection ? $this->id : $this->resource->urgensi,
    //         'waktu' => $isCollection ? $this->id : $this->resource->waktu,
    //         'lokasi' => $isCollection ? $this->id : $this->resource->lokasi,
    //         'metode' => $isCollection ? $this->id : $this->resource->metode,
    //         'penyelenggara' => $isCollection ? $this->id : $this->resource->penyelenggara,
    //         'pimpinan' => $isCollection ? $this->id : $this->resource->pimpinan,
    //         'jenis' => $isCollection ? $this->id : $this->resource->jenis,
    //         'pemapar' => $isCollection ? $this->id : $this->resource->pemapar,
    //         'tautan' => $isCollection ? $this->id : $this->resource->tautan,
    //         'catatan' => $isCollection ? $this->id : $this->resource->catatan,
    //         'keterangan' => $isCollection ? $this->id : $this->resource->keterangan,
    //         'penanggung_jawab' => PenanggungJawabResource::collection($isCollection ? $this->whenLoaded('penanggung_jawab') : $this->resource->penanggung_jawab)
    //     ];
    // }
}
