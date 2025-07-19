<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Cargo;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class Usercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $users = User::with(['area', 'cargo']);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $areas = Area::all();
        $cargos = Cargo::all();
        $roles = Role::all();
        return view('users.create', compact('areas', 'cargos', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'area_id' => 'nullable|integer',
            'cargo_id' => 'nullable|integer',
            'role' => 'nullable|string|exists:roles,name',
        ]);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->area_id = $request->area_id;
        $user->cargo_id = $request->cargo_id;
        $user->save();

        // Asignar rol después de guardar el usuario
        if ($request->role) {
            $user->assignRole($request->role);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {   
        $user = User::findorfail($id);
        
        return view('users.show',  compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {   
        $areas = Area::all();
        $cargos = Cargo::all();
        $user = User::findorfail($id);
        $roles = Role::all();

        return view('users.edit', compact('user', 'areas', 'cargos', 'roles'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'cargo_id' => 'nullable|integer',
            'area_id' => 'nullable|integer',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        
        // Solo actualizar la contraseña si se proporciona
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        
        $user->cargo_id = $request->input('cargo_id');
        $user->area_id = $request->input('area_id');

        $user->save();

        // Asignar rol después de guardar el usuario
        if ($request->filled('role')) {
            // Sincronizar roles (esto removerá roles anteriores y asignará el nuevo)
            $user->syncRoles([$request->input('role')]);
        } elseif ($request->has('role') && empty($request->input('role'))) {
            // Si se envía un valor vacío, remover todos los roles
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findorfail($id);
        $user -> delete();

        return view('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
