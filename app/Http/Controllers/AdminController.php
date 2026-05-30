<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vote;
use App\Models\Candidate;
use App\Models\AuditLog;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    /**
     * Renders Admin Dashboard view
     */
    public function index()
    {
        $totalVoters = User::where('role', 'voter')->count();
        $votedCount = User::where('role', 'voter')->where('has_voted', true)->count();
        $votingOpen = Cache::get('voting_open', true);
        
        $turnout = [
            'total' => $totalVoters,
            'voted' => $votedCount,
            'percentage' => $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100) : 0
        ];

        // Dynamic openssl decryption tally compiler
        $candidates = Candidate::all();
        $tallies = [];
        foreach ($candidates as $c) {
            $tallies[$c->id] = 0;
        }

        $votes = Vote::all();
        foreach ($votes as $v) {
            $decryptedId = CryptoService::decrypt($v->encrypted_candidate, $v->iv, $v->tag);
            if (isset($tallies[$decryptedId])) {
                $tallies[$decryptedId]++;
            }
        }

        $chartData = $candidates->map(function ($c) use ($tallies) {
            return [
                'name' => $c->name,
                'votes' => $tallies[$c->id] ?? 0
            ];
        });

        $voters = User::where('role', 'voter')->select('id', 'nim', 'name', 'email', 'has_voted')->get();
        $logs = AuditLog::orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact('turnout', 'chartData', 'voters', 'logs', 'votingOpen'));
    }

    /**
     * Creates new Candidate
     */
    public function createCandidate(Request $request)
    {
        $name = $request->input('name');
        $vision = $request->input('vision');
        $mission = $request->input('mission');

        if (empty($name) || empty($vision) || empty($mission)) {
            return redirect()->back()->with('error', 'Semua kolom kandidat harus diisi.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            $allowedExtensions = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
            $ext = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($ext, $allowedExtensions)) {
                return redirect()->back()->with('error', 'Berkas foto harus berupa gambar (jpeg, png, jpg, gif, svg).');
            }
            
            if ($file->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran foto maksimal adalah 2MB.');
            }

            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            
            $targetDir = public_path('uploads/candidates');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $file->move($targetDir, $filename);
            $photoPath = 'uploads/candidates/' . $filename;
        }

        $candidate = Candidate::create([
            'name' => $name,
            'vision' => $vision,
            'mission' => $mission,
            'photo' => $photoPath
        ]);

        AuditLog::create([
            'event' => 'CANDIDATE_CREATED',
            'details' => "Admin created candidate ID {$candidate->id}: {$candidate->name}" . ($photoPath ? " with photo: $photoPath" : ""),
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Kandidat baru berhasil ditambahkan.');
    }

    /**
     * Deletes candidate
     */
    public function deleteCandidate($id, Request $request)
    {
        $candidate = Candidate::find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Kandidat tidak ditemukan.');
        }

        $name = $candidate->name;
        $candidate->delete();

        AuditLog::create([
            'event' => 'CANDIDATE_DELETED',
            'details' => "Admin deleted candidate ID $id: $name",
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Kandidat berhasil dihapus.');
    }

    /**
     * Toggles election gates
     */
    public function toggleVoting(Request $request)
    {
        $votingOpen = Cache::get('voting_open', true);
        $newStatus = !$votingOpen;
        Cache::put('voting_open', $newStatus);

        AuditLog::create([
            'event' => 'ELECTION_TOGGLED',
            'details' => "Admin toggled election status. Voting open: " . ($newStatus ? 'DIBUKA' : 'DITUTUP'),
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Status pemilihan berhasil diubah menjadi: ' . ($newStatus ? 'DIBUKA' : 'DITUTUP'));
    }

    /**
     * Deletes voter
     */
    public function deleteVoter($id, Request $request)
    {
        $voter = User::find($id);
        if (!$voter || $voter->role === 'admin') {
            return redirect()->back()->with('error', 'Pemilih tidak ditemukan.');
        }

        $nim = $voter->nim;
        $name = $voter->name;
        $voter->delete();

        AuditLog::create([
            'event' => 'USER_DELETED',
            'details' => "Admin deleted voter account: NIM $nim, Name: $name",
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Akun pemilih berhasil dihapus.');
    }
}
