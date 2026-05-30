@extends('layouts.app')

@section('title', 'Verifikasi OTP - SecVote')

@section('content')
<div class="row justify-content-center my-5">
  <div class="col-md-6 col-lg-5">
    <div class="card p-4 text-center shadow-box">
      <div class="candidate-avatar mx-auto mb-4 bg-grad-purple" style="background: rgba(79, 70, 229, 0.08); color: var(--accent-purple);">🔑</div>
      <h2 class="fw-bold text-dark" style="letter-spacing: -0.5px;">Verifikasi OTP</h2>
      <p class="text-muted small mb-4">
        Untuk menjaga integritas data pemilihan, masukkan 6 digit kode OTP yang telah dikirimkan ke email terdaftar Anda.
      </p>

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
        <button type="submit" class="btn btn-cyan w-100 py-2.5">Verifikasi & Masuk</button>
      </form>

      <p class="text-muted small mt-4 mb-0" style="font-size: 0.78rem;">
        Gunakan widget inbox simulasi di pojok kanan bawah untuk menyalin kode OTP tanpa SMTP server.
      </p>
    </div>
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
    document.getElementById('otp1').focus();
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
