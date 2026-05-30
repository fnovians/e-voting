@extends('layouts.app')

@section('title', 'Verifikasi OTP - SecVote')

@section('content')
<div class="auth-card-wrapper" style="max-width: 460px; margin: auto;">
  <!-- Centered brand logo -->
  <div class="text-center mb-4">
    <a class="logo text-decoration-none text-white fs-2 d-inline-flex align-items-center justify-content-center" href="/" style="background: none; -webkit-background-clip: unset; -webkit-text-fill-color: #FFFFFF; color: #FFFFFF; font-weight: 800; text-shadow: 0 2px 4px rgba(15, 23, 42, 0.15);">
      @if(file_exists(public_path('assets/img/logo.svg')))
        <img src="{{ asset('assets/img/logo.svg') }}" alt="SecVote Logo" style="height: 85px; object-fit: contain;">
      @elseif(file_exists(public_path('assets/img/logo.png')))
        <img src="{{ asset('assets/img/logo.png') }}" alt="SecVote Logo" style="height: 85px; object-fit: contain;">
      @elseif(file_exists(public_path('assets/img/logo.jpg')))
        <img src="{{ asset('assets/img/logo.jpg') }}" alt="SecVote Logo" style="height: 85px; object-fit: contain;">
      @elseif(file_exists(public_path('assets/img/logo.jpeg')))
        <img src="{{ asset('assets/img/logo.jpeg') }}" alt="SecVote Logo" style="height: 85px; object-fit: contain;">
      @else
        🗳️ Sec<span>Vote</span>
      @endif
    </a>
  </div>

  <div class="card auth-card">
    <div class="candidate-avatar mx-auto mb-4" style="background: rgba(93, 108, 240, 0.08); color: #5D6CF0; width: 64px; height: 64px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">🔑</div>
    <h3 class="fw-bold text-dark mb-1 text-center" style="letter-spacing: -0.5px; font-size: 1.75rem;">Verifikasi OTP</h3>
    <p class="text-muted small text-center mb-4" style="font-weight: 400; line-height: 1.45;">
      Untuk menjaga integritas data pemilihan, masukkan 6 digit kode OTP yang telah dikirimkan ke email terdaftar Anda.
    </p>

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

    <!-- OTP FORM -->
    <form action="/verify-otp" method="POST" id="otp-form" onsubmit="return compileOtp()">
      @csrf
      <div class="otp-container d-flex justify-content-center gap-2 mb-4">
        <input type="text" class="otp-input animate-input" maxlength="1" id="otp1" oninput="moveOtpFocus(this, 'otp2', null)" required>
        <input type="text" class="otp-input animate-input" maxlength="1" id="otp2" oninput="moveOtpFocus(this, 'otp3', 'otp1')" required>
        <input type="text" class="otp-input" maxlength="1" id="otp3" oninput="moveOtpFocus(this, 'otp4', 'otp2')" required>
        <input type="text" class="otp-input" maxlength="1" id="otp4" oninput="moveOtpFocus(this, 'otp5', 'otp3')" required>
        <input type="text" class="otp-input" maxlength="1" id="otp5" oninput="moveOtpFocus(this, 'otp6', 'otp4')" required>
        <input type="text" class="otp-input" maxlength="1" id="otp6" oninput="moveOtpFocus(this, null, 'otp5')" required>
      </div>
      
      <input type="hidden" name="otp" id="hidden-otp">
      <button type="submit" class="btn auth-btn-primary w-100 py-2.5">Verifikasi & Masuk</button>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach(input => {
      input.addEventListener('input', () => {
        input.value = input.value.replace(/[^0-9]/g, '');
      });
    });
    const firstInput = document.getElementById('otp1');
    if (firstInput) firstInput.focus();
  });

  function moveOtpFocus(current, nextId, prevId) {
    if (current.value.length >= 1 && nextId) {
      document.getElementById(nextId).focus();
    }
    
    current.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && current.value.length === 0 && prevId) {
        document.getElementById(prevId).focus();
      }
    });
  }

  function compileOtp() {
    let code = '';
    for (let i = 1; i <= 6; i++) {
      code += document.getElementById(`otp${i}`).value;
    }
    if (code.length < 6) {
      alert('Masukkan kode OTP lengkap (6 digit).');
      return false;
    }
    document.getElementById('hidden-otp').value = code;
    return true;
  }
</script>
@endsection
