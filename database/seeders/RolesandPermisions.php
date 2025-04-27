<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesandPermisions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        /* $user = User::find(4); // Cambia 1 por el ID del usuario que deseas asignar el rol
        // Crear permisos

        if ($user) {
            // Verifica si el rol 'admin users' existe, si no, crea el rol
            $roleadmin = Role::firstOrCreate(['name' => 'admin users']);

            // Asigna el rol al usuario
            $user->assignRole($roleadmin);
        }

        $manageUsersPermission = Permission::delete(['name' => 'admin users']);

        // Crear rol 'admin'
        $adminRole = Role::create(['name' => 'admin']);

        // Asignar el permiso de 'admin user' al rol 'admin'
        $adminRole->givePermissionTo($manageUsersPermission);*/
        $roleName = 1;

        // Intentamos encontrar el rol
        $role = Permission::findById($roleName);

        if ($role) {
            // Eliminar el rol
            $role->delete();
            echo "El rol '{$roleName}' ha sido eliminado.\n";
        } else {
            echo "El rol '{$roleName}' no existe.\n";
        }
    }
}
