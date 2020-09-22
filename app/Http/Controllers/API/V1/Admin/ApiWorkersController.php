<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\API\V1\ApiBaseController;
use App\Models\Users\WorkExperience;
use App\Models\Users\WorkExperienceResponsibility;
use App\Repositories\WorkerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\Companies\Company;


class ApiWorkersController extends ApiBaseController
{
    protected $workerRepo;

    public function updateSectors( Request $request ){
        
        try {

            $workerRepo = new WorkerRepository();

            if( ! $result = $workerRepo->adminUpdateSectors($request) ){

                return $this->apiErrorResponse(
                    false,
                    $workerRepo->workExp->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $workerRepo->workExp->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }


        return $this->apiSuccessResponse( compact( 'result' ), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    public function updateWorkerIntroduction( Request $request ){
        $user = User::find($request->user_id);

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        try {

            $user->workerDetail->isIntroductionUpdate = true;

            if( !$user->workerDetail->store($request)){

                return $this->apiErrorResponse(
                    false,
                    $user->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->workerDetail->getErrorsDetail()
                );
            }

            $user->isForIntroduction = true;
            $request->request->add(['id' => $user->id]);

            if( !$user->store($request)){

                return $this->apiErrorResponse(
                    false,
                    $user->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $introduction = $request->all() + ['education' => $user->WorkerDetail->Education ];

        return $this->apiSuccessResponse( compact('introduction'), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);        
    }

    public function updatePersonalDetails( Request $request ){
        
        try {
            $workerRepo = new WorkerRepository();
            
            if( ! $result = $workerRepo->adminUpdatePersonalDetails($request) ) {

                return $this->apiErrorResponse(
                    false,
                    $workerRepo->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $workerRepo->workerDetail->getErrorsDetail()
                );
            }
        
        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
            
        return $this->apiSuccessResponse( compact('result'), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }
    
    public function updateAffirmations( Request $request ){
        try {

            $workerRepo = new WorkerRepository();

            if( ! $result = $workerRepo->adminUpdateAffirmations($request) ){

                return $this->apiErrorResponse(
                    false,
                    $workerRepo->workExp->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $workerRepo->workExp->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }


        return $this->apiSuccessResponse( compact( 'result' ), true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    public function updateNextRole( Request $request )
    {

        $user = User::find($request->user_id);

        if (!$user->workerDetail) {

            return $this->apiErrorResponse( false, 'Something wrong.', 400 , 'internalServerError' );
        }

        try {

            if( !$user->workerDetail->store($request) ){

                return $this->apiErrorResponse(
                    false,
                    $user->workerDetail->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $user->workerDetail->getErrorsDetail()
                );
            }


            // if (isset($request->countries)) {

                $user->country = $request->countries;
                $user->save();
            // }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( [ 'worker_detail' => $user->workerDetail ], true, 'Successfully updated worker details', self::HTTP_STATUS_REQUEST_OK);
    }

    public function getCompanies(){
        try {
            $record = Company::pluck('name')->sortBy('name');
            $additional_search_options = WorkExperience::select('company_name')->where('company_name','!=',NULL)->distinct()->get()->pluck('company_name');
            $merged = $record->merge($additional_search_options)->sortBy('company_name');
            if ($record) {
                
                return $this->apiSuccessResponse($merged->sort()->values()->all(), true, 'Request OK', self::HTTP_STATUS_REQUEST_OK);
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }

    public function deleteUser($user_id){
        try {
            $user = User::find($user_id);
            
            // NOTE: Remove this if account reactivation is possible
            $user->email = uniqid('deleted_') . '_' . $user->email;
            $user->save();
            $user->skills()->delete();
            $user->delete();

            return $this->apiSuccessResponse([], true, 'Account successfully deleted.', self::HTTP_STATUS_REQUEST_OK);

        } catch(\Exception $e) {
            
            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }
    }
}
