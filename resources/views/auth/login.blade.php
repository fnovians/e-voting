@extends('layouts.app')

@section('title', 'Masuk - SecVote')

@section('content')
<div class="row justify-content-center my-4">
  <div class="col-md-6 col-lg-5">
    
    <div class="card p-4 shadow-box" id="auth-card">
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

      <!-- LOGIN FORM -->
      <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label text-muted small fw-bold">Nomor Induk Mahasiswa (NIM)</label>
          <input type="text" name="nim" class="form-control" placeholder="Contoh: 120203001" value="{{ old('nim') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted small fw-bold">Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <!-- SQL Injection Mitigation toggle (Scientific core) -->
        <div class="p-3 mb-4 rounded-3 border-glass bg-glass-light">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="mitigated" id="login-mitigation" value="1" checked style="cursor:pointer;">
            <label class="form-check-label fw-bold text-dark small" style="cursor:pointer;" for="login-mitigation">
              Mitigasi SQL Injection (Prepared Statements)
            </label>
          </div>
          <p class="text-muted mb-0 small-text mt-2" style="line-height: 1.35; font-size: 0.72rem;">
            Jika <strong>aktif</strong>, sistem menggunakan Eloquent (aman). Jika <strong>nonaktif</strong>, input digabungkan langsung ke kueri MySQL mentah yang rentan serangan bypass login!
          </p>
        </div>

        <button type="submit" class="btn btn-cyan w-100 py-2.5">Masuk Sistem</button>
      </form>

      <!-- SQL Bypass Analysis display in case of attack simulation -->
      @if (session('sqlQuery'))
        <div class="border-top border-glass mt-4 pt-3 text-start">
          <h5 class="text-danger fw-bold mb-2 small">🔬 Hasil Analisis Kueri SQL:</h5>
          <div class="code-viewer my-2" style="font-size: 0.78rem;">{{ session('sqlQuery') }}</div>
          <div class="alert alert-danger mb-0 small p-3 border-danger-subtle" style="white-space: pre-wrap; background-color: #fef2f2; color: #991b1b;">{{ session('analysis') }}</div>
        </div>
      @endif

      <div class="mt-4 pt-3 border-top border-glass text-start">
        <h6 class="text-dark small fw-bold mb-1">🔬 Akun Pengujian & Riset:</h6>
        <ul class="text-muted small ps-3 mb-0" style="line-height: 1.4; font-size: 0.75rem;">
          <li><strong>Mahasiswa (Voter):</strong> NIM: <code class="monospace-cyan" style="font-size: 0.7rem;">120203001</code> | Pass: <code class="monospace" style="font-size: 0.7rem;">password123</code></li>
          <li><strong>Administrator:</strong> NIM: <code class="monospace-cyan" style="font-size: 0.7rem;">000000000</code> | Pass: <code class="monospace" style="font-size: 0.7rem;">adminpassword</code></li>
        </ul>
      </div>
    </div>
    
  </div>
</div>
@endsection
