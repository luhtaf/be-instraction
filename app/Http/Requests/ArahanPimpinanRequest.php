<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArahanPimpinanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'arahan' => 'required|string',
            'deadline' => 'nullable|string',
            'pelaksana' => 'nullable|string',
            'status' => 'nullable|string',
            'penyelesaian' => 'nullable|string',
            'data_dukung' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'batas_konfirmasi' => 'nullable|date',
        ];
    }
}
