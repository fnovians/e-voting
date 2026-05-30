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
  <script>
    // Immediate Sidebar State Application to prevent visual layout shifts on load
    (function() {
      const collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
      if (collapsed) {
        document.write('<style>.sidebar-wrapper { width: 80px !important; } .main-content { margin-left: 80px !important; width: calc(100% - 80px) !important; } .logo-text, .nav-label, .sidebar-footer .text-truncate { display: none !important; } .sidebar-footer { padding: 1rem 0.5rem !important; display: flex !important; justify-content: center !important; } .sidebar-footer .dropdown-toggle { justify-content: center !important; } .sidebar-footer .rounded-circle { margin-right: 0 !important; } .sidebar-menu .nav-link { justify-content: center !important; padding: 0.75rem !important; } .sidebar-menu .nav-icon { margin-right: 0 !important; } .sidebar-header { padding: 1rem 0.5rem !important; flex-direction: column !important; gap: 1rem !important; justify-content: center !important; } .sidebar-header .logo { justify-content: center !important; } .sidebar-header #collapseSidebarBtn { margin-top: 0.25rem !important; }</style>');
      }
    })();
  </script>
  <!-- Chart.js for real-time results representation -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="@guest auth-page @endguest">

  @guest
    <main style="min-height: 100vh; width: 100vw; display: flex; align-items: center; justify-content: center; padding: 2rem; position: relative; z-index: 2;">
      @yield('content')
    </main>
  @endguest

  @auth
    <div class="app-layout">
      <!-- Sidebar Overlay for Mobile -->
      <div class="sidebar-overlay" id="sidebarOverlay"></div>

      <!-- Sidebar Wrapper -->
      <aside class="sidebar-wrapper" id="sidebarWrapper">
        <div class="sidebar-header">
          <a class="logo text-decoration-none d-inline-flex align-items-center gap-2" href="/">
            @if(file_exists(public_path('assets/img/logo.svg')))
              <img src="{{ asset('assets/img/logo.svg') }}" alt="SecVote Logo" style="height: 40px; object-fit: contain;">
            @elseif(file_exists(public_path('assets/img/logo.png')))
              <img src="{{ asset('assets/img/logo.png') }}" alt="SecVote Logo" style="height: 40px; object-fit: contain;">
            @elseif(file_exists(public_path('assets/img/logo.jpg')))
              <img src="{{ asset('assets/img/logo.jpg') }}" alt="SecVote Logo" style="height: 40px; object-fit: contain;">
            @elseif(file_exists(public_path('assets/img/logo.jpeg')))
              <img src="{{ asset('assets/img/logo.jpeg') }}" alt="SecVote Logo" style="height: 40px; object-fit: contain;">
            @else
              🗳️
            @endif
            <span class="logo-text" style="font-family: var(--font-title); font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px; background: var(--grad-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; color: #4F46E5;">
              PEMIRA
            </span>
          </a>
          <div class="d-flex align-items-center gap-2">
            <!-- Collapse Button (Desktop only) -->
            <button class="btn btn-link text-muted p-0 d-none d-lg-block" id="collapseSidebarBtn" style="box-shadow: none;" title="Toggle Sidebar">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-indent" viewBox="0 0 16 16" id="collapseIcon">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6 2h8a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 4.5 12.5v-2a.5.5 0 0 1 1 0z"/>
                <path fill-rule="evenodd" d="M9.854 8.354a.5.5 0 0 0 0-.708L7.207 5a.5.5 0 0 0-.707.707L8.793 8l-2.293 2.293a.5.5 0 0 0 .707.707z"/>
              </svg>
            </button>
            <!-- Close button on Mobile -->
            <button class="btn btn-link text-muted d-lg-none p-0" id="closeSidebarBtn" style="box-shadow: none;">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
              </svg>
            </button>
          </div>
        </div>

        <nav class="sidebar-menu">
          @if (Auth::user()->role === 'voter')
            <a class="nav-link {{ Route::is('voter.dashboard') || Route::is('vote.success') ? 'active' : '' }}" href="{{ route('voter.dashboard') }}">
              <span class="nav-icon">🗳️</span> <span class="nav-label">Dashboard Pemilu</span>
            </a>
          @else
            <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
              <span class="nav-icon">⚙️</span> <span class="nav-label">Dashboard Admin</span>
            </a>
          @endif

          <a class="nav-link {{ Route::is('research.lab') ? 'active' : '' }}" href="{{ route('research.lab') }}">
            <span class="nav-icon">🔬</span> <span class="nav-label">Lab Kriptografi</span>
          </a>

          <a class="nav-link {{ Route::is('security.lab') ? 'active' : '' }}" href="{{ route('security.lab') }}">
            <span class="nav-icon">🛡️</span> <span class="nav-label">Lab Injeksi SQL</span>
          </a>
        </nav>

        <div class="sidebar-footer dropdown">
          <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle w-100" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="outline: none; box-shadow: none;">
            <div class="rounded-circle text-white d-flex justify-content-center align-items-center me-2 fw-bold shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #4F46E5, #8B5CF6); flex-shrink: 0;">
              {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="text-start nav-label text-truncate" style="min-width: 0; flex-grow: 1;">
              <strong class="d-block text-dark text-truncate lh-1 mb-1" style="font-size: 0.85rem;" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</strong>
              <div class="d-flex align-items-center gap-1 flex-wrap">
                <span class="badge px-2 py-0.5 rounded-pill" style="font-size:0.6rem; background-color: rgba(79, 70, 229, 0.08); color: #4F46E5; border: 1px solid rgba(79, 70, 229, 0.2);">
                  {{ Auth::user()->role === 'admin' ? 'Admin HIMA' : 'Pemilih' }}
                </span>
                <span class="badge px-1.5 py-0.5 rounded-pill" style="font-size:0.6rem; background-color: rgba(34, 197, 94, 0.08); color: #22C55E; border: 1px solid rgba(34, 197, 94, 0.2);">
                  🟢 Online
                </span>
              </div>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-dark shadow-sm border-0" aria-labelledby="adminDropdown" style="border-radius: 12px; font-size: 0.85rem; background: #0F172A; z-index: 1060;">
            <li><a class="dropdown-item py-2 d-flex align-items-center gap-2" href="#" onclick="switchView('dashboard')"><span>👤</span> Profil Saya</a></li>
            <li><hr class="dropdown-divider bg-secondary"></li>
            <li>
              <button type="button" class="dropdown-item text-danger py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <span>🚪</span> Keluar
              </button>
            </li>
          </ul>
        </div>
      </aside>

      <!-- Main Content Area -->
      <main class="main-content">
        <!-- Mobile Top Header (only visible on mobile/tablet) -->
        <header class="main-content-header">
          <button class="miniburger-btn" id="miniburgerBtn" title="Toggle Menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
            </svg>
          </button>
          
          <span class="logo fs-5 d-inline-flex align-items-center">
            @if(file_exists(public_path('assets/img/logo.svg')))
              <img src="{{ asset('assets/img/logo.svg') }}" alt="SecVote Logo" style="height: 30px; object-fit: contain;">
            @elseif(file_exists(public_path('assets/img/logo.png')))
              <img src="{{ asset('assets/img/logo.png') }}" alt="SecVote Logo" style="height: 30px; object-fit: contain;">
            @elseif(file_exists(public_path('assets/img/logo.jpg')))
              <img src="{{ asset('assets/img/logo.jpg') }}" alt="SecVote Logo" style="height: 30px; object-fit: contain;">
            @elseif(file_exists(public_path('assets/img/logo.jpeg')))
              <img src="{{ asset('assets/img/logo.jpeg') }}" alt="SecVote Logo" style="height: 30px; object-fit: contain;">
            @else
              🗳️ Sec<span>Vote</span>
            @endif
          </span>
          
          <!-- Small Mobile User Profile Avatar -->
          <div class="rounded-circle text-white d-flex justify-content-center align-items-center fw-bold shadow-sm" style="width: 32px; height: 32px; background: linear-gradient(135deg, #2563EB, #4F46E5); font-size: 0.85rem;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
          </div>
        </header>

        <!-- Main Container Content -->
        <div class="main-container-wrapper">
          <!-- Flash Notifications Alerts -->
          <div id="alert-container">
            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-box border-glass mb-4" role="alert" style="background-color: #ecfdf5; border-color: rgba(16, 185, 129, 0.2); color: #065f46; border-radius: 12px;">
                <span>🔔</span>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-box border-glass mb-4" role="alert" style="background-color: #fef2f2; border-color: rgba(239, 68, 68, 0.2); color: #991b1b; border-radius: 12px;">
                <span>⚠️</span>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
          </div>

          @yield('content')
        </div>

        <!-- Footer inside main content -->
        <footer class="text-center py-4 border-top border-glass text-muted small-text mt-auto">
          <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div>
              <strong>Secure E-Voting System</strong> &copy; 2026. Universitas Negeri Surabaya.
            </div>
            <div class="text-muted text-md-end">
              Program Studi Informatika &bull; All Rights Reserved.
            </div>
          </div>
        </footer>
      </main>
    </div>
  @endauth

  @if (session('otp'))
    <!-- Simulated Smartphone widget for OTP (Light HIG theme) -->
    <div id="otp-email-widget">
      <div class="email-widget-header px-3 py-2 d-flex align-items-center justify-content-between">
        <span class="fw-bold text-dark">Simulasi Gmail Inbox 📱</span>
        <span class="badge rounded-pill bg-danger border border-white" style="font-size: 0.65rem;">Baru</span>
      </div>
      <div class="email-widget-body p-3">
        <h6 class="text-dark fw-bold mb-1" style="font-size: 0.85rem; font-weight: 700;">Dari: evoting@hima.univ.ac.id</h6>
        <p class="text-muted small mb-2" style="font-size: 0.78rem;">Kepada: Anda</p>
        <hr class="my-2 border-light">
        <div class="text-center py-2">
          <span class="text-muted small d-block mb-1">Kode OTP Verifikasi Anda:</span>
          <div class="monospace-cyan fs-5 fw-extrabold px-3 py-1.5 d-inline-block rounded-3" id="email-widget-code-box" style="background: rgba(37, 99, 235, 0.05); color: var(--accent-blue); letter-spacing: 2px;">
            {{ session('otp') }}
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Bootstrap 5 Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar Responsive Toggle Handler -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const miniburgerBtn = document.getElementById('miniburgerBtn');
      const closeSidebarBtn = document.getElementById('closeSidebarBtn');
      const collapseSidebarBtn = document.getElementById('collapseSidebarBtn');
      const sidebarWrapper = document.getElementById('sidebarWrapper');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      const mainContent = document.querySelector('.main-content');

      // Apply initial collapse state to DOM on load if set in localStorage
      if (localStorage.getItem('sidebar-collapsed') === 'true') {
        if (sidebarWrapper) sidebarWrapper.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('collapsed');
      }

      // Desktop Sidebar Collapse Toggle click handler
      if (collapseSidebarBtn && sidebarWrapper) {
        collapseSidebarBtn.addEventListener('click', () => {
          sidebarWrapper.classList.toggle('collapsed');
          if (mainContent) mainContent.classList.toggle('collapsed');
          const isCollapsed = sidebarWrapper.classList.contains('collapsed');
          localStorage.setItem('sidebar-collapsed', isCollapsed);
        });
      }

      if (miniburgerBtn && sidebarWrapper && sidebarOverlay) {
        miniburgerBtn.addEventListener('click', () => {
          sidebarWrapper.classList.add('active');
          sidebarOverlay.classList.add('active');
        });
      }

      if (closeSidebarBtn && sidebarWrapper && sidebarOverlay) {
        closeSidebarBtn.addEventListener('click', () => {
          sidebarWrapper.classList.remove('active');
          sidebarOverlay.classList.remove('active');
        });
      }

      if (sidebarOverlay && sidebarWrapper) {
        sidebarOverlay.addEventListener('click', () => {
          sidebarWrapper.classList.remove('active');
          sidebarOverlay.classList.remove('active');
        });
      }
    });
  </script>

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); background: rgba(15, 23, 42, 0.3); z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content" style="border-radius: 24px; border: 1px solid rgba(15, 23, 42, 0.08); box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15); background: #FFFFFF; overflow: hidden; animation: modalEntrance 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;">
        <div class="modal-body text-center p-4">
          <div class="mx-auto mb-3 d-flex align-items-center justify-content-center animate-glow" style="background: rgba(239, 68, 68, 0.08); color: #EF4444; width: 64px; height: 64px; border-radius: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
              <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
            </svg>
          </div>
          <h4 class="fw-bold text-dark mb-2">Konfirmasi Keluar</h4>
          <p class="text-muted small mb-4" style="line-height: 1.45;">Apakah Anda yakin ingin keluar dari sistem E-Voting? Pilihan suara Anda yang belum selesai dikirimkan tidak akan tersimpan.</p>
          
          <div class="d-flex gap-3">
            <button type="button" class="btn btn-light w-100 py-2.5 fw-semibold" data-bs-dismiss="modal" style="border-radius: 12px; border: 1px solid rgba(15, 23, 42, 0.08); font-size: 0.9rem; transition: all 0.2s ease;">Batal</button>
            <form action="{{ route('logout') }}" method="POST" class="w-100 m-0">
              @csrf
              <button type="submit" class="btn btn-danger w-100 py-2.5 fw-semibold" style="border-radius: 12px; background: #EF4444; border: none; font-size: 0.9rem; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">Keluar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    @keyframes pulseGlow {
      0% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.3); opacity: 0.5; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2); }
      100% { transform: scale(1); opacity: 1; }
    }
    @keyframes modalEntrance {
      0% {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
      }
      100% {
        opacity: 1;
        transform: scale(1) translateY(0);
      }
    }
    .animate-glow {
      animation: pulseLogout 2s infinite;
    }
    @keyframes pulseLogout {
      0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.2); }
      70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
      100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
  </style>

  @if (session('success') && (str_contains(session('success'), 'masuk') || str_contains(session('success'), 'aktif') || str_contains(session('success'), 'Berhasil') || str_contains(session('success'), 'OTP')))
    <!-- Successful Login Notification Modal -->
    <div class="modal fade" id="successLoginModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); background: rgba(15, 23, 42, 0.4); z-index: 99999;">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content text-center p-4" style="border-radius: 24px; border: 2.5px solid #22C55E; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2), 0 0 25px rgba(34, 197, 94, 0.15); background: #FFFFFF; animation: modalEntrance 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;">
          <div class="modal-body p-3">
            
            <!-- Animated Checkmark Container -->
            <div class="checkmark-wrapper-login mx-auto mb-4">
              <svg class="checkmark-svg-login" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark-circle-login" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check-login" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
              </svg>
            </div>
            
            <h4 class="fw-extrabold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.35rem;">Login Berhasil!</h4>
            <p class="text-muted small mb-4" style="line-height: 1.5; font-size: 0.85rem;">{{ session('success') }}</p>
            
            <button type="button" class="btn w-100 py-2.5 fw-semibold border-0" data-bs-dismiss="modal" style="border-radius: 12px; background: #22C55E; color: #FFFFFF !important; box-shadow: 0 8px 20px rgba(34, 197, 94, 0.25); font-size: 0.9rem;">
              Mulai
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const myModal = new bootstrap.Modal(document.getElementById('successLoginModal'));
        myModal.show();
      });
    </script>

    <style>
      /* Self-contained Premium Animated Checkmark SVG Styling for Login Success */
      .checkmark-wrapper-login {
        width: 80px;
        height: 80px;
        position: relative;
        animation: fadeWrapperLogin 2.8s ease-in-out infinite;
      }
      .checkmark-svg-login {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: block;
        stroke-width: 3;
        stroke: #22C55E;
        stroke-miterlimit: 10;
        box-shadow: inset 0px 0px 0px #22C55E;
        animation: fillCheckmarkLogin 2.8s ease-in-out infinite, scaleCheckmarkLogin 2.8s ease-in-out infinite;
      }
      .checkmark-circle-login {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 3;
        stroke-miterlimit: 10;
        stroke: #22C55E;
        fill: none;
        animation: strokeCircleLogin 2.8s cubic-bezier(0.65, 0, 0.45, 1) infinite;
      }
      .checkmark-check-login {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        stroke-width: 4;
        stroke: #FFFFFF;
        animation: strokeCheckLogin 2.8s cubic-bezier(0.65, 0, 0.45, 1) infinite;
      }

      @keyframes fadeWrapperLogin {
        0%, 5% { opacity: 0; }
        15%, 80% { opacity: 1; }
        90%, 100% { opacity: 0; }
      }
      @keyframes strokeCircleLogin {
        0% { stroke-dashoffset: 166; }
        5% { stroke-dashoffset: 166; }
        35%, 80% { stroke-dashoffset: 0; }
        95%, 100% { stroke-dashoffset: 166; }
      }
      @keyframes strokeCheckLogin {
        0%, 30% { stroke-dashoffset: 48; }
        55%, 80% { stroke-dashoffset: 0; }
        95%, 100% { stroke-dashoffset: 48; }
      }
      @keyframes fillCheckmarkLogin {
        0%, 25% { box-shadow: inset 0px 0px 0px 0px #22C55E; }
        45%, 80% { box-shadow: inset 0px 0px 0px 40px #22C55E; }
        95%, 100% { box-shadow: inset 0px 0px 0px 0px #22C55E; }
      }
      @keyframes scaleCheckmarkLogin {
        0%, 40% { transform: scale(1); }
        50% { transform: scale(1.08); }
        60%, 80% { transform: scale(1); }
        95%, 100% { transform: scale(1); }
      }
    </style>
  @endif
</body>
</html>
