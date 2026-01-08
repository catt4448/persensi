@extends('layouts.auth')

@section('title', 'Login')

@section('header-title', 'Login')
@section('header-subtitle', 'Selamat datang kembali')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

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
                autofocus
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
                placeholder="Masukkan password" 
                required
            >
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">
            Ingat saya
        </label>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>
    </div>
</form>

<div class="text-muted">
    <p class="mb-2">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    <p class="mb-0"><a href="{{ route('password.email') }}">Lupa password?</a></p>
</div>
@endsection
