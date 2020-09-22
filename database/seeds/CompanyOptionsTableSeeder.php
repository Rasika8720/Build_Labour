<?php

use App\Models\Companies\Answer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // tiers

        DB::table('company_tiers')->insert([
            ['tier_name' => 'Tier 1'],
            ['tier_name' => 'Tier 2'],
            ['tier_name' => 'Tier 3'],
            ['tier_name' => 'Tier 4'],
        ]);

        // business types

        DB::table('company_business_types')->insert([
            ['business_type' => 'Residential'],
            ['business_type' => 'Commercial'],
            ['business_type' => 'Civil']
        ]);


        // main functions

        $main1 = new \App\Models\Options\MainFunction();
        $main1->main_name = 'Building Contractor';
        $main1->save();

        $main2 = new \App\Models\Options\MainFunction();
        $main2->main_name = 'Trade Services Contractor';
        $main2->save();

        $main3 = new \App\Models\Options\MainFunction();
        $main3->main_name = 'Supplier';
        $main3->save();

        $main4 = new \App\Models\Options\MainFunction();
        $main4->main_name = 'Design Consultant';
        $main4->save();

        $main5 = new \App\Models\Options\MainFunction();
        $main5->main_name = 'Training and Education Provider';
        $main5->save();

        Answer::create([
            'answer' => 'Wall painting',
            'main_function_id' => $main1->id
        ]);

        Answer::create([
            'answer' => 'Interior Design',
            'main_function_id' => $main4->id
        ]);

        // main functions
        DB::table('company_secondary_functions')->insert([
            ['secondary_name' => 'Item 1 - Building Contractor', 'main_id' => $main1->id],
            ['secondary_name' => 'Item 2 - Building Contractor', 'main_id' => $main1->id],
            ['secondary_name' => 'Item 3 - Building Contractor', 'main_id' => $main1->id],
            ['secondary_name' => 'Plumbing', 'main_id' => $main2->id],
            ['secondary_name' => 'Carpentry', 'main_id' => $main2->id],
            ['secondary_name' => 'Item 1 - Supplier', 'main_id' => $main3->id],
            ['secondary_name' => 'Item 2 - Supplier', 'main_id' => $main3->id],
            ['secondary_name' => 'Engineer', 'main_id' => $main4->id],
            ['secondary_name' => 'Architect', 'main_id' => $main4->id],
            ['secondary_name' => 'Item 1 - Training and Education Provider', 'main_id' => $main5->id],
            ['secondary_name' => 'Item 2 - Training and Education Provider', 'main_id' => $main5->id],
        ]);

    }
}
