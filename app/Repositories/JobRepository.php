<?php

namespace App\Repositories;

use App\Cache;
use App\Http\Resources\PeoplesResource;
use App\Http\Resources\JobsResource;
use App\Models\Companies\Company;
use App\Models\Companies\CompanyPost;
use App\Models\Companies\Job;
use App\Models\Companies\JobApplicant;
use App\Models\Companies\JobRequirement;
use App\Models\Companies\JobResponsibility;
use App\Models\Companies\JobRole;
use App\Models\Companies\JobStat;
use App\Models\Tickets\Ticket;
use App\Notifications\ChatMessage;
use App\Notifications\CompanyAwardToWorker;
use App\Notifications\CompanyChangesJobStatus;
use Illuminate\Support\Facades\Notification;
use JWTAuth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Users\Users;
use App\User;

use Torann\LaravelRepository\Repositories\AbstractRepository;

class JobRepository extends AbstractRepository
{

    /*
     * note
     * jobs property table
     * title - role
     * location
     *
     * companies property table
     * tier
     * sector
     * locations_json, address
     *
     * {
            "role": "Ter",
            "tiers":[
                "manufacturing"
            ],
            "sectors":[
                "programming"
            ],
            "locations": [
                "sydney",
                "james"
            ]
        }

     */
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = Job::class;
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
            'title',
            'location',
            'company.tier'
        ],
    ];

    public function getJob($jobId)
    {

        $this->job = Job::with('Responsibilities')->where('id', $jobId)->first();

        if ($this->job) {

            $this->job->Responsibilities;
            $this->job->requirements;

            return $this->job;
        }

        return false;
    }

    public function getFilter(Request $request)
    {

        $params = (object) $request->all();

        $jobs = Job::with('Responsibilities')->where('title', 'like', "%{$params->role}%");

        foreach ($params->locations as $location) {

            $location = trim($location);

            $jobs = $jobs->orWhere('location', 'LIKE', "%{$location}%");
        }

        $jobs = $jobs->get();

        return $jobs;
    }

    public function searchCompanyJobs(Request $request, $viewerType = null)
    {
        $user = JWTAuth::toUser();

        $column = $request->get('column') ? $request->get('column') : 'created_at';
        $order = $request->get('order') ? $request->get('order') : 'desc';
        $per_page = $request->get('per_page') ? $request->get('per_page') : 10; // pagination
        $limit = $request->get('limit') ? $request->get('limit') : 0; // limit

        // $noOfNew = JobApplicant::where('job_id',  $request->id)->whereBetween('applied_at', [$last3Days, $today])->count();

        $statViewedQuery = "(SELECT COUNT(*) FROM job_post_stats WHERE job_post_stats.job_id = job_posts.id AND job_post_stats.category = 'viewed')";
        $statInvitedQuery = "(SELECT COUNT(*) FROM job_post_stats WHERE job_post_stats.job_id = job_posts.id AND job_post_stats.category = 'invited')";
        $statFavouriteQuery = "(SELECT COUNT(*) FROM job_post_stats WHERE job_post_stats.job_id = job_posts.id AND job_post_stats.category = 'favourite')";
        $statNewQuery = "(SELECT COUNT(*) FROM job_post_applicants WHERE job_post_applicants.job_id = job_posts.id)";
        $statNotSuitableQuery = "(SELECT COUNT(*) FROM job_post_stats WHERE job_post_stats.job_id = job_posts.id AND job_post_stats.category = 'not_suitable')";

        $jobs = Job::with('company');
        $jobs = $jobs->select(
            'job_posts.*',
            \DB::raw("{$statViewedQuery} as stat_viewed"),
            \DB::raw("{$statInvitedQuery} as stat_invited"),
            \DB::raw("{$statFavouriteQuery} as stat_favourite"),
            \DB::raw("{$statNewQuery} as stat_new"),
            \DB::raw("{$statNotSuitableQuery} as stat_not_suitable"),
            \DB::raw("({$statViewedQuery} + {$statInvitedQuery} + {$statFavouriteQuery} + {$statNewQuery}) as stat_total")
        );

        $jobs = $jobs->leftjoin('job_roles as job_role', 'job_role.id', '=', 'job_posts.job_role_id');
        $jobs = $jobs->leftjoin('companies as company', 'company.id', '=', 'job_posts.company_id');

        $keyword = $request->keyword ? $request->keyword : '';
        $location = $request->location ? $request->location : '';

        if ($request->company_id) {

            $jobs = $jobs->where('company_id', $request->company_id);
        }

        $isTemplate = false;
        $jobStatus = true;

        if (!empty($request->status)) {

            if ($request->status == 'saved_templates') {

                $isTemplate = [1];
            }

            $jobStatus = $request->status == 'past_jobs' ? [0,2] : [1];

            if ($viewerType && $viewerType == 'viewer') {

                $jobStatus = [1];
            }
        }

        $jobs = $jobs->where('job_posts.is_template', $isTemplate)
            ->whereNotNull('job_posts.company_id')
            ->whereIn('status', $jobStatus)
            ->where(function ($query) use ($keyword) {
                $query->where('job_posts.title', 'like', "%{$keyword}%")
                    ->orWhere('job_posts.template_name', 'like', "%{$keyword}%")
                    ->orWhere('job_posts.location', 'like', "%{$keyword}%")
                    ->orWhere('job_role.job_role_name', 'like', "%{$keyword}%");
            });


        // if company only
        if ($user->Company && !isset($request->company_id)) {

            $jobs = $jobs->where('job_posts.company_id', $user->Company->id);
        }

        if (!empty($location)) {

            $jobs = $jobs->where(function ($query) use ($location) {
                $query->where('job_posts.location', 'like', "%{$location}%");
            });
        }

        // $jobs = $jobs->orderBy($column, $order);
        // $data = $jobs->paginate($per_page);

        $jobs = $jobs->orderBy('id', 'desc');
        $jobs = $jobs->get();

        if ($limit) {

            $jobs = $jobs->take($limit);
        }

        return $jobs;
    }

    public function createJob(Request $request)
    {

        $job = $this->saveJob($request, false);

        if ($job) {

            return $job;
        }

        return false;
    }

    public function saveTemplate(Request $request)
    {

        $job = $this->saveJob($request, true);

        if ($job) {

            return $job;
        }

        return false;
    }

    public function updateJob(Request $request)
    {

        $user = JWTAuth::toUser();

        $job = Job::find($request->id);
        $job->load(['Responsibilities', 'Requirements']);

        if ($job) {

            $data = $request->all();

            // deal past jobs >> status 0
            if (!$job->status || $job->is_template) {

                $data['template_name'] = null;
                $data['is_template'] = 0;

                $this->job = $job->replicate();
                $this->job->created_by = $user->id;
                $this->job->status = true;
                $this->job->push();

                // dd($this->job->toArray());
                $relations = $job->getRelations();

                foreach ($relations as $relation) {
                    foreach ($relation as $relationRecord) {
                        $newRelationship = $relationRecord->replicate();
                        $newRelationship->job_id = $this->job->id;
                        $newRelationship->push();
                    }
                }
            } else { // deal active jobs or active templates >> status 1

                $this->job = $job;
            }

            if ($request->job_role_id) {

                $jobRole = JobRole::find($request->job_role_id);

                if (!$jobRole) {

                    $message = "Can't process request";
                    $this->job->addError($message);

                    return false;
                }
            }

            if ($request->min_exp_month) {

                $this->job->saveParams('min_exp_month', $request->min_exp_month);
            }

            if ($request->min_exp_year) {

                $this->job->saveParams('min_exp_year', $request->min_exp_year);
            }

            if ($request->id) {

                $data['id'] = $request->id;
            }

            if ($request->responsibilities) {

                foreach ($request->responsibilities as $r) {

                    $r['items_json'] = array_filter($r['items']);
                    $r['job_id'] = $job->id;

                    $jobRes = new JobResponsibility();

                    if (!$jobRes->store($r)) {

                        $message = "Can't processed request";
                        $this->job->addError($message);

                        return false;
                    }
                }
            }

            if ($request->requirements) {

                $result = JobRequirement::where('job_id', $this->job->id)->delete();

                foreach ($request->requirements as $r) {

                    $items = $r['items'];

                    if (strtolower($r['title']) == 'tickets') {

                        $items = [];

                        foreach ($r['items'] as $item) {

                            if (!isset($item['id']) && isset($item['ticket'])) {

                                $checkTicket = Ticket::where('ticket', 'like', "%{$item['ticket']}")->first();

                                if ($checkTicket) {

                                    $tErrMessage = "Duplicate ticket found '{$item['ticket']}' ticket found in the system record. Matched to {$checkTicket->ticket} - {$checkTicket->description}. Please create or select another ticket.";
                                    $this->job->addError($tErrMessage);
                                    $this->job->errorsDetail = array('ticket' => [$tErrMessage]);

                                    return false;
                                }

                                $newTicket = new Ticket();

                                $newTicket->ticket = $item['ticket'];
                                $newTicket->description = $item['description'];
                                $newTicket->created_by = $user->id;
                                $newTicket->save();

                                $items[] = $newTicket->toArray();

                            } else if (isset($item['id']) && Ticket::find($item['id'])) {

                                $items[] = $item;
                            }
                        }
                    }

                    if($items){
                        $r['items_json'] = array_filter($items);
                        $r['job_id'] = $this->job->id;

                        $jobReq = new JobRequirement();

                        if (!$jobReq->store($r)) {

                            $message = "Can't processed request";
                            $this->job->addError($message);

                            return false;
                        }
                    }

                }
            }


            if ($this->job->store($data)) {

                return $this->job;
            }
        }

        return false;
    }

    public function saveJob(Request $request, $isTemplate = true)
    {

        $user = JWTAuth::toUser();

        $this->job = new Job();

        $data = $request->all();

        $data['created_by'] = $user->id;
        $data['is_template'] = $isTemplate;

        if ($isTemplate) {

            $data['status'] = true;
        }

        if ($user->Company) {

            $data['company_id'] = $user->Company->id;
        }

        // delete cache
        if ($request->cache_id) {

            $cache = Cache::find($request->cache_id);

            if ($cache) {

                $cache->delete();
            }
        }

        if ($request->job_role_id) {

            $jobRole = JobRole::find($request->job_role_id);

            if (!$jobRole) {

                $message = "Can't process request";
                $this->job->addError($message);

                return false;
            }
        }

        if ($request->min_exp_month) {

            $this->job->saveParams('min_exp_month', $request->min_exp_month);
        }

        if ($request->min_exp_year) {

            $this->job->saveParams('min_exp_year', $request->min_exp_year);
        }

        if ($job = $this->job->store($data)) {

            // job responsibilities

            if ($request->responsibilities) {

                foreach ($request->responsibilities as $r) {

                    $r['items_json'] = array_filter($r['items']);
                    $r['job_id'] = $job->id;

                    $jobRes = new JobResponsibility();

                    if (!$jobRes->store($r)) {

                        $message = "Can't processed request";
                        $this->job->addError($message);

                        return false;
                    }
                }
            }

            // job requirements
            if ($request->requirements) {

                $hasKills = false;

                foreach ($request->requirements as $r) {

                    $items = $r['items'];

                    if (strtolower($r['title']) == 'tickets') {

                        $items = [];

                        foreach ($r['items'] as $item) {

                            if (!isset($item['id'])) {

                                $checkTicket = Ticket::where('ticket', 'like', "%{$item['ticket']}")->first();

                                if ($checkTicket) {

                                    $tErrMessage = "Duplicate ticket found '{$item['ticket']}' ticket found in the system record. Matched to {$checkTicket->ticket} - {$checkTicket->description}. Please remove then create or select another ticket.";
                                    $this->job->addError($tErrMessage);
                                    $this->job->errorsDetail = array('ticket' => [$tErrMessage]);

                                    return false;
                                }

                                $newTicket = new Ticket();

                                $newTicket->ticket = $item['ticket'];
                                $newTicket->description = $item['description'];
                                $newTicket->created_by = $user->id;
                                $newTicket->save();

                                $items[] = $newTicket->toArray();
                            } else if (Ticket::find($item['id'])) {

                                $items[] = $item;
                            }
                        }
                    }

                    if (strtolower($r['title']) == 'skills' && array_filter($items)) {

                        $hasKills = true;
                    }

                    $r['items_json'] = array_filter($items);
                    $r['job_id'] = $job->id;
                    $r['white_card'] = $request->white_card;

                    $jobReq = new JobRequirement();

                    if (!$jobReq->store($r)) {

                        $message = "Can't processed request";
                        $this->job->addError($message);
                        return false;
                    }
                }
            }

            // skills required
            // if (!$hasKills) {

            //     $message = "Skills required.";
            //     $this->job->addError($message);
            //     $this->job->errorsDetail = array('skills' => ['Skills required.']);
            //     return false;
            // }

            $this->job->Requirements;
            $this->job->Responsibilities;

            return $this->job;
        }

        return false;
    }

    public function duplicate(Request $request)
    {

        $user = JWTAuth::toUser();

        $this->job = Job::find($request->id);
        $this->job->load(['Responsibilities', 'Requirements']);

        $newJob = $this->job->replicate();

        $rules = [
            'confirmation'  => 'required|in:duplicate_as_template,duplicate',
            'company_id' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules, ['confirmation.in' => 'confirmation value must be duplicate_as_template or duplicate']);

        if ($validator->fails()) {

            $this->job->errorsDetail = $validator->errors()->toArray();
            return false;
        }

        if ($request->confirmation == 'duplicate_as_template') {

            $newJob->is_template = true;
            $newJob->template_id = $this->job->id;
        }

        $newJob->company;
        $newJob->created_by = $user->id;
        $newJob->status = 1;
        $newJob->push();

        $relations = $this->job->getRelations();
        foreach ($relations as $relation) {
            foreach ($relation as $relationRecord) {
                $newRelationship = $relationRecord->replicate();
                $newRelationship->job_id = $newJob->id;
                $newRelationship->push();
            }
        }

        return $newJob;
    }

    public function deleteJob(Request $request)
    {

        $user = JWTAuth::toUser();

        $this->job = Job::find($request->id);

        $rules = [
            'confirmation'  => 'required|in:delete',
            'company_id' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules, ['confirmation.in' => 'confirmation value must be a delete value']);

        if ($validator->fails()) {

            $this->job->errorsDetail = $validator->errors()->toArray();
            return false;
        }

        $this->job->status = 0;
        $this->job->save();

        return $this->job;
    }

    public function saveRequirements(Request $request)
    {

        $this->jobRequirement = new JobRequirement();

        if (!$request->requirements) {

            $this->jobRequirement->addError('requirements is required');
        }

        $job = Job::find($request->id);

        if ($job && $request->requirements) {

            $excludeIds = [];

            foreach ($request->requirements as $r) {

                $r['items_json'] = $r['items'];
                $r['job_id'] = $job->id;

                if (isset($r['id'])) {

                    // update
                    $jobReq = JobRequirement::find($r['id']);

                    $excludeIds[] = $r['id'];
                } else {

                    // new
                    $jobReq = new JobRequirement();
                }


                if ($jobReq->store($r)) {

                    $excludeIds[] = $jobReq->id;
                }
            }

            JobRequirement::whereNotIn('id', $excludeIds)->where('job_id', $job->id)->delete();

            return $job->requirements;
        }

        return false;
    }

    public function saveResponsibilities(Request $request)
    {

        $this->jobResponsibility = new JobResponsibility();

        if (!$request->responsibilities) {

            $this->jobResponsibility->addError('responsibilities is required');
        }

        $job = Job::find($request->id);

        if ($job && $request->responsibilities) {

            $excludeIds = [];
            foreach ($request->responsibilities as $r) {

                $r['items_json'] = $r['items'];
                $r['job_id'] = $job->id;

                if (isset($r['id'])) {

                    // update
                    $jobRes = JobResponsibility::find($r['id']);

                    $excludeIds[] = $r['id'];
                } else {

                    // new
                    $jobRes = new JobResponsibility();
                }

                if ($jobRes->store($r)) {

                    $excludeIds[] = $jobRes->id;
                }
            }

            JobResponsibility::whereNotIn('id', $excludeIds)->where('job_id', $job->id)->delete();

            return $job->responsibilities;
        }

        return false;
    }

    public function getResponsibilities(Request $request)
    {

        $this->jobResponsibility = new JobResponsibility();

        $job = Job::find($request->id);

        if ($job) {

            return $job->responsibilities;
        }

        return false;
    }

    public function getStats(Request $request)
    {

        $noOfViews = JobStat::where(['job_id' => $request->id, 'category' => 'viewed'])->count();
        $invited = JobStat::where(['job_id' => $request->id, 'category' => 'invited'])->pluck('scored_to');
        $not_suitable = JobStat::where(['job_id' => $request->id, 'category' => 'not_suitable'])->pluck('scored_to');
        $favourites = JobStat::where(['job_id' => $request->id, 'category' => 'favourite'])->pluck('scored_to');

        $last3Days = Carbon::now()->subDays(3);
        $today = Carbon::now();

        // deal no of new

        $noOfApplicants = JobApplicant::where('job_id',  $request->id)->count();
        $noOfCompanyViewedApplicants = JobStat::where(['job_id' => $request->id, 'category' => 'company_viewed_profile'])->count();
        $noOfNew = $noOfApplicants > $noOfCompanyViewedApplicants ? $noOfApplicants - $noOfCompanyViewedApplicants : 0;

        $noOfFavourites = count($favourites);
        $noOfNotSuitable = count($not_suitable);
        $noOfInvited = count($invited);

        $total = $noOfApplicants;

        return [
            'no_of_views' => $noOfViews,
            'no_of_invited' => $noOfInvited,
            'no_of_not_suitable' => $noOfNotSuitable,
            'no_of_favourite' => $noOfFavourites,
            'no_of_new' => $noOfNew,
            'total' => $total,
            'favourites' => $favourites,
            'not_suitable' => $not_suitable
        ];
    }

    public function openSearch(Request $request)
    {
        switch ($request->search_type) {
            case 'individuals':

                $data = User::where('role_id', 1)
                    ->join('work_experience', 'users.id', '=', 'work_experience.user_id')
                    ->with('Experiences')
                    ->when($request->search_string, function ($query) use ($request) {
                        $query->whereHas('WorkerDetail', function ($query) use ($request) {
                            $query->where('first_name', 'like', '%' . $request->search_string . '%');
                            $query->orWhere('most_recent_role', 'like', '%' . $request->search_string . '%');
                            $query->orWhere('profile_description', 'like', '%' . $request->search_string . '%');
                        });
                    })
                    ->when($request->title, function ($query) use ($request) {
                        $query->whereHas('Experiences', function ($query) use ($request) {
                            $query->where('job_role', 'like', '%' . $request->title . '%');
                        });
                    })
                    ->when($request->education, function ($query) use ($request) {
                        $query->whereHas('Educations', function ($query) use ($request) {
                            $query->where('school', 'like', '%' . $request->education . '%');
                            $query->orWhere('course', 'like', '%' . $request->education . '%');
                        });
                    })
                    ->when($request->ticket, function ($query) use ($request) {
                        $query->whereHas('Tickets', function ($query) use ($request) {
                            $query->where('ticket', 'like', '%' . $request->ticket . '%');
                        });
                    })
                    ->when($request->address, function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            foreach ($request->address as $address) {
                                $query->orWhere('address', 'like', '%' . $address . '%');
                            }
                        });
                    })
                    ->when($request->sort_option, function($query) use( $request ){
                        $sort_option = $request->sort_option;
                        $sort_order  = $request->sort_order;

                        switch ($sort_option) {
                            case 'Name':
                                $query->orderBy('first_name',$sort_order);
                                break;
                            case 'Role':
                                $query->orderBy('work_experience.job_role',$sort_order);
                                break;
                            case 'Most Recent':
                                $query->orderBy('users.created_at',$sort_order);
                                break;

                            default:
                                $query->orderBy('first_name',$sort_order);
                                break;
                        }
                    })
                    ->groupBy('email')
                    ->get();

                // $data = PeoplesResource::collection($data);
                break;
            case 'companies':

                $data = Company::select('companies.*');
                $data = $data->join('company_main_functions', 'companies.main_company_id', '=', 'company_main_functions.id')
                    ->when($request->search_string, function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            $query->where([['name', 'like', '%' . $request->search_string . '%']])
                                ->orWhere([['introduction', 'like', '%' . $request->search_string . '%']]);
                        });
                    })
                    ->when($request->industry, function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            $query->whereHas('MainFunction', function ($query) use ($request) {
                                $query->where('main_name', 'like', '%' . $request->industry . '%');
                            });
                            $query->orWhereHas('Specialization', function ($query) use ($request) {
                                $query->where('secondary_name', 'like', '%' . $request->industry . '%');
                            });
                            $query->orWhereHas('BusinessTYpe', function ($query) use ($request) {
                                $query->where('business_type', 'like', '%' . $request->industry . '%');
                            });
                        });
                    })
                    ->when($request->address, function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            foreach ($request->address as $address) {
                                $query->orWhere('address', 'like', '%' . $address . '%');
                            }
                        });
                    })
                    ->when($request->sort_option, function($query) use( $request ){
                        $sort_option = $request->sort_option;
                        $sort_order  = $request->sort_order;

                        switch ($sort_option) {
                            case 'Name':
                                $query->orderBy('name',$sort_order);
                                break;
                            case "Industry/Trade":
                                $query->orderBy('company_main_functions.main_name',$sort_order);
                                break;
                            case 'Most Recent':
                                $query->orderBy('created_at',$sort_order);
                                break;

                            default:
                                $query->orderBy('name',$sort_order);
                                break;
                        }
                    })
                    ->with('MainFunction')->get();

                break;
            case 'jobs':
                $data = Job::where([['status', '=', true], ['is_template', '=', false]])
                    ->when($request->search_string, function ($query) use ($request) {
                        $query->where([['title', 'like', '%' . $request->search_string . '%']]);
                        $query->orWhereHas('JobRole', function ($query) use ($request) {
                            $query->where('job_role_name', 'like', '%' . $request->search_string . '%');
                        });
                    })
                    ->when($request->ticket, function ($query) use ($request) {
                        $query->whereHas('Requirements', function ($query) use ($request) {
                            $query->where([['title', 'tickets'], ['items_json', 'like', '%' . $request->ticket . '%']]);
                        });
                    })
                    ->when($request->education, function ($query) use ($request) {
                        $query->whereHas('Requirements', function ($query) use ($request) {
                            $query->where([['title', 'qualifications'], ['items_json', 'like', '%' . $request->education . '%']]);
                        });
                    })
                    ->when($request->industry, function ($query) use ($request) {
                        $query->whereHas('Company', function ($query) use ($request) {
                            $query->whereHas('MainFunction', function ($query) use ($request) {
                                $query->where('main_name', 'like', '%' . $request->industry . '%');
                            });
                            $query->orWhereHas('Specialization', function ($query) use ($request) {
                                $query->where('secondary_name', 'like', '%' . $request->industry . '%');
                            });
                            $query->orWhereHas('BusinessTYpe', function ($query) use ($request) {
                                $query->where('business_type', 'like', '%' . $request->industry . '%');
                            });
                        });
                    })
                    ->when($request->company, function ($query) use ($request) {
                        $query->whereHas('Company', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->company . '%');
                        });
                    })
                    ->when($request->address, function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            foreach ($request->address as $address) {
                                $query->orWhere('location', 'like', '%' . $address . '%');
                            }
                        });
                    })
                    ->when($request->sort_option, function($query) use( $request ){
                        $sort_option = $request->sort_option;
                        $sort_order  = $request->sort_order;

                        switch ($sort_option) {

                            case "Location":
                                $query->orderBy('location',$sort_order);
                                break;
                            case 'Most Recent':
                                $query->orderBy('created_at',$sort_order);
                                break;

                            default:
                                $query->orderBy('created_at',$sort_order);
                                break;
                        }
                    })
                    ->with('company')->get();

                    if($request->sort_option && $request->sort_option == 'Role'){
                        switch ($request->sort_order) {
                            case 'asc':
                                $sorted = $data->sortBy('job_role_name');
                                $data = $sorted->values()->all();
                                break;
                            case 'desc':
                                $sorted = $data->sortByDesc('job_role_name');
                                $data = $sorted->values()->all();
                                break;
                            default:
                                $sorted = $data->sortBy('job_role_name');
                                $data = $sorted->values()->all();
                                break;
                        }
                    }elseif($request->sort_option && $request->sort_option == 'Tickets'){
                        switch ($request->sort_order) {
                            case 'asc':
                                $sorted = $data->sortBy('job_tickets');
                                $data = $sorted->values()->all();
                                break;
                            case 'desc':
                                $sorted = $data->sortByDesc('job_tickets');
                                $data = $sorted->values()->all();
                                break;
                            default:
                                break;
                        }
                    }elseif($request->sort_option && $request->sort_option == 'Education'){
                        switch ($request->sort_order) {
                            case 'asc':
                                $sorted = $data->sortBy('job_qualifications');
                                $data = $sorted->values()->all();
                                break;
                            case 'desc':
                                $sorted = $data->sortByDesc('job_qualifications');
                                $data = $sorted->values()->all();
                                break;
                            default:
                                break;
                        }
                    }


                break;

            case 'day_labour':
                $data = $request->all();
                break;
            default:
                return false;
                break;
        }

        return $data;
    }

    public function awardUser(Request $r)
    {
        $check_users_job = Job::where([['id' , '=', $r->id], ['created_by' , '=',  $r->userId]])->count();

        if($check_users_job === 0) return false;

        $successful_user = JobApplicant::where([['user_id', '=',  $r->awardId], ['job_id', '=',  $r->id]])->first();
        $successful_user_update = JobApplicant::where([['user_id', '=',  $r->awardId], ['job_id', '=',  $r->id]])->update(['selected' => 'Accepted']);
        $unsuccessful_users_update = JobApplicant::where([['job_id' , '=',  $r->id]])->where('user_id', '!=', $r->awardId )->update(['selected' => 'Declined']);
        $unsuccessful_users = JobApplicant::where([['job_id' , '=',  $r->id], ['selected', '=', 'Declined']])->get();
        $job = Job::where(['id' => $r->id])->update(['status' => 2]);

        if($successful_user_update) {
            $userNotification = (new Users)->f($r->awardId);
            $userJob = Job::where([['id' , '=', $r->id], ['created_by' , '=',  $r->userId]])->first();
            $userNotification->notify(new CompanyAwardToWorker($userJob));
        }

        if(!empty($unsuccessful_users)) {
            foreach($unsuccessful_users as $user) {

                $userNotification = (new Users)->f($user->user_id);
                $userJob = Job::where([['id', '=', $r->id], ['created_by', '=', $r->userId]])->first();
                Notification::send($userNotification, new CompanyChangesJobStatus($userJob, 'Filled'));
            }
        }

        return 'fool';
    }

    public function returnCurrentWorkerApplications(Request $r)
    {
        $select_array = array(
            'jpa.*',
            \DB::raw('DATE_FORMAT(jpa.applied_at, "%d %M %Y") as formatted_date'),
            'jp.*',
            'jr.*',
            'company.name as company_name',
            'company.photo_url as company_image'
        );
        $jobs = \DB::table('job_post_applicants as jpa')->select($select_array)
            ->join('job_posts as jp', 'jp.id', '=', 'jpa.job_id')
            ->leftjoin('job_roles as jr', 'jr.id', '=', 'jp.job_role_id')
            ->leftjoin('companies as company', 'company.id', '=', 'jp.company_id')
            ->where('jpa.user_id', $r->userId)
            ->get();

        return $jobs;
    }
}
