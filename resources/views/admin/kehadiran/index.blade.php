@extends('layouts.app')

@section('title', 'Kehadiran Sesi')
@section('page-title', 'Kehadiran Sesi')

@section('content')
<!-- Session Info Card -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>{{ $sesi->nama_sesi }}
                </h5>
                <small class="opacity-75">
                    <i class="bi bi-building me-1"></i>Kelas: {{ $sesi->kelas }} | 
                    <i class="bi bi-calendar-date me-1"></i>{{ \Carbon\Carbon::parse($sesi->tanggal)->format('d F Y') }} | 
                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($sesi->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($sesi->jam_selesai)->format('H:i') }}
                </small>
            </div>
            <a href="{{ route('admin.sesi.index', ['tanggal_dari' => \Carbon\Carbon::parse($sesi->tanggal)->format('Y-m-d'), 'tanggal_sampai' => \Carbon\Carbon::parse($sesi->tanggal)->format('Y-m-d')]) }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Sesi
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Mahasiswa</h6>
                            <h3 class="mb-0">{{ $totalMahasiswa }}</h3>
                        </div>
                        <i class="bi bi-people fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Hadir</h6>
                            <h3 class="mb-0">{{ $hadir }}</h3>
                        </div>
                        <i class="bi bi-check-circle fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tidak Hadir</h6>
                            <h3 class="mb-0">{{ $tidakHadir }}</h3>
                        </div>
                        <i class="bi bi-x-circle fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Persentase</h6>
                            <h3 class="mb-0">{{ $totalMahasiswa > 0 ? round(($hadir / $totalMahasiswa) * 100, 1) : 0 }}%</h3>
                        </div>
                        <i class="bi bi-percent fs-2 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-check me-2"></i>Daftar Kehadiran
        </h5>
        @if($sesi->status == 'aktif')
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKehadiranModal">
                <i class="bi bi-plus-circle me-1"></i>Tambah Kehadiran
            </button>
        @endif
    </div>

    <div class="card-body">
        <!-- Search & Filter -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.kehadiran.index', $sesi->id) }}" class="d-flex gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-control" 
                        placeholder="Cari NIM atau Nama..." 
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.kehadiran.index', $sesi->id) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.kehadiran.index', $sesi->id) }}" class="d-flex gap-2 justify-content-end">
                    <select name="status" class="form-select w-auto" style="min-width: 200px;">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha (Tidak Hadir)</option>
                    </select>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    @if(request('status'))
                        <a href="{{ route('admin.kehadiran.index', $sesi->id) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Table -->
        <form action="{{ route('admin.kehadiran.bulkUpdate', $sesi->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th style="width: 50px">No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th style="width: 150px">Waktu Hadir</th>
                            <th style="width: 150px">Status</th>
                            @if($sesi->status == 'aktif')
                                <th style="width: 180px">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswaList as $mhs)
                        <tr>
                            <td class="text-center">{{ $mahasiswaList->firstItem() + $loop->index }}</td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $mhs->nim }}</span>
                            </td>
                            <td>
                                <i class="bi bi-person-circle me-2 text-primary"></i>
                                <strong>{{ $mhs->nama }}</strong>
                            </td>
                            <td class="text-center">
                                @if($mhs->kehadiran_waktu_hadir)
                                    <small>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($mhs->kehadiran_waktu_hadir)->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusColors = [
                                        'hadir' => 'success',
                                        'terlambat' => 'warning',
                                        'izin' => 'info',
                                        'sakit' => 'primary',
                                        'alpha' => 'danger'
                                    ];
                                    $statusIcons = [
                                        'hadir' => 'check-circle',
                                        'terlambat' => 'clock-history',
                                        'izin' => 'envelope',
                                        'sakit' => 'heart-pulse',
                                        'alpha' => 'x-circle'
                                    ];
                                    $statusLabels = [
                                        'hadir' => 'Hadir',
                                        'terlambat' => 'Terlambat',
                                        'izin' => 'Izin',
                                        'sakit' => 'Sakit',
                                        'alpha' => 'Alpha'
                                    ];
                                    $status = $mhs->kehadiran_status;
                                @endphp
                                @if($status)
                                    <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} fs-6">
                                        <i class="bi bi-{{ $statusIcons[$status] ?? 'circle' }} me-1"></i>
                                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary fs-6">
                                        <i class="bi bi-circle me-1"></i>
                                        Belum Diabsen
                                    </span>
                                @endif
                            </td>
                            @if($sesi->status == 'aktif')
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <select name="status[{{ $mhs->id }}]" 
                                                class="form-select form-select-sm" 
                                                style="width: auto;">
                                            <option value="" {{ $status ? '' : 'selected' }}>Pilih status</option>
                                            <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="terlambat" {{ $status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                            <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="alpha" {{ $status == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $sesi->status == 'aktif' ? '6' : '5' }}" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Tidak ada data kehadiran</p>
                            @if(request('search') || request('status'))
                                <small>Coba gunakan filter lain atau <a href="{{ route('admin.kehadiran.index', $sesi->id) }}">reset pencarian</a></small>
                            @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sesi->status == 'aktif' && $mahasiswaList->count() > 0)
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            @endif
        </form>

        <!-- Pagination -->
@if($mahasiswaList->hasPages())
        <div class="d-flex justify-content-center mt-4">
    {{ $mahasiswaList->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Kehadiran Modal -->
@if($sesi->status == 'aktif')
<div class="modal fade" id="addKehadiranModal" tabindex="-1" aria-labelledby="addKehadiranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.kehadiran.store') }}">
                @csrf
                <input type="hidden" name="sesi_id" value="{{ $sesi->id }}">
                
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addKehadiranModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kehadiran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mahasiswa_id" class="form-label fw-bold">
                            Pilih Mahasiswa <span class="text-danger">*</span>
                        </label>
                        <select name="mahasiswa_id" 
                                id="mahasiswa_id" 
                                class="form-select form-select-lg @error('mahasiswa_id') is-invalid @enderror" 
                                required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($allMahasiswa as $mhs)
                                @php
                                    $hasKehadiran = \App\Models\Kehadiran::where('sesi_id', $sesi->id)
                                        ->where('mahasiswa_id', $mhs->id)
                                        ->exists();
                                @endphp
                                @if(!$hasKehadiran)
                                    <option value="{{ $mhs->id }}">
                                        {{ $mhs->nim }} - {{ $mhs->nama }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('mahasiswa_id')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>Hanya menampilkan mahasiswa yang belum terdaftar dalam sesi ini
                        </small>
                    </div>
                    
                    @if($allMahasiswa->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Tidak ada mahasiswa di kelas {{ $sesi->kelas }}
                        </div>
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" {{ $allMahasiswa->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
