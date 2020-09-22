<?php

use App\Models\Companies\Job;
use App\Models\Companies\JobApplicant;
use App\Models\Companies\JobStat;
use App\Models\Options\BusinessType;
use App\Models\Options\Tier;
use App\Models\Users\Education;
use App\Models\Tickets\Ticket;
use App\Models\Users\UserSkill;
use App\Models\Users\UserTicket;
use App\Models\Users\WorkerArea;
use App\Models\Users\WorkerDetail;
use App\Models\Users\WorkerTier;
use App\Models\Users\WorkExperience;
use App\Models\Users\WorkExperienceResponsibility;
use App\User;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AnotherWorker3TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $user = new User();
        $user->first_name = $faker->firstName;
        $user->last_name = $faker->lastName;
        $user->email = $faker->email;
        $user->password = 'secret';
        $user->date_of_birth = Carbon::parse('08/10/1988');
        $user->mobile_number = '+61 412345678';
        $user->address = $faker->address;
        $user->marital_status = 'Single';
        $user->gender = 'Male';
        $user->is_verified = Carbon::now();
        $user->role_id = 1; // worker
        $user->save();

        $responsibilities = [
            'Monitor Plant Electrical Systems.',
            'Able to Fixed Power Plant basic panels'
        ];

        $w = WorkExperience::create([

            'job_role' => 'Power Plant Electrician',
            'company_name' => 'Tesla',
            'user_id' => $user->id,
            'project_size' => '200,000',
            'start_month' => 6,
            'start_year' => 2011,
            'end_month' => 9,
            'end_year' => 2013,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        foreach ($responsibilities as $responsibility) {
            WorkExperienceResponsibility::create([
                'work_experience_id' => 1,
                'responsibility' => $responsibility
            ]);
        }

        WorkExperience::create([
            'job_role' => 'Electrician',
            'company_id' => 2,
            'user_id' => $user->id,
            // 'location' => 'Richmond, Victoria, Australia',
            'project_size' => '1,000,000',
            'start_month' => 2,
            'start_year' => 2009,
            'end_month' => 2,
            'end_year' => 2012,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $worker = WorkerDetail::create([
            'profile_description' => 'A Certified PSO Electrician.',
            'english_skill' => true,
            'drivers_license' => true,
            'right_to_work' => true,
            'main_skill' => '3 Years experienced in the electrical job industry',
            'introduction' => 'Able to find a Supervisor Job',
            'when' => 12,
            'max_distance' => 100,
            'state' => 'VIC',
            'nrole_right_to_work_au' => true,
            'user_id' => $user->id
        ]);

            $user2 = User::whereEmail(env('APP_EMAIL_SUPPORT'))->first();


            UserSkill::create([
                'user_id' => $user->id,
                'skill_id' => 1,
                'level_id' => 1
            ]);

            UserSkill::create([
                'user_id' => $user->id,
                'skill_id' => 5,
                'level_id' => 2
            ]);

            // worker areas

            $commercial = BusinessType::where('business_type', 'Commercial')->first();

            WorkerArea::insert([
                'worker_id' => $worker->id,
                'business_type_id' => $commercial->id
            ]);

            // worker tiers

            $tier1 = Tier::where('tier_name', 'Tier 1')->first();
            $tier2 = Tier::where('tier_name', 'Tier 2')->first();

            WorkerTier::insert([
                'worker_id' => $worker->id,
                'tier_id' => $tier1->id
            ]);

            WorkerTier::insert([
                'worker_id' => $worker->id,
                'tier_id' => $tier2->id
            ]);

            $job = Job::find(4);

            // job stat

            JobApplicant::create([
                'job_id' => $job->id,
                'user_id' => $user->id,
                'applied_at' => Carbon::now()
            ]);


            JobStat::create([
                'job_id' => $job->id,
                'scored_to' => $user->id,
                'performed_by' => $user->id,
                'category' => 'viewed',
                'created_at' => Carbon::now()
            ]);

            JobStat::create([
                'job_id' => $job->id,
                'scored_to' => $user->id,
                'performed_by' => $user->id,
                'category' => 'favourite',
                'created_at' => Carbon::now()
            ]);

    }
}