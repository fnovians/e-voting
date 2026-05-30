@extends('layouts.app')

@section('title', 'Registrasi Pemilih - SecVote')

@section('content')
<div class="auth-card-wrapper" style="max-width: 460px; margin: auto;">
  <div class="card auth-card">
    <div class="text-center mb-4">
      <div class="mb-3">
        <a class="logo text-decoration-none d-inline-flex align-items-center justify-content-center" href="/" style="background: none; -webkit-background-clip: unset; font-weight: 800;">
          @if(file_exists(public_path('assets/img/logo.svg')))
            <img src="{{ asset('assets/img/logo.svg') }}" alt="SecVote Logo" style="height: 70px; object-fit: contain;">
          @elseif(file_exists(public_path('assets/img/logo.png')))
            <img src="{{ asset('assets/img/logo.png') }}" alt="SecVote Logo" style="height: 70px; object-fit: contain;">
          @elseif(file_exists(public_path('assets/img/logo.jpg')))
            <img src="{{ asset('assets/img/logo.jpg') }}" alt="SecVote Logo" style="height: 70px; object-fit: contain;">
          @elseif(file_exists(public_path('assets/img/logo.jpeg')))
            <img src="{{ asset('assets/img/logo.jpeg') }}" alt="SecVote Logo" style="height: 70px; object-fit: contain;">
          @else
            🗳️ Sec<span style="color: var(--text-main);">Vote</span>
          @endif
        </a>
      </div>
      <h2 class="fw-extrabold text-dark" style="letter-spacing: -0.5px;">Sistem E-Voting HIMA</h2>
      <p class="text-muted small">Registrasi Akun Pemilih Baru HIMA/BEM</p>
    </div>
    
    <ul class="nav nav-pills nav-justified mb-4 p-1 rounded-3" id="auth-tabs" style="background: #F1F5F9;">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}">Masuk</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="{{ route('register') }}">Registrasi</a>
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

    <!-- REGISTER FORM -->
    <form action="{{ route('register') }}" method="POST">
      @csrf
      <div class="mb-3 text-start">
        <label class="form-label auth-label">Nomor Induk Mahasiswa (NIM)</label>
        <input type="text" name="nim" class="form-control auth-input" placeholder="Contoh: 120203006" value="{{ old('nim') }}" required>
      </div>
      <div class="mb-3 text-start">
        <label class="form-label auth-label">Nama Lengkap</label>
        <input type="text" name="name" class="form-control auth-input" placeholder="Contoh: Fajar Pratama" value="{{ old('name') }}" required>
      </div>
      <div class="mb-3 text-start">
        <label class="form-label auth-label">Email Kampus</label>
        <input type="email" name="email" class="form-control auth-input" placeholder="Contoh: fajar@mhs.unesa.ac.id" value="{{ old('email') }}" required>
      </div>
      <div class="mb-4 text-start">
        <label class="form-label auth-label">Password Baru</label>
        <input type="password" name="password" class="form-control auth-input" placeholder="Min. 8 karakter" required>
      </div>

      <button type="submit" class="btn auth-btn-primary w-100 py-2.5">Daftar Akun Pemilih</button>
    </form>
  </div>
</div>
@endsection
