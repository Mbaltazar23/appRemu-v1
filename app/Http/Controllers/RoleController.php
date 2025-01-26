<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleFormRequest;
use App\Models\Role;

class RoleController extends Controller {

    /**
     * Create the controller instance.
     */
    public function __construct() {
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        $roles = Role::query()
                ->orderBy('created_at', 'DESC')
                ->paginate(5);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $role = new Role();

        $permissions = Role::getAvailablePermissions();

        return view('roles.create', compact('role', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleFormRequest $request) {
        $role = Role::create($request->validated());

        if ($request->has('permissions')) {
            $role->updatePermissions($request->input('permissions'));
        }

        return redirect()->route('roles.index')->with('success', "Rol Registrado Exitosamente !!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role) {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role) {
        $permissions = Role::getAvailablePermissions();

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleFormRequest $request, Role $role) {
        $role->update($request->validated());

        if ($request->has('permissions')) {
            $role->updatePermissions($request->input('permissions'));
        }

        return redirect()->route('roles.show', $role)->with('success', "Rol Actualizado Exitosamente !!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role) {
        $role->delete();

        return redirect()->route('roles.index')->with('success', "Rol Eliminado Exitosamente !!");
    }

}
