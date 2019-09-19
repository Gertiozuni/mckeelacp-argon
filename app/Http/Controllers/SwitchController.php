<?php

namespace App\Http\Controllers;

use App\Models\NSwitch;
use Illuminate\Http\Request;

class SwitchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( NSwitch $switch )
    {
        $switch->load( 'ports', 'ports.vlans' );

        return view( 'network.switch.index', compact( 'switch' ) );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\NSwitch  $nSwitch
     * @return \Illuminate\Http\Response
     */
    public function edit(NSwitch $nSwitch)
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
