@extends('layouts.app')

@section('title', 'Registrasi Pemilih - SecVote')

@section('content')
<!-- Absolute Home page link -->
<a href="/" class="text-white text-decoration-none small position-absolute top-0 start-0 m-4 d-flex align-items-center gap-1.5" style="z-index: 10;">
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
  </svg>
  Home page
</a>

<div class="auth-card-wrapper" style="max-width: 440px;">
  <!-- Centered brand logo -->
  <div class="text-center mb-4">
    <a class="logo text-decoration-none text-white fs-3" href="/" style="background: none; -webkit-background-clip: unset; -webkit-text-fill-color: #FFFFFF; color: #FFFFFF; font-weight: 800; text-shadow: 0 2px 4px rgba(15, 23, 42, 0.1);">
      🗳️ Sec<span>Vote</span>
    </a>
  </div>

  <div class="card auth-card">
    <h3 class="fw-bold text-dark mb-1 text-center" style="letter-spacing: -0.5px; font-size: 1.75rem;">Create Account</h3>
    <p class="text-muted small text-center mb-4" style="font-weight: 400;">Register a new voter profile for election</p>

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
      <div class="mb-3">
        <label class="form-label auth-label">NIM (Nomor Induk Mahasiswa)</label>
        <input type="text" name="nim" class="form-control auth-input" placeholder="Enter your NIM" value="{{ old('nim') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label auth-label">Nama Lengkap</label>
        <input type="text" name="name" class="form-control auth-input" placeholder="Enter your Full Name" value="{{ old('name') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label auth-label">Email Kampus</label>
        <input type="email" name="email" class="form-control auth-input" placeholder="Enter your Campus Email" value="{{ old('email') }}" required>
      </div>
      
      <div class="mb-4">
        <label class="form-label auth-label">Password Baru</label>
        <div class="password-toggle-wrapper">
          <input type="password" name="password" id="register-password" class="form-control auth-input" placeholder="Enter a Password" required>
          <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('register-password', this)" title="Show/Hide Password">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off" viewBox="0 0 24 24" id="eye-icon">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
              <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn auth-btn-primary w-100 py-2.5 mb-4">Sign up</button>
    </form>
    
    <div class="text-center small text-muted">
      Already have an account? <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #5D6CF0;">Sign in</a>
    </div>
  </div>
</div>

<script>
  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const svg = btn.querySelector('svg');
    if (input.type === 'password') {
      input.type = 'text';
      // Open eye icon
      svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    } else {
      input.type = 'password';
      // Closed eye icon
      svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    }
  }
</script>
@endsection
