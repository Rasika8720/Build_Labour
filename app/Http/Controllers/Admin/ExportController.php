<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Users\WorkerDetail;
use App\User;
use App\Models\Companies\Job;
use App\Models\Tickets\Ticket;
use App\Models\Companies\Company;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ExportController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Display
    |--------------------------------------------------------------------------
    */

    /**
     * Create a new controller instance.
     *
     * @param User $user
     * @param Job $job
     * @param Ticket $ticket
     */
    public function __construct(
        User $user,
        Job $job,
        Ticket $ticket,
        Company $company,
        Request $request
    ) {
        $this->user = $user;
        $this->job = $job;
        $this->ticket = $ticket;
        $this->company = $company;
        $this->r = $request;
    }
    
    public function export()
    {
        // /export?dateFrom=2019-01-01&dateTo=2019-12-31&type=csv&exportBy=job-posts
        // /export?dateFrom=2019-01-01&dateTo=2019-12-31&type=csv&exportBy=workers

        $exportBy = $this->r->exportBy;
        $columns = [];
        $rows = [];
        $filename = "BL_{$this->r->exportBy}.csv";

        switch ($exportBy) {

            case 'workers':

                $generated = $this->generateByWorkers();
                $columns = $generated['cols'];
                $rows = $generated['data'];
                break;

            case 'companies':

                $generated = $this->generateByCompanies();
                $columns = $generated['cols'];
                $rows = $generated['data'];
                break;

            case 'job-posts' :

            default:

                $generated = $this->generateByJobs();
                $columns = $generated['cols'];
                $rows = $generated['data'];
        }

        $handle = fopen('php://memory', "rw+");

        //add csv headers
        fputcsv($handle, $columns);

        foreach ($rows as $row) {

            fputcsv($handle, $row);
        }

        // reset the file pointer to the start of the file
        fseek($handle, 0);

        // tell the browser it's going to be a csv file
        header('Content-Type: text/csv');

        // tell the browser we want to save it instead of displaying it
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        // make php send the generated csv lines to the browser
        fpassthru($handle);
    }

    public function showExports()
    {
        return view('admin.exports');
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function generateByJobs()
    {
        $dateFrom = Carbon::parse($this->r->dateFrom . '00:00:00');
        $dateTo = Carbon::parse($this->r->dateTo . '23:59:59');

        $cols = [
            'id',
            'company',
            'title',
            'location',
            'exp_level',
            'contract_type',
            'salary',
            'reports_to',
            'created_at',
        ];

        $jobs = Job::query();
        $jobs = $jobs->select([
            'job_posts.id',
            'job_posts.created_at',
            'job_posts.title',
            'job_posts.job_role_id',
            'job_posts.salary',
            'job_posts.location',
            'job_posts.exp_level',
            'job_posts.contract_type',
            'job_posts.reports_to_json',
            'company.name as company',
            'job_role.job_role_name'
        ]);

        $jobs = $jobs->join('companies as company', 'job_posts.company_id', '=', 'company.id');
        $jobs = $jobs->leftJoin('job_roles as job_role', 'job_posts.job_role_id', '=', 'job_role.id')
                     //->where('job_posts.created_at', '>=', $dateFrom)
                     //->where('job_posts.created_at', '<=', $dateTo)
                     ->orderBy('id', 'desc')
                     ->get();

        $data = array_map(function($job) use($cols) {

            $data = array();

            foreach ($cols as $col) {

                if ($col == 'title' && !$job[$col]) {

                    $job[$col] = $job['job_role_name'];
                }

                if ($col == 'reports_to')
                {

                    if ($job['reports_to_json']) {

                        $reportsTo = json_decode($job['reports_to_json'], true);

                        $job[$col] = implode(",", $reportsTo);

                    } else {

                        $job[$col] = "";
                    }
                }

                $data[$col] = $job[$col];
            }

            return $data;

        }, $jobs->toArray());

        $cols = array_map(function($col) {

            return strtoupper(str_replace("_", " ", $col));
        }, $cols);

        return compact('cols', 'data');
    }

    public function generateByWorkers()
    {
        $dateFrom = Carbon::parse($this->r->dateFrom . '00:00:00');
        $dateTo = Carbon::parse($this->r->dateTo . '23:59:59');

        $cols = [
            'id',
            'full_name',
            'company',
            'job_role',
            'email',
            'created_at',
    //        'experiences'
        ];

        $users = User::with('Experiences');

        $users = $users->select(
            'users.*',
            'worker.user_id as worker_user_id'
        );

        $users = $users->join('worker_details as worker', 'users.id', '=', 'worker.user_id')
                        ->where('users.role_id', 1) // worker role
                        //->where('users.created_at', '>=', $dateFrom)
                        //->where('users.created_at', '<=', $dateTo)
                        ->orderBy('users.id', 'desc')
                        ->get();

        $data = $users->map(function($user, $key) use ($cols) {

            $user->full_name;

            $tempCompanies = $user->experiences;
            $company = NULL;

            if ($tempCompanies && count($tempCompanies) > 0) {

                if ($tempCompanies[0]->company_id) {

                    $company= $tempCompanies[0]->company->name;

                } else {

                    $company= $tempCompanies[0]->company_name;
                }
            }

            $user->role_name = $user->role;
            $user->company = $company;

            foreach ($cols as $col) {

                if ($col == 'job_role') {

                    if (count($user->experiences) > 0) {

                        $user->{$col} = $user->experiences[0]->job_role;
                    }
                }

                $data[$col] = $user->{$col};
            }

            return $data;
        });

        $cols = array_map(function($col) {

            return strtoupper(str_replace("_", " ", $col));
        }, $cols);

        return compact('cols', 'data');
    }

    public function generateByCompanies()
    {
        $dateFrom = Carbon::parse($this->r->dateFrom . '00:00:00');
        $dateTo = Carbon::parse($this->r->dateTo . '23:59:59');

        $cols = [
            'id',
            'name',
            'no_of_workers',
            'created_at'
        ];

        $companies = Company::query()
                    //->where('created_at', '>=', $dateFrom)
                    //->where('created_at', '<=', $dateTo)
                    ->orderBy('id', 'desc')
                    ->get();

        $data = $companies->map(function($company, $key) use ($cols) {

            $company->no_of_workers;
            $data = [];

            foreach ($cols as $col) {

                $data[$col] = $company->{$col};
            }

            return $data;
        });

        $cols = array_map(function($col) {

            return strtoupper(str_replace("_", " ", $col));
        }, $cols);

        return compact('cols', 'data');
    }
}
