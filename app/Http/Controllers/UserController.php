<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use App\Models\User;

use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::orderBy('name')
            ->with( 'roles' )
            ->paginate( 10 );

        return view( 'users.users', compact( 'users' ) );
    }

    /**
     * Show the form for creating or editing a user
     *
     * @return \Illuminate\View\View
     */
    public function form( User $user )
    {
        if( $user->id ) 
        {
            $user->role = $user->roles[0]->name;
        }

        $roles = Role::orderBy('name')->get();

        return view( 'users.usersform', compact( 'user', 'roles' ) );
    }

    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'name' => [ 'required', 'unique:users,name', 'alpha' ],
            'email' => [ 'required', 'unique:users,email', 'email' ],
            'role' => [ 'required' ]
        ]);

        /* insert user and get id */
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make( config( 'user.password' ) );
        $user->save();

        /* assign the role */
        $user->assignRole( $request->role );

        flash( 'User successfully created.' )->success();
        return redirect( '/users' );
    }

    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => [ 'required', Rule::unique( 'users' )->ignore( $user->id ), 'alpha' ],
            'email' => [ 'required', Rule::unique( 'users' )->ignore( $user->id ), 'email' ],
            'role' => [ 'required' ]
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        /* removed old role add new role */
        $user->syncRoles( $request->role );
        
        flash( $user->name . ' has been successfully updated.' )->success();
        return redirect( '/users' );
    }

    /**
     * Remove the specified user from storage
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        flash( $user->name . ' has been successfully deleted.' )->success();
        return redirect( '/users' );
    }
}
