<?php

namespace App\Console\Commands;

use App\Models\Kehadiran;
use App\Models\Mahasiswa;
use App\Models\Sesi;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FinalizeSesiKehadiran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kehadiran:finalize-sesi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Isi alpha otomatis untuk mahasiswa yang belum absen setelah sesi berakhir';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $timestamp = $now->toDateTimeString();

        $sesiList = Sesi::where(function ($q) use ($now) {
            $q->whereDate('tanggal', '<', $now->toDateString())
                ->orWhere(function ($q2) use ($now) {
                    $q2->whereDate('tanggal', $now->toDateString())
                        ->where('jam_selesai', '<=', $now->format('H:i:s'));
                });
        })->get();

        $inserted = 0;
        $closed = 0;

        foreach ($sesiList as $sesi) {
            $mahasiswaIds = Mahasiswa::where('kelas', $sesi->kelas)
                ->pluck('id')
                ->all();

            if (empty($mahasiswaIds)) {
                continue;
            }

            $existingIds = Kehadiran::where('sesi_id', $sesi->id)
                ->pluck('mahasiswa_id')
                ->all();

            $missingIds = array_diff($mahasiswaIds, $existingIds);

            if (!empty($missingIds)) {
                $rows = [];
                foreach ($missingIds as $mahasiswaId) {
                    $rows[] = [
                        'sesi_id' => $sesi->id,
                        'mahasiswa_id' => $mahasiswaId,
                        'status' => 'alpha',
                        'waktu_hadir' => null,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                Kehadiran::insert($rows);
                $inserted += count($rows);
            }

            if ($sesi->status !== 'selesai') {
                $sesi->update(['status' => 'selesai']);
                $closed++;
            }
        }

        $this->info("Selesai: {$inserted} kehadiran alpha dibuat, {$closed} sesi ditutup.");

        return 0;
    }
}
