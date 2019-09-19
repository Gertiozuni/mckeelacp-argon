<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Vlan;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class VlansTableSeeder extends Seeder
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

        $totalUsers = DB::connection( 'legacy' )->table( 'network_vlans' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' legacy vlans to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' vlans/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'network_vlans' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' vlans' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $vlan = Vlan::find( $legacyItem->id );
                
                if ( ! $vlan )
                {
                    $vlan = new Vlan;
                }

                /* Basic fields.. */
                $vlan->id = $legacyItem->id;
                $vlan->vlan = $legacyItem->vlan;
                $vlan->description = $legacyItem->notes;
                $vlan->subnet = $legacyItem->subnet;
                $vlan->alert = $legacyItem->alert;

                $vlan->save();
            }
            \DB::commit();
        }
    }
}
