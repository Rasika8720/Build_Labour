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

class AnotherWorker2TableSeeder extends Seeder
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
        $user->gender = 'Male';
        $user->is_verified = Carbon::now();
        $user->role_id = 1; // worker
        $user->save();

        $responsibilities = [
            'Install fixtures and apparatuses; repair and replace hardware.',
            'Constructs custom cabinetry and furniture as needed; repairs or modifies furniture as needed.'
        ];

        $w = WorkExperience::create([

            'job_role' => 'CARPENTRY SUPERVISOR',
            'company_name' => 'Merkie\'s Construction',
            'user_id' => $user->id,
            'project_size' => '150,000',
            'start_month' => 1,
            'start_year' => 2001,
            'end_month' => 1,
            'end_year' => 2010,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        foreach ($responsibilities as $responsibility) {
            WorkExperienceResponsibility::create([
                'work_experience_id' => 1,
                'responsibility' => $responsibility
            ]);
        }

        $worker = WorkerDetail::create([
            'profile_description' => 'Carpenter',
            'english_skill' => true,
            'drivers_license' => true,
            'right_to_work' => true,
            'main_skill' => 'Looking for a company to utilize my skills.',
            'introduction' => 'Looking for a company to utilize my skills.',
            'when' => 8,
            'max_distance' => 250,
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
            'skill_id' => 2,
            'level_id' => 1
        ]);

        UserSkill::create([
            'user_id' => $user->id,
            'skill_id' => 3,
            'level_id' => 1
        ]);

        UserSkill::create([
            'user_id' => $user->id,
            'skill_id' => 5,
            'level_id' => 2
        ]);

        // tickets

        $ticket = Ticket::where('ticket', 'FWPCOT2238')->first();

        UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id
        ]);


        // worker areas

        $commercial = BusinessType::where('business_type', 'Civil')->first();

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

        $tier2 = Tier::where('tier_name', 'Tier 2')->first();

        WorkerTier::insert([
            'worker_id' => $worker->id,
            'tier_id' => $tier2->id
        ]);

        $tier3 = Tier::where('tier_name', 'Tier 3')->first();

        WorkerTier::insert([
            'worker_id' => $worker->id,
            'tier_id' => $tier3->id
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