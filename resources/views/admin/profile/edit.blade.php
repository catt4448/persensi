@extends('layouts.app')

@section('title', 'Pengaturan Profil')
@section('page-title', 'Pengaturan Profil')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-person-gear me-2"></i>Profil Saya
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold">
                        <i class="bi bi-person me-1 text-primary"></i>Nama
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control form-control-lg @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">
                        <i class="bi bi-envelope me-1 text-primary"></i>Email
                        <span class="text-danger">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">
                        <i class="bi bi-shield-lock me-1 text-primary"></i>Password Baru
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                        placeholder="Kosongkan jika tidak ingin mengganti"
                    >
                    @error('password')
                        <div class="invalid-feedback">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-bold">
                        <i class="bi bi-shield-check me-1 text-primary"></i>Konfirmasi Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control form-control-lg"
                        placeholder="Ulangi password baru"
                    >
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
