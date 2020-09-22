<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Companies\Company;

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
}

