<?php

namespace App\Repositories;

use App\Events\Users\DeleteEducation;
use App\Http\Resources\PeoplesResource;
use App\Models\Companies\Company;
use App\Models\Companies\Job;
use App\Models\Options\BusinessType;
use App\Models\Options\Tier;
use App\Models\Users\Education;
use App\Models\Users\WorkerArea;
use App\Models\Users\WorkerDetail;
use App\Models\Users\WorkerTier;
use App\Models\Users\WorkExperience;
use App\Models\Users\WorkExperienceResponsibility;
use Carbon\Carbon;
use App\User;
use JWTAuth;
use Illuminate\Http\Request;

use Torann\LaravelRepository\Repositories\AbstractRepository;

class WorkerRepository extends AbstractRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = WorkerDetail::class;

    public $workExp = null;
    public $workerDetail = null;

    public function addExperience(Request $request) {

        $user = JWTAuth::toUser();

        $this->workExp = new WorkExperience();
        $this->workExp->setUserId($user->id);

        if (!$this->workExp->store($request)) {

            return false;
        }

        $responsibilities = [];

        if ($request->responsibilities) {

            foreach ($request->responsibilities as $r) {

                if (!empty($r)) {

                    $responsibilities[] = array('work_experience_id' => $this->workExp->id, 'responsibility' => $r);
                }
            }

            WorkExperienceResponsibility::insert($responsibilities);
        }

        $this->workExp->responsibilities;
        $this->workExp->Company;

        return $this->workExp;
    }

    public function updateExperience(Request $request) {


        $user = JWTAuth::toUser();

        $workExp = WorkExperience::find($request->id);

        if (!$workExp) {

            $this->workExp = new WorkExperience();
            $this->workExp->addError('Something wrong!');

            return false;
        }


        $this->workExp = $workExp;
        $this->workExp->setUserId($user->id);

        if (!$this->workExp->store($request)) {

            return false;
        }

        $responsibilities = [];

        if ($request->responsibilities) {

            WorkExperienceResponsibility::where('work_experience_id', $this->workExp->id)->delete();

            foreach ($request->responsibilities as $r) {

                if (!empty($r)) {

                    $responsibilities[] = array('work_experience_id' => $this->workExp->id, 'responsibility' => $r);
                }
            }

            WorkExperienceResponsibility::insert($responsibilities);
        }

        $this->workExp->responsibilities;
        $this->workExp->Company;

        return $this->workExp;
    }

    public function deleteAboutMe() {

        $user = JWTAuth::toUser();

        if ($user->workerDetail) {

            $user->date_of_birth = null;
            $user->gender = null;
            $user->marital_status = null;
            $user->save();

            $user->workerDetail->english_skill = null;
            $user->workerDetail->drivers_license = null;
            $user->workerDetail->save();

        }

        return true;
    }

    public function deleteIdealRole() {

        $user = JWTAuth::toUser();

        if ($user->workerDetail) {

            $user->workerDetail->introduction = null;
            $user->workerDetail->when = null;
            $user->workerDetail->max_distance = null;
            $user->workerDetail->state = null;
            $user->workerDetail->right_to_work = 0;
            $user->workerDetail->save();

        }

        return true;
    }

    public function deleteEducation(Request $request) {

        $education = Education::find($request->id);
        $user = JWTAuth::toUser();

        if ($education) {

            $education->delete();

            event(new DeleteEducation($user));
            return $education;
        }


        return false;
    }

    public function updateCurrentRole(Request $request) {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return false;
        }

        $this->workExp = new WorkExperience();
        $this->workExp->setUserId($user->id);
        $this->workExp->isOnboarding = true;

        $rules = [
            'most_recent_role'  => 'required',
            'exp_year'       => 'nullable|integer',
            'exp_month'      => 'nullable|integer'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ( $validator->fails() ) {

            $this->workExp->errors = $validator->errors()->all();
            $this->workExp->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        $user->workerDetail->exp_month = empty($request->exp_month) ? null : $request->exp_month;
        $user->workerDetail->exp_year = empty($request->exp_month) ? null : $request->exp_year;

        $user->workerDetail->most_recent_role = $request->most_recent_role;
        $user->workerDetail->save();

        return true;
    }

    public function updateSectors(Request $request) {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return false;
        }

        if (isset($request->sectors) && empty($request->sectors)) {

            WorkerArea::where('worker_id', $user->workerDetail->id)->delete();
        }

        if (isset($request->tiers) && empty($request->tiers)) {

            WorkerTier::where('worker_id', $user->workerDetail->id)->delete();
        }

        if (!empty($request->sectors)) {

            WorkerArea::where('worker_id', $user->workerDetail->id)->delete();

            $businessTypes = $request->sectors;

            $businessTypes = array_filter($businessTypes, function($id) {

                // check businsss type

                $b = BusinessType::find($id);

                if ($b) {

                    return $id;
                }

            });


            $saveBusinessTypes = array_map(function($id) use($user) {

                return array('business_type_id' => $id, 'worker_id' => $user->workerDetail->id);

            }, $businessTypes);

            WorkerArea::insert($saveBusinessTypes);

        }

        if (!empty($request->tiers)) {

            WorkerTier::where('worker_id', $user->workerDetail->id)->delete();

            $tiers = $request->tiers;

            $tiers = array_filter($tiers, function($id) {

                // check tier

                $b = Tier::find($id);

                if ($b) {

                    return $id;
                }

            });

            $saveTiers = array_map(function($id) use($user) {

                return array('tier_id' => $id, 'worker_id' => $user->workerDetail->id);

            }, $tiers);

            WorkerTier::insert($saveTiers);

        }

        return true;
    }

    public function updateAffirmations(Request $request) {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return false;
        }

        if ($request->drivers_license_state) {

            $user->workerDetail->saveParams('drivers_license_state', $request->drivers_license_state);
        }

        if ($request->drivers_license_type) {

            $user->workerDetail->saveParams('drivers_license_type', $request->drivers_license_type);
        }

        // australian tfn
        $user->workerDetail->saveParams('australian_tfn', $request->australian_tfn ? $request->australian_tfn : '');


        if (isset($request->right_to_work)) {

            $user->workerDetail->right_to_work = $request->right_to_work;

        } else {

            $user->workerDetail->right_to_work = NULL;
        }

        if (isset($request->has_tfn)) {

            $user->workerDetail->has_tfn = $request->has_tfn;

        } else {

            $user->workerDetail->has_tfn = NULL;
        }

        if (isset($request->has_abn)) {

            $user->workerDetail->has_abn = $request->has_abn;

        } else {

            $user->workerDetail->has_abn = NULL;
        }

        if (isset($request->english_skill)) {

            $user->workerDetail->english_skill = $request->english_skill;

        } else {

            $user->workerDetail->english_skill = NULL;
        }

        if (isset($request->english_skill_competency)) {

            $user->workerDetail->english_skill_competency = $request->english_skill_competency;

        } else {

            $user->workerDetail->english_skill_competency = NULL;
        }

        if (isset($request->drivers_license)) {

            $user->workerDetail->drivers_license = $request->drivers_license;

        } else {

            $user->workerDetail->drivers_license = NULL;
        }

        if (isset($request->can_drive_manual)) {

            $user->workerDetail->can_drive_manual = $request->can_drive_manual;

        } else {

            $user->workerDetail->can_drive_manual = 0;
        }

        if (isset($request->has_registered_vehicle)) {

            $user->workerDetail->has_registered_vehicle = $request->has_registered_vehicle;

        } else {

            $user->workerDetail->has_registered_vehicle = NULL;
        }

        $user->workerDetail->save();

        return true;
    }

    public function updatePersonalDetails(Request $request) {

        $user = JWTAuth::toUser();

        if (!$user->workerDetail) {

            return false;
        }

        $this->workerDetail = $user->workerDetail;

        $rules = [
            'country_birth' => 'nullable',
            'gender' => 'nullable|in:Male,male,Female,female,Other,other',
            'date_of_birth' => 'nullable|date|before:-18 years'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ( $validator->fails() ) {

            $this->workerDetail->errors = $validator->errors()->all();
            $this->workerDetail->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        if (isset($request->country_birth)) {

            $user->country_birth = $request->country_birth;

        } else {

            $user->country_birth = NULL;
        }

        if (isset($request->gender)) {

            $user->gender = $request->gender;

        } else {

            $user->gender = NULL;
        }

        if (isset($request->date_of_birth)) {

            $user->date_of_birth = Carbon::parse($request->date_of_birth);

        } else {

            $user->date_of_birth = NULL;
        }

        $user->save();

        return true;

    }

    public function getWorker(Request $request)
    {

        $user = User::find($request->userid);

        if (!$user) {

            return false;
        }

        if (!$user->workerDetail) {

            return $user;
        }

        $exp = null;
        $jobRole = '';

        if ($user->workerDetail) {

            $exp = $user->workerDetail->getLatestExperience();
        }

        if ($exp) {

            $jobRole = $exp->job_role;
        }

        $user->job_role = $jobRole;

        $user->experiences;
        $user->role;
        $user->skills;
        $user->workerDetail;
        $user->tickets;

        if ($user->workerDetail) {

            $user->workerDetail->education;
            $user->workerDetail->sectors;
            $user->workerDetail->tiers;
        }

        $educations = Education::where('user_id', $user->id)
            ->orderBy('end_year', 'DESC')
            ->get();

        $user->educations = $educations;

        return $user;

    }

    public function adminUpdateSectors( Request $request ){

        $user = User::find($request->user_id);

        if (!$user->workerDetail) {

            return false;
        }

        if (isset($request->sectors) && empty($request->sectors)) {

            WorkerArea::where('worker_id', $user->workerDetail->id)->delete();
        }

        if (isset($request->tiers) && empty($request->tiers)) {

            WorkerTier::where('worker_id', $user->workerDetail->id)->delete();
        }

        if (!empty($request->sectors)) {

            WorkerArea::where('worker_id', $user->workerDetail->id)->delete();

            $businessTypes = $request->sectors;

            $businessTypes = array_filter($businessTypes, function($id) {

                // check businsss type

                $b = BusinessType::find($id);

                if ($b) {

                    return $id;
                }

            });


            $saveBusinessTypes = array_map(function($id) use($user) {

                return array('business_type_id' => $id, 'worker_id' => $user->workerDetail->id);

            }, $businessTypes);

            WorkerArea::insert($saveBusinessTypes);

        }

        if (!empty($request->tiers)) {

            WorkerTier::where('worker_id', $user->workerDetail->id)->delete();

            $tiers = $request->tiers;

            $tiers = array_filter($tiers, function($id) {

                // check tier

                $b = Tier::find($id);

                if ($b) {

                    return $id;
                }

            });

            $saveTiers = array_map(function($id) use($user) {

                return array('tier_id' => $id, 'worker_id' => $user->workerDetail->id);

            }, $tiers);

            WorkerTier::insert($saveTiers);

        }

        return true;
    }

    public function adminUpdatePersonalDetails( Request $request ){
        $user = User::find($request->user_id);

        if (!$user->workerDetail) {

            return false;
        }

        $this->workerDetail = $user->workerDetail;

        $rules = [
            'country_birth' => 'nullable',
            'gender' => 'nullable|in:Male,male,Female,female,Other,other',
            'date_of_birth' => 'nullable|date|before:-18 years'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ( $validator->fails() ) {

            $this->workerDetail->errors = $validator->errors()->all();
            $this->workerDetail->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        if (isset($request->country_birth)) {

            $user->country_birth = $request->country_birth;

        } else {

            $user->country_birth = NULL;
        }

        if (isset($request->gender)) {

            $user->gender = $request->gender;

        } else {

            $user->gender = NULL;
        }

        if (isset($request->date_of_birth)) {

            $user->date_of_birth = Carbon::parse($request->date_of_birth);

        } else {

            $user->date_of_birth = NULL;
        }

        $user->save();

        return true;
    }

    public function adminUpdateAffirmations( Request $request ){
        $user = User::find($request->user_id);

        if (!$user->workerDetail) {

            return false;
        }

        if ($request->drivers_license_state) {

            $user->workerDetail->saveParams('drivers_license_state', $request->drivers_license_state);
        }

        if ($request->drivers_license_type) {

            $user->workerDetail->saveParams('drivers_license_type', $request->drivers_license_type);
        }

        // australian tfn
        $user->workerDetail->saveParams('australian_tfn', $request->australian_tfn ? $request->australian_tfn : '');


        if (isset($request->right_to_work)) {

            $user->workerDetail->right_to_work = $request->right_to_work;

        } else {

            $user->workerDetail->right_to_work = NULL;
        }

        if (isset($request->has_tfn)) {

            $user->workerDetail->has_tfn = $request->has_tfn;

        } else {

            $user->workerDetail->has_tfn = NULL;
        }

        if (isset($request->has_abn)) {

            $user->workerDetail->has_abn = $request->has_abn;

        } else {

            $user->workerDetail->has_abn = NULL;
        }

        if (isset($request->english_skill)) {

            $user->workerDetail->english_skill = $request->english_skill;

        } else {

            $user->workerDetail->english_skill = NULL;
        }

        if (isset($request->english_skill_competency)) {

            $user->workerDetail->english_skill_competency = $request->english_skill_competency;

        } else {

            $user->workerDetail->english_skill_competency = NULL;
        }

        if (isset($request->drivers_license)) {

            $user->workerDetail->drivers_license = $request->drivers_license;

        } else {

            $user->workerDetail->drivers_license = NULL;
        }

        if (isset($request->can_drive_manual)) {

            $user->workerDetail->can_drive_manual = $request->can_drive_manual;

        } else {

            $user->workerDetail->can_drive_manual = 0;
        }

        if (isset($request->has_registered_vehicle)) {

            $user->workerDetail->has_registered_vehicle = $request->has_registered_vehicle;

        } else {

            $user->workerDetail->has_registered_vehicle = NULL;
        }

        $user->workerDetail->save();

        return true;
    }
}
