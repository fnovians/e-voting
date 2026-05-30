@extends('layouts.app')

@section('title', 'Dashboard Pemilu HIMA - SecVote')

@section('content')
<!-- Hero Section (Minimalist SaaS layout) -->
<div class="card p-5 mb-5 border-glass bg-glass-light rounded-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.04) 0%, rgba(79, 70, 229, 0.04) 100%) !important;">
  <div class="row align-items-center">
    <div class="col-lg-8">
      <h1 class="fw-extrabold text-dark display-5 mb-3" style="letter-spacing: -1.5px;">
        Pemilihan Ketua HIMA Informatika 2026
      </h1>
      <p class="text-muted fs-5 mb-4" style="font-weight: 400; max-width: 650px; line-height: 1.5;">
        Berpartisipasi dalam pemilihan yang aman, transparan, dan rahasia dengan teknologi kriptografi tercanggih.
      </p>
    </div>
  </div>
  
  <!-- Stats Row -->
  <div class="row g-4 mt-3 border-top border-glass pt-4">
    <div class="col-md-4">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-white p-2 border border-glass" style="box-shadow: 0 1px 3px rgba(0,0,0,0.02);">👥</div>
        <div>
          <span class="text-muted small-text d-block text-uppercase fw-bold tracking-wider">Total Kategori</span>
          <strong class="text-dark fs-5">{{ count($categories) }} Kategori Pemilihan</strong>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-3 bg-white p-2 border border-glass" style="box-shadow: 0 1px 3px rgba(0,0,0,0.02);">🗳️</div>
        <div>
          <span class="text-muted small-text d-block text-uppercase fw-bold tracking-wider">Total Pemilih</span>
          <strong class="text-dark fs-5">{{ $totalVoters }} Mahasiswa Aktif</strong>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div>
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted small-text text-uppercase fw-bold tracking-wider">Voting Progress</span>
          <strong class="text-cyan small fw-bold">{{ $turnoutPercentage }}% Turnout</strong>
        </div>
        <div class="progress rounded-pill border-glass bg-white" style="height: 8px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
          <div class="progress-bar rounded-pill bg-cyan" role="progressbar" style="width: {{ $turnoutPercentage }}%;" aria-valuenow="{{ $turnoutPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Voter Status Panel -->
<div class="voter-status-bar d-flex justify-content-between align-items-center mb-5 p-4 rounded-4 shadow-sm border-glass">
  <div>
    <h5 class="text-dark fw-bold mb-1">Status Keikutsertaan Anda</h5>
    <p class="text-muted mb-0 small">Anda dapat memberikan <strong>satu suara per kategori</strong> pemilihan.</p>
  </div>
  <span class="badge rounded-pill bg-primary-subtle text-primary fw-bold border border-primary-subtle px-3 py-2" style="font-size: 0.78rem;">{{ count($votedCategoryIds) }} / {{ count($categories) }} Kategori Selesai</span>
</div>

@if(!$votingOpen)
  <div class="alert alert-danger fw-bold shadow-sm rounded-4 mb-5 border-0 d-flex align-items-center gap-3" role="alert" style="background-color: #fef2f2; color: #991b1b;">
    <div class="fs-4">🛑</div>
    <div>
      <h6 class="mb-1 fw-bold">Pemilihan Sedang Ditutup!</h6>
      <span class="small fw-normal">Administrator saat ini sedang menutup gerbang pemilihan. Anda tidak dapat melakukan pencoblosan sampai sesi pemilihan dibuka kembali.</span>
    </div>
  </div>
@endif

