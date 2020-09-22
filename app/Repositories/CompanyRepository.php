<?php

namespace App\Repositories;

use App\Cache;
use App\Http\Resources\PeoplesResource;
use App\Models\Companies\Answer;
use App\Models\Companies\Company;
use App\Models\Companies\CompanyPost;
use App\Models\Companies\CompanySpecialized;
use App\Models\Companies\Job;
use App\Models\Companies\JobApplicant;
use App\Models\Companies\JobRequirement;
use App\Models\Companies\JobResponsibility;
use App\Models\Companies\JobRole;
use App\Models\Options\SecondaryFunction;
use App\Models\Users\Users;
use App\Models\Users\WorkerDetail;
use Illuminate\Http\Request;
use Torann\LaravelRepository\Repositories\AbstractRepository;
use JWTAuth;

class CompanyRepository extends AbstractRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = Company::class;

    public $company = null;
    public $job = null;
    public $jobRequirement = null;
    public $jobResponsibility = null;

    /**
     * Valid searchable columns
     *
     * @return array
     */
    protected $searchable = [
        'query' => [
            'name',
        ],
    ];

    public function updateCompany(Request $request)
    {


        $user = JWTAuth::toUser();

        $this->company = new Company();

        if (!$user->company) {

            return false;
        }

        $this->company = $user->company;

        if ($user->company->store($request)) {

            // save specialization

            if (isset($request->secondary_functions) && is_array($request->secondary_functions)) {

                CompanySpecialized::where('company_id', $user->company->id)->delete();

                $secondaryFunctions = SecondaryFunction::whereIn('id', $request->secondary_functions)
                    ->where('main_id', $request->main_company_id)->pluck('id');

                foreach ($secondaryFunctions->toArray() as $id) {

                    CompanySpecialized::insert([
                        'company_id' => $this->company->id,
                        'secondary_id' => $id
                    ]);
                }
            }

            if (!empty($request->main_company_id) && !empty($request->main_function_answer)) {

                $existingAnswer = Answer::where('answer', $request->main_function_answer)
                    ->where('main_function_id', $request->main_company_id)
                    ->exists();


                if (!$existingAnswer) {

                    Answer::create([
                        'main_function_id' => $request->main_company_id,
                        'answer' => $request->main_function_answer
                    ]);
                }
            }

            $user->company->BusinessType;
            $user->company->Tier;
            $user->company->MainFunction;
            $user->company->Specialization;
            $user->company->user_email = $request->email;

            return $user->company;
        }

        return false;
    }

    public function uploadPhoto(Request $request)
    {

        $user = JWTAuth::toUser();
        $errMessage = 'Cannot upload a photo';
        $this->company = new Company();

        if ($user->company && $user->company->uploadProfilePhoto($request)) {

            $this->company = $user->company;
            return $this->company;
        }

        $this->company->addError($errMessage);
        $this->company->errorsDetail = ['photo' => $errMessage];

        return false;
    }

    public function getWorkers($id)
    {

        $company = Company::find($id);

        if ($company) {

            $workers = $company->getWorkers();

            return PeoplesResource::collection($workers);
        }

        return [];
    }

    public function getApplicants(Request $request)
    {

        $company = Company::find($request->id);
        $limit = is_int((int) $request->limit) ? $request->limit : 0;

        // get the jobs within a company as set of id

        // get the applicants within a company by set of job id

        if ($company) {

            $setOfJobId = $company->Jobs()->pluck('id')->toArray();

            $setOfUserId = JobApplicant::whereIn('job_id', $setOfJobId)->latest('applied_at')->get();

            $applicants = collect();

            foreach ($setOfUserId as $user) {

                $applicants->push(Users::find($user->user_id));
            }

            if ($limit) {

                $applicants = $applicants->take($limit);
            }

            return PeoplesResource::collection($applicants);
        }

        return [];
    }

    public function getJobs($id)
    {

        $jobs = Job::where('company_id', $id)
            ->orWhere('company_id', null)
            ->get();

        return $jobs;
    }

    public function getJob(Request $request)
    {

        $job = Job::find($request->jid);

        if ($job) {

            $job->requirements;
            $job->responsibilities;

            $company = Company::find($request->id);

            if ($company) {

                $job->company = $company;
            }

            return $job;
        }

        return false;
    }

    public function getJobPreview(Request $request)
    {
        $cache = Cache::find($request->cacheid);
        $company = Company::find($request->id);

        if ($cache && $company) {

            $jobCache = json_decode($cache->json_content);

            if ($jobCache) {

                $jobCache->company = $company;

                return $jobCache;
            }
        }

        return null;
    }

    public function getCompany($id)
    {

        $company = $this->find($id);

        if ($company && $company->Specialization) {

            $company->BusinessType;
            $company->Tier;
            $company->MainFunction;
            $company->Specialization;

            return $company;
        }

        return [];
    }

    public function getCompanySpecialization($id)
    {

        $company = $this->find($id);

        if ($company && $company->Specialization) {

            return $company->Specialization;
        }

        return [];
    }

    public function getPosts($id)
    {

        $posts = CompanyPost::with('job')
            ->where('company_id', $id)
            ->get();

        return $posts;
    }

    public function getJobPosts($id, $status)
    {

        $jobs = Job::with('Company', 'PostedBy')
            ->where('company_id', $id)
            ->where('is_template', false);


        if ($status == 'active' || $status == 'closed') {

            switch ($status) {

                case 'active':
                    $statusBool = 1;
                    break;
                case 'closed':
                    $statusBool = 0;
                    break;
                default:
                    $statusBool = 1;
            }

            $jobs = $jobs->where('status', $statusBool);
        }

        $jobs = $jobs->get();

        return $jobs;
    }

    public function searchJobRoles(Request $request)
    {

        $keyword = $request->keyword ? $request->keyword : '';

        $jobRoles = JobRole::where('job_role_name', 'like', "%{$keyword}%")
            ->orWhere('job_role_name', 'like', "%{$keyword}%")
            ->take(10)
            ->get();

        return $jobRoles;
    }

    public function getJobTemplates(Request $request)
    {

        $templates = Job::with(['company', 'postedBy'])
            ->where('company_id', $request->id)
            ->where('is_template', true)
            ->get();

        return $templates;
    }

    public function createJobTemplate(Request $request)
    {

        $company = Company::find($request->id);
        $user = JWTAuth::toUser();

        $this->job = new Job();

        if (!$company) {

            return false;
        }

        if (empty($request->title)) {

            $errMessage = 'Title is required.';

            $this->job->addError($errMessage);
            $this->job->errorsDetail = ['title' => $errMessage];

            return false;
        }

        $this->job->title = $request->title;
        $this->job->created_by = $user->id;
        $this->job->company_id = $request->id;
        $this->job->is_template = true;

        $this->job->save();

        return $this->job;
    }

    public function viewJobTemplate(Request $request)
    {

        $company = Company::find($request->id);
        $template = Job::where('id', $request->tid)
            ->where('is_template', true)
            ->first();

        $this->job = new Job();

        if (!$template || !$company) {

            return false;
        }

        $this->job = $template;

        return $this->job;
    }

    public function deleteTemplate(Request $request)
    {

        $company = Company::find($request->id);
        $template = Job::where('id', $request->tid)
            ->where('is_template', true)
            ->first();

        $this->job = new Job();

        if (!$template || !$company) {

            return false;
        }

        $this->job = $template;

        $this->job->delete();

        return $this->job;
    }

    public function adminUpdateCompany(Request $request)
    {

        $company = $this->find($request->id);

        if ($company->store($request)) {

            // save specialization

            if (isset($request->secondary_functions) && is_array($request->secondary_functions)) {

                CompanySpecialized::where('company_id', $request->id)->delete();

                $secondaryFunctions = SecondaryFunction::whereIn('id', $request->secondary_functions)
                    ->where('main_id', $request->main_company_id)->pluck('id');

                foreach ($secondaryFunctions->toArray() as $id) {

                    CompanySpecialized::insert([
                        'company_id' => $company->id,
                        'secondary_id' => $id
                    ]);
                }
            }

            if (!empty($request->main_company_id) && !empty($request->main_function_answer)) {

                $existingAnswer = Answer::where('answer', $request->main_function_answer)
                    ->where('main_function_id', $request->main_company_id)
                    ->exists();


                if (!$existingAnswer) {

                    Answer::create([
                        'main_function_id' => $request->main_company_id,
                        'answer' => $request->main_function_answer
                    ]);
                }
            }

            $company->BusinessType;
            $company->Tier;
            $company->MainFunction;
            $company->Specialization;

            return $company;
        }

        return false;
    }
}
