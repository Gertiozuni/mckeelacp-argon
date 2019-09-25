<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NSwitch;
use App\Models\Campus;

use App\Helpers\SwitchHelper;

class SwitchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $campuses = Campus::orderBy( 'name' )
            ->with( [ 'switches' => function( $query ) {
                $query->withCount( 'ports' );
                $query->withCount( 'logs' );
            } ] )
            ->get();

        return view( 'network.switches', compact( 'campuses' ) );
    }

    /**
     * Show the form for creating or editing a switch
     *
     * @return \Illuminate\Http\Response
     */
    public function form( NSwitch $switch )
    {
        $campuses = Campus::orderBy( 'name' )->get();
        return view( 'network.switchesform', compact( 'switch', 'campuses' ) );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'ip_address' => [ 'required', 'unique:switches,ip_address', 'ip' ],
            'location'  => [ 'required' ],
            'fiber_ports' => [ 'required', 'numeric' ],
            'campus_id' => [ 'required', 'numeric' ]
        ]);

        /* insert form data */
        $switch = new NSwitch;
        $switch->fill( $request->all() );

        $helper = new SwitchHelper;

        /* if we can ping it, lets go ahead and get all the info. if we cant, make it inactive and dont sync it */
        $ping = $helper->canPing( $switch->ip_address );
        if( ! $ping )
        {
            $switch->active = 0;
            $switch->save();
            flash( 'Switch has been added but could not be synced.' );
        }
        else 
        {
            /* call the helper to do telnet connection to get the rest of the data automaticsally */
            $helper->add( $switch );
            flash( 'Switch has been successfully added' );
        }

        return redirect( '/network/switches' );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NSwitch  $nSwitch
     * @return \Illuminate\Http\Response
     */
    public function show(NSwitch $switch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NSwitch  $nSwitch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NSwitch $switch)
    {
        request()->validate([
            'ip_address' => [ 'required', 'unique:switches,ip_address', 'ip' ],
            'location'  => [ 'required' ],
            'fiber_ports' => [ 'required', 'numeric' ],
            'campus_id' => [ 'required', 'numeric' ]
        ]);

        /* insert form data */
        $switch->fill( $request->all() );

        /* can we ping it? */
        $helper = new SwitchHelper;
        $ping = $helper->canPing( $switch->ip_address );

        if( ! $ping )
        {
            flash( 'Switch has been updated but could not be synced.' );
        }
        else 
        {
            $helper->sync( $switch );
            flash( 'Switch has been successfully updated' );
        }

        return redirect( '/network/switches' );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NSwitch  $nSwitch
     * @return \Illuminate\Http\Response
     */
    public function destroy(NSwitch $switch)
    {
        // $switch->delete();

        return response()->json([
            'status' => 'success'
        ]);    
    }
}
