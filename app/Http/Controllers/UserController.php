<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolUser;
use App\Models\User;
use App\Services\SchoolService;

class UserController extends Controller
{

    protected $schoolService;
    /**
     * Create the controller instance.
     *
     * This constructor will apply authorization for the User resource.
     */
    public function __construct(SchoolService $schoolService)
    {
        $this->authorizeResource(User::class, 'user');
        $this->schoolService = $schoolService;
    }

    /**
     * Display a listing of the resource.
     *
     * Retrieves a paginated list of users excluding the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
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
    public function create()
    {
        $user = new User();

        $roles = Role::all();

        $schools = School::all();

        $associatedSchoolIds = SchoolUser::pluck('school_id')->unique()->toArray();

        return view('users.create', compact('user', 'schools', 'roles', 'associatedSchoolIds'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * This method stores a new user and associates selected schools if provided.
     *
     * @param  UserFormRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserFormRequest $request)
    {
        $user = User::create($request->validated());

        // Assign schools if provided
        if ($request->has('school_ids') && ! empty($request->input('school_ids'))) {
            $user->schools()->attach($request->input('school_ids'));
            // Procesar los parámetros relacionados con los colegios
            $this->schoolService->handleSchoolParameters($request->input('school_ids'));
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
    public function show(User $user)
    {
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
    public function edit(User $user)
    {
        $roles = Role::all();

        $schools = School::all();

        $associatedSchoolIds = SchoolUser::where('user_id', '!=', $user->id)
            ->pluck('school_id')
            ->unique()
            ->toArray();

        return view('users.edit', compact('user', 'schools', 'roles', 'associatedSchoolIds'));
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
    public function update(UserFormRequest $request, User $user)
    {
        // Get the current school_ids of the user
        $currentSchoolIds = $user->schools->pluck('id')->toArray();

        // Update user attributes
        $user->update($user->getUpdateAttributes($request->validated()));

        // Get the school_ids sent in the request
        $newSchoolIds = $request->input('school_ids', []);

        // Synchronizing school relationships
        $user->schools()->sync($newSchoolIds);

        // Determine the school_ids that have been deleted
        $removedSchoolIds = array_diff($currentSchoolIds, $newSchoolIds);

        // Determine the school_ids that have been added
        $addedSchoolIds = array_diff($newSchoolIds, $currentSchoolIds);

        // If schools have been deleted, delete the associated parameters
        if (! empty($removedSchoolIds)) {
            $this->schoolService->deleteSchoolParameters($removedSchoolIds);
        }

        // If new schools have been added, process the parameters
        if (! empty($addedSchoolIds)) {
            $this->schoolService->handleSchoolParameters($addedSchoolIds);
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
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario Eliminado Exitosamente !!');
    }

}
