<?php

use App\Course;
use Illuminate\Database\Seeder;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Course::insert([
           'course_name' => 'Construction & Engineer'
        ]);

        Course::insert([
            'course_name' => 'Computer Literation'
        ]);

        Course::insert([
            'course_name' => 'Advanced Welding Certification'
        ]);
    }
}
