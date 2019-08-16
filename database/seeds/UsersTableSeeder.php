<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Carbon\Carbon;

use Symfony\Component\Console\Output\ConsoleOutput;

class UsersTableSeeder extends Seeder
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

        /* Get total count of users... */
        $totalUsers = DB::connection( 'legacy' )->table( 'users' )->count();
        $output->writeLn( 'Found ' . $totalUsers . ' legacy users to migrate' );
        $output->writeLn( 'Importing at ' . $perLoop . ' users/loop' );
        $output->writeLn( '' );

        $foundHim = false;
        for ( $i = 0; $i <= $totalUsers; $i += $perLoop )
        {
            $thisBatch = DB::connection( 'legacy' )->table( 'users' )->orderBy( 'id' )->offset( $i )->limit( $perLoop )->get();
            $output->writeLn( 'Batch ' . $i . '/' . $totalUsers . "\tFound " . count( $thisBatch ) . ' users' );

            \DB::beginTransaction();
            foreach ( $thisBatch as $legacyUser )
            {
                $user = User::find( $legacyUser->id );
                
                if ( ! $user )
                {
                    $user = new User;
                }

                /* Basic fields.. */
                $user->id = $legacyUser->id;
                $user->name = $legacyUser->name;
                $user->email = $legacyUser->email;
                $user->password = $legacyUser->password;
                $user->created_at = $legacyUser->created_at;
                $user->updated_at = $legacyUser->updated_at;
            
                $user->save();
            }
            \DB::commit();
        }
    }
}
