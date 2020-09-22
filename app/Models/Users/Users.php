<?php

namespace App\Models\Users;

use App\Mails\ResendVerificationCodeEmail;
use App\Message;
use App\Models\BaseModel;
use App\Models\Companies\Company;
use App\User;
use App\Helpers\Utils;
use App\Models\Companies\JobApplicant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\Users\WorkExperience;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Users extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, SoftDeletes, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'id', 'email', 'first_name', 'last_name', 'password', 'training_provider',
        'date_of_birth', 'country', 'address', 'mobile_number', 'role_id', 'gender', 'marital_status', 'country_birth'
    ];

    protected $hidden = ['password', 'remember_token', 'updated_at', 'created_at', 'verification_code', 'firebase'];

    protected $appends = ['identifier', 'full_name', 'dob_formatted', 'device_token', 'user_type', 'displayed_company'];

    public $sql;
    public $bindings;

    public $isWeb = true;

    /* Optional information to be updated */
    public $isOptionalTransaction = false;

    public $isForIntroduction = false;

    public $isEmployerSignup = false;
    /**
     * @return array
     */

    private function rules()
    {

        if ($this->isOptionalTransaction) {

            return [
                'gender' => 'nullable|in:Male,male,Female,female,Other,other',
                'marital_status' => 'nullable',
                'date_of_birth' => 'nullable|date|before:-18 years',
            ];
        }

        if ($this->isForIntroduction) {

            return [
                'first_name'    => 'required',
                'last_name'     => 'required',
            ];
        }

        if ($this->isEmployerSignup) {

            return [
                'email'         => 'required|string|email|max:50|unique:users',
                'password'      => 'required|string|min:6|max:24|confirmed',
            ];
        }



        if ($this->id && !$this->isEmployerSignup) {

            // validation rules for updated users
            return [
                'first_name'    => 'required',
                'last_name'     => 'required',
                'mobile_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/'
            ];
        }


        return [
            'email'         => 'required|string|email|max:50|unique:users',
            'password'      => 'required|string|min:6|max:24|confirmed',
            'first_name'    => 'required',
            'last_name'     => 'required',
            'mobile_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/'
        ];
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class);
    }

    public function validationMessages()
    {
        return [
            'gender.in'  => 'Gender must be Male, Female or Other',
            'date_of_birth.before' => 'You must be at least 18 years old.'
        ];
    }

    /**
     * Validate a user request
     *
     * @param $request
     * @return bool
     */

    private function validate($request)
    {

        if ($this->id) {

            $rules = $this->rules();

            $validator = \Validator::make($request->all(), $rules, $this->validationMessages());

            // email must not be modified
//            if ($request->email && $request->email != $this->email) {
//
//                $validator->errors()->add('email', 'Not allowed to modify Email or Username');
//
//                $this->errors = $validator->errors()->all();
//                $this->errorsDetail = $validator->errors()->toArray();
//                return false;
//            }

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                $this->errorsDetail = $validator->errors()->toArray();
                return false;
            }
        } else {

            $validator = \Validator::make($request->all(), $this->rules(), $this->validationMessages());

            if ($validator->fails()) {
                $this->errors = $validator->errors()->all();
                $this->errorsDetail = $validator->errors()->toArray();
                return false;
            }
        }

        return true;
    }

    /**
     * Common saving method for the model
     *
     * @param Request $r
     * @return $this|bool
     *
     */

    public function store(Request $r)
    {

        if (!$this->validate($r) & $r->update_company_email !== true) {

            return false;
        }

        $this->fill($r->all());
        $pk = $this->primaryKey;

        if ($r->$pk) {

            $this->exists = true;
        } else {

            // do stuff for new users here
            // $this->is_verified = null;
            // $this->verification_code = $this->generateVerificationCode();

            $this->is_verified = Carbon::now();
            $this->verification_code = null;
        }

        if ($this->isEmployerSignup) {

            $this->role_id = 2;
        }

        try {

            $this->save();
        } catch (\Exception $e) {

            $this->errors[] = $e->getMessage();

            return false;
        }

        return true;
    }

    public function setFirstNameAttribute($name)
    {
        if (!empty($name)) {

            $this->attributes['first_name'] = ucfirst($name);
        }
    }

    public function setLastNameAttribute($name)
    {
        if (!empty($name)) {

            $this->attributes['last_name'] = ucfirst($name);
        }
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {

            $this->attributes['password'] = \Hash::make($password);
        }
    }

    public function setMobileNumberAttribute($mobile)
    {

        if (!empty($mobile)) {

            $mobile = str_replace('+61', '', trim($mobile));

            $this->attributes['mobile_number'] = '+61' . $mobile;
        }
    }

    public function getJwtToken()
    {
        // the JWTSubject class is App/User and not $this
        $jwt_subject = (new User)->find($this->id);

        if (!$jwt_subject) {
            return false;
        }

        return JWTAuth::fromUser($jwt_subject);
    }

    /**
     * @param $email
     * @return mixed
     */
    public function emailExists($email)
    {
        $user =  static::where('email', $email)
            ->first();

        return $user;
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {

        if (!empty($this->first_name) && !empty($this->last_name)) {

            return $this->first_name . ' ' . $this->last_name;
        }

        return '';
    }

    public function getDobFormattedAttribute()
    {
        return \Carbon\Carbon::parse($this->date_of_birth)->format('d F Y');
    }

    public function getDeviceTokenAttribute()
    {

        return $this->firebase ? $this->firebase->device_token : '';
    }

    public function getIdentifierAttribute()
    {

        return (string) $this->id;
    }

    public function getUserTypeAttribute()
    {

        switch ($this->role_id) {

            case 1: $userType = 'worker'; break;
            case 2: $userType = 'company'; break;
            case 3: $userType = 'contractor'; break;
            default: $userType = 'trainer'; break;
        }

        return $userType;
    }

    public function resendVerificationCode()
    {
        $this->verification_code = $this->generateVerificationCode();

        try {
            $this->save();
            // \Mail::to( $this->email )->send( new ResendVerificationCodeEmail( $this ) );
        } catch (\Exception  $e) {
            $this->addError($e->getMessage());
            return false;
        }

        return $this;
    }

    public function verify($verification_code)
    {
        if (!$this->id) {

            $message = 'Unknown user id';
            $this->addError($message);
            $this->errorsDetail = array('verification' => [$message]);
            return false;
        }

        if (!$verification_code) {

            $message = 'Verification code must not be empty';
            $this->addError($message);
            $this->errorsDetail = array('verification' => [$message]);
            return false;
        }

        if ($this->is_verified) {

            $message = 'User was already verified';
            $this->addError($message);
            $this->errorsDetail = array('verification' => [$message]);
            return false;
        }

        if ($verification_code != $this->verification_code) {

            $message = 'Incorrect verification code';
            $this->addError($message);
            $this->errorsDetail = array('verification' => [$message]);
            return false;
        }

        $this->verification_code = null;
        $this->is_verified = date('Y-m-d H:i:s');
        $this->save();

        return $this;
    }

    public function getWelcomeName()
    {

        $headerName = "";

        if ($this->role_id == 1) {

            if ($this->first_name) {

                $headerName = $this->first_name;
            }
        } else {

            $user = User::find($this->id);

            if ($user && $user->Company) {

                $headerName = $user->Company->name;
            }
        }

        if (empty($headerName)) {

            $headerName = $this->email;
        }

        return $headerName;
    }

    /**
     * @param Request $r
     * @return $this|bool
     */
    public function uploadProfilePhoto(Request $request)
    {

        try {

            if (!$this->id) {
                $this->addError('Unknown user');
                return false;
            }

            $validator = \Validator::make($request->all(), ['photo' => 'required|image64:jpeg,jpg,png']);

            if ($validator->fails()) {
                $this->addError($validator->errors()->first());
                return false;
            }

            $photo = $request->get('photo');

            // $ext = $request->photo->getClientOriginalExtension();
            $ext = '.png';

            // rename photo files if you like
            $new_filename   = 'p_' . str_random(12) . '.' . $ext;
            $destination    = $this->generateUserImagePath($request);
            $url = url('/storage' . $destination . $new_filename);

            // Defaults to a storage path but you may save it to a public for non sensitive files
            // Do not forget to call php artisan storage:link
            $dir_path  = storage_path() . '/app/public' . $destination;

            if (!is_dir($dir_path)) {
                mkdir($dir_path, 755, true);
            }

            $file_path = $dir_path . $new_filename;

            // default compression to 320x320
            // you need to improve this to handle images that has large difference in aspect ratio

            Image::make(file_get_contents($photo))
                ->resize(320, 320)
                ->save($file_path);

            // file path can be handy on some cases
            // $file_path  = $dir_path.$new_filename;

            // you may generate thumbnails if needed
            // Utils::generateThumbnail( $file_path, [] );

            $this->profile_photo_url = $url;

            $this->save();
        } catch (\Exception $e) {

            $this->addError($e->getMessage());
            $this->errorsDetail = ['image' => ['Something wrong while processing the image']];
            return false;
        }

        return $this;
    }

    /**
     * User image path is designed so that it would be easy to have a daily backup
     * without downloading everything
     *
     * @return string
     *
     */
    private function generateUserImagePath()
    {
        $date = $this->created_at;
        $m  =  date('md', strtotime($date));
        $y  =  date('Y', strtotime($date));

        return '/images/' . $y . '/' . $m . '/' . Utils::convertInt($this->id) . '/';
    }

    private function generateVerificationCode()
    {

        if ($this->isWeb) {

            $code = strtoupper(str_random(50));
        } else {

            $code = strtoupper(str_random(6));
        }

        $has_code = static::where('verification_code', $code)->count();

        if ($has_code) {

            return $this->generateVerificationCode();
        }

        return $code;
    }

    public function JobApplicants()
    {
        return $this->belongsToMany(JobApplicant::class, 'job_post_applicants', 'id', 'user_id');
    }



    public function getDisplayedCompanyAttribute()
    {
        $experience = WorkExperience::where('user_id',$this->id)->with('Company')
        ->orderBy('isCurrent', 'desc')
        ->orderBy('end_year', 'desc')
        ->orderBy('end_month', 'desc')
        ->orderBy('start_year', 'desc')
        ->orderBy('start_month', 'desc')->first();

        $company = NULL;

        if ($experience) {

            if ($experience->company_id) {

                if( isset($experience->company->name) )
                    $company= $experience->company->name;

            } else {

                $company= $experience->company_name;
            }
        }

        return $company;
    }

    public static function findUsersFromChatRequestArray($chatRequests, $column)
    {
        $status_items = self::userInfoStatusJoiner($chatRequests);
        $user_array = [];

        if(isset($status_items['Accepted']))
            $user_array['Accepted'] = self::returnUserInfoFull($status_items['Accepted'], $column);

        return $user_array;
    }

    public static function arrayValuesToString($array, $column)
    {
        $string = '';

        foreach($array as $key => $item) {
                $string .= $item->$column . ',';
        }

        return substr($string, 0, -1);
    }

    private static function userInfoStatusJoiner($requestedUserInfo)
    {
        $request = null;
        foreach($requestedUserInfo as $key => $requested_user)
        {
            $request[$requestedUserInfo[$key]['status']][] = $requested_user;
        }

        return $request;
    }

    private static function userLinkBuilder($users)
    {
        foreach($users as $key => $user) {
            $users[$key]->profile_link = ($user->role === 1) ? '/user/profile/'.$user->id : '/company/profile/'.$user->company_id;
        }

        return $users;
    }

    private static function returnUserInfoFull($array, $column)
    {

        $where_string = self::arrayValuesToString($array, $column);
        $where_array = explode(',', $where_string);
        $select_array = [
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.profile_photo_url',
            'users.role_id as role',
            'companies.id as company_id',
            'companies.name as company_name',
            'companies.photo_url as company_image'
        ];
        $requested_users = DB::table('users')
            ->select($select_array)
            ->leftJoin('companies', 'users.id', '=', 'companies.created_by')
            ->whereIn('users.id', $where_array)
            ->get();
        $user_links = self::userLinkBuilder($requested_users);
        $user_unread = self::userUnreadCheck($user_links);

        return $user_unread;
    }

    private static function userUnreadCheck($users)
    {
        foreach($users as $key => $user) {
            $current_user = \JWTAuth::toUser();
            $users[$key]->chat_history_count = DB::table('chat_history as ch')
                ->leftJoin('chat_channels as cc', function($join) use($user, $current_user){
                    $join->on('cc.connection_id', '=', \DB::raw($user->id . $current_user->id))
                        ->orOn('cc.connection_id', '=', \DB::raw($current_user->id . $user->id));
                })
                ->where('ch.seen_by', '!=', 'true')
                ->where('ch.sent_by', '=', $user->id)
                ->count();
        }

        return $users;
    }
}
