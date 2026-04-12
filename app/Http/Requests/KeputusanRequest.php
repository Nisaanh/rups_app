<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeputusanRequest extends FormRequest
{
    /**
     * Izinkan user untuk melakukan request ini.
     * Kita set true karena keamanan role sudah dijaga di Controller/Route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan Validasi
     */
    public function rules(): array
    {
        return [
            'nomor_keputusan' => 'required|string|max:255',
          
            'periode_year'    => 'required|digits:4|integer|min:2020|max:' . (date('Y') + 1),
        ];
    }

    /**
     * Pesan Error Custom (Bahasa Indonesia)
     */
    public function messages(): array
    {
        return [
            'nomor_keputusan.required' => 'Nomor keputusan wajib diisi.',
           
            'periode_year.required'    => 'Tahun periode wajib diisi.',
            'periode_year.digits'      => 'Tahun harus terdiri dari 4 angka.',
            'periode_year.min'         => 'Tahun periode minimal 2020.',
            'periode_year.max'         => 'Tahun periode tidak boleh lebih dari ' . (date('Y') + 1),
        ];
    }
}