<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Role;
use App\Models\School;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::getUsersExcludingAuthenticated();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = new User();

        $roles = Role::all();

        $schools = School::all();

        return view('users.create', compact('user', 'schools', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserFormRequest $request)
    {
        $user = User::create($request->validated());

        // Asignar colegios si existen
        if ($request->has('school_ids')) {
            $user->schools()->attach($request->input('school_ids'));
        }

        return redirect()->route('users.index')->with('success', 'Usuario Registrado Exitosamente !!');
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $schools = $user->schools;

        return view('users.show', compact('user', 'schools'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        $schools = School::all();

        return view('users.edit', compact('user', 'schools','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserFormRequest $request, User $user)
    {
        // Actualizar los atributos del usuario
        $user->update($user->getUpdateAttributes($request->validated()));

        // Obtener los IDs de colegios enviados en la solicitud
        if ($request->has('school_ids')) {
            $schoolIds = $request->input('school_ids', []);
            // Sincronizar las relaciones de colegios
            $user->schools()->sync($schoolIds);
        }

        return redirect()->route('users.show', $user)->with('success', 'Usuario Actualizado Exitosamente !!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario Eliminado Exitosamente !!');
    }
}
