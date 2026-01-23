@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-speedometer2 me-2"></i>Selamat Datang, {{ auth()->user()->name }}!
                </h5>
                <p class="card-text text-muted">
                    Ini adalah dashboard admin. Anda dapat mengelola semua aspek aplikasi dari sini.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-white-50 mb-2">Total Mahasiswa</h6>
                    <h2 class="mb-0">{{ $totalMahasiswa ?? 0 }}</h2>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-mortarboard"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-white-50 mb-2">Kehadiran Hari Ini</h6>
                    <h2 class="mb-0">{{ $kehadiranHariIni ?? 0 }}</h2>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-white-50 mb-2">Sesi Aktif</h6>
                    <h2 class="mb-0">{{ $totalSesiAktif ?? 0 }}</h2>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-white-50 mb-2">Total Users</h6>
                    <h2 class="mb-0">{{ $totalUsers ?? 0 }}</h2>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-activity me-2"></i>Aktivitas Terkini
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Aktivitas</th>
                                <th>User</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada aktivitas
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
                    <i class="bi bi-list-check me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                 <div class="d-grid gap-2">
                    <a href="{{ route('users.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus me-2"></i>Tambah Admin
                    </a>
                    <a href="{{ route('admin.sesi.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-calendar-plus me-2"></i>Buat Sesi Baru
                    </a>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-warning">
                        <i class="bi bi-gear me-2"></i>Pengaturan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
