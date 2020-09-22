<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use App\Models\Tickets\Ticket;
use App\User;

class UserTicket extends BaseModel
{

    protected $table = 'user_tickets';
    protected $primaryKey = 'id';

    protected $fillable = [ 'user_id', 'ticket_id' ];

    const CREATED_AT = null;
    const UPDATED_AT = null;


    public function User() {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Ticket() {

        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

}
