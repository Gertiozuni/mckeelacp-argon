<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use Carbon\Carbon;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware( 'guest' )->except( 'logout' );
    }

    public function username()
    {
       $login = request()->input( 'email' );
       $field = filter_var( $login, FILTER_VALIDATE_EMAIL ) ? 'email' : 'name';
       request()->merge( [ $field => $login ] );
       return $field;
    }

    /*
    *   records when the user logs in
    */
    protected function authenticated( Request $request, $user )
    {
        $user->update([
            'last_login' => Carbon::now()
        ]);
    }
}
