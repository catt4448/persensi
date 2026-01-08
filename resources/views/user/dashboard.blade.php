@extends('layouts.app')

@section('title', 'Dashboard User')
@section('page-title', 'Dashboard User')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-speedometer2 me-2"></i>Selamat Datang, {{ auth()->user()->name }}!
                </h5>
                <p class="card-text text-muted">
                    Ini adalah dashboard Anda. Lihat informasi kehadiran dan riwayat aktivitas Anda di sini.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-white-50 mb-2">Kehadiran Bulan Ini</h6>
                    <h2 class="mb-0">0</h2>
                    <small class="text-white-50">Hari</small>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-white-50 mb-2">Kehadiran Hari Ini</h6>
                    <h2 class="mb-0">
                        @if(true)
                            <i class="bi bi-check-circle"></i>
                        @else
                            <i class="bi bi-x-circle"></i>
                        @endif
                    </h2>
                    <small class="text-white-50">Status</small>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-white-50 mb-2">Persentase Kehadiran</h6>
                    <h2 class="mb-0">0%</h2>
                    <small class="text-white-50">Dari total hari</small>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-percent"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Kehadiran Terkini
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Keluar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada riwayat kehadiran
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Absen Masuk
                    </button>
                    <button class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Absen Keluar
                    </button>
                    <hr>
                    <button class="btn btn-outline-info">
                        <i class="bi bi-person-circle me-2"></i>Lihat Profil
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark-text me-2"></i>Riwayat Lengkap
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Informasi
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Email:</strong><br>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </p>
                <p class="mb-0">
                    <strong>Bergabung:</strong><br>
                    <small class="text-muted">{{ auth()->user()->created_at->format('d M Y') }}</small>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
