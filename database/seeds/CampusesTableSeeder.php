<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Campus;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class CampusesTableSeeder extends Seeder
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

        $totalUsers = DB::connection( 'legacy' )->table( 'campuses' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' legacy campuses to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' campuses/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'campuses' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' campuses' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyItem )
            {
                $campus = Campus::find( $legacyItem->id );
                
                if ( ! $campus )
                {
                    $campus = new Campus;
                }

                /* Basic fields.. */
                $campus->id = $legacyItem->id;
                $campus->name = $legacyItem->name;
                $campus->abbreviation = $legacyItem->abbreviation;
                $campus->code = $legacyItem->code;
                $campus->save();
            }
            \DB::commit();
        }
    }
}
