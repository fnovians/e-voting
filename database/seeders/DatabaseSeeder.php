<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Candidate;
use App\Models\AuditLog;
use App\Models\Vote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks to truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Candidate::truncate();
        Vote::truncate();
        AuditLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Seed Candidates
        Candidate::create([
            'id' => 1,
            'name' => 'Calon 1: Andika & Bella',
            'vision' => 'Mewujudkan HIMA yang inklusif, kolaboratif, dan progresif berbasis teknologi informasi.',
            'mission' => "1. Mengembangkan minat bakat mahasiswa melalui pelatihan intensif.\n2. Digitalisasi birokrasi dan layanan kemahasiswaan HIMA.\n3. Mempererat hubungan dan solidaritas antar angkatan mahasiswa.",
        ]);

        Candidate::create([
            'id' => 2,
            'name' => 'Calon 2: Citra & Dharma',
            'vision' => 'Membangun HIMA yang responsif, berintegritas tinggi, dan aktif berkontribusi bagi masyarakat luas.',
            'mission' => "1. Menyediakan advokasi mahasiswa yang cepat tanggap, transparan, dan solutif.\n2. Menyelenggarakan pengabdian masyarakat secara berkala dan terstruktur.\n3. Meningkatkan iklim riset dan publikasi karya ilmiah mahasiswa.",
        ]);

        // 2. Seed Users
        // Admin
        User::create([
            'nim' => '000000000',
            'name' => 'Administrator HIMA',
            'email' => 'admin@hima.univ.ac.id',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
            'has_voted' => false
        ]);

        // Voters
        $voters = [
            ['nim' => '120203001', 'name' => 'Budi Santoso', 'email' => 'budi@mahasiswa.univ.ac.id'],
            ['nim' => '120203002', 'name' => 'Siti Aminah', 'email' => 'siti@mahasiswa.univ.ac.id'],
            ['nim' => '120203003', 'name' => 'Rian Hidayat', 'email' => 'rian@mahasiswa.univ.ac.id'],
            ['nim' => '120203004', 'name' => 'Dewi Lestari', 'email' => 'dewi@mahasiswa.univ.ac.id'],
            ['nim' => '120203005', 'name' => 'Eko Prasetyo', 'email' => 'eko@mahasiswa.univ.ac.id'],
        ];

        foreach ($voters as $voter) {
            User::create([
                'nim' => $voter['nim'],
                'name' => $voter['name'],
                'email' => $voter['email'],
                'password' => Hash::make('password123'),
                'role' => 'voter',
                'has_voted' => false
            ]);
        }

        // 3. Log System seeding
        AuditLog::create([
            'event' => 'SYSTEM_INIT',
            'details' => 'Database initialized and seeded with default Administrator, Voters, and Candidates.',
            'ip_address' => '127.0.0.1'
        ]);
    }
}
