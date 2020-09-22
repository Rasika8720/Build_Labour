<?php

use App\Models\Skills\Skill;
use App\Models\Users\UserSkill;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'skills';
        $result = Skill::all();

       // if (!$result) {
            DB::table($table_name)->insert([
                [ 'name' => 'Quality Control' ],
                [ 'name' => 'Communication Skills' ],
                [ 'name' => 'Time Management' ],
                [ 'name' => 'Can Accept Criticism' ],
                [ 'name' => 'Teamwork' ],
            ]);
     //   }


    }
}
