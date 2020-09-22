<?php

namespace App\Http\Controllers\API\V1;

use App\Course;
use App\Models\Companies\Answer;
use App\School;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

/**
 * Class UsersController
 * @property User user
 * @package App\Http\Controllers
 */
class ApiGeneralController extends ApiBaseController
{

    /**
     * @OA\Get(
     *      path="/locations?keyword=South",
     *      tags={"General"},
     *      summary="Search Locations",
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
     *          description="Experiences"
     *      )
     * )
     */

    public function searchLocation( Request $request ) {

        try {

            $keyword = trim($request->keyword);
            $types = $request->types;
            $curl_handle=curl_init();

            $endpoint = str_replace(' ', '%20', $keyword) . '.json';
            $uri = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$endpoint}?country=au&access_token=" . env('MAPBOX_KEY');

           // $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$keyword}.json?country=au&types={$types}&access_token=" . env('MAPBOX_KEY');

            curl_setopt($curl_handle,CURLOPT_URL,$uri);

        //    dd("https://api.mapbox.com/geocoding/v5/mapbox.places/{$keyword}.json?country=au&types={$types}&access_token=" . env('MAPBOX_KEY'));
            curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
            curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);

            $buffer = curl_exec($curl_handle);

            curl_close($curl_handle);

            $buffer = json_decode($buffer, true);

            if (!$buffer) {

                $buffer = ['features' => []];
            }

//            $buffer['features'] = array_filter($buffer['features'], function($val) {
//
//                return isset($val['place_type']) &&
//                    $val['place_type'] &&
//                    count($val['place_type']) > 0 &&
//                    ($val['place_type'][0] == 'address' || $val['place_type'][0] == 'poi' || $val['place_type'][0] == 'place')  ;
//            });
//
//            // street, suburb, state, postcode
//
//            $buffer['features'] = array_map(function($val) {
//
//                $address = [];
//
//                $address[] = $val['text'];
//
//                foreach ($val['context'] as $c) {
//
//                    if (strpos($c['id'], 'locality') !== false) {
//
//                        $address[] = $c['text'];
//                    }
//
//                    if (strpos($c['id'], 'place') !== false) {
//
//                        $address[] = $c['text'];
//                    }
//
//                    if (strpos($c['id'], 'region') !== false) {
//
//                        $address[] = $c['text'];
//                    }
//
//                    if (strpos($c['id'], 'postcode') !== false) {
//
//                        $address[] = $c['text'];
//                    }
//                }
//
//                $address = implode(", ", $address);
//
//                return [
//                    'place_name' => $address
//                ];
//
//            }, $buffer['features']);
//
//            $buffer['features'] = array_values($buffer['features']);

        } catch (\Exception $e) {

            return $this->apiSuccessResponse([ 'locations' => ['features'=> []] ], true, 'Success', self::HTTP_STATUS_REQUEST_OK);

        }

        return $this->apiSuccessResponse([ 'locations' => $buffer ], true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/countries",
     *      tags={"General"},
     *      summary="Get countries",
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
     *          description="Experiences"
     *      )
     * )
     */
    public function getCountries() {

        $countries = config('countries');

        return $this->apiSuccessResponse( compact('countries'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);

    }

    /**
     * @OA\Get(
     *      path="/courses?keyword=C",
     *      tags={"General"},
     *      summary="Search Courses",
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
     *          description="Experiences"
     *      )
     * )
     */
    public function getCourses(Request $request) {

        $keyword = $request->keyword ? $request->keyword : '';

        $courses = Course::where('course_name', 'like', "%{$keyword}%")
                ->get();

        return $this->apiSuccessResponse( compact('courses'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);

    }

    /**
     * @OA\Get(
     *      path="/schools?keyword=C",
     *      tags={"General"},
     *      summary="Search Schools",
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
     *          description="Experiences"
     *      )
     * )
     */
    public function getSchools(Request $request) {

        $keyword = $request->keyword ? $request->keyword : '';

        $schools = School::where('school_name', 'like', "%{$keyword}%")
            ->take(25)->get();

        return $this->apiSuccessResponse( compact('schools'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);

    }

    /**
     * @OA\Get(
     *      path="/answers/{mainid}?keyword=in",
     *      tags={"General"},
     *      summary="Search Main Function Answers",
     *      security={{"BearerAuth":{}}},
     *      @OA\Parameter(
     *          in="path",
     *          name="mainid",
     *          description="main id",
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
     *          response=500,
     *          description="Internal Server Error"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Experiences"
     *      )
     * )
     */
    public function getAnswers(Request $request) {

        $keyword = $request->keyword ? $request->keyword : '';
        $mainid = empty($request->mainid) ? null : $request->mainid;

        try {

            $answers = Answer::where('answer', 'like', "%{$keyword}%")
                ->where('main_function_id', $mainid)
                ->take(50)->get();

            if (!$answers) {

                return $this->apiErrorResponse(false, 'Something wrong!', self::HTTP_STATUS_INVALID_INPUT, 'invalidInput');

            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'answers' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/open-search?keyword=john",
     *      tags={"General"},
     *      summary="Open search by searching a worker",
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
    public function search(Request $request) {

        $keyword = empty(trim($request->keyword)) ? '' : trim($request->keyword);
        $people = [];

        if (!empty($keyword)) {

            $people = User::query();

            $people = $people->select(
                'users.*',
                'we.id as work_exp_id',
                'we.job_role as work_exp_role',
                'we.isCurrent as work_exp_current',
                'we.job_role as role',
                'c.name as company_name',
                'c.id as company_id',
                'cbt.business_type as sector',
                'ct.tier_name as tier',
                'worker.user_id as worker_user_id',
                \DB::raw("IF(worker.state IS NOT NULL, 'yes', 'no') AS willing_to_relocate")
            );

            $people = $people->leftJoin('work_experience as we', 'users.id', '=', 'we.user_id');
            $people = $people->join('worker_details as worker', 'users.id', '=', 'worker.user_id');
            $people = $people->leftJoin('companies as c', 'we.company_id', '=', 'c.id');
            $people = $people->leftJoin('company_business_types as cbt', 'cbt.id', '=', 'c.business_type_id');
            $people = $people->leftJoin('company_tiers as ct', 'ct.id', '=', 'c.tier_id');

            $likeRaw = "(first_name LIKE '%{$keyword}%'";
            $likeRaw .= " OR last_name LIKE '%{$keyword}%'";
            $likeRaw .= " OR email LIKE '%{$keyword}%'";
            $likeRaw .= " OR users.id LIKE '%{$keyword}%'";
            $likeRaw .= " OR users.address LIKE '%{$keyword}%'";
            $likeRaw .= " OR we.job_role LIKE '%{$keyword}%'";
            $likeRaw .= " OR cbt.business_type LIKE '%{$keyword}%'"; //sector
            $likeRaw .= " OR ct.tier_name LIKE '%{$keyword}%')";

            $people = $people->whereRaw($likeRaw);
            $people = $people->groupBy('email')->get();
        }

        return $this->apiSuccessResponse( compact('people'), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }
}
