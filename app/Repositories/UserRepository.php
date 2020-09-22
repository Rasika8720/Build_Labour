<?php

namespace App\Repositories;

use App\Mails\ResendVerificationCodeEmail;
use App\Models\Companies\Answer;
use App\Models\Companies\Company;
use App\Models\Companies\CompanyPost;
use App\Models\Companies\CompanySpecialized;
use App\Models\Companies\Job;
use App\Models\Options\SecondaryFunction;
use App\Models\Skills\Skill;
use App\Models\Users\Bookmark;
use App\Models\Users\Users;
use App\Models\Users\UserSkill;
use App\Models\Users\WorkerDetail;
use App\User;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use Illuminate\Http\Request;

use Torann\LaravelRepository\Repositories\AbstractRepository;

class UserRepository extends AbstractRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = User::class;

    public $workerDetail = null;
    public $user = null;
    public $company = null;
    public $bookmark = null;

    public function __construct()
    {
        $this->workerDetail = new WorkerDetail();
    }

    public function saveMainSkill(Request $request)
    {

        $user = JWTAuth::toUser();

        if ($user->workerDetail) {

            $this->workerDetail = $user->workerDetail;
            $user->workerDetail->isMainSkillUpdate = true;

            if (!$user->workerDetail->store($request)) {

                return false;
            }


            if (!$this->saveSkills($request)) {

                return false;
            }

            return [
                'main_skill' => $user->workerDetail->main_skill,
                'skills' => $user->skills
            ];
        }

        return false;
    }

    public function saveSkills(Request $request)
    {

        $user = JWTAuth::toUser();

        if (isset($request->skills)) {

            UserSkill::where('user_id', '=', $user->id)->delete();

            $skills = $request->skills;

            foreach ($skills as $skill) {

                if ($skill['skill_name'] != '') {

                    $newSkill = Skill::firstOrCreate([
                        'name' => $skill['skill_name']
                    ]);

                    UserSkill::updateOrCreate([
                        'user_id' => $user->id,
                        'skill_id' => $newSkill->id,
                    ], [
                        'level_id' => $skill['level_id']
                    ]);
                }
            }
        }

        return $user->skills;
    }

    public function deleteMainSkills(Request $request)
    {

        // delete user skills
        // empty worker_details.main_skill field

        $user = JWTAuth::toUser();

        if ($user->workerDetail) {

            $user->workerDetail->main_skill = null;
            $user->workerDetail->save();
            UserSkill::where('user_id', $user->id)->delete();
        }

        return true;
    }

    public function company()
    {

        $user = JWTAuth::toUser();

        if ($user) {

            if (!$user->company) {

                return false;
            }

            $user->company->BusinessType;
            $user->company->Tier;
            $user->company->MainFunction;
            $user->company->Specialization;

            return $user->company;
        }

        return false;
    }

    public function registerCompany(Request $request)
    {

        $this->user = new Users;
        $this->company = new Company();

        $rules = [
            'company_name'  => 'required',
            'company_main_company_id'  => 'required|integer',
            //  'company_secondary_functions' => 'required|array',
            'company_business_type_id' => 'nullable|integer',
            'company_tier_id' => 'nullable|integer',
            'company_photo' => 'nullable|image64:jpeg,jpg,png',
            'company_address'  => 'required',
            'company_contact_number' => 'required|digits:10',
            'company_operate_outside_states' => 'required|boolean',
            'company_website' => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
            'email'     => 'required|string|email|max:50|unique:users',
            'password'  => 'required|string|min:6|max:24|confirmed'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            $this->user->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        // user validation
        $this->user->isEmployerSignup = true;

        if (!$this->user->store($request)) {

            return false;
        }

        $this->company->setUserId($this->user->id);

        if (!$this->company->store($request)) {

            return false;
        }

        // insert new main function answer

        $existingAnswer = Answer::where('answer', $request->company_main_function_answer)
            ->where('main_function_id', $request->company_main_company_id)
            ->exists();

        if (!$existingAnswer && !empty($request->company_main_function_answer)) {

            Answer::create([
                'main_function_id' => $request->company_main_company_id,
                'answer' => $request->company_main_function_answer
            ]);
        }

        // company specialize

        if ($request->company_secondary_functions && $request->company_main_company_id) {

            $secondaryFunctions = SecondaryFunction::whereIn('id', $request->company_secondary_functions)
                ->where('main_id', $request->company_main_company_id)->pluck('id');

            foreach ($secondaryFunctions->toArray() as $id) {

                CompanySpecialized::insert([
                    'company_id' => $this->company->id,
                    'secondary_id' => $id
                ]);
            }
        }

        // check company photo validation
        if ($request->company_photo && !$this->company->uploadProfilePhoto($request)) {

            return false;
        }

        $user = $this->user;
        $company = $this->company;
        $token = $this->user->getJwtToken();

        Mail::to($user->email)->send(new ResendVerificationCodeEmail($user));

        $user->makeHidden([
            'device_token',
            'dob_formatted',
            'full_name',
            'identifier',
            'is_verified',
        ]);

        $company->makeHidden([
            'address',
            'business_type_id',
            'created_at',
            'created_by',
            'locations',
            'main_company_id',
            'no_of_workers',
            'operate_outside_states',
            'phone',
            'states',
            'tier_id',
            'updated_at',
            'website',
            'workers'
        ]);

        return compact('user', 'company', 'token');
    }

    public function doBookMark(Request $request)
    {

        $this->bookmark = new Bookmark();

        $user = JWTAuth::toUser();

        if ($user) {

            $data = ['user_id' => $user->id];
            $tempBookmark = null;

            if ($request->post_id) {

                $post = CompanyPost::find($request->post_id);

                if (!$post) {

                    $message = "Can't processed request";
                    $this->bookmark->addError($message);

                    return false;
                }

                $data['post_id'] = $post->id;
                $this->bookmark = Bookmark::where('user_id', $user->id)->where('post_id', $request->post_id)->first();
            } else if ($request->job_id) {

                $job = Job::find($request->job_id);

                if (!$job) {

                    $message = "Can't processed request";
                    $this->bookmark->addError($message);

                    return false;
                }

                $data['job_id'] = $job->id;

                $this->bookmark = Bookmark::where('user_id', $user->id)->where('job_id', $request->job_id)->first();
            } else {

                $message = "Can't processed request";
                $this->bookmark->addError($message);

                return false;
            }

            // if the post id exists then delete
            if ($this->bookmark) {

                $this->bookmark->delete();
                $this->bookmark->unbookmarked = true;

                if ($request->post_id) {

                    Bookmark::where('user_id', $user->id)->where('post_id', $request->post_id)->delete();
                }

                if ($request->job_id) {

                    Bookmark::where('user_id', $user->id)->where('job_id', $request->job_id)->delete();
                }
            } else {

                $this->bookmark = new Bookmark();

                if (!$this->bookmark->store($data)) {

                    return false;
                }
            }

            if ($request->job_id) {

                $jobBookmark = [
                    'id' => $this->bookmark->id,
                    'company_id' => null,
                    'company_name' => null,
                    'company_photo' => null,
                    'location' => null,
                    'title' => null,
                    'unbookmarked' => $this->bookmark->unbookmarked
                ];

                if (isset($job) && $job->Company) {

                    $jobBookmark['company_id'] = $job->Company->id;
                    $jobBookmark['company_name'] = $job->Company->name;
                    $jobBookmark['company_photo'] = $job->Company->photo_url;
                    $jobBookmark['location'] = $job->location;
                    $jobBookmark['title'] = $job->job_role_name;
                    $jobBookmark['job_id'] = $job->id;
                    $jobBookmark['post_id'] = null;
                }

                return $jobBookmark;
            }

            return $this->bookmark;
        }
    }

    public function getPostJobsBookmarks()
    {

        $user = JWTAuth::toUser();

        if ($user) {

            $query = Bookmark::with('Job');

            $bookmarks = $query
                ->where('user_id', $user->id)
                ->where('post_id', null)
                ->get();

            if ($bookmarks) {

                $jobs = [];

                foreach ($bookmarks as $bookmark) {

                    if ($bookmark->Job && $bookmark->Job->Company) {

                        $jobPost = [
                            'bookmark_id' => $bookmark->id,
                            'company_photo' => $bookmark->Job->Company->photo_url,
                            'company_id' => $bookmark->Job->Company->id,
                            'company_name' => $bookmark->Job->Company->name,
                            'job_id' => $bookmark->Job->id,
                            'job_role' => $bookmark->Job->job_role_name,
                            'location' => $bookmark->Job->location
                        ];

                        $jobs[] = $jobPost;
                    }
                }

                return $jobs;
            }
        }

        return [];
    }

    public function getPostBookmarksById()
    {

        $user = JWTAuth::toUser();

        if ($user) {

            $bookmarks = Bookmark::where('user_id', $user->id)
                ->where('job_id', null)
                ->pluck('post_id');

            if ($bookmarks) {

                return $bookmarks;
            }
        }

        return [];
    }

    public function getJobBookmarksById()
    {

        $user = JWTAuth::toUser();

        if ($user) {

            $bookmarks = Bookmark::where('user_id', $user->id)
                ->where('post_id', null)
                ->pluck('job_id');

            if ($bookmarks) {

                return $bookmarks;
            }
        }

        return [];
    }
}
