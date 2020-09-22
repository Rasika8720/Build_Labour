<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Companies\JobRole;
use App\User;
use App\Models\Companies\Job;
use App\Models\Tickets\Ticket;
use App\Models\Companies\Company;
use Illuminate\Http\Request;
use App\Http\Resources\UsersResource;
use App\Http\Resources\WorkerResource;
use App\Http\Resources\JobsResource;
use App\Http\Resources\TicketsResource;
use App\Http\Resources\CompaniesResource;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\JobRolesResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class DatatableController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param User $user
     * @param Job $job
     * @param Ticket $ticket
     */
    public function __construct(User $user, Job $job, Ticket $ticket, Company $company)
    {
        $this->user = $user;
        $this->job = $job;
        $this->ticket = $ticket;
        $this->company = $company;
    }

    public function showUsers() {
        return view('admin.users');
    }

    public function showJobs() {
        return view('admin.jobs');
    }

    public function showTickets() {
        return view('admin.tickets');
    }

    public function showCompanies() {
        return view('admin.companies');
    }

    public function showJobRoles() {
        return view('admin.job-roles');
    }

    public function showWorkers() {
        return view('admin.workers');
    }
    /**
     * Get users for the data table.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getUsersDatatable(Request $request)
    {
        $column = $request->get('column');
        if ($column == 'full_name') {
            $column = 'first_name';
        }

        if ($column == 'willing to relocate') {
            $column = 'willing_to_relocate';
        }

        $order = $request->get('order') ? $request->get('order') : 'asc';
        $per_page = $request->get('per_page') ? $request->get('per_page') : 10;
        $search_text = $request->get('search_text') ? $request->get('search_text') : '';

        $users = User::query();

        $users = $users->select(
            'users.*',
            'we.id as work_exp_id',
            'we.job_role as work_exp_role',
            'we.isCurrent as work_exp_current',
            'we.job_role as role',
            'c.name as company_name',
            'c.id as company_id',
            'cbt.business_type as sector',
            'ct.tier_name as tier',
            'worker.user_id as worker_user_id',
            DB::raw("IF(worker.state IS NOT NULL, 'yes', 'no') AS willing_to_relocate")
        );

        $users = $users->leftJoin('work_experience as we', 'users.id', '=', 'we.user_id');
        $users = $users->join('worker_details as worker', 'users.id', '=', 'worker.user_id');
        $users = $users->leftJoin('companies as c', 'we.company_id', '=', 'c.id');
        $users = $users->leftJoin('company_business_types as cbt', 'cbt.id', '=', 'c.business_type_id');
        $users = $users->leftJoin('company_tiers as ct', 'ct.id', '=', 'c.tier_id');

        if (!empty($request->search_text)) {

            $likeRaw = "(first_name LIKE '%{$search_text}%'";
            $likeRaw .= " OR last_name LIKE '%{$search_text}%'";
            $likeRaw .= " OR email LIKE '%{$search_text}%'";
            $likeRaw .= " OR users.id LIKE '%{$search_text}%'";
            $likeRaw .= " OR users.address LIKE '%{$search_text}%'";
            $likeRaw .= " OR we.job_role LIKE '%{$search_text}%'";
            $likeRaw .= " OR cbt.business_type LIKE '%{$search_text}%'"; //sector
            $likeRaw .= " OR ct.tier_name LIKE '%{$search_text}%')";

            $users = $users->whereRaw($likeRaw);
        }

        $users = $users->groupBy('email');
        $users = $users->orderBy($column, $order);

        $users = $users->paginate($per_page);

        return UsersResource::collection($users);
    }

    public function getWorkersDatatable(Request $request)
    {
        $column = $request->get('column');
        if ($column == 'full_name') {
            $column = 'first_name';
        }

        if ($column == 'willing to relocate') {
            $column = 'willing_to_relocate';
        }

        $order = $request->get('order') ? $request->get('order') : 'asc';
        $per_page = $request->get('per_page') ? $request->get('per_page') : 10;
        $search_text = $request->get('search_text') ? $request->get('search_text') : '';

        $users = User::query();

        $users = $users->select(
            'users.*',
            'we.id as work_exp_id',
            'we.job_role as work_exp_role',
            'we.isCurrent as work_exp_current',
            'we.job_role as role',
            'c.name as company_name',
            'c.id as company_id',
            'cbt.business_type as sector',
            'ct.tier_name as tier',
            'worker.user_id as worker_user_id',
            DB::raw("IF(worker.state IS NOT NULL, 'yes', 'no') AS willing_to_relocate")
        );

        $users = $users->leftJoin('work_experience as we', 'users.id', '=', 'we.user_id');
        $users = $users->join('worker_details as worker', 'users.id', '=', 'worker.user_id');
        $users = $users->leftJoin('companies as c', 'we.company_id', '=', 'c.id');
        $users = $users->leftJoin('company_business_types as cbt', 'cbt.id', '=', 'c.business_type_id');
        $users = $users->leftJoin('company_tiers as ct', 'ct.id', '=', 'c.tier_id');



        if (!empty($request->search_text)) {

            $likeRaw = "(first_name LIKE '%{$search_text}%'";
            $likeRaw .= " OR last_name LIKE '%{$search_text}%'";
            $likeRaw .= " OR email LIKE '%{$search_text}%'";
            $likeRaw .= " OR users.id LIKE '%{$search_text}%'";
            $likeRaw .= " OR users.address LIKE '%{$search_text}%'";
            $likeRaw .= " OR we.job_role LIKE '%{$search_text}%'";
            $likeRaw .= " OR cbt.business_type LIKE '%{$search_text}%'"; //sector
            $likeRaw .= " OR ct.tier_name LIKE '%{$search_text}%')";

            $users = $users->whereRaw($likeRaw);
        }



        $users = $users->groupBy('email');
        if($column != 'company'){
            $users = $users->orderBy($column, $order);
        }

        $users = $users->whereHas('Role', function( $query ) use($request){
            $query->where('name','Worker');
        });


        $users = $users->get();
        if($request->company_filter && $request->company_filter != 'all'){
            $users = $users->where('displayed_company',$request->company_filter);
        }

        if($column == 'company'){
            if($order == 'asc'){
                $users = $users->sortBy('displayed_company');

            }else{
                $users = $users->sortByDesc('displayed_company');
            }
        }
        $users = $users->values()->all();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $collection = new Collection($users);

        $currentPageSearchResults = $collection->slice(($currentPage - 1) * $per_page, $per_page)->all();

        $paginatedSearchResults= new LengthAwarePaginator($currentPageSearchResults, count($collection), $per_page);


        return WorkerResource::collection($paginatedSearchResults);
    }

    public function getJobsDatatable(Request $request)
    {
        $column = $request->get('column') ? $request->get('column') : 'id';
        $order = $request->get('order') ? $request->get('order') : 'asc';
        $per_page = $request->get('per_page') ? $request->get('per_page') : 10;
        $search_text = $request->get('search_text') ? $request->get('search_text') : '';

        $query = $this->job->select(
            'job_posts.*',
            'companies.name as cname' )->where('is_template', false)->leftJoin('companies', 'job_posts.company_id', '=', 'companies.id');

        $query = $query->where(function($query) use($search_text) {

            $query
                ->where('job_posts.id', 'LIKE', '%'.$search_text.'%')
                ->orWhere('job_posts.title', 'LIKE', '%'.$search_text.'%')
                ->orWhere('job_posts.description', 'LIKE', '%'.$search_text.'%')
                ->orWhere('job_posts.about', 'LIKE', '%'.$search_text.'%')
                ->orWhere('job_posts.exp_level', 'LIKE', '%'.$search_text.'%')
                ->orWhere('job_posts.contract_type', 'LIKE', '%'.$search_text.'%')
                ->orWhere('title', 'LIKE', '%'.$search_text.'%');
        });

        if($column == 'company'){
            $query->orderBy('cname',$order);
        }else{
            $query = $query->orderBy($column, $order);
        }

        $data = $query->paginate($per_page);

        return JobsResource::collection($data);
    }

    public function getJobRolesDatatable(Request $request)
    {
        $column = $request->get('column') ? $request->get('column') : 'id';
        $order = $request->get('order') ? $request->get('order') : 'asc';
        $per_page = $request->get('per_page') ? $request->get('per_page') : 10;
        $search_text = $request->get('search_text') ? $request->get('search_text') : '';

        $query = JobRole::query();

        $query = $query->where(function($query) use($search_text) {

            $query
                ->where('id', 'LIKE', '%'.$search_text.'%')
                ->orWhere('job_role_name', 'LIKE', '%'.$search_text.'%');
        });


        $query = $query->orderBy($column, $order);
        $data = $query->paginate($per_page);

        return JobRolesResource::collection($data);
    }

    public function getTicketsDatatable(Request $request)
    {
        $column = $request->get('column') ? $request->get('column') : 'id';
        $order = $request->get('order') ? $request->get('order') : 'asc';
        $per_page = $request->get('per_page') ? $request->get('per_page') : 10;
        $search_text = $request->get('search_text') ? $request->get('search_text') : '';

        $query = $this->ticket
                    ->where('ticket', 'LIKE', '%'.$search_text.'%')
                    ->orWhere('description', 'LIKE', '%'.$search_text.'%')
                    ->orWhere('id', 'LIKE', '%'.$search_text.'%')
                    ->orderBy($column, $order);

        $data = $query->paginate($per_page);

        return TicketsResource::collection($data);
    }

    public function getCompaniesDatatable(Request $request)
    {
        $column = $request->get('column');

        $order = $request->get('order') ? $request->get('order') : 'asc';
        $per_page = $request->get('per_page') ? $request->get('per_page') : 10;
        $search_text = $request->get('search_text') ? $request->get('search_text') : '';

        $query = $this->company
                    ->where('name', 'LIKE', '%'.$search_text.'%')
                    ->orderBy($column, $order);

        $data = $query->paginate($per_page);

        return CompaniesResource::collection($data);
    }

}
