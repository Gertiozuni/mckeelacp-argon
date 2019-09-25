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
        /* init */
        $event = request()->event;
        $port = request()->port;
        $start = request()->startDate;
        $end = request()->endDate;

        // get the logs
        $logs = $switch->logs()
            ->orderByDesc( 'created_at' )
            ->when( $event, function( $query ) use ( $event ) {
                $query->where( 'event', 'LIKE', '%' . $event . '%' );
            } )
            ->when( $port, function( $query ) use ( $switch, $port ) {
                $temp = $switch->ports()->where( 'port', $port )->first();
                $query->where( 'port_id', $temp->id );
            } )
            ->when( $start || $end, function( $query ) use ( $start, $end ) {
                if( $start && $end )
                {
                    $query->whereBetween( 'created_at', [ $start, $end ] );
                }
                else if( $start )
                {
                    $query->where( 'created_at', '>=', $start );
                }
                else if( $end )
                {
                    $query->where( 'created_at', '<=', $end );
                }
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