<!-- Candidate Section (Horizontal Premium Stripe Layout) -->
@forelse ($categories as $cat)
  <div class="d-flex justify-content-between align-items-center mb-4 mt-5">
    <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">{{ $cat->name }}</h3>
    @if(in_array($cat->id, $votedCategoryIds))
      <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">✓ Sudah Memilih</span>
    @else
      <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-bold">Belum Memilih</span>
    @endif
  </div>
  
  @if($cat->description)
    <p class="text-muted mb-4">{{ $cat->description }}</p>
  @endif

  <div class="row g-4 mb-5">
    @forelse ($cat->candidates as $cand)
      <div class="col-12">
        <div class="card p-4 candidate-card border-glass shadow-sm {{ in_array($cat->id, $votedCategoryIds) ? 'opacity-75 bg-light' : '' }}">
          <!-- Left: Candidate Photo or Avatar -->
          @if ($cand->photo)
            <img src="{{ asset($cand->photo) }}" alt="{{ $cand->name }}" class="candidate-avatar" style="object-fit: cover;">
          @else
            <div class="candidate-avatar bg-secondary text-white">{{ substr($cand->name, 0, 1) }}</div>
          @endif
          
          <!-- Middle: Candidate details -->
          <div class="candidate-body">
            <h4 class="text-dark fw-bold mb-1" style="letter-spacing: -0.5px;">{{ $cand->name }}</h4>
            <span class="text-muted d-block small mb-2 italic">Kandidat {{ $cat->name }}</span>
            
            <div class="text-muted small mb-0" style="max-width: 600px; line-height: 1.45;">
              <strong>Visi:</strong> "{{ \Illuminate\Support\Str::limit($cand->vision, 100) }}"
            </div>
          </div>
          
          <!-- Right: Action CTA Buttons -->
          <div class="d-flex flex-column sm-flex-row gap-2 flex-shrink-0 align-self-stretch justify-content-center border-start border-glass ps-4">
            <button class="btn btn-outline-cyan btn-sm py-2 px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#candidateModal{{ $cand->id }}">
              Lihat Profil
            </button>
            
            @if(in_array($cat->id, $votedCategoryIds))
              <button class="btn btn-secondary btn-sm py-2 px-3 fw-bold" disabled>Terkunci</button>
            @else
              <button class="btn btn-cyan btn-sm py-2 px-3 fw-bold" onclick="openConfirmationModal('{{ $cand->id }}', '{{ $cand->name }}', '{{ $cat->name }}')" {{ !$votingOpen ? 'disabled' : '' }}>
                Pilih Kandidat
              </button>
            @endif
          </div>
        </div>
      </div>

      <!-- Candidate Detailed Vision & Mission Modal (Apple-Inspired Style) -->
      <div class="modal fade" id="candidateModal{{ $cand->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow-box border-glass">
            <div class="modal-header border-glass px-4 py-3 bg-light">
              <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                Profil Lengkap Paslon
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start">
              @if ($cand->photo)
                <div class="text-center mb-4">
                  <img src="{{ asset($cand->photo) }}" alt="{{ $cand->name }}" class="rounded-4 border border-glass shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
                </div>
              @endif
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-2 fw-bold d-block mx-auto" style="width: fit-content;">{{ $cat->name }}</span>
              <h4 class="text-dark fw-bold mb-3 text-center">{{ $cand->name }}</h4>
              <div class="mb-4">
                <strong class="text-dark small d-block mb-1 text-uppercase tracking-wider">Visi Utama:</strong>
                <p class="text-muted small mb-0" style="line-height: 1.45;">"{{ $cand->vision }}"</p>
              </div>
              <div>
                <strong class="text-dark small d-block mb-2 text-uppercase tracking-wider">Misi Kerja:</strong>
                <ul class="text-muted small ps-3 mb-0" style="line-height: 1.55;">
                  @foreach (explode("\n", $cand->mission) as $mission)
                    @if(trim($mission))
                      <li class="mb-1">{{ $mission }}</li>
                    @endif
                  @endforeach
                </ul>
              </div>
            </div>
            <div class="modal-footer border-glass px-4 py-3 bg-light">
              <button type="button" class="btn btn-secondary btn-sm py-2 px-3 fw-bold" data-bs-dismiss="modal">Tutup</button>
              @if(!in_array($cat->id, $votedCategoryIds) && $votingOpen)
                <button type="button" class="btn btn-cyan btn-sm py-2 px-4 fw-bold" data-bs-dismiss="modal" onclick="openConfirmationModal('{{ $cand->id }}', '{{ $cand->name }}', '{{ $cat->name }}')">Pilih Paslon</button>
              @endif
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center text-muted card p-4 border-glass">Belum ada kandidat di kategori ini.</div>
    @endforelse
  </div>
