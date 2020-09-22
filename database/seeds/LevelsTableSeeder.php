<?php

use App\Models\Skills\Level;
use App\Models\Skills\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table_name = 'levels';

        $result = Level::all();

       // if (!$result) {
            DB::table($table_name)->insert([
                [ 'name' => 'Beginner' ],
                [ 'name' => 'Competent' ],
                [ 'name' => 'Expert' ],
            ]);
       // }

    }
}
