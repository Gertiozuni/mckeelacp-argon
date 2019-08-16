<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\NSwitch;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class SwitchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
 		/* Init */
        Model::unguard();
        $output = new ConsoleOutput();
        $output->writeLn( '' );
        $perLoop = 100;

        $totalUsers = DB::connection( 'legacy' )->table( 'network_switches' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' legacy switches to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' switches/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'network_switches' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' switches' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $switch = NSwitch::find( $legacyItem->id );
                
                if ( ! $switch )
                {
                    $switch = new NSwitch;
                }

                /* Basic fields.. */
                $switch->id = $legacyItem->id;
                $switch->ip_address = $legacyItem->ip_address;
                $switch->mac_address = $legacyItem->mac_address;
                $switch->ethernet_ports = $legacyItem->ethernet_ports;
                $switch->fiber_ports = 4;
                $switch->campus_id = $legacyItem->campus_id;
                $switch->location = $legacyItem->location;
                $switch->sub_location = $legacyItem->sub_location;
                $switch->model = $legacyItem->model;
                $switch->active = $legacyItem->active;
                $switch->uptime = $legacyItem->uptime;
                $switch->checked_in = $legacyItem->checked_in;

                $switch->save();
            }
            \DB::commit();
        }
    }
}
