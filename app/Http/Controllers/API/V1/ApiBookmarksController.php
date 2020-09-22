<?php

namespace App\Http\Controllers\API\V1;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use JWTAuth;

class ApiBookmarksController extends ApiBaseController
{

    /**
     * @var UserRepository
     */
    protected $userRepo;

    public function __construct(UserRepository $repository) {

        $this->userRepo = $repository;
    }

    /**
     * @OA\Post(
     *      path="/bookmarks",
     *      tags={"Bookmarks"},
     *      summary="Bookmark a post or post with job",
     *      security={{"BearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="job_id",
     *                      description="<b>Required</b> Job ID",
     *                      type="integer",
     *                      example=1
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
    public function postBookmark( Request $request )
    {

        try {

            if (!$bookmark = $this->userRepo->doBookMark($request)) {

                return $this->apiErrorResponse(
                    false,
                    $this->userRepo->bookmark->getErrors( true ),
                    self::HTTP_STATUS_INVALID_INPUT,
                    'invalidInput',
                    $this->userRepo->bookmark->getErrorsDetail()
                );
            }

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        $message = "Successfully bookmarked post!";

        if (isset($bookmark['unbookmarked'])) {

            $message = "Successfully unbookmark post!";
        }

        return $this->apiSuccessResponse( compact( 'bookmark' ), true, $message, self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/bookmarks/posts/jobs",
     *      tags={"Bookmarks"},
     *      summary="Get auth bookmarks for post jobs",
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
    public function getPostJobs( Request $request )
    {

        try {

            $bookmarks = $this->userRepo->getPostJobsBookmarks($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'bookmarks' ), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/bookmarks/posts/ids",
     *      tags={"Bookmarks"},
     *      summary="Get auth posts bookmarks by set of id",
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
    public function getPostsById()
    {

        try {

            $bookmarks = $this->userRepo->getPostBookmarksById();

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'bookmarks' ), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }

    /**
     * @OA\Get(
     *      path="/bookmarks/posts/jobs/ids",
     *      tags={"Bookmarks"},
     *      summary="Get auth job bookmarks by set of id",
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
    public function getJobsById()
    {

        try {

            $bookmarks = $this->userRepo->getJobBookmarksById();

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'bookmarks' ), true, 'Success', self::HTTP_STATUS_REQUEST_OK);
    }
}
