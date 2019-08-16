<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\PortVlan;
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

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'network_vlan_map' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' vlan maps' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $portvlan = PortVlan::find( $legacyItem->id );
                
                if ( ! $portvlan )
                {
                    $portvlan = new PortVlan;
                }

                /* Basic fields.. */
                $portvlan->id = $legacyItem->id;
                $portvlan->port_id = $legacyItem->port_id;
                $portvlan->vlan = $legacyItem->vlan;

                $portvlan->save();
            }
            \DB::commit();
        }
    }
}
