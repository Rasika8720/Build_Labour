<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Users\UserSkill::truncate();
        DB::statement("SET foreign_key_checks=1");

        $this->call(AdministratorsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(JobRolesSeeder::class);
        $this->call(CourseTableSeeder::class);
        $this->call(SchoolTableSeeder::class);
        $this->call(CompanyOptionsTableSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(SkillsTableSeeder::class);
        $this->call(LevelsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AnotherWorkerTableSeeder::class);
        $this->call(AnotherWorker1TableSeeder::class);
        $this->call(AnotherWorker2TableSeeder::class);
    }
}
