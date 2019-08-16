<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Permission;

use App\Rules\AlphaSpaces;

use Carbon\Carbon;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {

        if( $request->ajax() ) 
        {
            $search = $request->search; 

            $permissions = Permission::orderBy( 'name' )
                ->when( $search, function( $query, $search ) {
                    return $query->where( 'name', 'LIKE', '%' . $search . '%' );
                })
                ->paginate( 10 );

            return response()->json([
                'permissions' => $permissions
            ]);
        }

        return view( 'users.permissions' );
    }   

    /**
     * Show the form for creating or editing
     *
     * @return \Illuminate\Http\Response
     */
    public function form( Permission $perm )
    {
        return view( 'users.permissionsform', compact( 'perm' ) );
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
            'name' => [ 'required', 'unique:permissions,name', new AlphaSpaces ]
        ]);

        Permission::insert([
            'name' => $request->name,
            'guard_name' => 'web',
            'created_at' => Carbon::now()
        ]);
        
        flash( 'Permission successfully created.' )->success();
        return redirect( '/permissions' );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Permission $perm )
    {
        $request->validate([
            'name' => [ 'required', Rule::unique( 'permissions' )->ignore( $perm->id ), new AlphaSpaces ]
        ]);

        $perm->name = $request->name;
        $perm->save();
        
        flash( 'Permission successfully updated.' )->success();
        return redirect( '/permissions' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $perm)
    {
        $perm->delete(); 

        return response()->json([
            'status' => 'success'
        ]);    
    }
}
