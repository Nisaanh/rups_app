<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TindakLanjutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Izinkan semua user yang sudah login
    }

    public function rules(): array
    {
        return [
            'arahan_id'     => 'required|exists:arahan,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer',
            'tindak_lanjut' => 'required|string|min:10', // Samakan nama field dengan Blade
            'evidence'      => 'nullable|file|mimes:pdf,jpg,png,docx|max:5120', // Naikkan ke 5MB agar aman
            
        ];
    }

    public function messages(): array
    {
        return [
            'arahan_id.required'     => 'Pilih arahan yang akan ditindaklanjuti.',
            'unit_kerja_id.required' => 'Unit kerja tidak terdeteksi.',
            'tindak_lanjut.required' => 'Penjelasan tindak lanjut wajib diisi.',
            'tindak_lanjut.min'      => 'Penjelasan minimal 10 karakter.',
            'evidence.mimes'         => 'Bukti harus berupa format: pdf, jpg, png, atau docx.',
            'evidence.max'           => 'Ukuran bukti tidak boleh lebih dari 5MB.',
        ];
    }
}