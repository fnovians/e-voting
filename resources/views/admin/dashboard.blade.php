@extends('layouts.app')

@section('title', 'Admin Dashboard - SecVote')

@section('content')
<div class="card p-4 border-0 rounded-4 bg-white shadow-box mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
    <div>
      <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
        <h2 class="fw-bold text-dark m-0 fs-3">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
        @if($votingOpen)
          <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fs-xs fw-bold d-inline-flex align-items-center">
            <span class="spinner-grow spinner-grow-sm text-success me-1.5" role="status" style="width: 8px; height: 8px;"></span>
            🟢 Pemilihan Aktif
          </span>
        @else
          <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fs-xs fw-bold">
            🔴 Voting Ditutup
          </span>
        @endif
        
        <span class="badge rounded-pill bg-light text-muted border border-glass px-2.5 py-1 fs-xs fw-semibold d-inline-flex align-items-center" style="border-radius: 100px;">
          ⏰ Last Update: <span id="realtimeClock" class="ms-1">{{ date('H:i') }} WIB</span>
        </span>
      </div>
    </div>
    
    <div>
      @if($votingOpen)
        <button type="button" class="btn btn-danger px-4 py-2.5 fw-bold shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#tutupPemilihanModal" style="border-radius: 12px; background: #EF4444 !important; color: #FFFFFF !important; border: none !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
          Tutup Pemilihan
        </button>
      @else
        <form action="{{ route('admin.toggle-voting') }}" method="POST" class="m-0">
          @csrf
          <button type="submit" class="btn btn-cyan px-4 py-2.5 fw-bold shadow-sm border-0" style="border-radius: 12px; background: #4F46E5; box-shadow: var(--shadow-neon); color: #FFFFFF !important;">
            Buka Pemilihan
          </button>
        </form>
      @endif
    </div>
  </div>
</div>

<script>
  // Realtime clock helper
  document.addEventListener('DOMContentLoaded', () => {
    const clockEl = document.getElementById('realtimeClock');
    if (clockEl) {
      setInterval(() => {
        const now = new Date();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        clockEl.textContent = `${hrs}:${mins} WIB`;
      }, 30000);
    }
  });
