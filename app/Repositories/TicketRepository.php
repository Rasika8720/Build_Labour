<?php

namespace App\Repositories;

use App\Models\Tickets\Ticket;
use App\Models\Users\UserTicket;
use Torann\LaravelRepository\Repositories\AbstractRepository;
use JWTAuth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TicketRepository extends AbstractRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = Ticket::class;

    public $ticketHandler = null;

    /**
     * Valid searchable columns
     *
     * @return array
     */
    protected $searchable = [
        'query' => [
            'ticket',
            'description'
        ],
    ];

    /**
     * Current User Tickets
     *
     * @return collection
     */
    public function getUserTickets() {

        $user = JWTAuth::toUser();
        $tickets = $user->tickets;

        return $tickets;
    }

    /**
     * Save User Tickets
     *
     * @return collection | boolean
     */
    public function saveUserTickets( Request $request ) {

        $user = JWTAuth::toUser();

        $this->ticketHandler = new Ticket();
        $tickets = $request->tickets ? $request->tickets : [];
        $saveTickets = [];

        if (isset($request->has_whitecard)) {

            $user->workerDetail->has_whitecard = $request->has_whitecard;
            $user->workerDetail->save();
        }

        UserTicket::where('user_id', $user->id)->delete();

        foreach ($tickets as $ticket) {

            if (! isset($ticket['ticket_id'])) {

                // check if the ticket already exists
                $checkTicket = Ticket::where('ticket', 'like', "%{$ticket['ticket']}")->first();

                if ($checkTicket) {

                    $this->ticketHandler->addError("Duplicate ticket found '{$ticket['ticket']}' ticket found in the system record. Matched to {$checkTicket->ticket} - {$checkTicket->description}. Please create or select another ticket.");
                    return false;
                }

                $ticket['ticket_id'] = Ticket::insertGetId([
                    'ticket' => $ticket['ticket'],
                    'description' => $ticket['description'],
                    'created_by' => $user->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            }

            $saveTickets[] = ['ticket_id' => $ticket['ticket_id'], 'user_id' => $user->id];
        }

        UserTicket::insert($saveTickets);

        return $user->tickets;

    }

    public function searchTickets(Request $r) {

        $tickets = Ticket::where('ticket', 'like', "%{$r->keyword}%")
                        ->orWhere('description', 'like', "%{$r->keyword}%")
                        ->get()
                        ->take(10);

        return $tickets;
    }

    public function getTicketHandle() {

        return $this->ticketHandler;
    }
}