<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Vlan;

class VlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vlans = Vlan::orderBy( 'vlan' )->get();

        return view( 'network.vlans', compact( 'vlans' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form( Vlan $vlan )
    {
        return view( 'network.vlansform', compact( 'vlan' ) );
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
            'vlan' => [ 'required', 'unique:vlans,vlan', 'numeric' ],
            'subnet' => [ 'required', 'numeric' ],
        ]);

        Vlan::insert([
            'vlan' => $request->vlan,
            'subnet' => $request->subnet,
            'description' => $request->description,
        ]);

        flash( 'Vlan has been successfully saved' );
        return redirect( '/network/vlans' );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vlan  $vlan
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Vlan $vlan )
    {
        $request->validate([
            'vlan' => [ 'required', Rule::unique( 'vlans', 'vlan' )->ignore( $vlan->id ), 'numeric' ],
            'subnet' => [ 'required', 'numeric' ],
        ]);

        $vlan->fill( $request->all() );
        $vlan->save();

        flash( 'Vlan has been successfully updated' );
        return redirect( '/network/vlans' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vlan  $vlan
     * @return \Illuminate\Http\Response
     */
    public function destroy( Vlan $vlan )
    {
        $vlan->delete();

        return response()->json([
            'success' => 'success'
        ]);
    }
}
