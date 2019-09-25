<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NSwitch;
use App\Models\Vlan;
use Carbon\Carbon;

class SwitchController extends Controller
{
    /**
     * Display a listing of the switch.
     */
    public function index( NSwitch $switch )
    {
        $switch->load( 'ports', 'ports.vlans' );
        $switch->ports->loadCount( 'logs' );

        /* get the vlans */
        $vlans = Vlan::orderBy('vlan')->get();

        return view( 'network.switch.index', compact( 'switch', 'vlans' ) );
    }

    /**
     * Display a listing of the switch logs.
     */
    public function logs( NSwitch $switch )
    {

        $search = request()->search;

        // get the logs
        $logs = $switch->logs()
            ->orderByDesc( 'created_at' )
            ->when( $search, function( $query ) use ( $search ) {
                return $query->where( 'event', 'LIKE', '%' . $search . '%' );
            } )
            ->with( 'user', 'port' )
            ->paginate( 20 );

        // if ajex return object
        if( request()->ajax() )
        {
            return response()->json([
                'logs' => $logs
            ]);
        }

        // saftey check
        if( ! $switch->logs->count() )
        {
            flash( 'Could not find any logs for this switch.' );
            return back();
        }
        
        return view( 'network.switch.logs', compact( 'switch', 'logs' ) );
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
