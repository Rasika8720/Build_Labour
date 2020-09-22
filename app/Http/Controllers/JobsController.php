<?php

namespace App\Http\Controllers;

use App\Cache;
use App\User;
use App\Models\Companies\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Companies\JobApplicant;

class JobsController extends Controller
{
    public function view(Request $request)
    {

        try {

            $user = $this->getAuthFromToken();
            $role = $user->isAdmin() ? 'company' : $user->role->name;;

            if ($user) {

                // check if user already applied for the job
                $already_applied = 0;
                $temp_already_applied = JobApplicant::where([['user_id','=',$user->id,],['job_id','=', $request->jid]])->first();

                if ($temp_already_applied) {

                    $already_applied = 1;
                }

                $isMyCompany = $user->company && $user->company->id == (int) $request->cid ? true : false;

                if (isset($request->cid) && isset($request->jid)) {

                    // user a company then redirect to applicants page
                    if ($user->Company && (isset($request->v) && $request->v == 'details') && $isMyCompany) {

                        return redirect(route('applicants', ['cid' => $request->cid, 'jid' => $request->jid]));

                    } else {

                        $isPreviewMode = 0;

                        return view('jobs.view')->with( compact( 'role','already_applied', 'isPreviewMode') );
                    }

                }

                return view('errors.404');
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {

            return view('errors.500');
        }
    }

    public function viewApplicants(Request $request)
    {
        $user = $this->getAuthFromToken();

        $hasCompareParam = false;

        if ($request->compare) {

            $hasCompareParam = true;
        }

        $isMyCompany = $user->company && $user->company->id == (int) $request->cid ? true : false;

        return view('jobs.applicants')->with(compact('hasCompareParam', 'isMyCompany'));
    }

    public function appliedTo(Request $request)
    {
        try {

            $user = $this->getAuthFromToken();

            if ($user) {
                if ($user && $user->role_id == 1) {
                    return view('jobs.applied-to');
                }
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {

            return view('errors.500');
        }
    }

    public function list(Request $request)
    {
        try {
            $type = isset($request->type) ? $request->type : null;

            if ($type == 'templates' || $type == 'active' || $type == 'closed') {
                $user = $this->getAuthFromToken();

                if ($user) {
                    if ($user && $user->role_id == 2) {
                        return view('jobs.list')->with('company_id', Company::where('created_by', $user->id)->first()->id);
                    }
                    return redirect('/job/search');
                }

                $this->clearAuthToken();

            } else {
                return view('errors.404');
            }

        } catch (\Exception $e) {

            return view('errors.500');
        }
    }

    public function post(Request $request)
    {

        try {
            $user = $this->getAuthFromToken();

            if ($user) {
                if (! $_GET || isset($request->jid) || isset($request->cache_id)) {
                    return view('jobs.post')->with('company_id', Company::where('created_by', $user->id)->first()->id);
                }

                return view('errors.404');
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {

            return view('errors.500');
        }
    }

    public function postDayLabour(Request $request)
    {

        try {
            $user = $this->getAuthFromToken();

            if ($user) {
                if (! $_GET || isset($request->jid) || isset($request->cache_id)) {
                    return view('jobs.post-day-labour')->with('company_id', Company::where('created_by', $user->id)->first()->id);
                }

                return view('errors.404');
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {

            return view('errors.500');
        }
    }

    public function preview(Request $request)
    {

        $cache = null;

        if ($request->cache_id) {

            $cache = Cache::find($request->cache_id);
        }

        try {

            $user = $this->getAuthFromToken();

            if ($cache && $user) {

                if($user->isAdmin()){
                    $role = 'Company';
                }
                else{
                    $role = $user->role->name;
                }

                $already_applied = 0;
                $isPreviewMode = 1;

                return view('jobs.view')->with(compact('role', 'already_applied', 'isPreviewMode'));
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {

            return view('errors.500');
        }

        return view('errors.500');
    }

    public function search()
    {
        return redirect('/job/search/all?type=jobs');
    }

    public function searchAll()
    {

        try {

            $user = $this->getAuthFromToken();
            $userId =  $user ? $user->id : '';

            $isShowMostRecent = false;
            $isShowJobAds = false;
            $companyId = null;

            if ($user->Company) {

                $isShowMostRecent = true;
                $companyId = $user->Company->id;

            } else {

                $isShowJobAds = true;
            }

            return view('jobs.search_all', compact('userId', 'isShowJobAds', 'isShowMostRecent', 'companyId'));

        } catch (\Exception $e) {

        }

        return view('errors.500');

    }
}

