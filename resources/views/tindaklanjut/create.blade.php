<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Input Progres Tindak Lanjut</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- FORM INPUT --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('tindaklanjut.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="arahan_id" value="{{ $selectedArahanId }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Arahan</label>
                            <select class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 text-sm font-bold" disabled>
                                @foreach($arahanList as $arahan)
                                <option value="{{ $arahan->id }}" {{ $selectedArahanId == $arahan->id ? 'selected' : '' }}>
                                    [{{ $arahan->keputusan->periode_year }}] - {{ Str::limit($arahan->strategi, 80) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Unit Kerja Pelaksana</label>
                            @php $currentArahan = $arahanList->where('id', $selectedArahanId)->first(); @endphp
                            <input type="text" value="{{ $currentArahan->unitKerja->name ?? '' }}" class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-800 font-bold text-sm" readonly>
                            <input type="hidden" name="unit_kerja_id" value="{{ $currentArahan->unit_kerja_id ?? '' }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- ALERT ERROR VALIDASI --}}
                        @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <strong class="font-bold">Gagal Simpan!</strong>
                            <ul class="mt-2 text-sm">
                                @foreach ($errors->all() as $error)
                                <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Bulan Progres</label>
                            <select name="periode_bulan" class="w-full rounded-lg border-gray-300 text-sm" required>
                                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bulan)
                                <option value="{{ $index + 1 }}" {{ date('n') == $index + 1 ? 'selected' : '' }}>{{ $bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Tahun</label>
                            <input type="number" name="periode_tahun" value="{{ date('Y') }}" class="w-full rounded-lg border-gray-300 text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Uraian Tindak Lanjut <span class="text-red-500">*</span></label>
                        <textarea name="tindak_lanjut" rows="4" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Tuliskan progres pekerjaan..." required></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-1">Evidence / Bukti Pendukung</label>
                        <input type="file" name="evidence" class="text-sm text-gray-500">
                    </div>

                    <div class="pt-6 border-t flex justify-between items-center bg-gray-50 -mx-6 -mb-6 p-6">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="create_another" value="yes" checked class="text-blue-600"><span class="text-xs font-bold text-gray-600 uppercase">Input Lagi</span></label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="create_another" value="no" class="text-blue-600"><span class="text-xs font-bold text-gray-600 uppercase">Selesai</span></label>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('tindaklanjut.index') }}" class="px-6 py-2 text-xs font-bold text-gray-400 uppercase">Batal</a>
                            <button type="submit" class="px-8 py-2 bg-blue-600 text-white text-xs font-black uppercase rounded-lg shadow-lg hover:bg-blue-700 transition">Simpan Laporan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>