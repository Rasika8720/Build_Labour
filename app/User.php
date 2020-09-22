<?php

/***
 * User used for authentications
 */

namespace App;

use App\Models\Companies\Company;
use App\Models\Tickets\Ticket;
use App\Models\Users\Bookmark;
use App\Models\Users\Education;
use App\Models\Users\FirebaseUsers;
use App\Models\Users\Users;
use App\Models\Users\UserSkill;
use App\Models\Users\UserTicket;
use App\Models\Users\WorkerDetail;
use App\Models\Users\WorkExperience;
use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends Users implements JWTSubject
{
    use Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Return a Object relates to Role Class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Role() {

        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Return a collection relates to Work Experience class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Experiences() {

        return $this->hasMany(WorkExperience::class, 'user_id', 'id')->with('Company')
                    ->orderBy('isCurrent', 'desc')
                    ->orderBy('end_year', 'desc')
                    ->orderBy('end_month', 'desc')
                    ->orderBy('start_year', 'desc')
                    ->orderBy('start_month', 'desc');
    }

    /**
     * Return a collection relates to Worker Skills
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Skills() {

        return $this->hasMany(UserSkill::class, 'user_id', 'id');
    }

    /**
     * Return a collection relates to Worker Educations
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Educations() {

        return $this->hasMany( Education::class, 'user_id', 'id');
    }

    /**
     * Return a collection relates to Bookmarks
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Bookmarks() {

        return $this->hasMany( Bookmark::class, 'user_id', 'id');
    }

    /**
     * Return a collection relates to Worker Detail, e.g Worker Extra information
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function WorkerDetail() {

        return $this->belongsTo(WorkerDetail::class, 'id', 'user_id');
    }


    /**
     * Return a collection relates to Tickets
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Tickets() {

        return $this->belongsToMany(Ticket::class, 'user_tickets', 'user_id', 'ticket_id');
    }

    /**
     * Return a collection relates to Company, e.g User as a company
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Company() {

        return $this->belongsTo(Company::class, 'id', 'created_by');
    }

    public function Firebase() {

        return $this->belongsTo(FirebaseUsers::class, 'id', 'user_id');
    }

    /**
     * Check if user is admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return DB::table('administrators')->where('user_id', '=', $this->id)->first() !== null;
    }
}
