<?php

namespace App\Models\Companies;

use App\Helpers\Utils;
use App\Models\BaseModel;
use App\Models\Options\BusinessType;
use App\Models\Options\MainFunction;
use App\Models\Options\SecondaryFunction;
use App\Models\Options\Tier;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends BaseModel
{
    use SoftDeletes;

    private $userId = null;

    protected $table = 'companies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'business_type_id',
        'tier_id',
        'main_company_id',
        'main_function_answer',
        'photo_url',
        'address',
        'phone',
        'introduction',
        'website',
        'operate_outside_states',
        'states',
        'created_by',
        'locations_json',
    ];

    protected $appends = [ 'locations', 'no_of_workers' ];

    protected $hidden = [ 'locations_json' ];
    /**
     * @return array
     */
    private function rules()
    {
        return [
            'name'          => 'required',
            'address'       => 'nullable',
            'phone'         => 'nullable',
            'sector'        => 'nullable',
            'introduction'  => 'nullable|maxWords:50',
            'operate_outside_states' => 'nullable|boolean',
            'website'       => 'nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
        ];
    }


    public function validationMessages()
    {

        return [
            'name.required'  => 'Company name is required.',
            'name.min'  => 'Company name must be at least 5 characters',
            'introduction.max_words' => 'The introduction field must have at least 10 words and not exceeded to 50 words.',
        ];
    }

    public function transformRequest($data) {

        $transformed = [];

        if (isset($data['company_name'])) {

            $transformed['name'] = $data['company_name'];
        }

        if (isset($data['company_business_type_id'])) {

            $transformed['business_type_id'] = $data['company_business_type_id'];
        }

        if (isset($data['company_tier_id'])) {

            $transformed['tier_id'] = $data['company_tier_id'];
        }

        if (isset($data['company_tier_id'])) {

            $transformed['tier_id'] = $data['company_tier_id'];
        }

        if (isset($data['company_main_company_id'])) {

            $transformed['main_company_id'] = $data['company_main_company_id'];
        }

        if (isset($data['company_main_function_answer'])) {

            $transformed['main_function_answer'] = $data['company_main_function_answer'];
        }

        if (isset($data['company_address'])) {

            $transformed['address'] = $data['company_address'];
        }

        if (isset($data['company_contact_number'])) {

            $transformed['phone'] = $data['company_contact_number'];
        }

        if (isset($data['company_operate_outside_states'])) {

            $transformed['operate_outside_states'] = $data['company_operate_outside_states'];
        }

        if (isset($data['company_website'])) {

            $transformed['website'] = $data['company_website'];
        }

        if (isset($data['company_states'])) {

            $transformed['states'] = $data['company_states'];
        }

        return array_merge($transformed, $data);
    }

    /**
     * Validate a user request
     *
     * @param $request
     * @return bool
     */

    private function validate( $data ){

        $validator = \Validator::make($data, $this->rules(), $this->validationMessages());

        if ( $validator->fails() ) {

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        return true;
    }

    public function CreatedBy() {

        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function Workers() {

        return $this->belongsToMany(User::class, 'work_experience', 'company_id', 'user_id')->groupBy('email');
    }

    public function getWorkers() {

        $workers = $this->Workers;

        return $workers;
    }

    public function Jobs() {

        return $this->hasMany( Job::class, 'company_id', 'id');
    }

    public function BusinessType() {

        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }

    public function Tier () {

        return $this->belongsTo(Tier::class, 'tier_id');
    }

    public function MainFunction() {

        return $this->belongsTo(MainFunction::class, 'main_company_id');
    }

    public function Specialization() {

        return $this->belongsToMany(SecondaryFunction::class, 'company_specialization', 'company_id', 'secondary_id');
    }

    public function Posts() {

        return $this->hasMany(CompanyPost::class, 'company_id', 'id');
    }

    public function setUserId($userId) {

        $this->userId = $userId;
    }

    public function setStatesAttribute($states) {

        if (!empty($states) && is_array($states)) {

            $this->attributes['states'] = implode(",", $states);

        } else {

            $this->attributes['states'] = NULL;
        }
    }

    public function store(Request $r) {

        $data = $this->transformRequest($r->all());
        

        if( ! $this->validate( $data )) {

            return false;
        }

        $this->fill( $data );

        $pk = $this->primaryKey;

        if ($r->$pk) {

            $this->exists = true;
        }

        if ($this->userId) {

            $this->created_by = $this->userId;
        }

        try{

            $this->save();

        } catch (\Exception $e){

            $this->errors[] = $e->getMessage();

            return false;
        }

        return $this;
    }

    /**
     * @param Request $r
     * @return $this|bool
     */
    public function uploadProfilePhoto( Request $request )
    {

        try {

            if( ! $this->id ){
                $this->addError(  'Unknown user' ) ;
                return false;
            }

            if ($request->company_photo) {

                $rule = [ 'company_photo' => 'required|image64:jpeg,jpg,png' ];

            } else {

                $rule = [ 'photo' => 'required|image64:jpeg,jpg,png' ];
            }

            $validator = \Validator::make( $request->all(),  $rule);

            if( $validator->fails() ){
                $this->addError(  $validator->errors()->first() ) ;
                $this->errorsDetail = $validator->errors()->toArray();
                return false;
            }

            if ($request->company_photo) {

                $photo = $request->get('company_photo');

            } else {

                $photo = $request->get('photo');
            }

            // $ext = $request->photo->getClientOriginalExtension();
            $ext = '.png';

            // rename photo files if you like
            $new_filename   = 'p_'.str_random( 12 ).'.'.$ext;
            $destination    = $this->generateUserImagePath( $request );
            $url = url( '/storage'.$destination.$new_filename );

            // Defaults to a storage path but you may save it to a public for non sensitive files
            // Do not forget to call php artisan storage:link
            $dir_path  = storage_path() . '/app/public'.$destination;

            if( ! is_dir( $dir_path )){
                mkdir( $dir_path , 755 , true );
            }

            $file_path = $dir_path.$new_filename;

            // default compression to 320x320
            // you need to improve this to handle images that has large difference in aspect ratio

            Image::make(  file_get_contents($photo) )
                ->resize( 320, 320 )
                ->save( $file_path );

            // file path can be handy on some cases
            // $file_path  = $dir_path.$new_filename;

            // you may generate thumbnails if needed
            // Utils::generateThumbnail( $file_path, [] );

            $this->photo_url = $url;

            $this->save();


        } catch (\Exception $e) {

            $this->addError(  $e->getMessage() ) ;
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
    private function generateUserImagePath( )
    {
        $date = $this->created_at;
        $m  =  date( 'md' , strtotime( $date ));
        $y  =  date( 'Y' , strtotime( $date ));

        return '/images/companies/'.$y.'/'.$m.'/'.Utils::convertInt( $this->id ).'/';
    }

    public function getLocationsAttribute() {

        if (Utils::isJson($this->locations_json)) {

            return json_decode($this->locations_json);
        }

        return [];
    }

    public function getNoOfWorkersAttribute() {

        return $this->Workers->count();
    }
}
