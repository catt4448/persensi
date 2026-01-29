@extends('layouts.app')

@section('title', 'Perangkat')
@section('page-title', 'Perangkat')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-router me-2"></i>Daftar Perangkat
        </h5>
        <a href="{{ route('admin.devices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tambah Perangkat
        </a>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.devices.index') }}" class="d-flex gap-2">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Cari Device ID atau Nama..."
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
            <div class="col-md-6 text-end">
                <small class="text-muted">
                    Total: <strong>{{ $devices->total() }}</strong> perangkat
                </small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th style="width: 50px">No</th>
                        <th>Device ID</th>
                        <th>Nama</th>
                        <th>Token</th>
                        <th style="width: 120px">Status</th>
                        <th style="width: 180px">Terakhir Aktif</th>
                        <th style="width: 160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                    <tr>
                        <td class="text-center">{{ $devices->firstItem() + $loop->index }}</td>
                        <td><code>{{ $device->device_id }}</code></td>
                        <td>{{ $device->name ?? '-' }}</td>
                        <td><code>{{ $device->token }}</code></td>
                        <td class="text-center">
                            @if($device->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $device->last_seen_at ? $device->last_seen_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.devices.edit', $device->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.devices.destroy', $device->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus perangkat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p class="mb-0">Tidak ada data perangkat</p>
                                @if(request('search'))
                                    <small>Coba gunakan kata kunci lain atau <a href="{{ route('admin.devices.index') }}">reset pencarian</a></small>
                                @else
                                    <small><a href="{{ route('admin.devices.create') }}">Tambah perangkat pertama</a></small>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($devices->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $devices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
