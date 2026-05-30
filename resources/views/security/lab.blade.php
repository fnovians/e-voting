@extends('layouts.app')

@section('title', 'Lab Injeksi SQL - SecVote')

@section('content')
<h2 class="fw-bold text-bright mb-2">Laboratorium Pengujian Keamanan: SQL Injection</h2>
<p class="text-muted mb-4 subtitle">
  Uji coba ketangguhan sistem e-voting ini terhadap serangan pembobolan autentikasi login (SQL Injection) pada database MySQL/MariaDB secara langsung.
</p>

<div class="row g-4">
  
  @php
    $testResults = session('sql_test_results');
    $isVulnSubmit = $testResults && !$testResults['mitigated'];
    $isSafeSubmit = $testResults && $testResults['mitigated'];
  @endphp

  <!-- VULNERABLE PANEL (LEFT) -->
  <div class="col-md-6">
    <div class="card p-4 border-danger bg-glass-danger h-100 shadow-box">
      <span class="badge align-self-start mb-2 px-3 py-1.5" style="background: rgba(239, 68, 68, 0.1) !important; color: var(--accent-pink) !important; border: 1px solid rgba(239, 68, 68, 0.2) !important; font-weight: 600;">Kondisi Rentan (Sebelum Mitigasi)</span>
      <h4 class="text-danger fw-bold mb-1">Formulir Celah Injeksi</h4>
      <p class="text-muted small mb-4">
        Sistem menyusun query SQL dengan menggabungkan string mentah (*string concatenation*). Ketikkan payload SQL Injection pada kolom NIM untuk mem-bypass autentikasi MySQL!
      </p>

      <form action="{{ route('security.test') }}" method="POST">
        @csrf
        <input type="hidden" name="mitigated" value="0">
        
        <div class="mb-3">
          <label class="form-label text-muted">Input NIM (Payload Serangan)</label>
          <input type="text" name="nim" class="form-control text-monospace border-danger-subtle" 
                 placeholder="120203001" 
                 value="{{ $isVulnSubmit ? old('nim') : "' OR '1'='1" }}" required style="font-family: monospace;">
        </div>
        
        <div class="mb-3">
          <label class="form-label text-muted">Password</label>
          <input type="text" name="password" class="form-control border-danger-subtle" 
                 placeholder="Bebas" 
                 value="{{ $isVulnSubmit ? old('password') : 'sembarang' }}">
        </div>
        
        <button type="submit" class="btn btn-danger w-100 py-2">Luncurkan Serangan (Vulnerable)</button>
      </form>

      @if ($isVulnSubmit)
        <div class="border-top border-danger mt-4 pt-3 text-start">
          <h5 class="text-danger fw-bold mb-2 small">🔬 Hasil Serangan di MySQL:</h5>
          <div class="code-viewer my-2">{{ $testResults['sqlQuery'] }}</div>
          <div class="alert {{ $testResults['success'] ? 'alert-danger' : 'alert-info' }} mb-0 small p-3" style="white-space: pre-wrap;">{{ $testResults['analysis'] }}</div>
        </div>
      @else
        <div class="code-viewer my-3" id="lab-vuln-sql-view">SELECT * FROM users WHERE nim = '' AND role = 'voter';</div>
      @endif
    </div>
  </div>

  <!-- MITIGATED PANEL (RIGHT) -->
  <div class="col-md-6">
    <div class="card p-4 border-cyan bg-glass-light h-100 shadow-box">
      <span class="badge align-self-start mb-2 px-3 py-1.5" style="background: rgba(37, 99, 235, 0.1) !important; color: var(--accent-blue) !important; border: 1px solid rgba(37, 99, 235, 0.2) !important; font-weight: 600;">Kondisi Aman (Sesudah Mitigasi)</span>
      <h4 class="text-cyan fw-bold mb-1">Formulir Aman Terproteksi</h4>
      <p class="text-muted small mb-4">
        Sistem menggunakan parameterisasi kueri (*Prepared Statements*). Payload SQL Injection di bawah ini akan diperlakukan sebagai string literal biasa oleh MySQL dan ditolak secara aman.
      </p>

      <form action="{{ route('security.test') }}" method="POST">
        @csrf
        <input type="hidden" name="mitigated" value="1">
        
        <div class="mb-3">
          <label class="form-label text-muted">Input NIM (Payload Serangan)</label>
          <input type="text" name="nim" class="form-control text-monospace border-cyan-subtle" 
                 placeholder="120203001" 
                 value="{{ $isSafeSubmit ? old('nim') : "' OR '1'='1" }}" required style="font-family: monospace;">
        </div>
        
        <div class="mb-3">
          <label class="form-label text-muted">Password</label>
          <input type="text" name="password" class="form-control border-cyan-subtle" 
                 placeholder="Bebas" 
                 value="{{ $isSafeSubmit ? old('password') : 'sembarang' }}">
        </div>
        
        <button type="submit" class="btn btn-cyan w-100 py-2">Uji Keamanan (Mitigated)</button>
      </form>

      @if ($isSafeSubmit)
        <div class="border-top border-cyan mt-4 pt-3 text-start">
          <h5 class="text-cyan fw-bold mb-2 small">🔬 Hasil Analisis Keamanan:</h5>
          <div class="code-viewer my-2">{{ $testResults['sqlQuery'] }}</div>
          <div class="alert {{ $testResults['success'] ? 'alert-success' : 'alert-info' }} mb-0 small p-3" style="white-space: pre-wrap;">{{ $testResults['analysis'] }}</div>
        </div>
      @else
        <div class="code-viewer my-3" id="lab-safe-sql-view">SELECT * FROM users WHERE nim = ? AND passwordHash = ?;</div>
      @endif
    </div>
  </div>

</div>
@endsection
