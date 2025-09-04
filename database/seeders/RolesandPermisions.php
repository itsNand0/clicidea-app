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
        // $userId = 5; // Cambia este valor según el usuario deseado
        // $user = User::find($userId);

        // if ($user) {
        //     // Crear o buscar el rol 'admin users'
        //     $roleAdmin = Role::firstOrCreate(['name' => 'admin users']);

        //     // Asignar el rol solo si el usuario no lo tiene
        //     if (!$user->hasRole($roleAdmin)) {
        //     $user->assignRole($roleAdmin);
        //     echo "Rol 'admin users' asignado al usuario con ID {$userId}.\n";
        //     } else {
        //     echo "El usuario con ID {$userId} ya tiene el rol 'admin users'.\n";
        //     }
        // } else {
        //     echo "Usuario con ID {$userId} no encontrado.\n";
        // }

        $roladmin = Role::firstOrCreate(['name' => 'admin']);
        $rolrencar = Role::firstOrCreate(['name' => 'encargado']);
        $roloperador = Role::firstOrCreate(['name' => 'operador']);

        Permission::firstOrCreate(['name' => 'users.ver'])->syncRoles([$roladmin]);
        Permission::firstOrCreate(['name' => 'users.crear'])->syncRoles([$roladmin]);
        Permission::firstOrCreate(['name' => 'users.editar'])->syncRoles([$roladmin]);
        Permission::firstOrCreate(['name' => 'users.eliminar'])->syncRoles([$roladmin]);

        Permission::firstOrCreate(['name' => 'incidencias.ver'])->syncRoles([$roladmin, $rolrencar, $roloperador]);
        Permission::firstOrCreate(['name' => 'incidencias.crear'])->syncRoles([$roladmin, $rolrencar]);
        Permission::firstOrCreate(['name' => 'incidencias.editar'])->syncRoles([$roladmin, $rolrencar, $roloperador]);
        Permission::firstOrCreate(['name' => 'incidencias.eliminar'])->syncRoles([$roladmin, $rolrencar]);
        Permission::firstOrCreate(['name' => 'incidencias.exportarExcel'])->syncRoles([$roladmin, $rolrencar]);
        Permission::firstOrCreate(['name' => 'incidencias.asignar'])->syncRoles([$roladmin, $rolrencar]);
        Permission::firstOrCreate(['name' => 'incidencias.resolver'])->syncRoles([$roladmin, $rolrencar, $roloperador]);
        Permission::firstOrCreate(['name' => 'incidencias.cambiarEstado'])->syncRoles([$roladmin, $rolrencar, $roloperador]);
        Permission::firstOrCreate(['name' => 'incidencias.comentarios'])->syncRoles([$roladmin, $rolrencar, $roloperador]);

        Permission::firstOrCreate(['name' => 'clientes.ver'])->syncRoles([$roladmin]);
        Permission::firstOrCreate(['name' => 'clientes.crear'])->syncRoles([$roladmin]);
        Permission::firstOrCreate(['name' => 'clientes.editar'])->syncRoles([$roladmin]);
        Permission::firstOrCreate(['name' => 'clientes.eliminar'])->syncRoles([$roladmin]);
    }
}
