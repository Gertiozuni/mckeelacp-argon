<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('name')
            ->paginate( 10 );

        return view( 'users.roles', compact( 'roles' ) );
    }

    /**
     * Show the form for roles
     *
     * @return \Illuminate\Http\Response
     */
    public function form( Role $role )
    {
        return view( 'users.rolesform', compact( 'role' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {
        $request->validate([
            'name' => [ 'required', 'unique:roles,name', 'alpha' ]
        ]);

        Role::insert([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);
        
        flash( 'Role successfully created.' )->success();
        return redirect( '/roles' );
    }

    /**
     *  Display the role to edit permissions
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( Role $role )
    {
        // load in the permissions 
        $role->load('permissions')->get();

        /* get the permissomns this role doesnt have */
        $permissions = Permission::orderBy('name')->get();

        return view( 'users.role', compact( 'role', 'permissions' ) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Role $role )
    {
        $request->validate([
            'name' => [ 'required', Rule::unique( 'roles' )->ignore( $role->id ), 'alpha' ]
        ]);

        $role->name = $request->name;
        $role->save();

        flash( $role->name . ' has been successfully updated.' )->success();
        return redirect( '/roles' );
    }

    /**
     * Update the permissions
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePermissions( Request $request, Role $role )
    {
        $role->syncPermissions( $request->permissions );

        flash( 'Permissions for ' . $role->name . ' have been successfully updated.' )->success();
        return redirect( '/roles' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( Role $role )
    {
        $role->delete();
        flash( $role->name . ' has been successfully deleted.' )->success();
        return redirect( '/roles' );
    }
}
