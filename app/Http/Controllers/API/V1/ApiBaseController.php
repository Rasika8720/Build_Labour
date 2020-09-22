<?php
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Build Labour Api",
 *      description="A product of Appetiser",
 *      @OA\Contact(
 *          name="API Support",
 *          email="dennis.agulo@appetiser.com.au"
 *      )
 * )
 */

/**
 *  @OA\Server(
 *      url="/api/v1",
 *      description="Base Endpoint"
 * )
 */

/**
 * @OA\SecurityScheme(
 *      securityScheme="BearerAuth",
 *      type="http",
 *      scheme="bearer"
 * )
 */
namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Companies\Company;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use JWTAuth;

class ApiBaseController extends Controller
{
    const HTTP_STATUS_REQUEST_OK    = 200;
    const HTTP_STATUS_INVALID_INPUT = 422;
    const HTTP_STATUS_BAD_REQUEST   = 400;
    const HTTP_STATUS_UNAUTHORIZED  = 401;
    const HTTP_STATUS_NOT_FOUND     = 404;
    const INTERNAL_SERVER_ERROR     = 500;
    
    protected $response = [];
    protected $user;

    /**
     * Standard format for successful API requests
     * @param $data
     * @param $success
     * @param $message
     * @param $http_status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiSuccessResponse( $data, $success = true , $message = '', $http_status = 200 )
    {
        return response()->json(compact( 'success', 'message', 'http_status', 'data' ), $http_status);
    }

    /**
     * Standard format for error API requests
     *
     * @param $success
     * @param $message
     * @param $http_status
     * @param $error_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiErrorResponse( $success = false , $message, $http_status , $error_code = null, $errors = null )
    {
        if ($http_status == 422) {
            return response()->json( compact( 'success', 'message', 'http_status', 'error_code', 'errors' ), $http_status);
        }
        
        return response()->json( compact( 'success', 'message', 'http_status', 'error_code' ), $http_status);
    }


    /**
     * Evaluate the resulting data from corresponding Model Repository
     * 
     * @return response formatted
     */
    protected function checkResponseData( $model, $responseData, $successMessage, $badRequest = null)
    {
        if ($badRequest) {
            return $this->apiErrorResponse(false, $responseData['error'], self::HTTP_STATUS_BAD_REQUEST, 'badRequest');
        }
        if ($responseData[$model]) {
            return $this->apiSuccessResponse($responseData, true, $successMessage, self::HTTP_STATUS_REQUEST_OK);
        }
        if (strpos($responseData['error'], 'not found') !== false) {
            return $this->apiErrorResponse(false, $responseData['error'], self::HTTP_STATUS_NOT_FOUND, $model . 'NotFound');
        }

        return $this->apiErrorResponse(false, $responseData['error'], self::INTERNAL_SERVER_ERROR, 'sqlError');
    }

    /**
     * Manual authentication. Alternative if you do not want to use the JWT Middleware
     *
     * @param Request $r
     * @return bool
     */
    protected function authenticate( Request $r )
    {
        try {
            if ( ! $user = JWTAuth::authenticate( $r->token ) ) {
                $this->response = [
                    'status' => 404,
                    'error_code' => 'userNotFound',
                    'message'=>'User not found'
                ];
                return false;
            }

        }catch( TokenExpiredException $e) {

            $this->response = [
                'status' => 404,
                'error_code' => 'tokenExpired',
                'message'=>'Token expired'
            ];
            return false;

        } catch ( TokenInvalidException  $e) {

            $this->response = [
                'status' => 404,
                'error_code' => 'invalidToken',
                'message'=>'Invalid Token'
            ];
            return false;

        } catch (JWTException $e ) {

            $this->response = [
                'status' => 404,
                'error_code' => 'tokenAbsent',
                'message'=>'Token Absent '
            ];
            return false;

        }catch (\Exception $e ) {

            $this->response     =   [
                'status' => 404,
                'error_code' => 'Unknown Error',
                'message'=>'Unknown error'
            ];
            return false;
        }
        // the token is valid and we have found the user via the sub claim
        return $user;
    }

    /**
     * get current viewer user type
     *
     * @param Request $r
     * @return string
     */
    protected function viewerType( Request $r )
    {
        $user = JWTAuth::toUser();

        $viewerType = 'viewer';

        if ($r->company_id) {

            $company = Company::where('created_by', $user->id)->where('id' , $r->company_id)->first();

            if ($company) {

                $viewerType = $user->Company && $user->Company->id == $company->id ? 'company' : 'viewer';
            }
        }

        return $viewerType;
    }

}