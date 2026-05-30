@extends('layouts.app')

@section('title', 'Suara Berhasil Dikirim - SecVote')

@section('content')
<!-- Voter Status Alert card -->
<div class="voter-status-bar d-flex justify-content-between align-items-center mb-4 p-4 rounded-4 shadow-sm border-glass">
  <div>
    <h5 class="text-dark fw-bold mb-1">Status Keikutsertaan Anda</h5>
    <p class="text-muted mb-0 small">Sistem mengamankan pilihan Anda secara anonim menggunakan **One Person One Vote**.</p>
  </div>
  <span class="badge rounded-pill bg-success-subtle text-success fw-bold border border-success-subtle px-3 py-2" style="background-color: #ecfdf5; color: #059669; font-size: 0.78rem;">✓ Sudah Memilih</span>
</div>

<div class="row justify-content-center">
  <div class="col-lg-9">
    
    <div class="card p-5 text-center success-overlay shadow-box border-glass rounded-4 bg-white">
      <div class="success-icon mb-4">✓</div>
      <h2 class="fw-extrabold text-dark mb-3" style="letter-spacing: -1px;">Suara Anda Berhasil Disimpan!</h2>
      <p class="text-muted mx-auto mb-5" style="max-width: 600px; line-height: 1.5;">
        Hak pilih Anda telah sukses digunakan. Menggunakan prinsip <strong>Anonymous Voting</strong>, identitas Anda telah ditandai sebagai 'Sudah Memilih' secara terpisah dari suara fisik Anda di database MySQL yang terenkripsi penuh.
      </p>

      <div class="card bg-light border-glass text-start mx-auto p-4" style="max-width: 700px; background-color: #F8FAFC !important; box-shadow: none;">
        <h5 class="text-cyan fw-bold border-bottom border-glass pb-2 mb-3 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
          <span>🔐</span> Data Kriptografi Suara Anda (Tabel Votes MySQL):
        </h5>
        
        @php
          $plaintext = session('plaintext', 'Kandidat ID (Telah terpisah & didekripsi di memori server saja)');
          $ciphertext = session('ciphertext', $latestVote->encrypted_candidate ?? 'Belum ada suara masuk.');
          $iv = session('iv', $latestVote->iv ?? 'Belum ada suara masuk.');
          $tag = session('tag', $latestVote->tag ?? 'AES-256-CBC: tag kosong');
        @endphp

        <div class="d-flex flex-column gap-3 small">
          <div>
            <strong class="text-muted d-block mb-1 small-text tracking-wider text-uppercase">Plaintext Awal (Pilihan Anda):</strong>
            <span class="monospace-cyan" id="crypto-plaintext">{{ $plaintext }}</span>
          </div>
          <div>
            <strong class="text-muted d-block mb-1 small-text tracking-wider text-uppercase">Ciphertext AES-256-GCM (Tersimpan di DB MySQL):</strong>
            <span class="monospace text-break" id="crypto-ciphertext" style="word-break: break-all;">{{ $ciphertext }}</span>
          </div>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong class="text-muted d-block mb-1 small-text tracking-wider text-uppercase">Initialization Vector (IV):</strong>
              <span class="monospace-cyan" id="crypto-iv">{{ $iv }}</span>
            </div>
            <div class="col-md-6">
              <strong class="text-muted d-block mb-1 small-text tracking-wider text-uppercase">Authentication Tag (GCM Tag):</strong>
              <span class="monospace" id="crypto-tag">{{ $tag ?: 'AES-256-CBC mode: tag tidak diperlukan' }}</span>
            </div>
          </div>
          <div class="border-top border-glass pt-3 mt-1 text-muted small-text" style="line-height: 1.4;">
            💡 <strong>Analisis Ilmiah:</strong> Perhatikan bagaimana pilihan Anda disandikan menjadi teks acak tak terbaca. Karena kunci enkripsi disimpan di server dengan aman, administrator atau peretas yang membobol database tidak dapat mengetahui siapa memilih siapa, menjaga aspek <strong>Kerahasiaan Pemilu</strong>.
          </div>
        </div>
      </div>
    </div>
    
  </div>
</div>
@endsection
