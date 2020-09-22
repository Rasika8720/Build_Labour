<?php

use App\School;
use Illuminate\Database\Seeder;

class SchoolTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $schools = [];

        $schools[] = ['name'=> 'The University of New South Wales'];
        $schools[] = ['name'=> 'The University of Sydney'];
        $schools[] = ['name'=> 'The University of Melbourne'];
        $schools[] = ['name'=> 'Monash University'];
        $schools[] = ['name'=> 'The University of Queensland'];
        $schools[] = ['name'=> 'Australian National University'];
        $schools[] = ['name'=> 'The University of Adelaide'];
        $schools[] = ['name'=> 'Queensland University of Technology'];
        $schools[] = ['name'=> 'Macquarie University'];
        $schools[] = ['name'=> 'The University of Western Australia'];
        $schools[] = ['name'=> 'RMIT University'];
        $schools[] = ['name'=> 'University of Technology Sydney'];
        $schools[] = ['name'=> 'Curtin University'];
        $schools[] = ['name'=> 'Deakin University'];
        $schools[] = ['name'=> 'University of Wollongong'];
        $schools[] = ['name'=> 'Griffith University'];
        $schools[] = ['name'=> 'The University of Newcastle'];
        $schools[] = ['name'=> 'La Trobe University'];
        $schools[] = ['name'=> 'University of South Australia'];
        $schools[] = ['name'=> 'Swinburne University of Technology'];
        $schools[] = ['name'=> 'University of Tasmania'];
        $schools[] = ['name'=> 'Charles Sturt University'];
        $schools[] = ['name'=> 'Western Sydney University'];
        $schools[] = ['name'=> 'University of New England, Australia'];
        $schools[] = ['name'=> 'James Cook University'];
        $schools[] = ['name'=> 'Murdoch University'];
        $schools[] = ['name'=> 'Flinders University'];
        $schools[] = ['name'=> 'Victoria University'];
        $schools[] = ['name'=> 'Edith Cowan University'];
        $schools[] = ['name'=> 'University of Canberra'];
        $schools[] = ['name'=> 'Southern Cross University'];
        $schools[] = ['name'=> 'University of Southern Queensland'];
        $schools[] = ['name'=> 'Australian Catholic University'];
        $schools[] = ['name'=> 'Central Queensland University'];
        $schools[] = ['name'=> 'Federation University Australia'];
        $schools[] = ['name'=> 'Bond University '];
        $schools[] = ['name'=> 'Charles Darwin University'];
        $schools[] = ['name'=> 'University of the Sunshine Coast'];
        $schools[] = ['name'=> 'The University of Notre Dame Australia'];
        $schools[] = ['name'=> 'Torrens University Australia'];
        $schools[] = ['name'=> 'Melbourne Institute of Technology'];

        foreach ($schools as $s) {

            $name = strtolower(str_replace(" ", "_", $s['name']));

            School::create([
                'school_name' => $s['name'],
                'logo_url' => env('APP_URL') . '/img/schools/' . $name . '.png',
            ]);
        }
    }
}
