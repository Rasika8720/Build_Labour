<?php

namespace App\Http\Controllers;

use App\Cache;
use App\User;
use App\Company;
use App\Upload;
use App\UploadAd;
use App\JobPost;
use App\Models\Companies\Job;
use App\JobRole;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Companies\JobApplicant;

use Response;

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

    public function GetUploadsValus()
    { 
        return Upload::latest()->get();
    }

    public function uploadAdsApprove($id,Request $request)
    {
        $req = UploadAd::whereIn('id', $request->selected)->whereIn('status',[2,3])->get();
        //dd($req);
        Upload::where('id',$id)->update(['status' => 2]);
        $Upload = Upload::where('id',$id)->latest()->first();

        foreach ($req as $r) {
            $companyCreate = Company::firstOrCreate(['name' => $r->company_name, 'photo_url' => $r->job_logo]);
            JobRole::firstOrCreate(['job_role_name' => $r->job_role]);
            $company_id = Company::select(['id'])->where(['name' => $r->company_name])->first()->id;
            $job_role_id = JobRole::select(['id'])->where([ 'job_role_name'=> $r->job_role])->first()->id;
            JobPost::Create([
                'title'=>$r->title, 
                'description'=>$r->description, 
                'about'=>$r->about, 
                'exp_level'=>$r->exp_level, 
                'contract_type'=>$r->contract_type, 
                'salary'=>$r->salary, 
                'salary_type'=>$r->salary_type, 
                'project_size'=>$r->project_size, 
                'location'=>$r->location, 
                'company_id'=>$company_id, 
                'job_role_id'=>$job_role_id, 
                'status'=>1, 
            ]);
            if ($companyCreate) {
                $textEmail = strtolower(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\ ]/', '', $r->company_name))).'@test.com';
                User::create([
                    'first_name' => $r->company_name,
                    'role_id' => 2,
                    'email' => $textEmail,
                    'password' => Hash::make('password123!'),
                ]);
            }
        }

        return Response::json(['msg'=>'file successfully approved','text'=>'Approved','fileName'=>$Upload->file_name],200);
    }

    public function uploadAdsUpdate(Request $request)
    {
        UploadAd::where('id',$request->id)->update([
            'company_name' => $request->company_name, 
            'description' => $request->description,
            'location' => $request->location, 
            'salary' => $request->salary, 
            'job_role' => $request->job_role, 
            'job_url' => $request->job_url, 
            'title' => $request->title, 
            'about' => $request->about, 
            'contract_type' => $request->contract_type, 
            'status' => 3,
        ]);
        $UploadAd = UploadAd::where('id',$request->id)->latest()->first();

        return Response::json(['msg'=>'Successfully updated','UploadAdsUpdated'=>$UploadAd],200);
    }

    public function uploadJson(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:json,txt|file',
            'jsonData' => 'required',
        ]);
        $file = $request->file('file');

        $fileName = $file->getClientOriginalName();
        $exsists=Upload::where('file_name',$fileName)->first();

        if($exsists==null){
            $data['file_name'] = $fileName;
            $data['uploaded_date'] = date("Y-m-d");
            $data['status'] = 1;
            // $data['approve']='/storage/Jfiles/' . $fileName;
            
            $file->storeAs('/Jfiles/',$fileName,'public');
            $id=Upload::create($data)->id;
            
            $jsonData=json_decode($request->jsonData, true);
            foreach ($jsonData as $j) {
                $jj['company_name'] = $j['job_ad_advertisers_details'];
                $jj['description'] = $j['job_ad_details'];
                $jj['location'] = $j['job_ad_location'];
                $jj['salary'] = $j['job_ad_salary'];
                $jj['job_role'] = $j['job_ad_title'];
                $jj['title'] = $j['job_ad_title'];
                $jj['job_url'] = $j['job_ad_url'];
                $jj['job_logo'] = $j['logo_advertisers'];
                $jj['date_posted'] = $j['date_posted'];
                $jj['upload_id'] = $id;
                if ($j['job_ad_advertisers_details'] && $j['job_ad_details'] && $j['job_ad_location'] && $j['job_ad_title'] && $j['job_ad_salary'] && $j['job_ad_url']) {
                    $jj['status'] = 2;
                }else{
                    $jj['status'] = 1;
                }
                // dd($jj);
                UploadAd::create($jj);
            }

            $fileReturn= '/storage/Jfiles/' . $fileName;

            return Response::json(['msg'=>'Json File added successfully','viewFile'=>$fileReturn],200);
        }else{
            return Response::json(['status'=>'error','msg'=>'file already exists']);
        }
    }
}

