<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArahanRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan melakukan request ini.
     */
    public function authorize(): bool
    {
        return true; // Keamanan role sudah ditangani di Controller/Gate
    }

    /**
     * Aturan Validasi.
     */
    public function rules(): array
    {
        return [
            // Gunakan nama tabel yang sesuai di database (biasanya 'keputusan' atau 'keputusans')
            'keputusan_id'      => 'required|exists:keputusan,id', 
            'unit_kerja_id'     => 'required|exists:unit_kerja,id',
            'pic_unit_kerja_id' => 'required|exists:users,id',
            'tanggal_terbit'    => 'required|date',
            'strategi'          => 'required|string|min:10',
            
            // Opsional: Validasi untuk fitur looping redirect
            'create_another'    => 'nullable|in:yes,no' 
        ];
    }

    /**
     * Pesan Error Custom (Bahasa Indonesia).
     */
    public function messages(): array
    {
        return [
            'keputusan_id.required'      => 'Keputusan RUPS wajib dipilih.',
            'keputusan_id.exists'        => 'Data keputusan tidak valid.',
            'unit_kerja_id.required'     => 'Unit kerja pelaksana wajib dipilih.',
            'pic_unit_kerja_id.required' => 'PIC unit kerja wajib ditentukan.',
            'tanggal_terbit.required'    => 'Tanggal terbit arahan wajib diisi.',
            'tanggal_terbit.date'        => 'Format tanggal tidak valid.',
            'strategi.required'          => 'Strategi/Isi arahan wajib diisi.',
            'strategi.min'               => 'Strategi minimal berisi 10 karakter.',
        ];
    }
}