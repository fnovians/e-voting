<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan_Hasil_Suara_PEMIRA_{{ date('Y') }}.pdf</title>
  <style>
    /* Clean PDF/Print Design System & Workspace Print Preview */
    @page {
      size: A4;
      margin: 20mm;
    }
    
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #1E293B;
      background: #F1F5F9; /* Clean slate workspace background on screen */
      margin: 0;
      padding: 0;
      font-size: 11pt;
      line-height: 1.5;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Print Preview Wrapper on Screen */
    .print-preview-container {
      width: 100%;
      min-height: 100vh;
      padding: 30px 15px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* High-Fidelity A4 Paper Sheet Representation */
    .paper-sheet {
      background: #FFFFFF;
      width: 100%;
      max-width: 210mm; /* Strict A4 Width */
      min-height: 297mm; /* Strict A4 Height */
      box-sizing: border-box;
      padding: 20mm;
      border: 1px solid rgba(15, 23, 42, 0.06);
      border-radius: 12px;
      box-shadow: 0 15px 45px rgba(15, 23, 42, 0.08);
      margin: 0 auto;
      position: relative;
    }

    /* Kop Surat (Official Letterhead) Header */
    .kop-surat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 3.5px double #0F172A;
      padding-bottom: 12px;
      margin-bottom: 25px;
      gap: 15px;
    }
    .kop-logo {
      width: 75px;
      height: 75px;
      flex-shrink: 0;
    }
    .kop-logo svg, .kop-logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
    .kop-text-container {
      flex: 1;
      text-align: center;
    }
    .kop-univ {
      font-size: 13pt;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      margin: 0 0 2px 0;
      color: #0F172A;
    }
    .kop-org {
      font-size: 11pt;
      font-weight: 700;
      text-transform: uppercase;
      margin: 0 0 2px 0;
      color: #334155;
    }
    .kop-panitia {
      font-size: 12pt;
      font-weight: 800;
      text-transform: uppercase;
      color: #4F46E5;
      margin: 0 0 4px 0;
    }
    .kop-alamat {
      font-size: 8.5pt;
      color: #64748B;
      margin: 0;
      font-style: italic;
    }

    /* Report Title */
    .report-title-container {
      text-align: center;
      margin-bottom: 25px;
    }
    .report-title {
      font-size: 14pt;
      font-weight: 800;
      text-transform: uppercase;
      margin: 0 0 5px 0;
      letter-spacing: 0.5px;
      color: #0F172A;
    }
    .report-meta {
      font-size: 9.5pt;
      color: #475569;
      margin: 0;
    }
    .report-status-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 6px;
      font-size: 8.5pt;
      font-weight: 700;
      text-transform: uppercase;
      margin-top: 8px;
    }
    .status-active {
      background-color: #DCFCE7;
      color: #15803D;
      border: 1px solid #BBF7D0;
    }
    .status-closed {
      background-color: #FEE2E2;
      color: #B91C1C;
      border: 1px solid #FECACA;
    }

    /* KPI Summary Cards Grid */
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-bottom: 25px;
    }
    .summary-card {
      background: #F8FAFC;
      border: 1px solid #E2E8F0;
      border-radius: 8px;
      padding: 12px;
      text-align: center;
    }
    .summary-label {
      font-size: 8pt;
      text-transform: uppercase;
      color: #64748B;
      font-weight: 600;
      margin-bottom: 4px;
    }
    .summary-value {
      font-size: 13pt;
      font-weight: 800;
      color: #0F172A;
    }

    /* Tables */
    .section-title {
      font-size: 11pt;
      font-weight: 800;
      color: #0F172A;
      border-bottom: 1.5px solid #E2E8F0;
      padding-bottom: 6px;
      margin-bottom: 12px;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      margin-bottom: 25px;
      -webkit-overflow-scrolling: touch;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 500px; /* Ensures text has room in mobile scroll view */
    }
    th {
      background: #0F172A;
      color: #FFFFFF;
      font-size: 9pt;
      font-weight: 700;
      text-transform: uppercase;
      padding: 10px 12px;
      text-align: left;
      border: 1px solid #0F172A;
    }
    td {
      padding: 10px 12px;
      font-size: 9.5pt;
      border: 1px solid #E2E8F0;
    }
    tr:nth-child(even) td {
      background-color: #F8FAFC;
    }
    .text-center {
      text-align: center;
    }
    .text-right {
      text-align: right;
    }
    .fw-bold {
      font-weight: 700;
    }

    /* Cryptographic Trust Seal Box */
    .trust-container {
      background: #F0FDF4;
      border: 1px dashed #4ade80;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 30px;
    }
    .trust-header {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 800;
      color: #166534;
      font-size: 9.5pt;
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    .trust-details {
      font-size: 8.5pt;
      color: #1E3A8A;
      margin: 0;
      line-height: 1.4;
    }

    /* Signatures Section */
    .signature-container {
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
      gap: 20px;
      page-break-inside: avoid;
    }
    .signature-box {
      width: 220px;
      text-align: center;
      font-size: 9.5pt;
    }
    .sig-space {
      height: 65px;
    }
    .sig-name {
      font-weight: 700;
      text-decoration: underline;
      margin-bottom: 2px;
    }
    .sig-title {
      font-size: 8.5pt;
      color: #64748B;
    }

    /* Print Controls & Styling */
    .print-controls {
      background: #FFFFFF;
      padding: 12px 24px;
      border: 1px solid rgba(15, 23, 42, 0.08);
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      width: 100%;
      max-width: 210mm;
      box-sizing: border-box;
      margin-bottom: 20px;
    }
    .print-title {
      font-weight: 800;
      font-size: 1rem;
      color: #0F172A;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .control-actions {
      display: flex;
      gap: 10px;
    }
    .print-btn {
      background: #4F46E5;
      color: #FFFFFF;
      border: none;
      padding: 8px 20px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.85rem;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
      transition: all 0.2s;
    }
    .print-btn:hover {
      background: #4338CA;
    }
    .back-btn {
      background: #FFFFFF;
      color: #475569;
      border: 1px solid #CBD5E1;
      padding: 8px 20px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.2s;
    }
    .back-btn:hover {
      background: #F8FAFC;
      color: #0F172A;
    }

    /* ==========================================================================
       RESPONSIVE MEDIA QUERIES (SCREEN ONLY VIEWPORT ADAPTABILITY)
       ========================================================================== */
    @media (max-width: 768px) {
      .print-preview-container {
        padding: 10px 5px;
      }
      .print-controls {
        flex-direction: column;
        align-items: stretch;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
      }
      .print-title {
        justify-content: center;
        font-size: 0.95rem;
        margin-bottom: 8px;
      }
      .control-actions {
        justify-content: center;
      }
      .paper-sheet {
        padding: 12mm;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      }
    }

    @media (max-width: 600px) {
      .paper-sheet {
        padding: 15px; /* Squeeze margins on tiny displays */
      }
      .kop-surat-header {
        flex-direction: column;
        text-align: center;
        gap: 12px;
        padding-bottom: 15px;
      }
      .kop-logo {
        margin: 0 auto;
        width: 65px;
        height: 65px;
      }
      /* Hide right accent emblem to maximize space */
      .kop-surat-header .kop-logo:last-child {
        display: none;
      }
      .kop-univ {
        font-size: 11pt;
      }
      .kop-org {
        font-size: 9.5pt;
      }
      .kop-panitia {
        font-size: 10pt;
      }
      .kop-alamat {
        font-size: 7.5pt;
      }
      .report-title {
        font-size: 12pt;
      }
      .summary-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
      }
      .summary-value {
        font-size: 11pt;
      }
      .signature-container {
        flex-direction: column;
        align-items: center;
        gap: 35px;
        margin-top: 35px;
      }
      .signature-box {
        width: 100%;
        max-width: 250px;
      }
      .sig-space {
        height: 45px;
      }
    }

    /* ==========================================================================
       STRICT PRINT SPECIFICATIONS (STRIPS BORDERS & FORCE LAYOUT STABILITY)
       ========================================================================== */
    @media print {
      body {
        background: #FFFFFF !important;
        color: #000000 !important;
      }
      .print-preview-container {
        padding: 0 !important;
        background: none !important;
        min-height: auto !important;
      }
      .print-controls {
        display: none !important;
      }
      .paper-sheet {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        min-height: auto !important;
        background: #FFFFFF !important;
      }
      .table-responsive {
        overflow-x: visible !important;
      }
      table {
        min-width: auto !important;
      }
      /* Ensure Kop Surat header layout remains clean and structured on paper */
      .kop-surat-header {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 15px !important;
      }
      .kop-surat-header .kop-logo:last-child {
        display: block !important;
      }
      .summary-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
      }
      .signature-container {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
      }
    }
  </style>
