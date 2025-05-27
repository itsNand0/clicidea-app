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
        // Buscar el usuario por ID
        $userId = 5; // Cambia este valor según el usuario deseado
        $user = User::find($userId);

        if ($user) {
            // Crear o buscar el rol 'admin users'
            $roleAdmin = Role::firstOrCreate(['name' => 'admin users']);

            // Asignar el rol solo si el usuario no lo tiene
            if (!$user->hasRole($roleAdmin)) {
            $user->assignRole($roleAdmin);
            echo "Rol 'admin users' asignado al usuario con ID {$userId}.\n";
            } else {
            echo "El usuario con ID {$userId} ya tiene el rol 'admin users'.\n";
            }
        } else {
            echo "Usuario con ID {$userId} no encontrado.\n";
        }
        /*
        $manageUsersPermission = Permission::delete(['name' => 'admin users']);

        // Crear rol 'admin'
        $adminRole = Role::create(['name' => 'admin']);

        // Asignar el permiso de 'admin user' al rol 'admin'
        $adminRole->givePermissionTo($manageUsersPermission);
        $roleName = 1;

        // Intentamos encontrar el rol
        $role = Permission::findById($roleName);

        if ($role) {
            // Eliminar el rol
            $role->delete();
            echo "El rol '{$roleName}' ha sido eliminado.\n";
        } else {
            echo "El rol '{$roleName}' no existe.\n";
        }*/
    }
}
