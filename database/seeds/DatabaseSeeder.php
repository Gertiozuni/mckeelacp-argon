<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // $this->call(CampusesTableSeeder::class);
        // $this->call(VlansTableSeeder::class);
        // $this->call(SwitchesTableSeeder::class);
        // $this->call(PortsTableSeeder::class);
        // $this->call(ClassroomStudentsTableSeeder::class);
        // $this->call(ClassroomTeachersTableSeeder::class);
        //$this->call(PortVlanTableSeeder::class);
        $this->call(SeedSwitchLogsTable::class);

    }
}
