<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PetaPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view peta',
            'create peta',
            'edit peta',
            'delete peta',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin → semua
        $admin = Role::findByName('admin');
        $admin->givePermissionTo($permissions);

        // Juru → view + create + edit
        $juru = Role::findByName('juru');
        $juru->givePermissionTo(['view peta', 'create peta', 'edit peta']);

        // Dinas → view only
        $dinas = Role::findByName('dinas');
        $dinas->givePermissionTo(['view peta']);

        // Petani → view only
        $petani = Role::findByName('petani');
        $petani->givePermissionTo(['view peta']);
    }
}
