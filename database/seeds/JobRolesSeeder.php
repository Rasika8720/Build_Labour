<?php

use App\Models\Companies\JobRole;
use Illuminate\Database\Seeder;

class JobRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobRole::insert(array(
            ['job_role_name' => 'Master Carpenter'],
            ['job_role_name' => 'Plumber'],
            ['job_role_name' => 'Project Manager']
        ));
    }
}
