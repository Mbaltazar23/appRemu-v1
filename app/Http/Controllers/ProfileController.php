<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     *
     * This method returns the view for the user's profile page.
     * It doesn't require any data from the user model.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.profile');
    }
    /**
     * Update the user's profile information.
     *
     * This method validates and updates the user's profile details, 
     * including the name, email, and optionally the password.
     * If a new password is provided, it hashes the password before saving.
     */
    public function update(ProfileUpdateRequest $request)
    {
        // Find the authenticated user by their ID
        $user = User::find(auth()->user()->id);
        // If a new password is provided, hash and update it
        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        // Update other user profile details such as name and email
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        // Redirect back to the profile page with a success message
        return redirect()->back()->with('success', 'Usuario Actualizado Exitosamente !!');
    }
}
