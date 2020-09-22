<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompaniesResource;
use App\Models\Companies\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function __construct(Company $company)
    {        
        $this->company = $company;
    }
    
    public function index() {
        return view('admin.companies');
    }

    /**
     * Get companies for the data table.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
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
