<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Imports\MahasiswaImport;
use Maatwebsite\Excel\Facades\Excel;


class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with('user');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('no_kartu', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }
        
        $mahasiswa = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('admin.mahasiswa.index', compact('mahasiswa'));
    }

    public function create()
    {
        return view('admin.mahasiswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim'      => 'required|unique:mahasiswa,nim|max:20',
            'nama'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email|max:255',
            'no_kartu' => 'required|unique:mahasiswa,no_kartu|max:50',
            'kelas'    => 'required|string|max:2|min:1',
        ], [
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'no_kartu.required' => 'UID RFID wajib diisi',
            'no_kartu.unique' => 'UID RFID sudah terdaftar',
            'kelas.required' => 'Kelas wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
        ]);

        try {
            // Generate email jika tidak diisi
            $email = $request->email ?? ($request->nim . '@student.budiluhur.ac.id');

            // Buat User otomatis
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $email,
                'password' => Hash::make('12345678'), // password default
                'role'     => 'user',
            ]);

            // Buat Mahasiswa
            $mahasiswa = Mahasiswa::create([
                'user_id'  => $user->id,
                'nim'      => $request->nim,
                'nama'     => $request->nama,
                'no_kartu' => $request->no_kartu,
                'kelas'    => strtoupper($request->kelas),
            ]);

            $this->insertMissingKehadiranRaw($mahasiswa->id, strtoupper($request->kelas));

            return redirect()->route('mahasiswa.index')
                ->with('success', 'Mahasiswa & akun user berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        return view('admin.mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nim' => 'required|unique:mahasiswa,nim,' . $mahasiswa->id . '|max:20',
            'nama' => 'required|string|max:255',
            'no_kartu' => 'required|unique:mahasiswa,no_kartu,' . $mahasiswa->id . '|max:50',
            'kelas' => 'required|string|max:2|min:1',
        ], [
            'nim.required' => 'NIM wajib diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'no_kartu.required' => 'UID RFID wajib diisi',
            'no_kartu.unique' => 'UID RFID sudah terdaftar',
            'kelas.required' => 'Kelas wajib diisi',
        ]);

        try {
            $kelasLama = $mahasiswa->kelas;
            $kelasBaru = strtoupper($request->kelas);

            $mahasiswa->update([
                'nim' => $request->nim,
                'nama' => $request->nama,
                'no_kartu' => $request->no_kartu,
                'kelas' => $kelasBaru,
            ]);

            if ($kelasLama !== $kelasBaru) {
                $this->deleteKehadiranByKelasRaw($mahasiswa->id, $kelasLama);
                $this->insertMissingKehadiranRaw($mahasiswa->id, $kelasBaru);
            }

            // Sinkronkan nama user
            if ($mahasiswa->user) {
                $mahasiswa->user->update([
                    'name' => $request->nama
                ]);
            }

            return redirect()->route('mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        try {
            // Hapus user juga jika ada
            if ($mahasiswa->user) {
                $mahasiswa->user->delete();
            }

            $mahasiswa->delete();

            return back()->with('success', 'Mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus mahasiswa: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120' // max 5MB
        ], [
            'file.required' => 'File wajib diupload',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        try {
            Excel::import(new MahasiswaImport, $request->file('file'));

            return redirect()
                ->route('mahasiswa.index')
                ->with('success', 'Data mahasiswa berhasil diimport');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    private function insertMissingKehadiranRaw(int $mahasiswaId, string $kelas): void
    {
        $now = Carbon::now();
        $tanggal = $now->toDateString();
        $jam = $now->format('H:i:s');
        $timestamp = $now->toDateTimeString();

        DB::statement(
            'INSERT INTO kehadiran (sesi_id, mahasiswa_id, status, waktu_hadir, created_at, updated_at)
             SELECT s.id, ?, "alpha", NULL, ?, ?
             FROM sesi s
             WHERE s.kelas = ?
               AND (s.tanggal < ? OR (s.tanggal = ? AND s.jam_selesai <= ?))
               AND NOT EXISTS (
                   SELECT 1 FROM kehadiran k
                   WHERE k.sesi_id = s.id AND k.mahasiswa_id = ?
               )',
            [
                $mahasiswaId,
                $timestamp,
                $timestamp,
                $kelas,
                $tanggal,
                $tanggal,
                $jam,
                $mahasiswaId,
            ]
        );
    }

    private function deleteKehadiranByKelasRaw(int $mahasiswaId, string $kelas): void
    {
        DB::statement(
            'DELETE k FROM kehadiran k
             INNER JOIN sesi s ON s.id = k.sesi_id
             WHERE k.mahasiswa_id = ? AND s.kelas = ?',
            [$mahasiswaId, $kelas]
        );
    }
}
