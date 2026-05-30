@extends('layouts.app')

@section('title', 'Masuk - SecVote')

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
    <h3 class="fw-bold text-dark mb-1 text-center" style="letter-spacing: -0.5px; font-size: 1.75rem;">Welcome Back!</h3>
    <p class="text-muted small text-center mb-4" style="font-weight: 400;">We missed you! Please enter your details.</p>
    
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
      <div class="mb-3">
        <label class="form-label auth-label">NIM (Nomor Induk Mahasiswa)</label>
        <input type="text" name="nim" class="form-control auth-input animate-input" placeholder="Enter your NIM" value="{{ old('nim') }}" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label auth-label">Password</label>
        <div class="password-toggle-wrapper">
          <input type="password" name="password" id="login-password" class="form-control auth-input" placeholder="Enter Password" required>
          <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('login-password', this)" title="Show/Hide Password">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off" viewBox="0 0 24 24" id="eye-icon">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
              <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
          </button>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="rememberMe" style="cursor: pointer; border-color: rgba(15,23,42,0.18);">
          <label class="form-check-label text-muted small" for="rememberMe" style="cursor: pointer; font-weight: 500;">
            Remember me
          </label>
        </div>
        <a href="#" class="small text-decoration-none fw-bold" style="color: #5D6CF0;">Forgot password?</a>
      </div>

      <button type="submit" class="btn auth-btn-primary w-100 py-2.5 mb-3">Sign in</button>
      
      <button type="button" class="btn auth-btn-google w-100 py-2.5 mb-4" onclick="alert('Login SSO Kampus sedang diintegrasikan!')">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48" style="flex-shrink: 0;">
          <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
          <path fill="#4285F4" d="M46.5 24c0-1.61-.15-3.16-.42-4.66H24v8.8h12.7c-.55 2.87-2.17 5.3-4.61 6.94l7.19 5.57c4.21-3.88 6.62-9.59 6.62-16.65z"/>
          <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.98-6.19z"/>
          <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.19-5.57c-2.02 1.35-4.61 2.16-7.31 2.16-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        </svg>
        Sign in with Google
      </button>
    </form>
    
    <div class="text-center small text-muted">
      Don't have an account? <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #5D6CF0;">Sign up</a>
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
