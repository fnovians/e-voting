<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vote;
use App\Models\Candidate;
use App\Models\AuditLog;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ResearchController extends Controller
{
    /**
     * Renders Cryptographic Research Lab
     */
    public function index()
    {
        // 1. Fetch Users
        $users = User::all()->map(function ($u) {
            return [
                'nim' => $u->nim,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'salt' => 'bcrypt_rounds_12',
                'passwordHash' => substr($u->password, 0, 16) . '...' . substr($u->password, -8),
                'hasVoted' => (bool)$u->has_voted
            ];
        });

        // 2. Fetch raw votes
        $votes = Vote::all();

        // 3. Compile decrypted side-by-side comparison list
        $candidates = Candidate::all();
        $cMap = [];
        foreach ($candidates as $c) {
            $cMap[$c->id] = $c->name;
        }

        $voteComparison = Vote::all()->map(function ($v, $idx) use ($cMap) {
            $plaintext = CryptoService::decrypt($v->encrypted_candidate, $v->iv, $v->tag);
            $candName = $cMap[$plaintext] ?? "Kandidat ID: $plaintext (Unknown)";

            return [
                'index' => $idx + 1,
                'plaintext' => "ID: $plaintext ($candName)",
                'ciphertext' => $v->encrypted_candidate,
                'iv' => $v->iv,
                'tag' => $v->tag,
                'timestamp' => $v->timestamp
            ];
        });

        // 4. Fetch Logs
        $logs = AuditLog::orderBy('created_at', 'desc')->take(100)->get();

        return view('research.lab', compact('users', 'votes', 'voteComparison', 'logs'));
    }

    /**
     * Renders SQL Injection security sandbox lab
     */
    public function securityLab()
    {
        return view('security.lab');
    }

    /**
     * Resets MySQL tables to seeded state
     */
    public function resetDb()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            return redirect()->route('research.lab')->with('success', 'Database MySQL berhasil di-reset ke data awal!');
        } catch (\Exception $e) {
            return redirect()->route('research.lab')->with('error', 'Gagal mereset database: ' . $e->getMessage());
        }
    }
}
