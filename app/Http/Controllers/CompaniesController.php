<?php

namespace App\Http\Controllers;

use App\Models\Companies\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function showCompany($id = null)
    {
        try {
            if ($id == null) {
                $user = $this->getAuthFromToken();

                if ($user) {
                    return view('companies.profile')->with('company_id',
                                (Company::where('created_by', $user->id)->first())->id);
                }

                $this->clearAuthToken();
            }
            
            return view('companies.profile')->with('company_id', $id);
        
        } catch (\Exception $e) {

            return view('errors.500');
        }
    }
}

