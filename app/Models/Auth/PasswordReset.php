<?php

namespace App\Models\Auth;

use App\Mails\ResetPasswordEmail;
use App\Models\BaseModel;
use App\Models\Users\Users;
use App\Validators\ResetPasswordValidator;
use DB;
use Illuminate\Http\Request;

class PasswordReset extends BaseModel
{

  //  public $incrementing = false;
  //  protected $primaryKey = null;

    /**
     *  The attributes that are mass assignable.
     *
     *  @var array
     */
    protected $fillable = [ 'email', 'token' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
    ];

    const UPDATED_AT = null;

    private function rules()
    {
        return [
            'email'     => 'required|string|email|exists:users',
            'password'  => 'required|string|min:8|max:50|confirmed'
        ];
    }

    public function validationMessages()
    {
        return [
            'email.required'     => 'Please fill in your e-mail address.',
            'email.email'        => 'Email address format is invalid.',
            'email.exists'       => 'This email address was not registered in our system.',

            'password.required'  => 'Please fill in your password.',
            'password.max'       => 'Password may not be greater than 50 characters',
            'password.min'       => 'Password must be at least 8 characters',
            'password.confirmed' => 'Passwords do not match',
        ];
    }

    public function store( Request $r )
    {

        $validator = \Validator::make( $r->all() , [
            'email' => 'required|string|email|exists:users',
        ]);

        if( $validator->fails() ){

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();
            return false;
        }

        $this->fill( $r->all() );

        try{
            $this->save();
        }catch( \Exception $e ) {
            $this->errors = $e->getMessage();
            return false;
        }

        return $this;
    }

    /**
     *
     * @param $request
     * @return array
     *
     */
    public function sendResetToken( Request $request )
    {

        try {

            PasswordReset::where( 'email',  $request->email )->delete();

            // Tokens are 5 capitalized random characters
            $token  = strtoupper( str_random( 5 ) );
            $request->merge( [ 'token' => \Hash::make( $token ) ] );

            if( ! $this->store( $request ) ){
                return false;
            }

            $user   = Users::where( 'email' , $request->email )->first();

            \Mail::to( $request->email )->send( new ResetPasswordEmail( $user, $token ));


            return $this;

        } catch( \Exception $e ){

            $this->errors[]     =   $e->getMessage();
            return false;
        }

    }

    /**
     *
     * @param $request
     * @return array
     *
     */
    public function sendResetTokenWeb( Request $request )
    {

        try {

            PasswordReset::where( 'email',  $request->email )->delete();

            // Tokens are 50 capitalized random characters
            $token  = strtoupper( str_random( 50 ) );
            $request->merge( [ 'token' => \Hash::make( $token ) ] );

            if( ! $this->store( $request ) ){

                $this->errorsDetail = array('email' => ['Cannot verify email in the system.']);
                
                return false;
            }

            $user = Users::where( 'email' , $request->email )->first();

            \Mail::to( $request->email )->send( new ResetPasswordEmail( $user, $token ));

            return $this;

        } catch( \Exception $e ){

            $this->errors[] = $e->getMessage();

            return false;
        }

    }

    /**
     * Reset the password using token and password
     *
     * @param $request
     * @return bool
     */
    public function resetPassword($request)
    {
        $validator = \Validator::make( $request->all(), $this->rules(), $this->validationMessages() );

        if ($validator->fails() ){
            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();
            return false;
        }

        $password_reset = PasswordReset::where( 'email', $request->email )->first();

        if( ! $password_reset ){
            $message = 'Email '.$request->email.' did not request for a password reset';
            $this->addError( $message );
            $this->errorsDetail = array('email' => [$message]);
            return false;
        }

        // check for expiration... if created_at is more than 1 hr then invalidate the request
        // remove or modify this code if token should not expire or expires not a hr

        if( time() > ( strtotime( $password_reset->created_at ) + 3600 ) ){

            $message = 'Token expired. You have to reset password within an hour after request';
            $this->addError( $message );
            $this->errorsDetail = array('token' => [$message]);
            return false;
        }

        if( \Hash::check( $request->token , $password_reset->token ) ){
            
            $user = Users::where( 'email',  $request->email )->first();
            $user->password = trim($request->password);

            try{
                $user->save();
                $password_reset->delete();
            }catch( \Exception $e ){
                $this->addError( ' Exception found : '.$e->getMessage() );
                return false;
            }

            return true;
        }


        $message = 'Invalid Token';
        $this->addError( $message );
        $this->errorsDetail = array('token' => [$message]);
        return false;

    }


}
