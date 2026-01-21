<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Sesi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    public function index(Request $request, $sesi_id)
    {
        $sesi = Sesi::findOrFail($sesi_id);
        $query = Kehadiran::with('mahasiswa')
            ->where('sesi_id', $sesi_id);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by NIM or Nama
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Order by nama mahasiswa
        $kehadiran = $query->join('mahasiswa', 'kehadiran.mahasiswa_id', '=', 'mahasiswa.id')
            ->orderBy('mahasiswa.nama')
            ->select('kehadiran.*')
            ->paginate(20)
            ->withQueryString();

        // Get all mahasiswa in the session's class for dropdown
        $allMahasiswa = Mahasiswa::where('kelas', $sesi->kelas)
            ->orderBy('nama')
            ->get();

        // Statistics
        $totalMahasiswa = Mahasiswa::where('kelas', $sesi->kelas)->count();
        $hadir = Kehadiran::where('sesi_id', $sesi_id)
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();
        $tidakHadir = Kehadiran::where('sesi_id', $sesi_id)
            ->whereIn('status', ['izin', 'sakit', 'alpha'])
            ->count();

        return view('admin.kehadiran.index', compact(
            'sesi',
            'kehadiran',
            'allMahasiswa',
            'totalMahasiswa',
            'hadir',
            'tidakHadir'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi,id',
            'mahasiswa_id' => 'required|exists:mahasiswa,id'
        ], [
            'sesi_id.required' => 'Sesi wajib dipilih',
            'sesi_id.exists' => 'Sesi tidak ditemukan',
            'mahasiswa_id.required' => 'Mahasiswa wajib dipilih',
            'mahasiswa_id.exists' => 'Mahasiswa tidak ditemukan',
        ]);

        try {
            $sesi = Sesi::findOrFail($request->sesi_id);
            $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);

            // Cek apakah mahasiswa di kelas yang sama dengan sesi
            if ($mahasiswa->kelas !== $sesi->kelas) {
                return back()->with('error', 'Mahasiswa tidak berada di kelas yang sama dengan sesi ini');
            }

            // Cek apakah sudah ada (tidak duplikat)
            $existing = Kehadiran::where('sesi_id', $request->sesi_id)
                ->where('mahasiswa_id', $request->mahasiswa_id)
                ->first();

            if ($existing) {
                return back()->with('error', 'Mahasiswa ini sudah terdaftar dalam sesi ini');
            }

            // Buat kehadiran dengan status hadir default
            Kehadiran::create([
                'sesi_id' => $request->sesi_id,
                'mahasiswa_id' => $request->mahasiswa_id,
                'waktu_hadir' => Carbon::now(),
                'status' => 'hadir'
            ]);

            return back()->with('success', 'Kehadiran berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan kehadiran: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Kehadiran $kehadiran)
    {
        $request->validate([
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpha'
        ], [
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        try {
            // Update hanya status
            $kehadiran->update([
                'status' => $request->status,
                // Jika status diubah jadi hadir/terlambat dan waktu_hadir belum ada, set waktu_hadir
                'waktu_hadir' => in_array($request->status, ['hadir', 'terlambat']) && !$kehadiran->waktu_hadir 
                    ? Carbon::now() 
                    : $kehadiran->waktu_hadir,
            ]);

            return back()->with('success', 'Status kehadiran berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}
