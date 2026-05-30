<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SecVote - E-Voting Laravel 12 & MySQL</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Custom Neon Glassmorphism CSS -->
  <link rel="stylesheet" href="/css/custom.css">
  <!-- Chart.js for real-time results representation -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <!-- Header Navigation -->
  <header class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a class="navbar-brand logo" href="#" onclick="switchView('dashboard')">
        🗳️ Sec<span>Vote</span>
      </a>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="nav-links" style="display: none;">
          <li class="nav-item">
            <a class="nav-link" id="nav-dashboard" onclick="switchView('dashboard')">Dashboard Pemilu</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="nav-admin" onclick="switchView('admin')" style="display: none;">Admin Panel</a>
          </li>
        </ul>
        
        <div class="d-flex align-items-center gap-3">
          <div id="user-badge" class="user-info-text text-end" style="display: none;">
            <span id="user-role-lbl" class="badge bg-success" style="font-size:0.65rem;">Pemilih</span>
            <strong id="user-name-lbl" class="d-block text-white">Nama Mahasiswa</strong>
          </div>
          <button id="btn-login-nav" class="btn btn-outline-cyan" onclick="switchView('login')">Masuk</button>
          <button id="btn-logout" class="btn btn-danger" onclick="handleLogout()" style="display: none;">Keluar</button>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content Container -->
  <main class="container my-5">
    <div id="alert-container"></div>

    <!-- VIEW 1: LOGIN & REGISTER -->
    <section id="login-view" class="view-section active">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          
          <!-- Tab selector inside Auth Card -->
          <div class="card p-4 shadow-box" id="auth-card">
            <div class="text-center mb-4">
              <h2 class="fw-extrabold text-white">Sistem E-Voting HIMA</h2>
              <p class="text-muted small">Dilindungi enkripsi AES-256 dan mitigasi SQL Injection</p>
            </div>
            
            <ul class="nav nav-pills nav-justified mb-4" id="auth-tabs">
              <li class="nav-item">
                <button class="nav-link active" id="pill-login" onclick="toggleAuthForm('login')">Masuk</button>
              </li>
              <li class="nav-item">
                <button class="nav-link" id="pill-register" onclick="toggleAuthForm('register')">Registrasi</button>
              </li>
            </ul>

            <!-- LOGIN FORM -->
            <form id="login-form" onsubmit="handleLogin(event)">
              <div class="mb-3">
                <label class="form-label text-muted">Nomor Induk Mahasiswa (NIM)</label>
                <input type="text" id="login-nim" class="form-control" placeholder="Contoh: 120203001" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted">Password</label>
                <input type="password" id="login-password" class="form-control" placeholder="••••••••" required>
              </div>



              <button type="submit" class="btn btn-cyan w-100 py-2">Masuk Sistem</button>
            </form>

            <!-- REGISTER FORM (Hidden by default) -->
            <form id="register-form" onsubmit="handleRegister(event)" style="display: none;">
              <div class="mb-3">
                <label class="form-label text-muted">Nomor Induk Mahasiswa (NIM)</label>
                <input type="text" id="reg-nim" class="form-control" placeholder="Contoh: 120203006" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted">Nama Lengkap</label>
                <input type="text" id="reg-name" class="form-control" placeholder="Contoh: Fajar Pratama" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted">Email Kampus</label>
                <input type="email" id="reg-email" class="form-control" placeholder="Contoh: fajar@mahasiswa.univ.ac.id" required>
              </div>
              <div class="mb-3 mb-4">
                <label class="form-label text-muted">Password Baru</label>
                <input type="password" id="reg-password" class="form-control" placeholder="Min. 8 karakter" required>
              </div>

              <button type="submit" class="btn btn-purple w-100 py-2">Daftar Akun Pemilih</button>
            </form>

            <div class="mt-4 pt-3 border-top border-glass">
              <h6 class="text-white small fw-bold">🔬 Akun Pengujian & Riset:</h6>
              <ul class="text-muted small ps-3 mb-0" style="line-height: 1.4;">
                <li><strong>Mahasiswa (Voter):</strong> NIM: <code class="monospace-cyan">120203001</code> | Pass: <code class="monospace">password123</code></li>
                <li><strong>Administrator:</strong> NIM: <code class="monospace-cyan">000000000</code> | Pass: <code class="monospace">adminpassword</code></li>
              </ul>
            </div>
          </div>
          
        </div>
      </div>
    </section>

    <!-- VIEW 2: VERIFIKASI OTP -->
    <section id="otp-view" class="view-section">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
          <div class="card p-4 text-center shadow-box">
            <div class="candidate-avatar mx-auto mb-4 bg-grad-purple">🔑</div>
            <h2 class="fw-bold text-white">Verifikasi OTP</h2>
            <p class="text-muted small mb-4">
              Untuk menjaga integritas data pemilihan, masukkan 6 digit kode OTP yang telah dikirimkan ke email terdaftar Anda.
            </p>

            <form id="otp-form" onsubmit="handleVerifyOtp(event)">
              <div class="otp-container d-flex justify-content-center gap-2 mb-4">
                <input type="text" class="otp-input" maxlength="1" onkeyup="moveOtpFocus(this, 'otp2', null)" id="otp1" required>
                <input type="text" class="otp-input" maxlength="1" onkeyup="moveOtpFocus(this, 'otp3', 'otp1')" id="otp2" required>
                <input type="text" class="otp-input" maxlength="1" onkeyup="moveOtpFocus(this, 'otp4', 'otp2')" id="otp3" required>
                <input type="text" class="otp-input" maxlength="1" onkeyup="moveOtpFocus(this, 'otp5', 'otp3')" id="otp4" required>
                <input type="text" class="otp-input" maxlength="1" onkeyup="moveOtpFocus(this, 'otp6', 'otp4')" id="otp5" required>
                <input type="text" class="otp-input" maxlength="1" onkeyup="moveOtpFocus(this, null, 'otp5')" id="otp6" required>
              </div>
              
              <button type="submit" class="btn btn-cyan w-100 py-2">Verifikasi & Masuk</button>
            </form>

            <p class="text-muted small mt-4 mb-0">
              Gunakan widget inbox simulasi di pojok kanan bawah untuk menyalin kode OTP tanpa SMTP server.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- VIEW 3: VOTER DASHBOARD -->
    <section id="dashboard-view" class="view-section">
      <!-- Voter Status Alert card -->
      <div class="voter-status-bar d-flex justify-content-between align-items-center mb-4 p-4 rounded-4">
        <div>
          <h4 class="text-white fw-bold mb-1">Status Keikutsertaan Anda</h4>
          <p class="text-muted mb-0 small">Sistem mengamankan pilihan Anda secara anonim menggunakan <strong>One Person One Vote</strong>.</p>
        </div>
        <span id="voter-status-badge" class="badge rounded-pill bg-warning p-2 px-3 fw-bold">Belum Memilih</span>
      </div>

      <!-- Voting choices section -->
      <div id="voting-panel">
        <h2 class="fw-bold text-white mb-2">Pemilihan Ketua HIMA 2026</h2>
        <p class="text-muted mb-4 subtitle">Tentukan hak suara Anda dengan menekan tombol vote. Hak pilih Anda dilindungi penuh oleh enkripsi AES-256-GCM.</p>

        <div class="row g-4" id="candidates-container">
          <!-- Candidates populated dynamically -->
        </div>
      </div>

      <!-- Vote success comparative view -->
      <div id="voted-success-panel" style="display: none;">
        <div class="card p-5 text-center success-overlay shadow-box">
          <div class="success-icon mb-4">✓</div>
          <h2 class="fw-extrabold text-white mb-3">Suara Anda Berhasil Disimpan!</h2>
          <p class="text-muted mx-auto mb-5" style="max-width: 600px;">
            Hak pilih Anda telah sukses digunakan. Menggunakan prinsip **Anonymous Voting**, identitas Anda telah ditandai sebagai 'Sudah Memilih' secara terpisah dari suara fisik Anda di database MySQL yang terenkripsi penuh.
          </p>

          <div class="card border-cyan bg-glass-dark text-start mx-auto p-4" style="max-width: 700px;">
            <h5 class="text-cyan fw-bold border-bottom border-glass pb-2 mb-3">
              🔐 Data Kriptografi Suara Anda (Tabel Votes MySQL):
            </h5>
            <div class="d-flex flex-column gap-3 small">
              <div>
                <strong class="text-muted d-block mb-1">Plaintext Awal (Pilihan Anda):</strong>
                <span class="monospace-cyan" id="crypto-plaintext">ID Kandidat: X</span>
              </div>
              <div>
                <strong class="text-muted d-block mb-1">Ciphertext AES-256-GCM (Tersimpan di DB MySQL):</strong>
                <span class="monospace text-break" id="crypto-ciphertext">Loading...</span>
              </div>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <strong class="text-muted d-block mb-1">Initialization Vector (IV):</strong>
                  <span class="monospace-cyan" id="crypto-iv">Loading...</span>
                </div>
                <div class="col-md-6">
                  <strong class="text-muted d-block mb-1">Authentication Tag (GCM Tag):</strong>
                  <span class="monospace-cyan" id="crypto-tag">Loading...</span>
                </div>
              </div>
              <div class="border-top border-glass pt-3 mt-1 text-muted small-text">
                💡 <strong>Analisis Ilmiah:</strong> Perhatikan bagaimana pilihan Anda disandikan menjadi teks acak tak terbaca. Karena kunci enkripsi disimpan di server dengan aman, administrator atau peretas yang membobol database tidak dapat mengetahui siapa memilih siapa, menjaga aspek <strong>Kerahasiaan Pemilu</strong>.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- VIEW 4: CRYPTOGRAPHIC RESEARCH LAB -->
    <section id="research-view" class="view-section">
      <h2 class="fw-bold text-white mb-2">Pusat Analisis & Riset Kriptografi</h2>
      <p class="text-muted mb-4">
        Panel ini dirancang khusus untuk bahan penelitian artikel ilmiah Anda. Menampilkan kondisi database MySQL real-time dan perbandingan dekripsi data.
      </p>

      <div class="card p-4 shadow-box">
        <ul class="nav nav-tabs mb-4" id="db-tabs">
          <li class="nav-item">
            <button class="nav-link active" onclick="switchDbTab('users')">Tabel Akun Pemilih (users)</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" onclick="switchDbTab('votes')">Tabel Enkripsi Suara (votes)</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" onclick="switchDbTab('audit')">Audit Logs Keamanan</button>
          </li>
        </ul>

        <!-- Users DB Tab -->
        <div id="db-tab-users-view" class="db-tab-content">
          <h4 class="text-white fw-bold mb-1">Tabel Pengguna & Status One Person One Vote</h4>
          <p class="text-muted small mb-3">
            Menyimpan profil mahasiswa beserta <strong>bcrypt password hash</strong> yang aman. Status <code>has_voted</code> mengunci sistem agar pemilih tidak dapat memberikan suara ganda.
          </p>
          <div class="table-responsive">
            <table class="table table-dark table-hover border-glass">
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
              <tbody id="db-users-tbody"></tbody>
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
            <table class="table table-dark table-hover border-glass">
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
              <tbody id="db-votes-tbody"></tbody>
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
            <table class="table table-dark table-hover border-glass">
              <thead>
                <tr>
                  <th>Waktu Log</th>
                  <th>Jenis Aktivitas</th>
                  <th>IP Address</th>
                  <th>Detail Catatan Audit</th>
                </tr>
              </thead>
              <tbody id="db-audit-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="d-flex justify-content-end gap-2 mt-4">
        <button class="btn btn-outline-cyan" onclick="loadResearchData()">Refresh Data Lab</button>
        <button class="btn btn-danger" onclick="handleResetDb()">Reset Database Awal</button>
      </div>
    </section>

    <!-- VIEW 5: SECURITY TESTING LAB -->
    <section id="security-view" class="view-section">
      <h2 class="fw-bold text-white mb-2">Laboratorium Pengujian Keamanan: SQL Injection</h2>
      <p class="text-muted mb-4">
        Uji coba ketangguhan sistem e-voting ini terhadap serangan pembobolan autentikasi login (SQL Injection) pada database MySQL/MariaDB secara langsung.
      </p>

      <div class="row g-4">
        <!-- VULNERABLE PANEL -->
        <div class="col-md-6">
          <div class="card p-4 border-danger bg-glass-danger h-100">
            <span class="badge bg-danger align-self-start mb-2 px-3">Kondisi Rentan (Sebelum Mitigasi)</span>
            <h4 class="text-danger fw-bold mb-1">Formulir Celah Injeksi</h4>
            <p class="text-muted small mb-4">
              Sistem menyusun query SQL dengan menggabungkan string mentah (*string concatenation*). Ketikkan payload SQL Injection pada kolom NIM untuk mem-bypass autentikasi MySQL!
            </p>

            <form onsubmit="handleSqlLabSubmit(event, false)">
              <div class="mb-3">
                <label class="form-label text-muted">Input NIM (Payload Serangan)</label>
                <input type="text" id="lab-vuln-nim" class="form-control text-monospace border-danger-subtle" placeholder="120203001" value="' OR '1'='1" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted">Password</label>
                <input type="text" id="lab-vuln-password" class="form-control border-danger-subtle" placeholder="Bebas" value="sembarang">
              </div>
              <button type="submit" class="btn btn-danger w-100 py-2">Luncurkan Serangan (Vulnerable)</button>
            </form>

            <div class="code-viewer my-3" id="lab-vuln-sql-view">SELECT * FROM users WHERE nim = '' AND role = 'voter';</div>
            <div class="alert alert-danger mb-0 small p-3" id="lab-vuln-analysis" style="display: none; white-space: pre-wrap;"></div>
          </div>
        </div>

        <!-- MITIGATED PANEL -->
        <div class="col-md-6">
          <div class="card p-4 border-cyan bg-glass-light h-100">
            <span class="badge bg-cyan text-dark align-self-start mb-2 px-3">Kondisi Aman (Sesudah Mitigasi)</span>
            <h4 class="text-cyan fw-bold mb-1">Formulir Aman Terproteksi</h4>
            <p class="text-muted small mb-4">
              Sistem menggunakan parameterisasi kueri (*Prepared Statements*). Payload SQL Injection di bawah ini akan diperlakukan sebagai string literal biasa oleh MySQL dan ditolak secara aman.
            </p>

            <form onsubmit="handleSqlLabSubmit(event, true)">
              <div class="mb-3">
                <label class="form-label text-muted">Input NIM (Payload Serangan)</label>
                <input type="text" id="lab-safe-nim" class="form-control text-monospace border-cyan-subtle" placeholder="120203001" value="' OR '1'='1" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted">Password</label>
                <input type="text" id="lab-safe-password" class="form-control border-cyan-subtle" placeholder="Bebas" value="sembarang">
              </div>
              <button type="submit" class="btn btn-cyan w-100 py-2">Uji Keamanan (Mitigated)</button>
            </form>

            <div class="code-viewer my-3" id="lab-safe-sql-view">SELECT * FROM users WHERE nim = ? AND passwordHash = ?;</div>
            <div class="alert alert-info mb-0 small p-3" id="lab-safe-analysis" style="display: none; white-space: pre-wrap;"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- VIEW 6: ADMIN DASHBOARD -->
    <section id="admin-view" class="view-section">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold text-white mb-1">Dashboard Administrator</h2>
          <p class="text-muted mb-0">Kontrol dan monitoring jalannya pemilihan umum secara real-time dari database MySQL.</p>
        </div>
        <button id="btn-toggle-voting" class="btn btn-outline-danger px-4" onclick="handleToggleVoting()">Tutup Pemilihan</button>
      </div>

      <!-- Summary Stats row -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card p-3 text-center bg-glass-light border-glass">
            <h1 class="fw-extrabold text-cyan mb-1" id="stat-total-voters">0</h1>
            <span class="text-muted text-uppercase small tracking-wider">Total Daftar Pemilih</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 text-center bg-glass-light border-glass">
            <h1 class="fw-extrabold text-purple mb-1" id="stat-votes-cast">0</h1>
            <span class="text-muted text-uppercase small tracking-wider">Jumlah Suara Masuk</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 text-center bg-glass-light border-glass">
            <h1 class="fw-extrabold text-white mb-1" id="stat-turnout">0%</h1>
            <span class="text-muted text-uppercase small tracking-wider">Partisipasi (Turnout)</span>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Results chart card -->
        <div class="col-lg-7">
          <div class="card p-4 shadow-box h-100">
            <h4 class="text-white fw-bold mb-1">Hasil Perolehan Suara Terenkripsi (Decrypted Tallies)</h4>
            <p class="text-muted small mb-3">Hasil kalkulasi dinamik dengan mendekripsi tabel suara AES-256 secara on-the-fly.</p>
            <div style="position:relative; height: 320px;">
              <canvas id="resultsChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Add candidate card -->
        <div class="col-lg-5">
          <div class="card p-4 shadow-box h-100">
            <h4 class="text-white fw-bold mb-1">Tambah Kandidat Paslon</h4>
            <p class="text-muted small mb-3">Daftarkan pasangan calon ketua dan wakil ketua HIMA baru ke MySQL.</p>
            <form onsubmit="handleCreateCandidate(event)">
              <div class="mb-3">
                <label class="form-label text-muted">Nama Pasangan Calon</label>
                <input type="text" id="cand-name" class="form-control" placeholder="Contoh: Elang & Fani" required>
              </div>
              <div class="mb-3">
                <label class="form-label text-muted">Visi Utama</label>
                <textarea id="cand-vision" class="form-control" rows="2" placeholder="Tuliskan visi kandidat..." required style="resize:none;"></textarea>
              </div>
              <div class="mb-3 mb-4">
                <label class="form-label text-muted">Misi Utama (Satu per baris)</label>
                <textarea id="cand-mission" class="form-control" rows="3" placeholder="1. Misi kesatu&#10;2. Misi kedua" required style="resize:none;"></textarea>
              </div>
              <button type="submit" class="btn btn-cyan w-100 py-2">Daftarkan Kandidat</button>
            </form>
          </div>
        </div>
      </div>

      <!-- User List Card -->
      <div class="card p-4 shadow-box mt-4">
        <h4 class="text-white fw-bold mb-1">Daftar Pemilih Terdaftar & Keikutsertaan</h4>
        <p class="text-muted small mb-3">Memantau nama pemilih tanpa melanggar Anonymous Voting (pilihan kandidat tidak terlihat di users).</p>
        <div class="table-responsive">
          <table class="table table-dark table-hover border-glass">
            <thead>
              <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Email Terdaftar</th>
                <th>Status Partisipasi</th>
                <th>Aksi CRUD</th>
              </tr>
            </thead>
            <tbody id="admin-users-tbody"></tbody>
          </table>
        </div>
      </div>
    </section>
  </main>



  <!-- Custom logic JS -->
  <script src="/js/app.js"></script>
</body>
</html>
