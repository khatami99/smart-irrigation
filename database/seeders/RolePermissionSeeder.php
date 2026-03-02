<?php
// database/seeders/RolePermissionSeeder.php
// INI VERSI LENGKAP — replace file seeder yang lama

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Daftar semua permissions ──
        $permissions = [
            // Data Iklim
            'view data-iklim',
            'create data-iklim',
            'edit data-iklim',
            'delete data-iklim',

            // Petak
            'view petak',
            'create petak',
            'edit petak',
            'delete petak',

            // Musim Tanam
            'view musim-tanam',
            'create musim-tanam',
            'edit musim-tanam',
            'delete musim-tanam',

            // Blangko OP ← BARU
            'view blangko-op',
            'create blangko-op',
            'edit blangko-op',
            'delete blangko-op',

            // Laporan
            'view laporan',
            'export laporan',

            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // RTT
            'view rtt',
            'create rtt',
            'edit rtt',
            'delete rtt',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── ADMIN — akses penuh ke semua ──
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // ── JURU/PENGAMAT — input data lapangan ──
        $juru = Role::firstOrCreate(['name' => 'juru']);
        $juru->syncPermissions([
            'view data-iklim', 'create data-iklim', 'edit data-iklim',
            'view petak',
            'view musim-tanam',
            'view blangko-op', 'create blangko-op', 'edit blangko-op', // ← bisa input & edit
            'view laporan',
            'view rtt',
            'create rtt',
            'edit rtt',
        ]);

        // ── DINAS — lihat & validasi laporan ──
        $dinas = Role::firstOrCreate(['name' => 'dinas']);
        $dinas->syncPermissions([
            'view data-iklim',
            'view petak',
            'view musim-tanam',
            'view blangko-op', // ← hanya lihat
            'view laporan', 'export laporan',
            'view rtt',
        ]);

        // ── PETANI — hanya lihat data umum ──
        $petani = Role::firstOrCreate(['name' => 'petani']);
        $petani->syncPermissions([
            'view data-iklim',
            'view petak',
            'view musim-tanam',
            'view laporan',
            // tidak ada akses blangko-op
        ]);

        // ── Buat user default ──
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@irigasi.id'],
            ['name' => 'Administrator', 'password' => Hash::make('admin123')]
        );
        $adminUser->syncRoles('admin');

        $juruUser = User::firstOrCreate(
            ['email' => 'juru@irigasi.id'],
            ['name' => 'Ahmad Juru', 'password' => Hash::make('juru123')]
        );
        $juruUser->syncRoles('juru');

        $dinasUser = User::firstOrCreate(
            ['email' => 'dinas@irigasi.id'],
            ['name' => 'Staf Dinas', 'password' => Hash::make('dinas123')]
        );
        $dinasUser->syncRoles('dinas');

        $this->command->info('✅ Roles & permissions berhasil diperbarui!');
        $this->command->table(
            ['Role', 'Email', 'Password', 'Akses Blangko OP'],
            [
                ['admin',  'admin@irigasi.id', 'admin123', 'Penuh (CRUD)'],
                ['juru',   'juru@irigasi.id',  'juru123',  'View + Input + Edit'],
                ['dinas',  'dinas@irigasi.id', 'dinas123', 'View saja'],
                ['petani', '—',               '—',        'Tidak ada akses'],
            ]
        );
    }
}
