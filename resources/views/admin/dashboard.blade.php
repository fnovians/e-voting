@extends('layouts.app')

@section('title', 'Admin Dashboard - SecVote')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
  <div>
    <h2 class="fw-bold text-dark mb-1">Dashboard Administrator</h2>
    <p class="text-muted mb-0">Manajemen Pemilu, Kategori, Kandidat, dan Pemilih.</p>
  </div>
  
  <form action="{{ route('admin.toggle-voting') }}" method="POST" class="m-0">
    @csrf
    <button type="submit" class="btn {{ $votingOpen ? 'btn-danger' : 'btn-cyan' }} px-4 shadow-sm fw-bold">
      {{ $votingOpen ? '🛑 Tutup Pemilihan' : '✅ Buka Pemilihan' }}
    </button>
  </form>
</div>

<!-- Navigation Tabs -->
<ul class="nav nav-tabs mb-4 border-bottom-0 gap-2" id="adminTabs" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active rounded-top-3 fw-bold px-4" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" style="background: rgba(37, 99, 235, 0.05); color: #2563EB; border: 1px solid rgba(37, 99, 235, 0.1); border-bottom: none;">📊 Overview & Hasil</button>
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
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="card p-4 text-center bg-white shadow-sm border-0 rounded-4" style="border-top: 4px solid #0EA5E9 !important;">
          <h1 class="fw-extrabold text-cyan mb-1 display-5">{{ $turnout['total'] }}</h1>
          <span class="text-muted text-uppercase small fw-bold tracking-wider">Daftar Pemilih</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center bg-white shadow-sm border-0 rounded-4" style="border-top: 4px solid #8B5CF6 !important;">
          <h1 class="fw-extrabold text-purple mb-1 display-5">{{ $turnout['voted'] }}</h1>
          <span class="text-muted text-uppercase small fw-bold tracking-wider">Suara Masuk</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-4 text-center bg-white shadow-sm border-0 rounded-4" style="border-top: 4px solid #10B981 !important;">
          <h1 class="fw-extrabold text-success mb-1 display-5">{{ $turnout['percentage'] }}%</h1>
          <span class="text-muted text-uppercase small fw-bold tracking-wider">Partisipasi</span>
        </div>
      </div>
    </div>
    <div class="card p-4 shadow-sm border-0 rounded-4 bg-white">
      <h4 class="text-dark fw-bold mb-1">Hasil Perolehan Suara Terenkripsi</h4>
      <p class="text-muted small mb-3">Hasil kalkulasi dinamik dengan mendekripsi tabel suara AES-256 MySQL pada memori server.</p>
      <div style="position:relative; height: 350px;">
        <canvas id="resultsChart"></canvas>
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
      e.target.style.background = 'rgba(37, 99, 235, 0.05)';
      e.target.style.color = '#2563EB';
      e.target.style.border = '1px solid rgba(37, 99, 235, 0.1)';
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

    // Create elegant linear gradients matching the light theme palette
    const gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(14, 165, 233, 0.85)');
    gradient1.addColorStop(1, 'rgba(139, 92, 246, 0.4)');

    const gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(139, 92, 246, 0.85)');
    gradient2.addColorStop(1, 'rgba(14, 165, 233, 0.4)');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Perolehan Suara',
          data: votes,
          backgroundColor: [gradient1, gradient2, 'rgba(15, 23, 42, 0.05)'],
          borderWidth: 0,
          borderRadius: 8,
          barPercentage: 0.6
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
@endsection