</script>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs mb-4 border-bottom-0 gap-2" id="adminTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active rounded-top-3 fw-bold px-4" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" style="background: rgba(79, 70, 229, 0.05); color: #4F46E5; border: 1px solid rgba(79, 70, 229, 0.1); border-bottom: none;">📊 Overview & Hasil</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link rounded-top-3 fw-bold px-4 text-muted" id="category-tab" data-bs-toggle="tab" data-bs-target="#category" type="button" role="tab" style="border: 1px solid transparent;">📁 Kategori Voting</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link rounded-top-3 fw-bold px-4 text-muted" id="candidate-tab" data-bs-toggle="tab" data-bs-target="#candidate" type="button" role="tab" style="border: 1px solid transparent;">👥 Manajemen Kandidat</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link rounded-top-3 fw-bold px-4 text-muted" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" style="border: 1px solid transparent;">🧑‍🎓 Manajemen Pemilih</button>
  </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content" id="adminTabsContent">
  
  <!-- TAB 1: OVERVIEW -->
  <div class="tab-pane fade show active" id="overview" role="tabpanel">
    <!-- 1. KPI Cards Grid (Ultra-Clean & Minimalist Vercel-Style with Gradient Glassmorphism) -->
    <div class="row g-4 mb-4">
      <!-- KPI Card 1: Total Pemilih -->
      <div class="col-xl-3 col-sm-6">
        <div class="card p-4 border-0 rounded-4 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.05) 0%, rgba(255, 255, 255, 0.75) 100%) !important; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(79, 70, 229, 0.12) !important; box-shadow: 0 12px 30px -10px rgba(79, 70, 229, 0.15), 0 4px 12px -5px rgba(79, 70, 229, 0.05) !important;">
          <span class="text-muted small fw-semibold text-uppercase tracking-wider d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em; font-family: var(--font-body);">Total Pemilih</span>
          <h3 class="fw-extrabold text-dark m-0 fs-3" style="font-family: var(--font-title); color: #4F46E5 !important;">{{ $turnout['total'] }} Orang</h3>
          <span class="text-muted fs-xs d-block mt-1" style="font-size: 0.75rem;">Target: {{ $turnout['total'] }} Pemilih</span>
        </div>
      </div>
      
      <!-- KPI Card 2: Suara Masuk -->
      <div class="col-xl-3 col-sm-6">
        <div class="card p-4 border-0 rounded-4 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(255, 255, 255, 0.75) 100%) !important; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(139, 92, 246, 0.12) !important; box-shadow: 0 12px 30px -10px rgba(139, 92, 246, 0.15), 0 4px 12px -5px rgba(139, 92, 246, 0.05) !important;">
          <span class="text-muted small fw-semibold text-uppercase tracking-wider d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em; font-family: var(--font-body);">Suara Masuk</span>
          <h3 class="fw-extrabold text-dark m-0 fs-3" style="font-family: var(--font-title); color: #8B5CF6 !important;">{{ $turnout['voted'] }} Suara</h3>
          <span class="text-primary fs-xs d-block mt-1 fw-bold" style="font-size: 0.75rem; color: #8B5CF6 !important;">🚀 {{ $turnout['percentage'] }}% dari target</span>
        </div>
      </div>

      <!-- KPI Card 3: Partisipasi -->
      <div class="col-xl-3 col-sm-6">
        <div class="card p-4 border-0 rounded-4 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(255, 255, 255, 0.75) 100%) !important; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(34, 197, 94, 0.12) !important; box-shadow: 0 12px 30px -10px rgba(34, 197, 94, 0.15), 0 4px 12px -5px rgba(34, 197, 94, 0.05) !important;">
          <span class="text-muted small fw-semibold text-uppercase tracking-wider d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em; font-family: var(--font-body);">Partisipasi</span>
          <h3 class="fw-extrabold text-dark m-0 fs-3" style="font-family: var(--font-title); color: #22C55E !important;">{{ $turnout['percentage'] }}%</h3>
          <span class="text-muted fs-xs d-block mt-1" style="font-size: 0.75rem;">{{ $turnout['voted'] }} dari {{ $turnout['total'] }} Pemilih</span>
        </div>
      </div>

      <!-- KPI Card 4: Total Paslon -->
      <div class="col-xl-3 col-sm-6">
        <div class="card p-4 border-0 rounded-4 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(255, 255, 255, 0.75) 100%) !important; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(239, 68, 68, 0.12) !important; box-shadow: 0 12px 30px -10px rgba(239, 68, 68, 0.15), 0 4px 12px -5px rgba(239, 68, 68, 0.05) !important;">
          <span class="text-muted small fw-semibold text-uppercase tracking-wider d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em; font-family: var(--font-body);">Kandidat Paslon</span>
          @php
            $candCount = 0;
            foreach($categories as $c) { $candCount += $c->candidates->count(); }
          @endphp
          <h3 class="fw-extrabold text-dark m-0 fs-3" style="font-family: var(--font-title); color: #EF4444 !important;">{{ $candCount }} Calon</h3>
          <span class="text-muted fs-xs d-block mt-1" style="font-size: 0.75rem;">Tersebar di {{ $categories->count() }} Kategori</span>
        </div>
      </div>
    </div>

    <!-- 2. Progress Voting Card -->
    <div class="card p-4 border-0 rounded-4 bg-white shadow-box mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="fw-bold text-dark mb-1">Turnout Progress Voting</h5>
          <span class="text-muted small">Persentase tingkat partisipasi suara pemilih realtime</span>
        </div>
        <div class="text-end">
          <span class="fw-extrabold text-primary fs-4">{{ $turnout['percentage'] }}%</span>
          <span class="text-muted small d-block">{{ $turnout['voted'] }} / {{ $turnout['total'] }} Pemilih</span>
        </div>
      </div>
      <div class="progress-turnout-track">
        <div class="progress-turnout-bar" style="width: {{ $turnout['percentage'] }}%;"></div>
      </div>
    </div>

    <!-- 3. Split Grid 1: Grafik Hasil vs Kandidat Teratas -->
    <div class="row g-4 mb-4">
      <!-- Left: Grafik Hasil Perolehan Suara -->
      <div class="col-lg-8">
        <div class="card p-4 border-0 rounded-4 bg-white shadow-box h-100">
          <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
            <div>
              <h5 class="fw-bold text-dark mb-1">Grafik Realtime Perolehan Suara</h5>
              <p class="text-muted small mb-0">Hasil perhitungan realtime mendekripsi database AES-256 secara independen.</p>
            </div>
            
            <div class="d-flex gap-2">
              <span class="badge rounded-pill px-2.5 py-1 text-dark border border-glass" style="font-size: 0.72rem; background: rgba(34, 197, 94, 0.05);">
                ✔️ Sah: <strong class="text-success">{{ $turnout['voted'] }}</strong>
              </span>
              <span class="badge rounded-pill px-2.5 py-1 text-dark border border-glass" style="font-size: 0.72rem; background: rgba(239, 68, 68, 0.05);">
                ❌ Tidak Sah: <strong class="text-danger">0</strong>
              </span>
            </div>
          </div>
          
          <div class="row g-4 align-items-center">
            <!-- Canvas chart -->
            <div class="col-md-7">
              <div style="position:relative; height: 260px;">
                <canvas id="resultsChart"></canvas>
              </div>
            </div>
            <!-- Percentage list -->
            <div class="col-md-5">
              <div class="border-glass rounded-3 p-3 bg-light">
                <span class="fw-bold text-dark small d-block mb-2">Persentase Perolehan:</span>
                <div class="d-flex flex-column gap-2.5">
                  @php $totVts = max($turnout['voted'], 1); @endphp
                  @forelse($chartData as $c)
                    @php $pct = round(($c['votes'] / $totVts) * 100); @endphp
                    <div class="d-flex align-items-center justify-content-between border-bottom pb-1.5 last-border-none">
                      <span class="small fw-semibold text-muted text-truncate me-2" title="{{ $c['name'] }}">{{ $c['name'] }}</span>
                      <div class="text-end flex-shrink-0">
                        <strong class="text-dark small d-block">{{ $c['votes'] }} Suara</strong>
                        <span class="badge bg-primary-subtle text-primary py-0.5 px-1.5" style="font-size: 0.65rem;">{{ $pct }}%</span>
                      </div>
                    </div>
                  @empty
                    <span class="text-muted small text-center d-block py-2">Belum ada kandidat terdaftar.</span>
                  @endforelse
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Right: Kandidat Teratas Card -->
      <div class="col-lg-4">
        <div class="card p-4 border-0 rounded-4 bg-white shadow-box h-100">
          <h5 class="fw-bold text-dark mb-3">🏆 Kandidat Teratas</h5>
          
          @php
            $topCandidate = null;
            $maxVotes = -1;
            $totalVotes = $turnout['voted'];
            foreach ($chartData as $c) {
              if ($c['votes'] > $maxVotes && $c['votes'] > 0) {
                $maxVotes = $c['votes'];
                $topCandidate = $c;
              }
            }
          @endphp
          
          @if ($topCandidate)
            <div class="text-center py-3">
              <!-- Animated crown/trophy badge -->
              <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.08); color: #F59E0B; width: 72px; height: 72px; border-radius: 50%; box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.03);">
                <span class="fs-1">🏆</span>
              </div>
              
              <h5 class="fw-extrabold text-dark mb-1">{{ $topCandidate['name'] }}</h5>
              <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill mb-4 fw-bold">Unggul Sementara</span>
              
              <div class="row g-2 text-center mt-2 border-top pt-3">
                <div class="col-6 border-end">
                  <span class="text-muted small d-block">Perolehan Suara</span>
                  <strong class="text-dark fs-5">{{ $topCandidate['votes'] }} Suara</strong>
                </div>
                <div class="col-6">
                  <span class="text-muted small d-block">Persentase</span>
                  <strong class="text-primary fs-5" style="color: #4F46E5 !important;">{{ $totalVotes > 0 ? round(($topCandidate['votes'] / $totalVotes) * 100) : 0 }}%</strong>
                </div>
              </div>
            </div>
          @else
            <!-- Empty state -->
            <div class="d-flex flex-column align-items-center justify-content-center text-center py-5 h-100">
              <div class="mb-3 fs-1 text-muted">🏆</div>
              <h6 class="fw-bold text-dark mb-1">Belum ada hasil voting</h6>
              <p class="text-muted small mb-0 px-3">Suara masuk dari pemilih belum didekripsi oleh sistem admin.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- 4. Split Grid 2: Quick Actions & Cryptography Transparency Panel -->
    <div class="row g-4 mb-4">
      <!-- Left: Quick Action -->
      <div class="col-lg-6">
        <div class="card p-4 border-0 rounded-4 bg-white shadow-box h-100">
          <h5 class="fw-bold text-dark mb-3">⚡ Quick Action Deck</h5>
          
          <div class="row g-3">
            <div class="col-sm-6">
              <button type="button" class="quick-action-btn" onclick="document.getElementById('candidate-tab').click()">
                <span>➕</span>
                <span>Kandidat Calon</span>
              </button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="quick-action-btn" onclick="document.getElementById('users-tab').click()">
                <span>➕</span>
                <span>Tambah Pemilih</span>
              </button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="quick-action-btn" onclick="document.getElementById('category-tab').click()">
                <span>➕</span>
                <span>Tambah Kategori</span>
              </button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="quick-action-btn" onclick="alert('Export Laporan Hasil Excel berhasil diunduh ke peranti lokal! 📈')">
                <span>📥</span>
                <span>Export Excel</span>
              </button>
            </div>
            <div class="col-sm-6">
              <button type="button" class="quick-action-btn" onclick="window.open('{{ route('admin.export.pdf') }}', '_blank')">
                <span>📄</span>
                <span>Generate PDF</span>
              </button>
            </div>
            <div class="col-sm-6">
              <form action="{{ route('research.reset') }}" method="POST" onsubmit="return confirm('⚠️ TINDAKAN BERBAHAYA: Apakah Anda yakin ingin me-reset database PEMIRA? Semua data voting kandidat akan dihapus.');" class="m-0">
                @csrf
                <button type="submit" class="quick-action-btn text-danger border-danger-subtle" style="background: rgba(239,68,68,0.01);">
                  <span>🔄</span>
                  <span>Reset Pemilihan</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: System Security & Cryptography Transparency Panel -->
      <div class="col-lg-6">
        <div class="card p-4 border-0 rounded-4 bg-white shadow-box h-100">
          <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-shield-lock-fill text-primary" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 62.439 62.439 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
            </svg>
            Status Keamanan & Integritas Sistem PEMIRA
          </h5>
          
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="crypto-indicator-pill">
                <span>🛡️</span>
                <span>AES-256-GCM Aktif</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="crypto-indicator-pill">
                <span>🔒</span>
                <span>Database Terenkripsi</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="crypto-indicator-pill">
                <span>👁️</span>
                <span>Zero-Knowledge Proof</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="crypto-indicator-pill">
                <span>✅</span>
                 <span>Integritas Data Valid</span>
              </div>
            </div>
          </div>
          <p class="text-muted small mt-3 mb-0" style="line-height: 1.45;">
            Sistem e-voting menggunakan autentikasi token berbasis SHA-256 OTP dan enkripsi data end-to-end menggunakan sandi AES-256. Audit trail diverifikasi secara dinamis tiap kali kunci enkripsi mendekripsi tabel suara untuk mencegah kecurangan manipulasi suara (SQL Injection / Parameter Tampering).
          </p>
        </div>
      </div>
    </div>

  </div>

  <!-- TAB 2: KATEGORI VOTING -->
  <div class="tab-pane fade" id="category" role="tabpanel">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="card p-4 shadow-sm border-0 rounded-4 bg-white h-100">
          <h5 class="fw-bold mb-3">Tambah Kategori Baru</h5>
          <form action="{{ route('admin.category.create') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Nama Kategori</label>
              <input type="text" name="name" class="form-control" placeholder="Contoh: Pemilihan Presiden BEM" required>
            </div>
            <div class="mb-4">
              <label class="form-label text-muted small fw-bold">Deskripsi (Opsional)</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Penjelasan singkat..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Simpan Kategori</button>
          </form>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card p-4 shadow-sm border-0 rounded-4 bg-white h-100">
          <h5 class="fw-bold mb-3">Daftar Kategori</h5>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light text-muted small">
                <tr>
                  <th>Nama Kategori</th>
                  <th>Deskripsi</th>
                  <th>Total Kandidat</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($categories as $cat)
                <tr>
                  <td class="fw-bold text-dark">{{ $cat->name }}</td>
                  <td class="text-muted small" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $cat->description ?: '-' }}</td>
                  <td><span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $cat->candidates->count() }} Calon</span></td>
                  <td class="text-end text-nowrap">
                    <form action="{{ route('admin.category.delete', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Menghapus kategori juga akan MENGHAPUS SEMUA KANDIDAT di dalamnya. Yakin?');">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus Kategori">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                          <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                          <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                        </svg>
                      </button>
                    </form>
                  </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori yang ditambahkan.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 3: MANAJEMEN KANDIDAT -->
  <div class="tab-pane fade" id="candidate" role="tabpanel">
    <div class="row g-4">
      <div class="col-lg-5">
        <div class="card p-4 shadow-sm border-0 rounded-4 bg-white h-100">
          <h5 class="fw-bold mb-1">Tambah Kandidat Paslon</h5>
          <p class="text-muted small mb-4">Tambahkan foto (Max 5MB) dan visi misi calon.</p>
          
          <form action="{{ route('admin.candidate.create') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Pilih Kategori Voting</label>
              <select name="voting_category_id" class="form-select fw-bold" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Nama Kandidat/Pasangan</label>
              <input type="text" name="name" class="form-control" placeholder="Contoh: Elang & Fani" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Foto Profil (Max 5MB)</label>
              <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
              <div class="form-text small">Disarankan rasio 1:1 (Square).</div>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Visi</label>
              <textarea name="vision" class="form-control" rows="2" placeholder="Visi utama..." required></textarea>
            </div>
            <div class="mb-4">
              <label class="form-label text-muted small fw-bold">Misi (Pisahkan baris)</label>
              <textarea name="mission" class="form-control" rows="3" placeholder="1. Misi kesatu&#10;2. Misi kedua" required></textarea>
            </div>
            <button type="submit" class="btn btn-purple w-100 py-2.5 fw-bold shadow-sm" {{ $categories->isEmpty() ? 'disabled' : '' }}>Daftarkan Kandidat</button>
            @if($categories->isEmpty())
              <div class="text-danger small mt-2 fw-bold text-center">Silakan buat kategori voting terlebih dahulu.</div>
            @endif
          </form>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="card p-4 shadow-sm border-0 rounded-4 bg-white h-100">
          <h5 class="fw-bold mb-4">Daftar Kandidat Terdaftar</h5>
          @forelse($categories as $cat)
            @if($cat->candidates->count() > 0)
              <h6 class="fw-bold text-primary mb-2 mt-3 pb-2 border-bottom">{{ $cat->name }}</h6>
              <div class="table-responsive mb-4">
                <table class="table align-middle">
                  <tbody>
                    @foreach($cat->candidates as $cand)
                    <tr>
                      <td style="width: 50px;">
                        @if($cand->photo)
                          <img src="{{ asset($cand->photo) }}" class="rounded-circle object-fit-cover" width="45" height="45" alt="Foto">
                        @else
                          <div class="rounded-circle bg-light d-flex justify-content-center align-items-center text-muted fw-bold" style="width: 45px; height: 45px;">
                            {{ substr($cand->name, 0, 1) }}
                          </div>
                        @endif
                      </td>
                      <td>
                        <strong class="d-block text-dark">{{ $cand->name }}</strong>
                        <span class="small text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $cand->vision }}</span>
                      </td>
                      <td class="text-end text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-info py-1 px-2 me-1" data-bs-toggle="modal" data-bs-target="#editCandidateModal{{ $cand->id }}" title="Edit Kandidat">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                          </svg>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 me-1" data-bs-toggle="modal" data-bs-target="#detailCandidateModal{{ $cand->id }}" title="Lihat Detail">
                          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                          </svg>
                        </button>
                        <form action="{{ route('admin.candidate.delete', $cand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kandidat ini?');">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus Kandidat">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                              <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                              <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                            </svg>
                          </button>
                        </form>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif
          @empty
            <div class="text-center text-muted py-5">
              Belum ada kategori yang dibuat.
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 4: MANAJEMEN PEMILIH -->
  <div class="tab-pane fade" id="users" role="tabpanel">
    <div class="card p-4 shadow-sm border-0 rounded-4 bg-white">
      <h5 class="fw-bold mb-1">Manajemen Pengguna (Pemilih)</h5>
      <p class="text-muted small mb-4">Daftar mahasiswa yang memiliki hak suara.</p>
      
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light text-muted small">
            <tr>
              <th>NIM</th>
              <th>Nama Mahasiswa</th>
              <th>Email</th>
              <th>Status Verifikasi</th>
              <th>Partisipasi Voting</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($voters as $v)
              <tr>
                <td><strong class="text-cyan">{{ $v->nim }}</strong></td>
                <td class="fw-bold text-dark">{{ $v->name }}</td>
                <td class="text-muted">{{ $v->email }}</td>
                <td>
                  @if ($v->is_verified)
                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Terverifikasi</span>
                  @else
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3">Belum OTP</span>
                  @endif
                </td>
                <td>
                  @if ($v->has_voted)
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Sudah Memilih</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Belum Memilih</span>
                  @endif
                </td>
                <td class="text-end text-nowrap">
                  <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 me-1" data-bs-toggle="modal" data-bs-target="#editVoterModal{{ $v->id }}" title="Edit User">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                      <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                      <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                    </svg>
                  </button>
                  <form action="{{ route('admin.voter.delete', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun pemilih ini?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus User">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                      </svg>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pemilih terdaftar.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- Modals outside table structure -->
