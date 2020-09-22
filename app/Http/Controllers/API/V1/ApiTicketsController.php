<?php

namespace App\Http\Controllers\API\V1;

use App\Repositories\TicketRepository;
use Illuminate\Http\Request;
use JWTAuth;

class ApiTicketsController extends ApiBaseController
{
    /**
     * @var TicketRepository
     */
    protected $ticketRepo;

    public function __construct(TicketRepository $repository) {

        $this->ticketRepo = $repository;
    }

    /**
     * @OA\Get(
     *      path="/tickets/search",
     *      tags={"Ticket"},
     *      summary="Search Ticket",
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

            $tickets = $this->ticketRepo->searchTickets($request);

        } catch(\Exception $e) {

            return $this->apiErrorResponse(false, $e->getMessage(), self::INTERNAL_SERVER_ERROR, 'internalServerError');
        }

        return $this->apiSuccessResponse( compact( 'tickets' ), true, '', self::HTTP_STATUS_REQUEST_OK);
    }
}
