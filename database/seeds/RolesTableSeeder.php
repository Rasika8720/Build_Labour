<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'Worker']);
        Role::create(['name' => 'Company']);
        Role::create(['name' => 'Contractor']);
        Role::create(['name' => 'Training']);
    }
}
