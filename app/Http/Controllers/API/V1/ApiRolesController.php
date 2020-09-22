<?php

namespace App\Http\Controllers\API\V1;

use App\Repositories\CompanyRepository;
use App\Role;
use Illuminate\Http\Request;

class ApiRolesController extends ApiBaseController
{

    /**
     * @var CompanyRepository
     */
    protected $companyRepo;

    public function __construct(CompanyRepository $repository) {

        $this->companyRepo = $repository;
    }

    /**
     * @OA\Get(
     *      path="/roles",
     *      tags={"Roles"},
     *      summary="Get Roles by List",
     *      security={},
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
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description=""
     *      )
     * )
     */
    public function index() {

        // idx
        $roles = [];

        try {

            $roles = Role::all();

        } catch (\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse(compact('roles'), true, 'Roles', 200 );
    }

    /**
     * @OA\Get(
     *      path="/roles/job/search",
     *      tags={"Roles"},
     *      summary="Search a job role",
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
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description=""
     *      )
     * )
     */
    public function searchJobRoles(Request $request) {

        try {

            $job_roles = $this->companyRepo->searchJobRoles($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'job_roles' ), true, 'Search Successful', self::HTTP_STATUS_REQUEST_OK);
    }
}
