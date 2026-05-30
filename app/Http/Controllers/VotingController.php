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

class VotingController extends Controller
{
    /**
     * Renders Voter Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $categories = VotingCategory::with('candidates')->get();
        $votedCategoryIds = $user->votedCategories()->pluck('voting_categories.id')->toArray();
        $votingOpen = Cache::get('voting_open', true);

        // Dynamic stats compiler for Hero Section
        $totalVoters = User::where('role', 'voter')->count();
        $votedCount = User::whereHas('votedCategories')->count(); // Voters who voted in at least one category
        $turnoutPercentage = $totalVoters > 0 ? round(($votedCount / $totalVoters) * 100) : 0;

        return view('voter.dashboard', compact('categories', 'votedCategoryIds', 'votingOpen', 'totalVoters', 'votedCount', 'turnoutPercentage'));
    }

    /**
     * Casts an encrypted vote securely
     */
    public function vote(Request $request)
    {
        $candidateId = $request->input('candidateId');
        $user = Auth::user();
        $ip = $request->ip();

        if ($user->role !== 'voter') {
            return redirect()->back()->with('error', 'Hanya mahasiswa/pemilih terdaftar yang dapat memberikan suara.');
        }

        // Check if election gates are closed
        $votingOpen = Cache::get('voting_open', true);
        if (!$votingOpen) {
            return redirect()->back()->with('error', 'Pemilihan sudah ditutup oleh Administrator HIMA.');
        }

        if (empty($candidateId)) {
            return redirect()->back()->with('error', 'ID Kandidat harus dipilih.');
        }

        // Verify Candidate exists
        $candidate = Candidate::find($candidateId);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Kandidat tidak ditemukan.');
        }

        $categoryId = $candidate->voting_category_id;

        // 1. One Person One Vote Lock Check per Category
        if ($user->votedCategories()->where('voting_categories.id', $categoryId)->exists()) {
            AuditLog::create([
                'event' => 'DOUBLE_VOTE_BLOCKED',
                'details' => "Double voting attempt blocked for NIM: {$user->nim} in category ID {$categoryId}.",
                'ip_address' => $ip
            ]);

            return redirect()->back()->with('error', 'Anda sudah menggunakan hak suara Anda untuk kategori pemilihan ini!');
        }

        // 2. Encryption (AES-256-GCM)
        $encrypted = CryptoService::encrypt($candidateId);
        
        if (!$encrypted['success']) {
            return redirect()->back()->with('error', 'Kesalahan sistem saat enkripsi suara.');
        }

        // 3. Mark User as Voted in Category
        $user->votedCategories()->attach($categoryId);
        $dbUser = User::find($user->id);
        $dbUser->has_voted = true;
        $dbUser->save();

        // 4. Save Vote Anonymously (No User ID relation)
        Vote::create([
            'encrypted_candidate' => $encrypted['ciphertext'],
            'iv' => $encrypted['iv'],
            'tag' => $encrypted['tag'],
            'voting_category_id' => $categoryId,
            'timestamp' => now()
        ]);

        // 5. Audit Log (Preserves voter anonymity)
        AuditLog::create([
            'event' => 'VOTE_CAST',
            'details' => "Anonymous vote successfully cast and stored in AES-256-GCM format.",
            'ip_address' => $ip
        ]);

        // Redirect to success view with dynamic AES details flashed in session
        return redirect()->route('vote.success')->with([
            'success' => 'Terima kasih, suara Anda telah berhasil disimpan secara aman!',
            'plaintext' => "Kandidat ID: $candidateId ({$candidate->name})",
            'ciphertext' => $encrypted['ciphertext'],
            'iv' => $encrypted['iv'],
            'tag' => $encrypted['tag']
        ]);
    }

    /**
     * Renders Voting success view
     */
    public function success()
    {
        $user = Auth::user();
        if (!$user->has_voted) {
            return redirect()->route('voter.dashboard');
        }

        // Grab the latest vote from MySQL to show in case flashed session is gone (e.g. on manual refresh)
        $latestVote = Vote::latest('timestamp')->first();
        
        return view('voter.success', compact('latestVote'));
    }
}
