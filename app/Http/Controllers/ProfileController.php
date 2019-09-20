<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


use App\Rules\CurrentPassword;

use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the index page
     */
    public function index()
    {
        $user = auth()->user();
        $user->load( 'roles' );

        return view( 'profile.index', compact( 'user' ) );
    }

    /**
     * Update the profile
     */
    public function update( User $user )
    {
        $auth = auth()->user();

        if( $user->id !== $auth->id && ! $auth->hasRole( 'admin' ) )
        {
            flash( 'You do not have permission to do this' );
            return back();
        }

        if( request()->email )
        {
            request()->validate([
                'email' => [ 'required', Rule::unique( 'users', 'email' )->ignore( $user->id ), 'email' ]
            ]);

            $user->email = request()->email;
            $user->save();

            flash( 'Email has been successfully updated' );
            return back();
        }
        else if( request()->password )
        {
            request()->validate([
                'old_password' => [ new CurrentPassword ],
                'password' => [ 'required', 'confirmed' ]
            ]);

            $user->password = Hash::make( request()->password );
            $user->save();

            flash( 'Password has been successfully updated' );
            return back();
        }
    }
}
