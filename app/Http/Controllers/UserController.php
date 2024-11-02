<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
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

        $role = auth()->user()->role;

        $roles = User::getRolesAccordingToUserRole($role);

        $schools = School::all();

        return view('users.create', compact('user', 'schools', 'role', 'roles'));
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

        // Asignar permisos
        if ($request->has('permissions')) {
            $user->updatePermissions($request->input('permissions'));
        }

        return redirect()->route('users.index');
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
        $role = auth()->user()->role;

        $roles = User::getRolesAccordingToUserRole($role);

        $schools = School::all();

        return view('users.edit', compact('user', 'schools', 'role', 'roles'));
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
        // Obtener los permisos seleccionados y actualizarlos
        $permissions = $request->input('permissions', []); // Asegúrate de que el input se llame 'permissions'
        $user->permissions = $permissions;
        $user->save();

        return redirect()->route('users.show', $user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }
}
