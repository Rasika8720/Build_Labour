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

class AnotherWorker1TableSeeder extends Seeder
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
        $user->mobile_number = '+61 712341228';
        $user->address = $faker->address;
        $user->marital_status = 'Married';
        $user->gender = 'Female';
        $user->is_verified = Carbon::now();
        $user->role_id = 1; // worker
        $user->save();

        $responsibilities = [
            'Accounting Management',
            'Manage Company information',
            'Keep Ledgers up to date'
        ];

        $w = WorkExperience::create([

            'job_role' => 'Bookkeeper',
            'company_name' => 'Jeff Automobile',
            'user_id' => $user->id,
            'project_size' => '200,000',
            'start_month' => 1,
            'start_year' => 2004,
            'end_month' => 3,
            'end_year' => 2009,
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
            'job_role' => 'Accounting Staff',
            'company_name' => 'Sim & Sims Hotel',
            'user_id' => $user->id,
            'project_size' => '300',
            'start_month' => 5,
            'start_year' => 2007,
            'end_month' => 6,
            'end_year' => 2015,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $worker = WorkerDetail::create([
            'profile_description' => 'Accountant',
            'english_skill' => true,
            'drivers_license' => true,
            'right_to_work' => true,
            'main_skill' => 'A professional Certified Public Accountant',
            'introduction' => 'Looking a company that needs good in numbers.',
            'when' => 8,
            'max_distance' => 51,
            'state' => 'VIC',
            'nrole_right_to_work_au' => true,
            'user_id' => $user->id
        ]);

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

        WorkerTier::insert([
            'worker_id' => $worker->id,
            'tier_id' => $tier1->id
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

    }
}