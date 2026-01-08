@extends('layouts.auth')

@section('title', 'Register')

@section('header-title', 'Daftar Akun')
@section('header-subtitle', 'Buat akun baru untuk memulai')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">
            <i class="bi bi-person me-2"></i>Nama Lengkap
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>
            <input 
                type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                placeholder="Masukkan nama lengkap" 
                value="{{ old('name') }}" 
                required 
                autofocus
            >
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">
            <i class="bi bi-envelope me-2"></i>Email
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
            <input 
                type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                id="email" 
                name="email" 
                placeholder="nama@email.com" 
                value="{{ old('email') }}" 
                required
            >
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">
            <i class="bi bi-lock me-2"></i>Password
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
            <input 
                type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                id="password" 
                name="password" 
                placeholder="Minimal 8 karakter" 
                required
            >
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <small class="form-text text-muted">Password minimal 8 karakter</small>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">
            <i class="bi bi-lock-fill me-2"></i>Konfirmasi Password
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock-fill"></i>
            </span>
            <input 
                type="password" 
                class="form-control" 
                id="password_confirmation" 
                name="password_confirmation" 
                placeholder="Ulangi password" 
                required
            >
        </div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>Daftar
        </button>
    </div>
</form>

<div class="text-muted">
    <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
</div>
@endsection
