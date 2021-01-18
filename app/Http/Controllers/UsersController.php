<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Companies\Company;
use App\UploadAd;
use App\Upload;

class UsersController extends Controller
{
    public function showProfile($id = null)
    {
        try {
            $page = \Route::current()->getName();

            $user = $this->getAuthFromToken();
            $viewerType = 'company';

            if ($user) {
                if ($id == null) {
                    if ($user->role_id == 1) {
                        return view('users.profile')->with(['user_id' => null, 'internal_role' => $user->role_id]);

                    } else if ($user->role_id == 2) {
                        return view('companies.profile')->with(['company_id' =>
                                    (Company::where('created_by', $user->id)->first())->id, 'viewer_type' => $viewerType, 'internal_role' => $user->role_id]);
                    }
                }

                if ($page == 'company_profile') {

                    $company = Company::where('created_by', $user->id)->first();

                    if ($user->role_id == 2 && $company && $company->id == $id) {

                        return redirect('/user/profile');
                    }

                    $viewerType = $company && $user->Company && $user->Company->id == $company->id ? 'company' : 'viewer';

                    return view('companies.profile')->with(['company_id' => $id, 'viewer_type' => $viewerType, 'internal_role' => $user->role_id]);
                }

                if ($page == 'profile') {

                    if ($user->role_id == 1 && $user->id == $id) {
                        return redirect('/user/profile');
                    }
                    // dd($user->role_id);
                    return view('users.profile')->with(['user_id' => $id, 'viewer_type' => $viewerType, 'internal_role' => $user->role_id]);
                }
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {

            echo $e->getMessage();
            return view('errors.500');
        }
    }

    public function showOnboarding()
    {
//        $user = $this->getAuthFromToken();
//        echo '<pre>';
//        print_r($user->WorkerDetail);
//        echo '</pre>';
//        die();
        try {
            $user = $this->getAuthFromToken();

            if ($user) {
                if ($user->role_id == 1) {

                    return view('users.onboarding')->with([/*'most_recent_role' => $user->workerDetail->most_recent_role,*/ 'user_id' => $user->id]);
                }

                return redirect('/user/profile');
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {
//            echo $e->getMessage();
            return view('errors.500');
        }
    }

    public function showVerifyForm(Request $r)
    {
        if (isset($r->tk)) {

            $user = User::whereVerificationCode($r->tk)->first();

            if ($user) {
                $user->verification_code = NULL;
                $user->is_verified = Carbon::now();
                $user->save();
                return redirect(route('login'));
            }
        }

        return view('errors.500');
    }


    
    public function UploadJobAdss($id = null)
    {
        try {
            $page = \Route::current()->getName();
            
            $user = $this->getAuthFromToken();
            $viewerType = 'company';
            
            if ($user) {
                if ($id == null) {
                    if ($user->role_id == 1) {
                        return view('users.profile')->with(['user_id' => null, 'internal_role' => $user->role_id]);
                        
                    } else if ($user->role_id == 2) {
                        return view('companies.profile')->with(['company_id' =>
                        (Company::where('created_by', $user->id)->first())->id, 'viewer_type' => $viewerType, 'internal_role' => $user->role_id]);
                    }
                }
                
                if ($page == 'company_profile') {
                    
                    $company = Company::where('created_by', $user->id)->first();
                    
                    if ($user->role_id == 2 && $company && $company->id == $id) {
                        
                        return redirect('/user/profile');
                    }
                    
                    $viewerType = $company && $user->Company && $user->Company->id == $company->id ? 'company' : 'viewer';
                    
                    return view('companies.profile')->with(['company_id' => $id, 'viewer_type' => $viewerType, 'internal_role' => $user->role_id]);
                }
                
                if ($page == 'profile') {
                    
                    if ($user->role_id == 1 && $user->id == $id) {
                        return redirect('/user/profile');
                    }
                    // dd('acvv');
                    return view('layouts.jobAds')->with(['user_id' => $id, 'viewer_type' => $viewerType, 'internal_role' => $user->role_id]);
                }
            }

            $this->clearAuthToken();

        } catch (\Exception $e) {

            echo $e->getMessage();
            return view('errors.500');
        }
    }
    public function UploadJobAds()
    {
        return view('layouts.jobAds')->with(['user_id' => null, 'viewer_type' => 'company', 'internal_role' => 5]);
    }
    public function UploadJson()
    {
        return view('layouts.uploadJson')->with(['user_id' => null, 'viewer_type' => 'company', 'internal_role' => 5]);
    }
    public function UploadAds($id)
    {
        $uploadAd=UploadAd::where('upload_id',$id)->get();
        $Upload = Upload::where('id',$id)->latest()->first();
        return view('layouts.uploadAds')->with(['user_id' => null, 'viewer_type' => 'company', 'internal_role' => 5,'ad_id'=>$uploadAd,'uploadId'=>$Upload]);
    }
    public function UploadAdsApproveEdit($id)
    {
        $uploadAd=UploadAd::where('id',$id)->get();
        return view('layouts.uploadAdsEdit')->with(['user_id' => null, 'viewer_type' => 'company', 'internal_role' => 5,'ad_id'=>$uploadAd]);
    }
    public function text(Request $request)
    {
        $requ = 'Dow*n?er E^DI% Se-r/v8\90|ic+es# ((Pt<>y)) Ltd';
        $re = strtolower(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\ ]/', '', $requ))).'@test.com';
        return $re;    
    }
}

