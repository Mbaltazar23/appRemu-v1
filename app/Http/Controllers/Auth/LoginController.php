<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\SchoolUser;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class LoginController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles authenticating users for the application and
      | redirecting them to your home screen. The controller uses a trait
      | to conveniently provide its functionality to your applications.
      |
     */

use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated() {
        $user = auth()->user();
        // Obtener la fecha y hora actual
        $currentDateTime = Carbon::now();
        // Verificamos si ya existe un historial de ingreso para el usuario
        $exists = History::where('user_id', $user->id)
                ->whereDate('created_at', $currentDateTime->toDateString()) // Misma fecha
                ->whereBetween('created_at', [
                    $currentDateTime->copy()->startOfHour(), // Hora de inicio de la hora actual
                    $currentDateTime->copy()->endOfHour(), // Hora final de la hora actual
                ])
                ->exists();

        // Si no existe, creamos el nuevo registro de ingreso
        if (!$exists) {
            History::create([
                'user_id' => $user->id,
                'action' => 'Ingreso',
            ]);
        }

        return redirect()->intended($this->redirectTo);
    }

    public function logout() {
        /* $userDetail = SchoolUser::where('user_id', auth()->user()->id)->first();

          // Verifica si el rol del usuario es "Contador"
          if ($userDetail && $userDetail->user->school_id_session) {
          // Establece school_id_session a null
          $userDetail->user->school_id_session = null;
          $userDetail->user->save();
          } */

        // Fetch all users and set 'school_id_session' to null for each
        $users = User::all();

        foreach ($users as $user) {
            $user->school_id_session = null;
            $user->save(); // Save the changes
        }

        Auth::logout();

        return redirect('/'); // Redirige a donde desees después del logout
    }

}
