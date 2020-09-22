<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Companies\Company;
use App\Models\Options\BusinessType;
use App\Models\Options\MainFunction;
use App\Models\Options\Tier;
use App\Models\Users\Users;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class ApiCompaniesController extends ApiBaseController
{

    /*
     *
     *      Route::get('search', 'ApiCompaniesController@search');
            Route::post('update', 'ApiCompaniesController@update');
            Route::post('photo', 'ApiCompaniesController@uploadPhoto');
            Route::get('{id}', 'ApiCompaniesController@view');
            Route::get('{id}/jobs', 'ApiCompaniesController@update');
            Route::get('{id}/workers', 'ApiCompaniesController@workers');
     */
    /**
     * @var CompanyRepository
     */
    protected $repository;

    public function __construct(CompanyRepository $repository) {

        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *      path="/company/search",
     *      tags={"Company"},
     *      summary="Search Company",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="keyword",
     *          description="Keyword",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function search( Request $request )
    {

        try {

            $companies = $this->repository->search($request->get('keyword'))->all();

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'companies' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}",
     *      tags={"Company"},
     *      summary="View a Limit Data Company",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function view( Request $request )
    {
        try {

            $company = $this->repository->getCompany($request->id);
            $user = \JWTAuth::toUser();

            if (!$company) {

                return $this->apiErrorResponse(false, 'Something wrong!', self::HTTP_STATUS_INVALID_INPUT, 'invalidInput');

            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'company', 'user' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/specialization",
     *      tags={"Company"},
     *      summary="Company Specialization",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function specialization ( Request $request ) {

        try {

            $specialization = $this->repository->getCompanySpecialization($request->id);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'specialization' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/people",
     *      tags={"Company"},
     *      summary="Get company people",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function workers( Request $request )
    {
        try {

            $people = $this->repository->getWorkers($request->id);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'people' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/applicants?limit=3",
     *      tags={"Company"},
     *      summary="Get applicants within the company different to job applicants",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function applicants( Request $request )
    {
        try {

            $applicants = $this->repository->getApplicants($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'applicants' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/options",
     *      tags={"Company"},
     *      summary="Get company options for selection",
     *      security={{"BearerAuth":{}}},
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function options() {

        $business_types = BusinessType::all();
        $tiers = Tier::all();
        $main_company_functions = MainFunction::with('items')->get();

        return compact(
            'business_types',
            'tiers',
            'main_company_functions'
        );
    }

    /**
     * @OA\Get(
     *      path="/company/options/{id}",
     *      tags={"Company"},
     *      summary="Get company options for selection",
     *      security={{"BearerAuth":{}}},
     *       @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company main function id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function getMainFunctionItems( Request $request ) {

        $mainFunction = MainFunction::find($request->id);

        $items = [];

        if ($mainFunction) {

            $items = $mainFunction->items;
        }

        return $this->apiSuccessResponse( $items , true, 'success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Post(
     *      path="/company/update",
     *      tags={"Company"},
     *      summary="Update a company in a user as company",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="name",
     *                      description="Company Name",
     *                      type="string",
     *                      example="Sample Company"
     *                  ),
     *                  @OA\Property(
     *                      property="business_type_id",
     *                      description="Business Entity Type ID",
     *                      type="integer",
     *                      example=2
     *                  ),
     *                  @OA\Property(
     *                      property="tier_id",
     *                      description="Tier ID",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="main_company_id",
     *                      description="Company Main function",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(
     *                      property="secondary_functions",
     *                      type="array",
     *                      @OA\Items(
     *                         type="integer",
     *                         example=1
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      description="Company Main Address",
     *                      type="string",
     *                      example="51st St. John Avenue, AU"
     *                  ),
     *                  @OA\Property(
     *                      property="contact_number",
     *                      description="Company Contact Number",
     *                      type="string",
     *                      example="123456789"
     *                  ),
     *                  @OA\Property(
     *                      property="operate_outside_states",
     *                      description="Company Can operate outside states",
     *                      type="boolean",
     *                      example=true
     *                  ),
     *                  @OA\Property(
     *                      property="website",
     *                      description="Company Website",
     *                      type="string",
     *                      example="www.sample.com"
     *                  ),
     *                  @OA\Property(
     *                      property="states",
     *                      type="array",
     *                      @OA\Items(
     *                         type="string",
     *                         example="VIC"
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="photo",
     *                      description="Company Photo",
     *                      type="string",
     *                      example="data:image/png;base64,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"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Invalid Input"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Request Created"
     *      )
     * )
     */
    public function update( Request $request )
    {

        try {

            if (!$company = $this->repository->updateCompany($request)) {

                return $this->apiErrorResponse(
                    false,
                    $this->repository->company->getErrors(),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $this->repository->company->getErrorsDetail()
                );
            }

            $user = JWTAuth::toUser();
            $userRequest = new Request;
            $userRequest->merge(['id' => $user->id, 'email' => $request->email, 'update_company_email' => true]);
            $user_email = (new Users)->store($userRequest);

            if( !$user_email ) {
                return $this->apiErrorResponse(
                    false,
                    $user_email,
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $this->repository->company->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'company' ), true, 'Successfully updated a company', self::HTTP_STATUS_REQUEST_OK);
    }

    public function adminUpdateCompany( Request $request ){
        DB::beginTransaction();

        try {

            if ( !$company = $this->repository->adminUpdateCompany( $request ) ) {

                return $this->apiErrorResponse(
                    false,
                    $this->repository->company->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $this->repository->company->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            DB::rollback();

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        DB::commit();

        return $this->apiSuccessResponse( compact('company'), true, 'Updated company successfully', self::HTTP_STATUS_REQUEST_OK);
     }
    /**
     * @OA\Post(
     *      path="/company/photo",
     *      tags={"Company"},
     *      summary="Upload photo",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="photo",
     *                      description="Image File",
     *                      format="file",
     *                      type="string",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function uploadPhoto( Request $request )
    {

        if( !$photoUrl = $this->repository->uploadPhoto( $request ) ) {
            return $this->apiErrorResponse(
                false,
                $this->repository->company->getErrors( true ),
                self::HTTP_STATUS_INVALID_INPUT,
                'invalidInput',
                $this->repository->company->getErrorsDetail()
            );
        }

        return $this->apiSuccessResponse([ 'photo_url' => $photoUrl ], true, 'Profile Photo Uploaded Successfully ', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/jobs",
     *      tags={"Company"},
     *      summary="Get company jobs",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function jobs( Request $request )
    {
        try {

            $jobs = $this->repository->getJobs($request->id);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'jobs' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }


    /**
     * @OA\Get(
     *      path="/company/{id}/posts",
     *      tags={"Company"},
     *      summary="Get company posts",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function posts( Request $request )
    {
        try {

            $posts = $this->repository->getPosts($request->id);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'posts' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/posts/jobs?status=active",
     *      tags={"Company"},
     *      summary="Get company Post Jobs by filter active or closed",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function jobPosts( Request $request )
    {
        try {

            $jobs = $this->repository->getJobPosts($request->id, $request->status);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'jobs' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/templates/{jid}",
     *      tags={"Company"},
     *      summary="View a job",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          in="path",
     *          name="jid",
     *          description="job id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function viewJob( Request $request ) {

        try {

            $job = $this->repository->getJob($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'job' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/job-preview/{cacheid}",
     *      tags={"Company"},
     *      summary="View a job",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          in="path",
     *          name="jid",
     *          description="cache id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function viewJobPreview( Request $request ) {

        try {

            $job = $this->repository->getJobPreview($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'job' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }


    /**
     * @OA\Get(
     *      path="/company/{id}/templates",
     *      tags={"Company"},
     *      summary="Get company Job templates",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function jobTemplates( Request $request ) {

        try {

            $jobs = $this->repository->getJobTemplates($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'jobs' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Post(
     *      path="/company/{id}/templates",
     *      tags={"Company"},
     *      summary="Create a company Job Template",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="title",
     *                      description="<b>Required</b> Title",
     *                      type="string",
     *                      example="Supervisor"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function createJobTemplate( Request $request )
    {
        DB::beginTransaction();


        if ( !$job_template = $this->repository->createJobTemplate( $request ) ) {

            DB::rollBack();
            return $this->apiErrorResponse(
                false,
                $this->repository->job->getErrors( true ),
                self::HTTP_STATUS_INVALID_INPUT,
                'invalidInput',
                $this->repository->job->getErrorsDetail()
            );
        }

        DB::commit();

        return $this->apiSuccessResponse( compact('job_template'), true, 'Successfully created job template', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/company/{id}/templates/{tid}",
     *      tags={"Company"},
     *      summary="View a company job template",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          in="path",
     *          name="tid",
     *          description="template id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function viewJobTemplate( Request $request ) {

        try {

            $job_template = $this->repository->viewJobTemplate($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'job_template' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Delete(
     *      path="/company/{id}/templates/{tid}",
     *      tags={"Company"},
     *      summary="Delete a company job template",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          description="company id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          in="path",
     *          name="tid",
     *          description="template id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Token"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Token Expired"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Token Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request OK"
     *      )
     * )
     */
    public function deleteJobTemplate( Request $request ) {

        DB::beginTransaction();

        if ( !$job_template = $this->repository->deleteTemplate( $request ) ) {

            DB::rollBack();
            return $this->apiErrorResponse(
                false,
                $this->repository->job->getErrors( true ),
                self::HTTP_STATUS_INVALID_INPUT,
                'invalidInput',
                $this->repository->job->getErrorsDetail()
            );
        }

        DB::commit();

        return $this->apiSuccessResponse( compact('job_template'), true, 'Successfully deleted job template', self::HTTP_STATUS_REQUEST_OK);
    }
}
