<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Sesi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function scan(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'device_id' => 'required|string|max:100',
            'token' => 'required|string|max:100',
        ], [
            'uid.required' => 'UID wajib diisi',
            'device_id.required' => 'Device ID wajib diisi',
            'token.required' => 'Token wajib diisi',
        ]);

        $deviceId = strtoupper(trim($request->device_id));
        $token = trim($request->token);

        $device = Device::where('device_id', $deviceId)
            ->where('token', $token)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Device tidak terdaftar atau token salah',
            ], 401);
        }

        $uid = strtoupper(preg_replace('/[^A-Fa-f0-9]/', '', $request->uid));
        if ($uid === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'UID tidak valid',
            ], 422);
        }

        $mahasiswa = Mahasiswa::where('no_kartu', $uid)->first();
        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu tidak terdaftar',
            ], 404);
        }

        $now = Carbon::now();
        $sesi = Sesi::where('kelas', $mahasiswa->kelas)
            ->where('status', 'aktif')
            ->whereDate('tanggal', $now->toDateString())
            ->where('jam_mulai', '<=', $now->format('H:i:s'))
            ->where('jam_selesai', '>=', $now->format('H:i:s'))
            ->orderBy('jam_mulai')
            ->first();

        if (!$sesi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada sesi aktif untuk kelas ini',
            ], 404);
        }

        $device->update([
            'last_seen_at' => $now,
        ]);

        $kehadiran = Kehadiran::where('sesi_id', $sesi->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($kehadiran && in_array($kehadiran->status, ['hadir', 'terlambat'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa sudah absen',
                'data' => [
                    'mahasiswa' => [
                        'id' => $mahasiswa->id,
                        'nim' => $mahasiswa->nim,
                        'nama' => $mahasiswa->nama,
                        'kelas' => $mahasiswa->kelas,
                    ],
                    'sesi' => [
                        'id' => $sesi->id,
                        'nama_sesi' => $sesi->nama_sesi,
                        'kelas' => $sesi->kelas,
                        'tanggal' => $sesi->tanggal,
                        'jam_mulai' => $sesi->jam_mulai,
                        'jam_selesai' => $sesi->jam_selesai,
                    ],
                    'kehadiran' => [
                        'status' => $kehadiran->status,
                        'waktu_hadir' => $kehadiran->waktu_hadir,
                    ],
                ],
            ], 409);
        }

        if ($kehadiran) {
            $kehadiran->update([
                'status' => 'hadir',
                'waktu_hadir' => $kehadiran->waktu_hadir ?? $now,
                'device_id' => $device->id,
            ]);
        } else {
            $kehadiran = Kehadiran::create([
                'sesi_id' => $sesi->id,
                'mahasiswa_id' => $mahasiswa->id,
                'status' => 'hadir',
                'waktu_hadir' => $now,
                'device_id' => $device->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil',
            'data' => [
                'mahasiswa' => [
                    'id' => $mahasiswa->id,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->nama,
                    'kelas' => $mahasiswa->kelas,
                ],
                'sesi' => [
                    'id' => $sesi->id,
                    'nama_sesi' => $sesi->nama_sesi,
                    'kelas' => $sesi->kelas,
                    'tanggal' => $sesi->tanggal,
                    'jam_mulai' => $sesi->jam_mulai,
                    'jam_selesai' => $sesi->jam_selesai,
                ],
                'kehadiran' => [
                    'status' => $kehadiran->status,
                    'waktu_hadir' => $kehadiran->waktu_hadir,
                ],
            ],
        ]);
    }
}
