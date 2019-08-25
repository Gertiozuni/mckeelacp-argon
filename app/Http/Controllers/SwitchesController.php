<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NSwitch;
use App\Models\Campus;

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
            ->with( 'switches' )
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
            'ip_address' => [ 'required', 'unique:switches,ip_address', 'ip_address' ],
            'location'  => [ 'required' ],
            'fiber_ports' => [ 'required', 'numeric' ],
            'campus_id' => [ 'required', 'numeric' ]
        ]);

        $switch = NSwitch::insert([
            'ip_address' => $request->ip_address,
            'fiber_port' => $request->fiber_port,
            'campus_id' => $request->campus_id,
            'location' => $request->location,
            'sub_location' => $request->sub_location
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NSwitch  $nSwitch
     * @return \Illuminate\Http\Response
     */
    public function show(NSwitch $nSwitch)
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
    public function update(Request $request, NSwitch $nSwitch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NSwitch  $nSwitch
     * @return \Illuminate\Http\Response
     */
    public function destroy(NSwitch $nSwitch)
    {
        //
    }
}