@empty
  <div class="col-12 text-center text-muted card p-5 border-glass mb-5">
    <h5>Belum ada Kategori Pemilihan yang dibuat.</h5>
  </div>
@endforelse

<!-- Cybersecurity trust-badges Section -->
<h3 class="fw-bold text-dark mb-4" style="letter-spacing: -0.5px;">Sistem Keamanan & Kredibilitas</h3>
<div class="row g-3 mb-5">
  <div class="col-md-4">
    <div class="card security-badge-card border-glass h-100">
      <div class="security-badge-icon">🔒</div>
      <h5 class="text-dark fw-bold mb-2">AES-256 Encryption</h5>
      <p class="text-muted small mb-0" style="line-height: 1.4;">
        Setiap suara dienkripsi penuh menggunakan algoritma AES-256 sebelum disimpan di database MySQL, mencegah kebocoran data.
      </p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card security-badge-card border-glass h-100">
      <div class="security-badge-icon">🛡️</div>
      <h5 class="text-dark fw-bold mb-2">Anonymous Voting</h5>
      <p class="text-muted small mb-0" style="line-height: 1.4;">
        Sistem memisahkan relasi data pemilih dengan kueri suara di database untuk menjamin kerahasiaan pilihan Anda secara mutlak.
      </p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card security-badge-card border-glass h-100">
      <div class="security-badge-icon">🔑</div>
      <h5 class="text-dark fw-bold mb-2">One Person One Vote</h5>
      <p class="text-muted small mb-0" style="line-height: 1.4;">
        Status keikutsertaan dikunci secara digital untuk mencegah manipulasi data suara ganda.
      </p>
    </div>
  </div>
</div>

<!-- Confirmation Modal (Apple-Inspired Style) -->
<div class="modal fade" id="confirmVoteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-box border-glass">
      <div class="modal-header border-glass px-4 py-3">
        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
          <span>🛡️</span> Konfirmasi Pilihan Suara
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="fs-1 mb-3">🗳️</div>
        <h4 class="text-dark fw-bold mb-1" id="confirm-candidate-name">Nama Kandidat</h4>
        <div class="text-primary mb-3 fw-bold small" id="confirm-category-name">Kategori</div>
        <p class="text-muted small" style="line-height: 1.45;">
          Pilihan Anda akan dienkripsi menggunakan teknologi <strong>AES-256</strong> secara aman di server. Setelah memilih, pilihan suara Anda <strong>tidak dapat diubah</strong> kembali.
        </p>
      </div>
      <div class="modal-footer border-glass px-4 py-3">
        <button type="button" class="btn btn-secondary btn-sm py-2 px-3 border border-glass bg-light text-dark fw-bold" data-bs-dismiss="modal">Batal</button>
        
        <form id="confirm-vote-form" action="{{ route('vote.cast') }}" method="POST" class="m-0">
          @csrf
          <input type="hidden" name="candidateId" id="confirm-candidate-id">
          <button type="submit" class="btn btn-cyan btn-sm py-2 px-4 fw-bold">Ya, Saya Yakin</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function openConfirmationModal(id, name, categoryName) {
    document.getElementById('confirm-candidate-id').value = id;
    document.getElementById('confirm-candidate-name').textContent = name;
    document.getElementById('confirm-category-name').textContent = categoryName;
    
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmVoteModal'));
    confirmModal.show();
  }
</script>
@endsection
