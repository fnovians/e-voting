@extends('layouts.app')

@section('title', 'Registrasi Pemilih - SecVote')

@section('content')
<div class="row justify-content-center my-4">
  <div class="col-md-6 col-lg-5">
    
    <div class="card p-4 shadow-box" id="auth-card">
      <div class="text-center mb-4">
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

      <!-- REGISTER FORM -->
      <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label text-muted small fw-bold">Nomor Induk Mahasiswa (NIM)</label>
          <input type="text" name="nim" class="form-control" placeholder="Contoh: 120203006" value="{{ old('nim') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Fajar Pratama" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted small fw-bold">Email Kampus</label>
          <input type="email" name="email" class="form-control" placeholder="Contoh: fajar@mahasiswa.univ.ac.id" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3 mb-4">
          <label class="form-label text-muted small fw-bold">Password Baru</label>
          <input type="password" name="password" class="form-control" placeholder="Min. 8 karakter" required>
        </div>

        <button type="submit" class="btn btn-purple w-100 py-2.5">Daftar Akun Pemilih</button>
      </form>
    </div>
    
  </div>
</div>
@endsection
