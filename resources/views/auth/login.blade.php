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

        <button type="submit" class="btn btn-cyan w-100 py-2.5">Masuk Sistem</button>
      </form>
    </div>
    
  </div>
</div>
@endsection
