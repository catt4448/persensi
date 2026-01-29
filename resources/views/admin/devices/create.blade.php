@extends('layouts.app')

@section('title', 'Tambah Perangkat')
@section('page-title', 'Tambah Perangkat')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-plus-circle me-2"></i>Tambah Perangkat
        </h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.devices.store') }}">
            @csrf

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="device_id" class="form-label fw-bold">
                        <i class="bi bi-upc-scan me-1 text-primary"></i>Device ID
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="device_id"
                        name="device_id"
                        class="form-control form-control-lg @error('device_id') is-invalid @enderror"
                        value="{{ old('device_id') }}"
                        required
                    >
                    @error('device_id')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold">
                        <i class="bi bi-tag me-1 text-primary"></i>Nama Perangkat
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="token" class="form-label fw-bold">
                        <i class="bi bi-key me-1 text-primary"></i>Token
                    </label>
                    <input
                        type="text"
                        id="token"
                        name="token"
                        class="form-control form-control-lg @error('token') is-invalid @enderror"
                        value="{{ old('token') }}"
                        placeholder="Kosongkan untuk auto-generate"
                    >
                    @error('token')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="is_active"
                            name="is_active"
                            value="1"
                            {{ old('is_active', '1') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="is_active">
                            Aktif
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.devices.index') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-arrow-left me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
