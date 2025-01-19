<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    /**
     * Set the school session for the authenticated user.
     *
     * This method validates the provided school ID, ensuring it exists in the database.
     * It then updates the authenticated user's session with the selected school ID.
     * Finally, it redirects the user to the home page with a success message indicating 
     * that the school was successfully selected.
     */
    public function setSchoolSession(Request $request)
    {
        // Validate that the school exists
        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);
        // Get the user by their logged in id
        $authUser = User::find(auth()->user()->id);
        // Save the school ID in the user session
        $authUser->update([
            'school_id_session' => $request->school_id,
        ]);
        // Redirect to home with a success message
        return redirect()->route('home')->with('success', __('Colegio seleccionado Exitosamente !!'));
    }
}