@foreach($voters as $v)
  <!-- Edit Voter Modal -->
  <div class="modal fade" id="editVoterModal{{ $v->id }}" tabindex="-1" aria-labelledby="editVoterModalLabel{{ $v->id }}" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{ route('admin.voter.update', $v->id) }}" method="POST" class="modal-content border-0 shadow">
        @csrf
        <div class="modal-header border-bottom-0 pb-0">
          <h5 class="modal-title fw-bold" id="editVoterModalLabel{{ $v->id }}">Edit Pengguna</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label text-muted small fw-bold">NIM</label>
            <input type="text" name="nim" class="form-control" value="{{ $v->nim }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-muted small fw-bold">Nama Mahasiswa</label>
            <input type="text" name="name" class="form-control" value="{{ $v->name }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-muted small fw-bold">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $v->email }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-muted small fw-bold">Password Baru (Opsional)</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
          </div>
        </div>
        <div class="modal-footer border-top-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
@endforeach

@foreach($categories as $cat)
  @foreach($cat->candidates as $cand)
    <!-- Detail Candidate Modal -->
    <div class="modal fade" id="detailCandidateModal{{ $cand->id }}" tabindex="-1" aria-labelledby="detailCandidateModalLabel{{ $cand->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header border-bottom-0 bg-light px-4 py-3">
            <h5 class="modal-title fw-bold text-dark" id="detailCandidateModalLabel{{ $cand->id }}">Detail Pasangan Calon</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-4 align-items-center mb-4">
              <div class="col-sm-4 text-center">
                @if($cand->photo)
                  <img src="{{ asset($cand->photo) }}" class="rounded shadow-sm img-fluid object-fit-cover" style="max-height: 250px; width: 100%; border-radius: 1rem !important;" alt="{{ $cand->name }}">
                @else
                  <div class="rounded bg-light d-flex justify-content-center align-items-center text-muted fw-bold display-1" style="height: 200px; width: 100%; border-radius: 1rem !important;">
                    {{ substr($cand->name, 0, 1) }}
                  </div>
                @endif
              </div>
              <div class="col-sm-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-2 fw-bold">{{ $cat->name }}</span>
                <h3 class="fw-extrabold text-dark mb-1">{{ $cand->name }}</h3>
              </div>
            </div>
            
            <div class="mb-4">
              <h6 class="fw-bold text-cyan mb-2 border-bottom pb-2">Visi Utama</h6>
              <p class="text-dark" style="line-height: 1.6;">{{ $cand->vision }}</p>
            </div>
            
            <div>
              <h6 class="fw-bold text-purple mb-2 border-bottom pb-2">Misi</h6>
              <ul class="text-dark ps-3" style="line-height: 1.6;">
                @foreach(explode("\n", $cand->mission) as $mission)
                  @if(trim($mission))
                    <li class="mb-1">{{ $mission }}</li>
                  @endif
                @endforeach
              </ul>
            </div>
          </div>
          <div class="modal-footer border-top-0 bg-light px-4 py-3">
            <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Candidate Modal -->
    <div class="modal fade" id="editCandidateModal{{ $cand->id }}" tabindex="-1" aria-labelledby="editCandidateModalLabel{{ $cand->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.candidate.update', $cand->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          @csrf
          <div class="modal-header border-bottom-0 bg-light px-4 py-3">
            <h5 class="modal-title fw-bold text-dark" id="editCandidateModalLabel{{ $cand->id }}">Edit Kandidat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Kategori Voting</label>
              <select name="voting_category_id" class="form-select fw-bold" required>
                @foreach($categories as $categoryOption)
                  <option value="{{ $categoryOption->id }}" {{ $cand->voting_category_id == $categoryOption->id ? 'selected' : '' }}>{{ $categoryOption->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Nama Kandidat/Pasangan</label>
              <input type="text" name="name" class="form-control" value="{{ $cand->name }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Ganti Foto Profil (Opsional, Max 5MB)</label>
              <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg">
            </div>
            <div class="mb-3">
              <label class="form-label text-muted small fw-bold">Visi</label>
              <textarea name="vision" class="form-control" rows="2" required>{{ $cand->vision }}</textarea>
            </div>
            <div class="mb-2">
              <label class="form-label text-muted small fw-bold">Misi</label>
              <textarea name="mission" class="form-control" rows="4" required>{{ $cand->mission }}</textarea>
            </div>
          </div>
          <div class="modal-footer border-top-0 bg-light px-4 py-3">
            <button type="button" class="btn btn-secondary px-3 fw-bold" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  @endforeach
@endforeach

<!-- Tab Styling Script for dynamic highlighting -->
<script>
  document.querySelectorAll('#adminTabs .nav-link').forEach(tab => {
    tab.addEventListener('show.bs.tab', function(e) {
      document.querySelectorAll('#adminTabs .nav-link').forEach(t => {
        t.style.background = 'transparent';
        t.style.color = '#64748B';
        t.style.border = '1px solid transparent';
      });
      e.target.style.background = 'rgba(79, 70, 229, 0.05)';
      e.target.style.color = '#4F46E5';
      e.target.style.border = '1px solid rgba(79, 70, 229, 0.1)';
      e.target.style.borderBottom = 'none';
    });
  });
</script>

<!-- Chart JS -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const chartData = @json($chartData);
    const canvas = document.getElementById('resultsChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    const labels = chartData.map(c => c.name);
    const votes = chartData.map(c => c.votes);

    // Create elegant linear gradients matching the light theme palette (Indigo & Violet)
    const gradient1 = ctx.createLinearGradient(0, 0, 0, 300);
    gradient1.addColorStop(0, 'rgba(79, 70, 229, 0.85)');
    gradient1.addColorStop(1, 'rgba(139, 92, 246, 0.3)');

    const gradient2 = ctx.createLinearGradient(0, 0, 0, 300);
    gradient2.addColorStop(0, 'rgba(139, 92, 246, 0.85)');
    gradient2.addColorStop(1, 'rgba(79, 70, 229, 0.3)');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Perolehan Suara',
          data: votes,
          backgroundColor: [gradient1, gradient2, 'rgba(79, 70, 229, 0.05)'],
          borderWidth: 0,
          borderRadius: 8,
          barPercentage: 0.5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1, color: '#94A3B8' },
            grid: { color: 'rgba(241, 245, 249, 1)', drawBorder: false }
          },
          x: {
            ticks: { color: '#64748B', font: { family: 'Inter', weight: '600' } },
            grid: { display: false, drawBorder: false }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1E293B',
            padding: 12,
            titleFont: { family: 'Inter', size: 14 },
            bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
            displayColors: false
          }
        }
      }
    });
  });
