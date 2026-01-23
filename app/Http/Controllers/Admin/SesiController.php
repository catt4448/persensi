<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sesi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;


class SesiController extends Controller
{
    public function index(Request $request)
    {
        $query = Sesi::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_sesi', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by tanggal
        if ($request->has('tanggal_dari') && $request->tanggal_dari) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
        // Auto close expired sessions
        $this->autoCloseSesi();
        
        $sesi = $query->orderBy('tanggal', 'desc')
                     ->orderBy('jam_mulai', 'desc')
                     ->paginate(15)
                     ->withQueryString();
                     
        return view('admin.sesi.index', compact('sesi'));
    }

    public function create()
    {
        return view('admin.sesi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'kelas' => 'required|string|max:2|min:1',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ], [
            'nama_sesi.required' => 'Nama sesi wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh di masa lalu',
            'jam_mulai.required' => 'Jam mulai wajib diisi',
            'jam_selesai.required' => 'Jam selesai wajib diisi',
        ]);

        // Custom validation: jam_selesai harus setelah jam_mulai
        if ($request->jam_mulai && $request->jam_selesai) {
            if (strtotime($request->jam_selesai) <= strtotime($request->jam_mulai)) {
                return back()->withInput()
                    ->withErrors(['jam_selesai' => 'Jam selesai harus setelah jam mulai']);
            }
        }

        try {
            // Nonaktifkan sesi lain di kelas yang sama
            Sesi::where('kelas', strtoupper($request->kelas))
                ->where('status', 'aktif')
                ->update(['status' => 'selesai']);

            // Buat sesi baru
            $sesi = Sesi::create([
                'nama_sesi' => $request->nama_sesi,
                'kelas' => strtoupper($request->kelas),
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'status' => 'aktif',
                'created_by' => auth()->id()
            ]);

            return redirect()->route('admin.sesi.index')
                ->with('success', 'Sesi presensi berhasil dibuat & diaktifkan.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Sesi $sesi)
    {
        $kehadiran = $sesi->kehadiran()->with('mahasiswa')->get();
        return view('admin.sesi.show', compact('sesi', 'kehadiran'));
    }

    public function edit(Sesi $sesi)
    {
        if ($sesi->status === 'selesai' || $this->isSesiExpired($sesi)) {
            return redirect()->route('admin.sesi.index')
                ->with('error', 'Sesi sudah selesai/melewati batas waktu dan tidak dapat diedit.');
        }

        return view('admin.sesi.edit', compact('sesi'));
    }

    public function update(Request $request, Sesi $sesi)
    {
        if ($sesi->status === 'selesai' || $this->isSesiExpired($sesi)) {
            return redirect()->route('admin.sesi.index')
                ->with('error', 'Sesi sudah selesai/melewati batas waktu dan tidak dapat diedit.');
        }

        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'kelas' => 'required|string|max:2|min:1',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ], [
            'nama_sesi.required' => 'Nama sesi wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jam_mulai.required' => 'Jam mulai wajib diisi',
            'jam_selesai.required' => 'Jam selesai wajib diisi',
        ]);

        // Custom validation: jam_selesai harus setelah jam_mulai
        if ($request->jam_mulai && $request->jam_selesai) {
            if (strtotime($request->jam_selesai) <= strtotime($request->jam_mulai)) {
                return back()->withInput()
                    ->withErrors(['jam_selesai' => 'Jam selesai harus setelah jam mulai']);
            }
        }

        try {
            $kelasBaru = strtoupper($request->kelas);
            if ($sesi->kelas !== $kelasBaru) {
                $sesi->kehadiran()->delete();
            }

            // Jika kelas berubah, nonaktifkan sesi aktif di kelas baru
            if ($sesi->kelas !== $kelasBaru && $sesi->status === 'aktif') {
                Sesi::where('kelas', $kelasBaru)
                    ->where('status', 'aktif')
                    ->where('id', '!=', $sesi->id)
                    ->update(['status' => 'selesai']);
            }

            $sesi->update([
                'nama_sesi' => $request->nama_sesi,
                'kelas' => $kelasBaru,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ]);

            return redirect()->route('admin.sesi.index')
                ->with('success', 'Data sesi berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Sesi $sesi)
    {
        try {
            if ($sesi->status === 'selesai' || $this->isSesiExpired($sesi)) {
                return redirect()->route('admin.sesi.index')
                    ->with('error', 'Sesi sudah selesai/melewati batas waktu dan tidak dapat dihapus.');
            }

            // Cek apakah sesi sudah ada kehadiran
            if ($sesi->kehadiran()->count() > 0) {
                return redirect()->route('admin.sesi.index')
                    ->with('error', 'Tidak dapat menghapus sesi yang sudah memiliki data kehadiran');
            }

            $sesi->delete();
            return redirect()->route('admin.sesi.index')
                ->with('success', 'Sesi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.sesi.index')
                ->with('error', 'Gagal menghapus sesi: ' . $e->getMessage());
        }
    }

    public function selesai($id)
    {
        try {
            $sesi = Sesi::findOrFail($id);
            $sesi->update(['status' => 'selesai']);
            return back()->with('success', 'Sesi berhasil ditutup');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menutup sesi: ' . $e->getMessage());
        }
    }

    private function autoCloseSesi()
    {
        $now = Carbon::now();

        Sesi::where('status', 'aktif')
            ->where(function ($q) use ($now) {
                $q->whereDate('tanggal', '<', $now->toDateString())
                ->orWhere(function ($q2) use ($now) {
                    $q2->whereDate('tanggal', $now->toDateString())
                        ->where('jam_selesai', '<=', $now->format('H:i:s'));
                });
            })
            ->update(['status' => 'selesai']);
    }

    private function isSesiExpired(Sesi $sesi): bool
    {
        $now = Carbon::now();
        $tanggal = Carbon::parse($sesi->tanggal)->format('Y-m-d');
        $jamSelesai = Carbon::parse($sesi->jam_selesai)->format('H:i:s');
        $batas = Carbon::parse("{$tanggal} {$jamSelesai}");

        return $batas->lessThanOrEqualTo($now);
    }
}
