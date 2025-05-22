<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class Usercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users =  User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = new User();
        $validate = $request -> validate
        ([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'password_confirmation' => 'required|string|max:255',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->remember_token = $request->_token;

        $user->save();

        return redirect()->route('users.index')->with('Registro creado correctamente');
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
        $user = User::findorfail($id);
        
        return view('users.edit', compact('user'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validate = $request -> validate
        ([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'password_confirmation' => 'required|string|max:255',
        ]);

        $user->name = $request -> input('name');
        $user->email = $request -> input('email');
        $user->password = $request -> input('password');

        $user -> save();

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
