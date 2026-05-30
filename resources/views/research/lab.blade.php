@extends('layouts.app')

@section('title', 'Lab Kriptografi & Riset - SecVote')

@section('content')
<h2 class="fw-bold text-bright mb-2">Pusat Analisis & Riset Kriptografi</h2>
<p class="text-muted mb-4 subtitle">
  Panel ini dirancang khusus untuk bahan penelitian artikel ilmiah Anda. Menampilkan kondisi database MySQL real-time dan perbandingan dekripsi data.
</p>

<div class="card p-4 shadow-box border-glass">
  <ul class="nav nav-tabs mb-4" id="db-tabs">
    <li class="nav-item">
      <button class="nav-link active" onclick="switchDbTab(this, 'users')">Tabel Akun Pemilih (users)</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" onclick="switchDbTab(this, 'votes')">Tabel Enkripsi Suara (votes)</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" onclick="switchDbTab(this, 'audit')">Audit Logs Keamanan</button>
    </li>
  </ul>

  <!-- Users DB Tab -->
  <div id="db-tab-users-view" class="db-tab-content">
    <h4 class="text-bright fw-bold mb-1">Tabel Pengguna & Status One Person One Vote</h4>
    <p class="text-muted small mb-3">
      Menyimpan profil mahasiswa beserta <strong>bcrypt password hash</strong> yang aman. Status <code>has_voted</code> mengunci sistem agar pemilih tidak dapat memberikan suara ganda.
    </p>
    <div class="table-responsive">
      <table class="table table-hover border-glass">
        <thead>
          <tr>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Email Terdaftar</th>
            <th>Peran</th>
            <th>Metode Sandi</th>
            <th>Password Hash Bcrypt</th>
            <th>Status Voting</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $u)
            <tr>
              <td><strong class="monospace-cyan">{{ $u['nim'] }}</strong></td>
              <td>{{ $u['name'] }}</td>
              <td>{{ $u['email'] }}</td>
              <td>
                @if ($u['role'] === 'admin')
                  <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(239, 68, 68, 0.1); color: var(--accent-pink); border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 600; font-size: 0.7rem;">
                    {{ $u['role'] }}
                  </span>
                @else
                  <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(100, 116, 139, 0.1); color: var(--text-muted); border: 1px solid rgba(100, 116, 139, 0.2); font-weight: 600; font-size: 0.7rem;">
                    {{ $u['role'] }}
                  </span>
                @endif
              </td>
              <td class="monospace">{{ $u['salt'] }}</td>
              <td class="monospace" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $u['passwordHash'] }}">{{ $u['passwordHash'] }}</td>
              <td>
                @if ($u['hasVoted'])
                  <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); font-weight: 600; font-size: 0.7rem;">
                    Sudah Memilih
                  </span>
                @else
                  <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(245, 158, 11, 0.1); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.2); font-weight: 600; font-size: 0.7rem;">
                    Belum Memilih
                  </span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Votes DB Tab -->
  <div id="db-tab-votes-view" class="db-tab-content" style="display: none;">
    <h4 class="text-purple fw-bold mb-1">Tabel Transparansi Enkripsi AES-256-GCM</h4>
    <p class="text-muted small mb-3">
      Menunjukkan perbandingan antara data <strong>sebelum enkripsi (Plaintext)</strong> dan <strong>sesudah enkripsi (Ciphertext)</strong> yang tersimpan di MySQL. Perhatikan bahwa di tabel database tidak ada kolom NIM/Nama pemilih (Anonymous Voting).
    </p>
    <div class="table-responsive">
      <table class="table table-hover border-glass">
        <thead>
          <tr>
            <th>No</th>
            <th>Plaintext Sebelum Enkripsi (Memori Server)</th>
            <th>Ciphertext Tersimpan (Tabel DB)</th>
            <th>Initialization Vector (IV)</th>
            <th>Authentication Tag (GCM)</th>
            <th>Waktu Masuk (Timestamp)</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($voteComparison as $v)
            <tr>
              <td><strong>{{ $v['index'] }}</strong></td>
              <td class="monospace-cyan fw-bold">{{ $v['plaintext'] }}</td>
              <td class="monospace text-break" style="max-width: 200px;">{{ $v['ciphertext'] }}</td>
              <td class="monospace-cyan">{{ $v['iv'] }}</td>
              <td class="monospace">{{ $v['tag'] ?: 'AES-256-CBC' }}</td>
              <td style="font-size:0.75rem; color:var(--text-muted);">{{ \Carbon\Carbon::parse($v['timestamp'])->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Belum ada suara masuk. Database terenkripsi kosong.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Audit Log Tab -->
  <div id="db-tab-audit-view" class="db-tab-content" style="display: none;">
    <h4 class="text-danger fw-bold mb-1">Audit Log & Aktivitas Keamanan Server</h4>
    <p class="text-muted small mb-3">
      Mencatat setiap percobaan login, kecocokan OTP, log penyerangan SQL Injection, dan penyisipan data ke MySQL sebagai bagian dari audit logs forensik.
    </p>
    <div class="table-responsive">
      <table class="table table-hover border-glass">
        <thead>
          <tr>
            <th>Waktu Log</th>
            <th>Jenis Aktivitas</th>
            <th>IP Address</th>
            <th>Detail Catatan Audit</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($logs as $l)
            @php
              $badgeStyle = 'background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); font-weight: 600;';
              if (str_contains($l->event, 'FAIL') || str_contains($l->event, 'ATTEMPT') || str_contains($l->event, 'ERROR') || str_contains($l->event, 'BLOCKED')) {
                $badgeStyle = 'background: rgba(239, 68, 68, 0.1); color: var(--accent-pink); border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 600;';
              } elseif (str_contains($l->event, 'INIT') || str_contains($l->event, 'TOGGLE') || str_contains($l->event, 'RESET')) {
                $badgeStyle = 'background: rgba(245, 158, 11, 0.1); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.2); font-weight: 600;';
              }
            @endphp
            <tr>
              <td style="font-size:0.75rem; color:var(--text-muted);">{{ \Carbon\Carbon::parse($l->created_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</td>
              <td><span class="badge rounded-pill px-3 py-1.5" style="{{ $badgeStyle }} font-size:0.68rem; display:block; text-align:center;">{{ $l->event }}</span></td>
              <td class="monospace">{{ $l->ip_address }}</td>
              <td style="font-size:0.8rem; font-weight:500;">{{ $l->details }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">Audit log kosong.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Action buttons -->
<div class="d-flex justify-content-end gap-2 mt-4">
  <a href="{{ route('research.lab') }}" class="btn btn-outline-cyan px-4">Refresh Data Lab</a>
  
  <form action="{{ route('research.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin me-reset seluruh database MySQL ke kondisi awal?')" class="m-0">
    @csrf
    <button type="submit" class="btn btn-danger px-4">Reset Database Awal</button>
  </form>
</div>

<script>
  function switchDbTab(btn, tabName) {
    const tabs = document.querySelectorAll('#db-tabs .nav-link');
    tabs.forEach(t => t.classList.remove('active'));
    btn.classList.add('active');

    const tabContents = document.querySelectorAll('.db-tab-content');
    tabContents.forEach(c => c.style.display = 'none');

    document.getElementById(`db-tab-${tabName}-view`).style.display = 'block';
  }
</script>
@endsection
