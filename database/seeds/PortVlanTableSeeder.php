<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\PortVlan;
use App\Models\Vlan;

use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class PortVlanTableSeeder extends Seeder
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

        $totalUsers = DB::connection( 'legacy' )->table( 'network_vlan_map' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' legacy vlan maps to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' vlan maps/loop' );
        $output->writeLn( '' );

        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::table( 'port_vlans' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' vlan maps' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                /* find vlan  */
                $vlan = Vlan::where( 'vlan', $legacyItem->vlan )->first();

                if( $vlan ) 
                {
                    $portvlan = DB::table( 'port_vlan' )->where( 'vlan_id', $vlan->id )->where( 'port_id', $legacyItem->port_id )->first();

                    if ( ! $portvlan )
                    {
                        DB::table( 'port_vlan' )->insert( [
                            'port_id' => $legacyItem->port_id,
                            'vlan_id' => $vlan->id
                        ]);
                    }
                }
            }
            \DB::commit();
        }
    }
}
