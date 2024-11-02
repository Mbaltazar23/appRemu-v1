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

    public function setSchoolSession(Request $request)
    {
        // Validar que el colegio exista
        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        //Obtener al usuario por su id logueado
        $authUser = User::find(auth()->user()->id);

        // Guardar el ID del colegio en la sesión del usuario
        $authUser->update([
            'school_id_session' => $request->school_id,
        ]);

        // Redirigir al home con un mensaje de éxito
        return redirect()->route('home')->with('success', __('Colegio seleccionado Exitosamente !!'));
    }
}
