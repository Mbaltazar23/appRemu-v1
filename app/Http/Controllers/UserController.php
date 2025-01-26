<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Role;
use App\Models\School;
use App\Models\User;

class UserController extends Controller {

    /**
     * Create the controller instance.
     * 
     * This constructor will apply authorization for the User resource.
     */
    public function __construct() {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     * 
     * Retrieves a paginated list of users excluding the authenticated user.
     * 
     * @return \Illuminate\View\View
     */
    public function index() {
        $users = User::getUsersExcludingAuthenticated();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * Fetches roles and schools to display in the create form.
     * 
     * @return \Illuminate\View\View
     */
    public function create() {
        $user = new User();

        $roles = Role::all();

        $schools = School::all();

        return view('users.create', compact('user', 'schools', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * This method stores a new user and associates selected schools if provided.
     * 
     * @param  UserFormRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserFormRequest $request) {
        $user = User::create($request->validated());

        // Assign schools if provided
        if ($request->has('school_ids') && !empty($request->input('school_ids'))) {
            $user->schools()->attach($request->input('school_ids'));
        }

        return redirect()->route('users.index')->with('success', 'Usuario Registrado Exitosamente !!');
    }

    /**
     * Display the specified resource.
     * 
     * Shows the user details along with associated schools.
     * 
     * @param  User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user) {
        $schools = $user->schools;

        return view('users.show', compact('user', 'schools'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * Retrieves roles and schools to display in the edit form for the user.
     * 
     * @param  User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user) {
        $roles = Role::all();

        $schools = School::all();

        return view('users.edit', compact('user', 'schools', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Updates the user's attributes and syncs school associations.
     * If no school IDs are provided, it detaches the user from all schools.
     * 
     * @param  UserFormRequest  $request
     * @param  User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserFormRequest $request, User $user) {
        // Update user attributes
        $user->update($user->getUpdateAttributes($request->validated()));

        // Get the school IDs sent in the request
        $schoolIds = $request->input('school_ids', []);

        // Check if school_ids were provided and are not empty
        if (!empty($schoolIds)) {
            // Sync school relationships
            $user->schools()->sync($schoolIds);
        } else {
            // If school_ids array is empty, remove all associations
            $user->schools()->detach();
        }

        return redirect()->route('users.show', $user)->with('success', 'Usuario Actualizado Exitosamente !!');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Deletes the user and removes any associated data.
     * 
     * @param  User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user) {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario Eliminado Exitosamente !!');
    }

}
