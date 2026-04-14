<?php

namespace App\Services;

use App\Models\Keputusan;
use App\Models\Arahan;
use App\Models\TindakLanjut;
use App\Models\User;
use App\Models\UnitKerja;
use Illuminate\Support\Facades\Auth;

class ExportService
{
    protected $user;
    protected $role;
    
    public function __construct()
    {
        $this->user = Auth::user();
        $this->role = $this->user->getRoleNames()->first();
    }
    
    /**
     * Get data based on user role
     */
    public function getData($type, $filters = [])
    {
        switch ($this->role) {
            case 'Admin':
                return $this->getAdminData($type, $filters);
            case 'Tim Monitoring':
                return $this->getTimMonitoringData($type, $filters);
            case 'Auditi':
                return $this->getAuditiData($type, $filters);
            case 'Atasan Auditi':
                return $this->getAtasanAuditiData($type, $filters);
            case 'Pengendali Teknis':
                return $this->getPengendaliTeknisData($type, $filters);
            case 'Pengendali Mutu':
                return $this->getPengendaliMutuData($type, $filters);
            case 'Penanggung Jawab':
                return $this->getPenanggungJawabData($type, $filters);
            default:
                return collect();
        }
    }
    
    /**
     * Admin - Semua data
     */
    private function getAdminData($type, $filters)
    {
        switch ($type) {
            case 'keputusan':
                return Keputusan::with('creator')
                    ->when($filters['periode'] ?? null, fn($q, $p) => $q->where('periode_year', $p))
                    ->latest()->get();
            case 'arahan':
                return Arahan::with(['keputusan', 'unitKerja', 'pic'])
                    ->when($filters['keputusan_id'] ?? null, fn($q, $id) => $q->where('keputusan_id', $id))
                    ->latest()->get();
            case 'tindak_lanjut':
                return TindakLanjut::with(['arahan', 'unitKerja', 'creator'])
                    ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
                    ->latest()->get();
            case 'users':
                return User::with('unitKerja')->get();
            case 'unit_kerja':
                return UnitKerja::with('parent')->get();
            default:
                return collect();
        }
    }
    
    /**
     * Tim Monitoring - Fokus ke monitoring
     */
    private function getTimMonitoringData($type, $filters)
    {
        switch ($type) {
            case 'keputusan':
                return Keputusan::with('creator')
                    ->when($filters['periode'] ?? null, fn($q, $p) => $q->where('periode_year', $p))
                    ->latest()->get();
            case 'arahan':
                return Arahan::with(['keputusan', 'unitKerja', 'pic'])
                    ->latest()->get();
            case 'tindak_lanjut':
                return TindakLanjut::with(['arahan', 'unitKerja', 'creator'])
                    ->whereIn('status', ['in_approval', 'approved'])
                    ->latest()->get();
            default:
                return collect();
        }
    }
    
    /**
     * Auditi - Hanya data milik sendiri
     */
    private function getAuditiData($type, $filters)
    {
        switch ($type) {
            case 'arahan':
                return Arahan::with(['keputusan', 'unitKerja'])
                    ->where('pic_unit_kerja_id', $this->user->id)
                    ->get();
            case 'tindak_lanjut':
                return TindakLanjut::with(['arahan', 'unitKerja'])
                    ->where('created_by', $this->user->id)
                    ->latest()->get();
            default:
                return collect();
        }
    }
    
    /**
     * Atasan Auditi - Bawahan yang perlu approval
     */
    private function getAtasanAuditiData($type, $filters)
    {
        $subordinateIds = User::where('pic_unit_kerja_id', $this->user->id)->pluck('id');
        
        if ($type === 'tindak_lanjut') {
            return TindakLanjut::with(['arahan', 'unitKerja', 'creator'])
                ->whereIn('created_by', $subordinateIds)
                ->where('status', 'pending')
                ->latest()->get();
        }
        return collect();
    }
    
    /**
     * Pengendali Teknis
     */
    private function getPengendaliTeknisData($type, $filters)
    {
        if ($type === 'tindak_lanjut') {
            return TindakLanjut::with(['arahan', 'unitKerja', 'creator'])
                ->where('status', 'in_approval')
                ->latest()->get();
        }
        return collect();
    }
    
    /**
     * Pengendali Mutu
     */
    private function getPengendaliMutuData($type, $filters)
    {
        if ($type === 'tindak_lanjut') {
            return TindakLanjut::with(['arahan', 'unitKerja', 'creator'])
                ->where('status', 'in_approval')
                ->latest()->get();
        }
        return collect();
    }
    
    /**
     * Penanggung Jawab
     */
    private function getPenanggungJawabData($type, $filters)
    {
        if ($type === 'tindak_lanjut') {
            return TindakLanjut::with(['arahan', 'unitKerja', 'creator'])
                ->where('status', 'in_approval')
                ->latest()->get();
        }
        return collect();
    }
    
    /**
     * Get available export types based on role
     */
    public function getAvailableTypes()
    {
        $types = [
            'keputusan' => 'Data Keputusan',
            'arahan' => 'Data Arahan',
            'tindak_lanjut' => 'Data Tindak Lanjut',
        ];
        
        if ($this->role == 'Admin') {
            $types['users'] = 'Data User';
            $types['unit_kerja'] = 'Data Unit Kerja';
        }
        
        if ($this->role == 'Auditi') {
            unset($types['keputusan']);
        }
        
        if (in_array($this->role, ['Atasan Auditi', 'Pengendali Teknis', 'Pengendali Mutu', 'Penanggung Jawab'])) {
            $types = ['tindak_lanjut' => 'Data Tindak Lanjut'];
        }
        
        return $types;
    }
}