</head>
<body>

  <div class="print-preview-container">
    
    <!-- Print Preview Floating Tool Deck -->
    <div class="print-controls">
      <div class="print-title">
        <span>👁️</span> Pratinjau Cetak Laporan
      </div>
      <div class="control-actions">
        <button class="back-btn" onclick="window.close()">Tutup</button>
        <button class="print-btn" onclick="window.print()">Cetak / Simpan PDF</button>
      </div>
    </div>

    <!-- Paper Sheet Document Container -->
    <div class="paper-sheet">
      
      <!-- Kop Surat Header -->
      <div class="kop-surat-header">
        <div class="kop-logo">
          <!-- Official UNESA Gold Crest Logo -->
          <img src="{{ asset('assets/img/logo-unesa.png') }}" alt="Logo UNESA">
        </div>
        <div class="kop-text-container">
          <h1 class="kop-univ">Universitas Negeri Surabaya</h1>
          <h2 class="kop-org">Himpunan Mahasiswa Informatika (HIMA IF)</h2>
          <h3 class="kop-panitia">Panitia Pemilihan Raya (PEMIRA)</h3>
          <p class="kop-alamat">Sekretariat: Gedung A10 Kampus Unesa Ketintang, Surabaya | Email: pemira@mhs.unesa.ac.id</p>
        </div>
        <div class="kop-logo">
          <!-- Official PEMIRA SecVote Application Logo -->
          <img src="{{ asset('assets/img/logo-app.png') }}" alt="Logo PEMIRA">
        </div>
      </div>

      <!-- Title of the Report -->
      <div class="report-title-container">
        <h2 class="report-title">Laporan Hasil Perolehan Suara Resmi</h2>
        <p class="report-meta">
          Dicetak pada: <strong>{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('d F Y - H:i') }} WIB</strong>
        </p>
        <span class="report-status-badge {{ $votingOpen ? 'status-active' : 'status-closed' }}">
          Status Pemilihan: {{ $votingOpen ? 'Pemilihan Aktif (Terbuka)' : 'Pemilihan Selesai (Ditutup)' }}
        </span>
      </div>

      <!-- Helicopter View Cards Row -->
      <div class="summary-grid">
        <div class="summary-card">
          <div class="summary-label">Total Pemilih</div>
          <div class="summary-value">{{ $turnout['total'] }}</div>
        </div>
        <div class="summary-card">
          <div class="summary-label">Suara Masuk</div>
          <div class="summary-value">{{ $turnout['voted'] }}</div>
        </div>
        <div class="summary-card">
          <div class="summary-label">Partisipasi</div>
          <div class="summary-value">{{ $turnout['percentage'] }}%</div>
        </div>
        <div class="summary-card">
          <div class="summary-label">Total Paslon</div>
          <div class="summary-value">{{ $reportData->count() }}</div>
        </div>
      </div>

      <!-- Candidate Standings Table -->
      <h3 class="section-title">Hasil Penghitungan Suara Kandidat</h3>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th class="text-center" style="width: 80px;">Peringkat</th>
              <th>Nama Calon / Pasangan Calon (Paslon)</th>
              <th>Kategori Pemilihan</th>
              <th class="text-right" style="width: 130px;">Jumlah Suara</th>
              <th class="text-right" style="width: 130px;">Persentase</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reportData as $index => $candidate)
              <tr>
                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $candidate['name'] }}</td>
                <td>{{ $candidate['category'] }}</td>
                <td class="text-right fw-bold">{{ $candidate['votes'] }} Suara</td>
                <td class="text-right fw-bold" style="color: #4F46E5;">{{ $candidate['percentage'] }}%</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data kandidat atau suara masuk.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Cryptographic Verification Shield Seal -->
      <div class="trust-container">
        <div class="trust-header">
          <!-- SVG Secure Badge -->
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-fill-check" viewBox="0 0 16 16" style="color: #166534;">
            <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99 1.616 2.106 3.53 3.098 4.09 3.34a.5.5 0 0 0 .596-.134c.56-.242 2.474-1.234 4.09-3.34 1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 55 55 0 0 0-2.887-.87C9.843.266 8.69 0 8 0m2.146 5.146a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793z"/>
          </svg>
          Sertifikasi Integritas Kriptografis
        </div>
        <p class="trust-details">
          Laporan ini digenerasi langsung oleh sistem e-voting berbasis kriptografi dengan enkripsi <strong>AES-256-GCM</strong>. Setiap suara yang masuk telah diverifikasi keasliannya dan terantai secara aman menggunakan enkripsi asimetris tingkat lanjut. Tanda tangan digital ini membuktikan bahwa database bebas dari manipulasi (Zero Database Alteration) dan audit log integritas berada dalam status valid/aman.
        </p>
      </div>

      <!-- Official Signature Blocks -->
      <div class="signature-container">
        <div class="signature-box">
          <p>Mengetahui,</p>
          <p class="fw-bold" style="margin-top:-5px;">Ketua Panitia PEMIRA {{ date('Y') }}</p>
          <div class="sig-space"></div>
          <p class="sig-name">M. Farchan Al-Rasyid</p>
          <p class="sig-title">NIM. 23051204012</p>
        </div>
        <div class="signature-box">
          <p>Disahkan di Surabaya,</p>
          <p class="fw-bold" style="margin-top:-5px;">Sekretaris PEMIRA {{ date('Y') }}</p>
          <div class="sig-space"></div>
          <p class="sig-name">Devina Salsabila</p>
          <p class="sig-title">NIM. 23051204085</p>
        </div>
      </div>

    </div>
  </div>

  <script>
    // Automatic trigger print dialog once loaded on screen
    window.onload = function() {
      // Small timeout to allow full rendering completion
      setTimeout(function() {
        window.print();
      }, 500);
    }
  </script>
</body>
</html>
