<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vote;
use App\Models\Candidate;
use App\Models\VotingCategory;
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
        $categories = VotingCategory::with('candidates')->get();

        return view('admin.dashboard', compact('turnout', 'chartData', 'voters', 'logs', 'votingOpen', 'categories'));
    }

    /**
     * Creates new Candidate
     */
    public function createCandidate(Request $request)
    {
        $name = $request->input('name');
        $vision = $request->input('vision');
        $mission = $request->input('mission');
        $categoryId = $request->input('voting_category_id');

        if (empty($name) || empty($vision) || empty($mission) || empty($categoryId)) {
            return redirect()->back()->with('error', 'Semua kolom kandidat dan kategori harus diisi.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            $allowedExtensions = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
            $ext = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($ext, $allowedExtensions)) {
                return redirect()->back()->with('error', 'Berkas foto harus berupa gambar (jpeg, png, jpg, gif, svg).');
            }
            
            // Max 5MB
            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran foto maksimal adalah 5MB.');
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
            'photo' => $photoPath,
            'voting_category_id' => $categoryId
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
     * Updates candidate
     */
    public function updateCandidate($id, Request $request)
    {
        $candidate = Candidate::find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Kandidat tidak ditemukan.');
        }

        $candidate->name = $request->input('name', $candidate->name);
        $candidate->vision = $request->input('vision', $candidate->vision);
        $candidate->mission = $request->input('mission', $candidate->mission);
        if ($request->has('voting_category_id')) {
            $candidate->voting_category_id = $request->input('voting_category_id');
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            $allowedExtensions = ['jpeg', 'png', 'jpg', 'gif', 'svg'];
            $ext = strtolower($file->getClientOriginalExtension());
            
            if (!in_array($ext, $allowedExtensions)) {
                return redirect()->back()->with('error', 'Berkas foto harus berupa gambar (jpeg, png, jpg, gif, svg).');
            }
            
            // Max 5MB
            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran foto maksimal adalah 5MB.');
            }

            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $file->getClientOriginalName());
            
            $targetDir = public_path('uploads/candidates');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            $file->move($targetDir, $filename);
            $candidate->photo = 'uploads/candidates/' . $filename;
        }

        $candidate->save();

        AuditLog::create([
            'event' => 'CANDIDATE_UPDATED',
            'details' => "Admin updated candidate ID $id: {$candidate->name}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Data kandidat berhasil diperbarui.');
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

    /**
     * Updates voter information
     */
    public function updateVoter($id, Request $request)
    {
        $voter = User::find($id);
        if (!$voter || $voter->role === 'admin') {
            return redirect()->back()->with('error', 'Pemilih tidak ditemukan.');
        }

        $voter->name = $request->input('name', $voter->name);
        $voter->nim = $request->input('nim', $voter->nim);
        $voter->email = $request->input('email', $voter->email);

        if ($request->filled('password')) {
            $voter->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
        }

        $voter->save();

        AuditLog::create([
            'event' => 'USER_UPDATED',
            'details' => "Admin updated voter account: NIM {$voter->nim}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Akun pemilih berhasil diperbarui.');
    }

    /**
     * Creates new Category
     */
    public function createCategory(Request $request)
    {
        $name = $request->input('name');
        $description = $request->input('description');

        if (empty($name)) {
            return redirect()->back()->with('error', 'Nama kategori harus diisi.');
        }

        $category = VotingCategory::create([
            'name' => $name,
            'description' => $description
        ]);

        AuditLog::create([
            'event' => 'CATEGORY_CREATED',
            'details' => "Admin created category: {$category->name}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Deletes Category
     */
    public function deleteCategory($id, Request $request)
    {
        $category = VotingCategory::find($id);
        if (!$category) {
            return redirect()->back()->with('error', 'Kategori tidak ditemukan.');
        }

        $name = $category->name;
        $category->delete();

        AuditLog::create([
            'event' => 'CATEGORY_DELETED',
            'details' => "Admin deleted category: $name",
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil dihapus beserta kandidat di dalamnya.');
    }

    /**
     * Renders printable PDF-friendly report of voting results
     */
    public function exportPdf()
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

        $reportData = $candidates->map(function ($c) use ($tallies, $votedCount) {
            $votes = $tallies[$c->id] ?? 0;
            return [
                'name' => $c->name,
                'category' => $c->votingCategory ? $c->votingCategory->name : 'N/A',
                'votes' => $votes,
                'percentage' => $votedCount > 0 ? round(($votes / $votedCount) * 100, 1) : 0
            ];
        })->sortByDesc('votes');

        $categories = VotingCategory::with('candidates')->get();

        return view('admin.pdf_report', compact('turnout', 'reportData', 'votingOpen', 'categories'));
    }
}

