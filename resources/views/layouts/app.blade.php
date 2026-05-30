<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'SecVote - Premium E-Voting Platform')</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Custom Light SaaS CSS -->
  <link rel="stylesheet" href="/css/custom.css">
  <!-- Chart.js for real-time results representation -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <!-- Navigation Navbar -->
  <header class="navbar navbar-expand-lg sticky-top navbar-light">
    <div class="container">
      <a class="navbar-brand logo" href="/">
        🗳️ Sec<span>Vote</span>
      </a>
      
      <!-- Election Status Badge (Voting Open) -->
      <span class="badge bg-emerald-subtle text-emerald fw-bold border border-emerald-subtle rounded-pill px-3 py-1-5 align-middle ms-2 d-none d-sm-inline-flex align-items-center gap-1.5" style="background-color: #ecfdf5; color: #059669; font-size: 0.78rem;">
        <span class="d-inline-block rounded-circle bg-success" style="width: 7px; height: 7px; animation: pulseGlow 1.8s infinite; background-color: #10b981;"></span>
        Voting Open
      </span>
      
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
          @auth
            @if (Auth::user()->role === 'voter')
              <li class="nav-item">
                <a class="nav-link {{ Route::is('voter.dashboard') || Route::is('vote.success') ? 'active' : '' }}" href="{{ route('voter.dashboard') }}">Dashboard Pemilu</a>
              </li>
            @else
              <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin Panel</a>
              </li>
            @endif
          @endauth
        </ul>
        
        <div class="d-flex align-items-center gap-3">
          @auth
          <!-- Notification Bell Trigger -->
          <button class="btn btn-link text-muted p-1 position-relative" style="box-shadow: none;" title="Notifikasi">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
              <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
            </svg>
            <span class="position-absolute top-1 start-80 translate-middle p-1 bg-danger border border-white rounded-circle">
              <span class="visually-hidden">New alerts</span>
            </span>
          </button>

            <!-- User Profile Dropdown -->
            <div class="dropdown ms-2">
              <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="rounded-circle text-white d-flex justify-content-center align-items-center me-2 fw-bold shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #2563EB, #4F46E5);">
                  {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="d-none d-md-block text-start me-1">
                  <strong class="d-block text-dark lh-1 mb-1" style="font-size: 0.88rem;">{{ Auth::user()->name }}</strong>
                  <span class="badge px-2 py-0 rounded-pill" style="font-size:0.65rem; background-color: rgba(37, 99, 235, 0.08); color: #2563EB; border: 1px solid rgba(37, 99, 235, 0.2);">
                    {{ Auth::user()->role === 'admin' ? 'Admin HIMA' : 'Pemilih' }}
                  </span>
                </div>
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" aria-labelledby="userDropdown" style="min-width: 200px;">
                <li><h6 class="dropdown-header text-muted fw-bold">Menu Pengguna</h6></li>
                <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#"><span style="font-size: 1.1rem;">👤</span> Profil Saya</a></li>
                <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#"><span style="font-size: 1.1rem;">⚙️</span> Pengaturan</a></li>
                <li><hr class="dropdown-divider border-light"></li>
                <li>
                  <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item py-2 text-danger fw-bold d-flex align-items-center gap-2"><span style="font-size: 1.1rem;">🚪</span> Keluar</button>
                  </form>
                </li>
              </ul>
            </div>
          @else
            <a href="{{ route('login') }}" class="btn btn-cyan btn-sm px-4">Masuk</a>
          @endauth
        </div>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <main class="container my-5" style="min-height: 70vh;">
    
    <!-- Flash Notifications Alerts -->
    <div id="alert-container">
      @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-box border-glass" role="alert" style="background-color: #ecfdf5; border-color: rgba(16, 185, 129, 0.2); color: #065f46;">
          <span>🔔</span>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-box border-glass" role="alert" style="background-color: #fef2f2; border-color: rgba(239, 68, 68, 0.2); color: #991b1b;">
          <span>⚠️</span>
          <div>{{ session('error') }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
    </div>

    @yield('content')
  </main>

  <!-- Footer -->
  <footer class="text-center py-4 border-top border-glass text-muted small-text mt-5">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <div>
        <strong>Secure E-Voting System</strong> &copy; 2026. Universitas Negeri Surabaya.
      </div>
      <div class="text-muted text-md-end">
        Program Studi Informatika &bull; All Rights Reserved.
      </div>
    </div>
  </footer>



  <!-- Bootstrap 5 Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    @keyframes pulseGlow {
      0% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.3); opacity: 0.5; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2); }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</body>
</html>