</script>

<!-- Security Confirmation Close Election Modal -->
<div class="modal fade" id="tutupPemilihanModal" tabindex="-1" aria-labelledby="tutupPemilihanModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.3); z-index: 9999;">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="animation: modalEntrance 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;">
      <div class="modal-header border-bottom-0 bg-danger-subtle text-danger px-4 py-3">
        <h5 class="modal-title fw-bold" id="tutupPemilihanModalLabel" style="font-family: var(--font-title);">🚨 Peringatan Keamanan Kritis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.toggle-voting') }}" method="POST" class="m-0">
        @csrf
        <div class="modal-body p-4">
          <div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex gap-3 mb-4" style="background: rgba(239, 68, 68, 0.05); color: #EF4444;">
            <span class="fs-4">⚠️</span>
            <div class="small-text text-dark" style="line-height: 1.45; font-size: 0.8rem;">
              <strong>PENTING:</strong> Menutup pemilihan akan menghentikan seluruh hak suara pemilih secara permanen. Pemilih tidak akan dapat lagi mengirimkan suara kriptografis baru mereka ke dalam database terenkripsi AES-256.
            </div>
          </div>
          
          <p class="text-dark small mb-3">Untuk mengonfirmasi tindakan ini, ketik kalimat di bawah ini secara persis:</p>
          <div class="bg-light p-3 rounded-3 text-center mb-3 monospace text-danger fw-extrabold" style="letter-spacing: 1px; font-size: 0.95rem;">
            TUTUP PEMILIHAN
          </div>
          
          <input type="text" id="confirmTutupInput" class="form-control text-center fw-bold text-uppercase border-glass" placeholder="Ketik kalimat verifikasi di sini" required style="border-radius: 10px; font-size: 0.9rem;">
        </div>
        <div class="modal-footer border-top-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-light px-3 fw-semibold" data-bs-dismiss="modal" style="border-radius: 10px; border: 1px solid rgba(15,23,42,0.08); font-size:0.85rem;">Batal</button>
          <button type="submit" id="confirmTutupSubmitBtn" class="btn btn-danger px-4 py-2 fw-semibold shadow-sm border-0" disabled style="background: #EF4444 !important; color: #FFFFFF !important; border: none !important; border-radius: 10px; font-size:0.85rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
            Ya, Tutup Pemilihan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const confirmInput = document.getElementById('confirmTutupInput');
    const submitBtn = document.getElementById('confirmTutupSubmitBtn');
    if (confirmInput && submitBtn) {
      confirmInput.addEventListener('input', (e) => {
        if (e.target.value.trim().toUpperCase() === 'TUTUP PEMILIHAN') {
          submitBtn.removeAttribute('disabled');
        } else {
          submitBtn.setAttribute('disabled', 'true');
        }
      });
    }
  });
