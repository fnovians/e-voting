@extends('layouts.app')

@section('title', 'Admin Dashboard - SecVote')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
  <div>
    <h2 class="fw-bold text-bright mb-1">Dashboard Administrator</h2>
    <p class="text-muted mb-0">Kontrol dan monitoring jalannya pemilihan umum secara real-time dari database MySQL.</p>
  </div>
  
  <form action="{{ route('admin.toggle-voting') }}" method="POST" class="m-0">
    @csrf
    <button type="submit" class="btn {{ $votingOpen ? 'btn-danger' : 'btn-cyan' }} px-4">
      {{ $votingOpen ? 'Tutup Pemilihan' : 'Buka Pemilihan' }}
    </button>
  </form>
</div>

<!-- Summary Stats row -->
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card p-3 text-center bg-glass-light border-glass shadow-sm">
      <h1 class="fw-extrabold text-cyan mb-1">{{ $turnout['total'] }}</h1>
      <span class="text-muted text-uppercase small tracking-wider">Total Daftar Pemilih</span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 text-center bg-glass-light border-glass shadow-sm">
      <h1 class="fw-extrabold text-purple mb-1">{{ $turnout['voted'] }}</h1>
      <span class="text-muted text-uppercase small tracking-wider">Jumlah Suara Masuk</span>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 text-center bg-glass-light border-glass shadow-sm">
      <h1 class="fw-extrabold text-cyan mb-1">{{ $turnout['percentage'] }}%</h1>
      <span class="text-muted text-uppercase small tracking-wider">Partisipasi (Turnout)</span>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Results chart card -->
  <div class="col-lg-7">
    <div class="card p-4 shadow-box h-100">
      <h4 class="text-bright fw-bold mb-1">Hasil Perolehan Suara Terenkripsi (Decrypted Tallies)</h4>
      <p class="text-muted small mb-3">Hasil kalkulasi dinamik dengan mendekripsi tabel suara AES-256 MySQL pada memori server.</p>
      <div style="position:relative; height: 320px;">
        <canvas id="resultsChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Add candidate card -->
  <div class="col-lg-5">
    <div class="card p-4 shadow-box h-100">
      <h4 class="text-bright fw-bold mb-1">Tambah Kandidat Paslon</h4>
      <p class="text-muted small mb-3">Daftarkan pasangan calon ketua dan wakil ketua HIMA baru ke MySQL.</p>
      
      <form action="{{ route('admin.candidate.create') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label class="form-label text-muted">Nama Pasangan Calon</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: Elang & Fani" required>
        </div>
        <div class="mb-3">
          <label class="form-label text-muted">Foto Pasangan Calon (Opsional)</label>
          <input type="file" name="photo" class="form-control" accept="image/*">
        </div>
        <div class="mb-3">
          <label class="form-label text-muted">Visi Utama</label>
          <textarea name="vision" class="form-control" rows="2" placeholder="Tuliskan visi kandidat..." required style="resize:none;"></textarea>
        </div>
        <div class="mb-3 mb-4">
          <label class="form-label text-muted">Misi Utama (Satu per baris)</label>
          <textarea name="mission" class="form-control" rows="3" placeholder="1. Misi kesatu&#10;2. Misi kedua" required style="resize:none;"></textarea>
        </div>
        <button type="submit" class="btn btn-cyan w-100 py-2">Daftarkan Kandidat</button>
      </form>
    </div>
  </div>
</div>

<!-- User List Card -->
<div class="card p-4 shadow-box mt-4 border-glass">
  <h4 class="text-bright fw-bold mb-1">Daftar Pemilih Terdaftar & Keikutsertaan</h4>
  <p class="text-muted small mb-3">Memantau nama pemilih tanpa melanggar Anonymous Voting (pilihan kandidat tidak terlihat di users).</p>
  
  <div class="table-responsive">
    <table class="table table-hover border-glass">
      <thead>
        <tr>
          <th>NIM</th>
          <th>Nama Mahasiswa</th>
          <th>Email Terdaftar</th>
          <th>Status Partisipasi</th>
          <th>Aksi CRUD</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($voters as $v)
          <tr>
            <td><strong class="monospace-cyan">{{ $v->nim }}</strong></td>
            <td>{{ $v->name }}</td>
            <td>{{ $v->email }}</td>
            <td>
              @if ($v->has_voted)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); font-weight: 600;">
                  ✓ Sudah Memilih
                </span>
              @else
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(245, 158, 11, 0.1); color: #D97706; border: 1px solid rgba(245, 158, 11, 0.2); font-weight: 600;">
                  ✗ Belum Memilih
                </span>
              @endif
            </td>
            <td>
              <form action="{{ route('admin.voter.delete', $v->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pemilih ini?')" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger py-1 px-3" style="font-size: 0.75rem;">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Belum ada pemilih terdaftar.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const chartData = @json($chartData);
    const canvas = document.getElementById('resultsChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    const labels = chartData.map(c => c.name);
    const votes = chartData.map(c => c.votes);

    // Create elegant linear gradients matching the light theme palette
    const gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(37, 99, 235, 0.85)');
    gradient1.addColorStop(1, 'rgba(79, 70, 229, 0.4)');

    const gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(79, 70, 229, 0.85)');
    gradient2.addColorStop(1, 'rgba(37, 99, 235, 0.4)');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Perolehan Suara',
          data: votes,
          backgroundColor: [gradient1, gradient2, 'rgba(15, 23, 42, 0.05)'],
          borderColor: ['#2563EB', '#4F46E5', '#64748B'],
          borderWidth: 1.5,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 1, color: '#64748B' },
            grid: { color: 'rgba(15, 23, 42, 0.05)' }
          },
          x: {
            ticks: { color: '#64748B', font: { family: 'Inter' } },
            grid: { display: false }
          }
        },
        plugins: {
          legend: { display: false }
        }
      }
    });
  });
</script>
@endsection
