<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Shows Login view
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin' 
                ? redirect()->route('admin.dashboard')
                : redirect()->route('voter.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Shows Registration view
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('voter.dashboard');
        }
        return view('auth.register');
    }

    /**
     * Shows OTP code entry view
     */
    public function showOtp()
    {
        if (Auth::check()) {
            return redirect()->route('voter.dashboard');
        }
        if (!session()->has('temp_user_id')) {
            return redirect()->route('login')->with('error', 'Sesi login tidak ditemukan. Silakan masuk ulang.');
        }
        return view('auth.otp');
    }

    /**
     * Handles authenticating voter/admin
     */
    public function login(Request $request)
    {
        $nim = $request->input('nim');
        $password = $request->input('password');
        $mitigated = $request->boolean('mitigated', true);
        $ip = $request->ip();

        if (empty($nim) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'NIM dan password harus diisi.');
        }

        if ($mitigated) {
            // 1. SAFE (Prepared statement-like Eloquent match)
            $sqlQuery = "SELECT * FROM users WHERE nim = ?;\n-- Parameters: [ \"$nim\", \"bcrypt(password)\" ]";
            $user = User::where('nim', $nim)->first();

            if (!$user) {
                AuditLog::create([
                    'event' => 'LOGIN_FAIL',
                    'details' => "Failed secure login attempt for NIM: $nim (User not found)",
                    'ip_address' => $ip
                ]);

                return redirect()->back()->withInput()->with([
                    'error' => 'Username/NIM atau password salah.',
                    'sqlQuery' => $sqlQuery,
                    'analysis' => 'Mitigasi Berhasil: Kueri terproteksi. Karakter khusus dalam input diperlakukan sebagai literal belaka. Pencarian nihil.'
                ]);
            }

            if (Hash::check($password, $user->password)) {
                // Generate 6-digit OTP code
                $otp = strval(rand(100000, 999999));
                $user->otp_code = $otp;
                $user->otp_expires_at = now()->addMinutes(5);
                $user->save();

                // Temp session storage
                session(['temp_user_id' => $user->id]);

                AuditLog::create([
                    'event' => 'LOGIN_CREDENTIALS_VALID',
                    'details' => "Valid credentials (mitigated) for NIM: {$user->nim}. OTP issued.",
                    'ip_address' => $ip
                ]);

                // Redirect to OTP page with data for the simulated smartphone notification widget
                return redirect()->route('otp')->with([
                    'success' => 'Kredensial valid. OTP telah dikirim!',
                    'otp' => $otp,
                    'temp_email' => $user->email
                ]);
            } else {
                AuditLog::create([
                    'event' => 'LOGIN_FAIL',
                    'details' => "Failed secure login attempt for NIM: {$user->nim} (Wrong password)",
                    'ip_address' => $ip
                ]);

                return redirect()->back()->withInput()->with([
                    'error' => 'Username/NIM atau password salah.',
                    'sqlQuery' => $sqlQuery,
                    'analysis' => 'Mitigasi Berhasil: Kueri berjalan aman, namun hash sandi Bcrypt tidak cocok.'
                ]);
            }
        } else {
            // 2. VULNERABLE (SQL Injection concatenated bypass)
            $sqlQuery = "SELECT * FROM users WHERE nim = '$nim' AND role = 'voter'";
            
            try {
                $results = DB::select($sqlQuery);

                if (count($results) > 0) {
                    $dbUser = $results[0];
                    $user = User::find($dbUser->id);

                    // Bypasses password check
                    Auth::login($user);

                    AuditLog::create([
                        'event' => 'SQL_INJECTION_SUCCESS',
                        'details' => "SQL Injection successful! Injected NIM: \"$nim\". Session active as: {$user->name}.",
                        'ip_address' => $ip
                    ]);

                    $targetRoute = $user->role === 'admin' ? 'admin.dashboard' : 'voter.dashboard';
                    return redirect()->route($targetRoute)->with([
                        'success' => "Bypass SQL Injection Berhasil! Selamat datang {$user->name}.",
                        'sqlQuery' => $sqlQuery,
                        'analysis' => "SERANGAN BERHASIL! 😱\n\nKueri SQL Anda digabungkan langsung dan dieksekusi di MySQL:\n$sqlQuery\n\nKondisi OR '1'='1' mengevaluasi kueri menjadi TRUE untuk setiap baris. Database MySQL mengembalikan daftar seluruh pengguna, dan sistem langsung meloginkan baris pertama yang ditemukan, yaitu: **{$user->name}** tanpa memeriksa password. Sistem berhasil ditembus!"
                    ]);
                } else {
                    AuditLog::create([
                        'event' => 'LOGIN_FAIL',
                        'details' => "Failed vulnerable login attempt with input NIM: \"$nim\"",
                        'ip_address' => $ip
                    ]);

                    return redirect()->back()->withInput()->with([
                        'error' => 'Username/NIM atau password salah.',
                        'sqlQuery' => $sqlQuery,
                        'analysis' => 'Login Gagal: Meskipun rentan, kueri database kosong karena tidak ada input literal atau bypass SQL Injection yang valid.'
                    ]);
                }
            } catch (\Exception $e) {
                AuditLog::create([
                    'event' => 'SQL_ERROR',
                    'details' => "Database syntax error triggered by NIM input: \"$nim\". Error: " . $e->getMessage(),
                    'ip_address' => $ip
                ]);

                return redirect()->back()->withInput()->with([
                    'error' => 'Database syntax error!',
                    'sqlQuery' => $sqlQuery,
                    'analysis' => "DATABASE SYNTAX ERROR: " . $e->getMessage() . "\n\nInput Anda merusak struktur sintaksis kueri SQL di server database MySQL. Ciri khas kerentanan SQL Injection!"
                ]);
            }
        }
    }

    /**
     * Verifies the 6-digit OTP code to complete login
     */
    public function verifyOtp(Request $request)
    {
        $otp = $request->input('otp');
        $tempUserId = session('temp_user_id');
        $ip = $request->ip();

        if (!$tempUserId) {
            return redirect()->route('login')->with('error', 'Sesi login tidak ditemukan. Silakan login ulang.');
        }

        $user = User::find($tempUserId);

        if (!$user || !$user->otp_code) {
            return redirect()->route('login')->with('error', 'Sesi login kedaluwarsa. Silakan masuk ulang.');
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            $user->otp_code = null;
            $user->save();
            session()->forget('temp_user_id');

            AuditLog::create([
                'event' => 'OTP_EXPIRED',
                'details' => "OTP expired for user NIM: {$user->nim}",
                'ip_address' => $ip
            ]);

            return redirect()->route('login')->with('error', 'Kode OTP telah kedaluwarsa. Silakan login ulang.');
        }

        if ($otp === $user->otp_code) {
            $otpCode = $user->otp_code;
            
            // Clear OTP
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            // Fully login
            Auth::login($user);
            session()->forget('temp_user_id');

            AuditLog::create([
                'event' => 'OTP_VERIFIED',
                'details' => "Successful double factor OTP verification for NIM: {$user->nim}",
                'ip_address' => $ip
            ]);

            $targetRoute = $user->role === 'admin' ? 'admin.dashboard' : 'voter.dashboard';
            return redirect()->route($targetRoute)->with('success', 'Verifikasi OTP berhasil! Sesi aktif.');
        } else {
            AuditLog::create([
                'event' => 'OTP_FAILED',
                'details' => "Failed OTP entry attempt for user NIM: {$user->nim}",
                'ip_address' => $ip
            ]);

            // Flash OTP credentials again to keep simulated widget populated
            return redirect()->back()->with([
                'error' => 'Kode OTP salah. Silakan periksa kembali.',
                'otp' => $user->otp_code,
                'temp_email' => $user->email
            ]);
        }
    }

    /**
     * Standard register voter handler
     */
    public function register(Request $request)
    {
        $nim = $request->input('nim');
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if (empty($nim) || empty($name) || empty($email) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Semua kolom registrasi harus diisi.');
        }

        if (User::where('nim', $nim)->exists()) {
            return redirect()->back()->withInput()->with('error', 'NIM sudah terdaftar.');
        }

        if (User::where('email', $email)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar.');
        }

        $user = User::create([
            'nim' => $nim,
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'voter',
            'has_voted' => false
        ]);

        AuditLog::create([
            'event' => 'USER_REGISTERED',
            'details' => "Voter account created for NIM: $nim, Name: $name",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('login')->with('success', 'Registrasi pemilih berhasil! Silakan masuk menggunakan NIM Anda.');
    }

    /**
     * SQL Injection sandbox test engine (avoids logging out the active researcher session)
     */
    public function testSqlInjection(Request $request)
    {
        $nim = $request->input('nim');
        $password = $request->input('password');
        $mitigated = $request->boolean('mitigated', true);
        $ip = $request->ip();

        if ($mitigated) {
            $sqlQuery = "SELECT * FROM users WHERE nim = ? AND passwordHash = ?;\n-- Parameters: [ \"$nim\", \"bcrypt(password)\" ]";
            
            $user = User::where('nim', $nim)->first();

            if (!$user) {
                $success = false;
                $message = 'Username/NIM atau password salah.';
                $analysis = 'Mitigasi Berhasil: Kueri terproteksi dengan parameterisasi. Celah bypass terblokir secara aman.';
            } else {
                $success = Hash::check($password, $user->password);
                $message = $success ? 'Login Berhasil!' : 'Username/NIM atau password salah.';
                $analysis = $success 
                    ? 'Mitigasi Berhasil: Parameter input valid dan dicocokkan dengan aman.' 
                    : 'Mitigasi Berhasil: NIM ditemukan, namun kueri hash password tidak cocok.';
            }
        } else {
            $sqlQuery = "SELECT * FROM users WHERE nim = '$nim' AND role = 'voter'";
            
            try {
                $results = DB::select($sqlQuery);

                if (count($results) > 0) {
                    $dbUser = $results[0];
                    $success = true;
                    $message = "Login Berhasil! (Bypass SQL Injection)";
                    $analysis = "SERANGAN BERHASIL! 😱\n\nKueri SQL Anda digabungkan langsung dan dieksekusi di MySQL:\n$sqlQuery\n\nKondisi OR '1'='1' mengevaluasi kueri menjadi TRUE untuk setiap baris. Database MySQL mengembalikan daftar seluruh pengguna, dan sistem langsung meloginkan baris pertama yang ditemukan, yaitu: **{$dbUser->name}** tanpa memeriksa password. Sistem berhasil ditembus!";

                    AuditLog::create([
                        'event' => 'SQL_INJECTION_SANDBOX_SUCCESS',
                        'details' => "SQL Injection sandbox bypass succeeded. Injected NIM: \"$nim\". Target: {$dbUser->name}.",
                        'ip_address' => $ip
                    ]);
                } else {
                    $success = false;
                    $message = 'Username/NIM atau password salah.';
                    $analysis = 'Login Gagal: Meskipun rentan, kueri database kosong karena tidak ada input literal atau bypass SQL Injection yang valid.';
                }
            } catch (\Exception $e) {
                $success = false;
                $message = 'Database syntax error!';
                $analysis = "DATABASE SYNTAX ERROR: " . $e->getMessage() . "\n\nInput Anda merusak struktur sintaksis kueri SQL di server database MySQL. Ciri khas kerentanan SQL Injection!";

                AuditLog::create([
                    'event' => 'SQL_INJECTION_SANDBOX_ERROR',
                    'details' => "SQL Injection sandbox triggered a database syntax error.",
                    'ip_address' => $ip
                ]);
            }
        }

        return redirect()->route('security.lab')->withInput()->with('sql_test_results', [
            'mitigated' => $mitigated,
            'sqlQuery' => $sqlQuery,
            'success' => $success,
            'message' => $message,
            'analysis' => $analysis
        ]);
    }

    /**
     * Clears authentication session
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::create([
                'event' => 'LOGOUT',
                'details' => "User logged out: NIM {$user->nim}",
                'ip_address' => $request->ip()
            ]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