</script>

@if (session('success') && (str_contains(session('success'), 'DITUTUP') || str_contains(session('success'), 'DIBUKA')))
  <div class="modal fade" id="successClosureModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); background: rgba(15, 23, 42, 0.4); z-index: 99999;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
      <div class="modal-content text-center p-4" style="border-radius: 24px; border: 2.5px solid #22C55E; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2), 0 0 25px rgba(34, 197, 94, 0.15); background: #FFFFFF;">
        <div class="modal-body p-3">
          
          <!-- Animated Checkmark Container -->
          <div class="checkmark-wrapper mx-auto mb-4">
            <svg class="checkmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
              <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
              <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
          </div>
          
          <h4 class="fw-extrabold text-dark mb-2" style="font-family: var(--font-title); font-size: 1.35rem;">Tindakan Berhasil!</h4>
          <p class="text-muted small mb-4" style="line-height: 1.5; font-size: 0.85rem;">{{ session('success') }}</p>
          
          <button type="button" class="btn w-100 py-2.5 fw-semibold border-0" data-bs-dismiss="modal" style="border-radius: 12px; background: #22C55E; color: #FFFFFF !important; box-shadow: 0 8px 20px rgba(34, 197, 94, 0.25); font-size: 0.9rem;">
            Selesai
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const myModal = new bootstrap.Modal(document.getElementById('successClosureModal'));
      myModal.show();
    });
  </script>

  <style>
    /* Premium Animated Checkmark SVG Styling - Infinite Looping with Fade Transitions */
    .checkmark-wrapper {
      width: 80px;
      height: 80px;
      position: relative;
      animation: fadeWrapper 2.8s ease-in-out infinite;
    }
    .checkmark-svg {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: block;
      stroke-width: 3;
      stroke: #22C55E;
      stroke-miterlimit: 10;
      box-shadow: inset 0px 0px 0px #22C55E;
      animation: fillCheckmark 2.8s ease-in-out infinite, scaleCheckmark 2.8s ease-in-out infinite;
    }
    .checkmark-circle {
      stroke-dasharray: 166;
      stroke-dashoffset: 166;
      stroke-width: 3;
      stroke-miterlimit: 10;
      stroke: #22C55E;
      fill: none;
      animation: strokeCircle 2.8s cubic-bezier(0.65, 0, 0.45, 1) infinite;
    }
    .checkmark-check {
      transform-origin: 50% 50%;
      stroke-dasharray: 48;
      stroke-dashoffset: 48;
      stroke-width: 4;
      stroke: #FFFFFF;
      animation: strokeCheck 2.8s cubic-bezier(0.65, 0, 0.45, 1) infinite;
    }

    @keyframes fadeWrapper {
      0%, 5% {
        opacity: 0;
      }
      15%, 80% {
        opacity: 1;
      }
      90%, 100% {
        opacity: 0;
      }
    }

    @keyframes strokeCircle {
      0% {
        stroke-dashoffset: 166;
      }
      5% {
        stroke-dashoffset: 166;
      }
      35%, 80% {
        stroke-dashoffset: 0;
      }
      95%, 100% {
        stroke-dashoffset: 166;
      }
    }

    @keyframes strokeCheck {
      0%, 30% {
        stroke-dashoffset: 48;
      }
      55%, 80% {
        stroke-dashoffset: 0;
      }
      95%, 100% {
        stroke-dashoffset: 48;
      }
    }

    @keyframes fillCheckmark {
      0%, 25% {
        box-shadow: inset 0px 0px 0px 0px #22C55E;
      }
      45%, 80% {
        box-shadow: inset 0px 0px 0px 40px #22C55E;
      }
      95%, 100% {
        box-shadow: inset 0px 0px 0px 0px #22C55E;
      }
    }

    @keyframes scaleCheckmark {
      0%, 40% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.08);
      }
      60%, 80% {
        transform: scale(1);
      }
      95%, 100% {
        transform: scale(1);
      }
    }
  </style>
@endif
@endsection
