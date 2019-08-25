<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\NSwitch;
use App\Models\Vlan;
use App\Models\Campus;
use App\Models\Port;
use App\Models\PortHistory;
use App\Models\PortVlan;
use App\Models\User;
use App\Models\SwitchHistory;

use Auth;
use Mail;
use Artisan;
use Carbon\Carbon;

use App\Mail\SwitchSyncMail;
class SyncSwitches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:sync-switches {switch?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the switches information.';

    /**
     * Telnet
     *
     * @var string
     */
    private $telnet;
    private $username;
    private $password;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
    *   Helper function to start the telnet client
    */
    private function startTelnet( $ip )
    {
        $this->username = config( 'mckeel.telnet_id' );
        $this->password = config( 'mckeel.telnet_pass' );
        $this->telnet = @fsockopen( $ip, 23, $errno, $errstr, 5 );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* init */
        $switchId = $this->argument('switch');
        $switches = NetworkSwitch::orderBy( 'campus_id', 'ASC' )
            ->when( $switchId, function( $query, $switchId ) {
                return $query->where( 'id', $switchId );
            })->get();

        $emailMessage = [];
        $clear = "\r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n clear \r\n";
        $noAlerts = NetworkVlan::where( 'alert', 0 )->pluck( 'vlan' )->toArray();

        foreach( $switches as $switch )
        {
            $rawPorts = [];
            $rawFiber = [];
            $rawSysInfo = [];
            $rawConfig = [];
            $ports = [];
            $portCount = 0;
            $model = '';
            $macAddress = '';
            $fiberPorts = [];
            $uptime = '';
            $messages = [];

            $switch->load( 'portsinfo', 'portsinfo.vlans' );
            /* Start Telnet Client */
            $this->startTelnet( $switch->ip_address );

            if( $this->telnet )
            {

                stream_set_timeout( $this->telnet, 2 );

                fputs( $this->telnet, "$this->username\r\n" );
                fputs( $this->telnet, "$this->password\r\n" );
                fputs( $this->telnet, "enable\r\n" );

                /* get ports */
                fputs( $this->telnet, "show interface status\r\n" );
                fputs( $this->telnet, "\r\n" );
                fputs( $this->telnet, "\r\n" );

                while($line = fgets( $this->telnet ) )
                {
                    $rawPorts[] = trim( $line );
                }

                /* get system info */
                fputs( $this->telnet, $clear );
                fputs( $this->telnet, "show system\r\n" );

                while( $line = fgets( $this->telnet ) )
                {
                    $rawSysInfo[] = trim($line);
                }

                /* get config */
                fputs( $this->telnet, $clear );
                fputs( $this->telnet, "show running-config\r\n" );
                fputs( $this->telnet, "\r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n \r\n" );

                while($line = fgets( $this->telnet ) )
                {
                    $rawConfig[] = trim($line);
                }

                /* get fiber ports */
                fputs( $this->telnet, $clear );
                fputs( $this->telnet, "show fiber-ports optical-transceiver\r\n" );

                while($line = fgets( $this->telnet ) )
                {
                    $rawFiber[] = trim($line);
                }

                fclose( $this->telnet );

                /* lets remove the first 9 items we dont even need */
                $rawPorts = array_slice( $rawPorts, 9 );

                /* Get rid of everything we don't need */
                $index = array_search( "console#clear", $rawSysInfo );
                $rawSysInfo = array_slice( $rawSysInfo, $index + 4 );

                /* Get rid of everything we don't need */
                $index = array_search( "console#clear", $rawConfig );
                $rawConfig = array_slice( $rawConfig, $index + 4 );

                /* Get rid of everything we don't need */
                $index = array_search( "console#clear", $rawFiber );
                $rawFiber = array_slice( $rawFiber, $index + 4 );

                $implode = implode( ',', $rawConfig );
                $config = preg_split( "/!/", $implode );

                /* lets get the port count, and determine the active ports */
                foreach( $rawPorts as $line )
                {
                    /* counts the lines containining 1/g##. Ex 1/g1, 1/g20, 1/g41 */
                    if( preg_match( "/[0-9]\/g([1-9]|[1-8][0-9]|9[0-9]|100)/", $line ) )
                    {
                        $portCount++;

                        preg_match("/1\/g(.*?) /", $line, $output );

                        $portNum = $output[ 1 ];

                        if ( strpos( $line, 'Up') )
                        {
                            $ports[ $portNum ][ 'active' ] = true;
                        }
                        else
                        {
                            $ports[ $portNum ][ 'active' ] = false;
                        }

                        $ports[ $portNum ][ 'fiber' ] = false;

                    }

                }

                 /* do we have any fiber ports active */
                foreach( $rawFiber as $line )
                {

                    /* counts the lines containining 1/g##. Ex 1/g1, 1/g20, 1/g41 */
                    if( preg_match( "/[0-9]\/g([1-9]|[1-8][0-9]|9[0-9]|100)/", $line ) )
                    {

                        preg_match("/1\/g(.*?) /", $line, $output );

                        if( $output[ 1 ] > ( $portCount - 4 ) )
                        {
                            $portNum = $output[ 1 ];
                            $ports[ $portNum ][ 'fiber' ] = true;
                        }
                    }

                }

                /* get the uptime */
                foreach( $rawSysInfo as $info )
                {

                    /* lets look for the uptime */
                    if( strpos( $info, 'Time' ) )
                    {

                        $output = preg_split("/System Up Time: /", $info );
                        $data = $output[ 1 ];

                        $uptimeData = explode( ' ', $data );
                        $timeData = explode( ':', $uptimeData[ 2 ] );

                        $days = $uptimeData[ 0 ];
                        $hours = preg_replace( "/[^0-9,.]/", "", $timeData[ 0 ] );
                        $mins  = preg_replace( "/[^0-9,.]/", "", $timeData[ 1 ] );
                        $seconds = preg_replace( "/[^0-9,.]/", "", $timeData[ 2 ] );

                        $uptime = Carbon::now();
                        $uptime->subDays( $days )->subHours( $hours )->subMinutes( $mins )->subSeconds( $seconds );
                        break;


                    }
                }

                /* if the current time and the saved time are 60 seconds apart, chances are it went down */
                if( $uptime->diffInSeconds( $switch->uptime ) > 60 )
                {

                    /* update the switch */
                    $switch->uptime = $uptime;

                    $emailMessage[ $switch->ip_address ][ 'uptime' ] = 'Uptime change detected. Possible power outage';

                    /* And add the disconnection to the history */
                    NetworkSwitchHistory::insert( [
                        'switch_id'     => $switch->id,
                        'created_at'    => Carbon::Now(),
                        'user_id'       => null,
                        'info'          => 'Switch power outage detected'
                    ] );
                }


                /* run through running config */
                foreach( $config as $line )
                {
                    /* counts the lines containining 1/g##. Ex 1/g1, 1/g20, 1/g41 */
                    if( preg_match( "/[0-9]\/g([1-9]|[1-8][0-9]|9[0-9]|100)/", $line ) )
                    {

                        preg_match("/1\/g(.*?),/", $line, $output );
                        $portNum = $output[ 1 ];

                        if( strpos( $line, 'access' ) )
                        {
                            preg_match("/access vlan (.*?),/", $line, $vlan);
                            $ports[ $portNum ][ 'mode' ] = 'access';
                            $ports[ $portNum ][ 'vlans' ] = $vlan[ 1 ];

                        }
                        else if( strpos( $line, 'general' ) )
                        {
                            $ports[ $portNum ][ 'mode' ] = 'general';

                            preg_match_all( "/add (.*?) tagged/", $line, $output );
                            $mode = $output[ 1 ];

                            /* get all the vlans */
                            foreach( $output[ 1 ] as $out )
                            {
                                $v = explode( ',', $out );

                                foreach( $v as $set )
                                {
                                    if( strpos( $set, '-' ) )
                                    {
                                        $numbers = explode( '-', $set );
                                        $range = range( $numbers[ 0 ], $numbers[ 1 ] );

                                        foreach( $range as $r )
                                        {
                                            $ports[ $portNum ][ 'vlans' ][] = $r;
                                        }
                                    }
                                    else
                                    {
                                        $ports[ $portNum ][ 'vlans' ][] = $set;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $ports[ $portNum ][ 'mode' ] = null;
                            $ports[ $portNum ][ 'vlans' ] = null;
                        }
                    }
                }



                // This is where the magic happens //

                foreach( $switch->portsinfo as $port )
                {
                    $number = $port->port;

                    /* check if active status has changed */
                    if( $port->active != $ports[ $number ][ 'active' ] )
                    {
                        if( $port->active )
                        {
                            $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'status' ] = 'Port became inactive.';
                        }
                        else
                        {
                            $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'status' ] = 'Port became active.';
                        }

                        $port->active = $ports[ $number ][ 'active' ];
                        $port->last_updated = Carbon::now();
                        $port->save();

                        if( $port->mode == 'access' && in_array( $port->vlans[0]->vlan, $noAlerts ) )
                        {
                            unset( $emailMessage[ $switch->ip_address ] );
                        }
                        else
                        {
                            /* Add the history */
                            NetworkPortHistory::insert( [
                                'info'          => $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'status' ],
                                'user_id'       => null,
                                'created_at'    => Carbon::now(),
                                'port_id'       => $port->id
                            ] );
                        }

                    }


                    $modeChange = false;

                    /* check if mode has changed */
                    if( isset( $ports[ $number ][ 'mode' ] ) )
                    {
                        if( $port->mode != $ports[ $number ][ 'mode' ] )
                        {
                            /* since the mode changed, delete the old vlans so we can add the new mode vlans */
                            $port->vlans()->delete();
                            $modeChange = true;

                            if( $port->mode == 'access' )
                            {
                                $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'mode' ] = 'Mode has been changed to general.';
                            }
                            else
                            {
                                $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'mode' ] = 'Mode has been changed to access.';
                            }

                            /* update the port */
                            $port->mode = $ports[ $number ][ 'mode' ];
                            $port->last_updated = Carbon::now();
                            $port->save();

                            /* add the new vlans */
                            $insert = [];
                            if( is_array( $ports[ $number ][ 'vlans' ] ) )
                            {
                                foreach( $ports[ $number ][ 'vlans' ] as $vlan )
                                {
                                    $insert[] = [
                                        'port_id'   => $port->id,
                                        'vlan'      => $vlan
                                    ];
                                }

                                NetworkVlanMap::insert( $insert );
                            }
                            else
                            {
                                NetworkVlanMap::insert( [
                                    'port_id'   => $port->id,
                                    'vlan'      => $ports[ $number ][ 'vlans' ]
                                ] );

                            }

                            /* Add the history */
                            NetworkPortHistory::insert( [
                                'info'          => $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'mode' ],
                                'user_id'       => null,
                                'created_at'    => Carbon::now(),
                                'port_id'       => $port->id
                            ] );
                        }
                    }

                    /* only check for vlan changes if the mode didnt change */
                    if( ! $modeChange )
                    {
                        $vlanChange = [];
                        /* first lets see if any vlans are being removed */
                        foreach( $port->vlans as $vlan )
                        {
                            if( isset( $ports[ $number ][ 'vlans' ] ) )
                            {
                                if( is_array( $ports[ $number ][ 'vlans' ] ) )
                                {
                                    if( ! in_array( $vlan->vlan, $ports[ $number ][ 'vlans' ] ) )
                                    {
                                        $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansremoved' ][] = $vlan->vlan;
                                        $vlan->delete();
                                    }
                                }
                                else
                                {
                                    if( $vlan->vlan != $ports[ $number ][ 'vlans' ] )
                                    {
                                        $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansremoved' ][] = $vlan->vlan;
                                        $vlan->delete();
                                    }
                                }
                            }
                        }



                        /* nows lets see if any vlans are being added */
                        if( isset( $ports[ $number ][ 'vlans' ] ) && count( $ports[ $number ][ 'vlans' ] ) )
                        {
                            if( is_array( $ports[ $number ][ 'vlans' ] ) )
                            {
                                foreach( $ports[ $number ][ 'vlans' ] as $vlan )
                                {
                                    $vlanExist = $port->vlans->where( 'vlan', '=', $vlan )->first();

                                    if( ! $vlanExist )
                                    {
                                        $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansadded' ][] = $vlan;

                                        NetworkVlanMap::insert( [
                                            'vlan'    => $vlan,
                                            'port_id' => $port->id
                                        ] );
                                    }

                                    // $port->last_updated = Carbon::now();
                                    // $port->save();

                                }
                            }
                            else
                            {
                                $vlanExist = $port->vlans->where( 'vlan', '=', $ports[ $number ][ 'vlans' ] )->first();

                                if( ! $vlanExist )
                                {
                                    $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansadded' ][] = $ports[ $number ][ 'vlans' ];

                                    NetworkVlanMap::insert( [
                                        'vlan'    => $ports[ $number ][ 'vlans' ],
                                        'port_id' => $port->id
                                    ] );
                                }

                                // $port->last_updated = Carbon::now();
                                // $port->save();

                            }
                        }


                        if( isset( $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansremoved' ] ) )
                        {
                            $message = 'The following vlans were removed: ';

                            foreach( $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansremoved' ] as $vlan )
                            {
                                $message .= $vlan . ',';
                            }

                            $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansremoved' ] = $message;

                            NetworkPortHistory::insert( [
                                'info'          => $message,
                                'user_id'       => null,
                                'created_at'    => Carbon::now(),
                                'port_id'       => $port->id
                            ] );

                            $port->last_updated = Carbon::now();
                            $port->save();

                        }

                        if( isset( $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansadded' ] ) )
                        {
                            $message = 'The following vlans were added: ';

                            foreach( $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansadded' ] as $vlan )
                            {
                                $message .= $vlan . ',';
                            }

                            $emailMessage[ $switch->ip_address ][ 'ports' ][ $number ][ 'vlansadded' ] = $message;

                            NetworkPortHistory::insert( [
                                'info'          => $message,
                                'user_id'       => null,
                                'created_at'    => Carbon::now(),
                                'port_id'       => $port->id
                            ] );

                            $port->last_updated = Carbon::now();
                            $port->save();


                        }

                    }
                }
            }
            $switch->checked_in = Carbon::now();
            $switch->save();
        }

        if( count( $emailMessage ) )
        {
            $users = User::all();
            Mail::to( $users )->send( new SwitchSyncMail( $emailMessage ) );
        }
    }
}
