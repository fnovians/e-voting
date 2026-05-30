@extends('layouts.app')

@section('title', 'Masuk - SecVote')

@section('content')
<div class="auth-card-wrapper" style="max-width: 460px; margin: auto;">
  <!-- Centered brand logo -->
  <div class="text-center mb-4">
    <a class="logo text-decoration-none text-white fs-2" href="/" style="background: none; -webkit-background-clip: unset; -webkit-text-fill-color: #FFFFFF; color: #FFFFFF; font-weight: 800; text-shadow: 0 2px 4px rgba(15, 23, 42, 0.15);">
      🗳️ Sec<span>Vote</span>
    </a>
  </div>

  <div class="card auth-card">
    <div class="text-center mb-4">
      <h2 class="fw-extrabold text-dark" style="letter-spacing: -0.5px;">Sistem E-Voting HIMA</h2>
      <p class="text-muted small">Dilindungi enkripsi AES-256 dan autentikasi ganda OTP</p>
    </div>
    
    <ul class="nav nav-pills nav-justified mb-4 p-1 rounded-3" id="auth-tabs" style="background: #F1F5F9;">
      <li class="nav-item">
        <a class="nav-link active" href="{{ route('login') }}">Masuk</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('register') }}">Registrasi</a>
      </li>
    </ul>

    <!-- Flash Notifications inside Auth Card -->
    @if (session('success'))
      <div class="alert alert-success py-2 px-3 small border-glass d-flex align-items-center gap-2 mb-3" role="alert" style="background-color: #ecfdf5; border-color: rgba(16, 185, 129, 0.15); color: #065f46; border-radius: 10px; font-size: 0.8rem;">
        <span>🔔</span>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger py-2 px-3 small border-glass d-flex align-items-center gap-2 mb-3" role="alert" style="background-color: #fef2f2; border-color: rgba(239, 68, 68, 0.15); color: #991b1b; border-radius: 10px; font-size: 0.8rem;">
        <span>⚠️</span>
        <div>{{ session('error') }}</div>
      </div>
    @endif

    <!-- LOGIN FORM -->
    <form action="{{ route('login') }}" method="POST">
      @csrf
      <div class="mb-3 text-start">
        <label class="form-label auth-label">Nomor Induk Mahasiswa (NIM)</label>
        <input type="text" name="nim" class="form-control auth-input" placeholder="Contoh: 120203001" value="{{ old('nim') }}" required>
      </div>
      <div class="mb-4 text-start">
        <label class="form-label auth-label">Password</label>
        <input type="password" name="password" class="form-control auth-input" placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn auth-btn-primary w-100 py-2.5">Masuk Sistem</button>
    </form>
  </div>
</div>
@endsection
