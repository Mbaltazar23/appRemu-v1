<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\SchoolUser;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
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
    public function __construct()
    {
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
    protected function authenticated()
    {
        $user = auth()->user();
        // Create a new entry record in the history without prior validations
        History::create([
            'user_id' => $user->id,
            'action'  => 'Ingreso',
        ]);

        return redirect()->intended($this->redirectTo);
    }

    public function logout()
    {
        // Fetch all users and set 'school_id_session' to null for each
        $userDetail = SchoolUser::where('user_id', auth()->user()->id)->first();

        if ($userDetail && $userDetail->user->school_id_session) {
            $userDetail->user->school_id_session = null;
            $userDetail->user->save();
        }

        Auth::logout();

        return redirect('/'); // Redirige a donde desees después del logout
    }
}
