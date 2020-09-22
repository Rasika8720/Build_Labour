<?php

namespace App\Models\Companies;

use App\Models\BaseModel;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Job extends BaseModel
{

    use SoftDeletes;
    protected $table = 'job_posts';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'description',
        'about',
        'exp_level',
        'contract_type',
        'salary',
        'salary_type',
        'project_size',
        'reports_to_json',
        'location',
        'job_role_id',
        'company_id',
        'created_by',
        'is_template',
        'template_name',
        'template_id'
    ];

    protected $hidden = [
     //   'reports_to_json'
    ];

    protected $appends = [
        'reports_to',
        'reports_to_str',
        'job_role_name',
        'job_tickets',
        'job_qualifications',
        'created_at_formatted',
        'stat_favourite',
        'stat_invited',
        'stat_new',
        'stat_not_suitable',
        'stat_total',
        'stat_viewed',
        'min_exp_month',
        'min_exp_year'
    ];

    /**
     * @return array
     */
    private function rules()
    {
        return [
            'title'         => 'required',
            'description'   => 'required',
            'contract_type' => 'required',
            'salary'        => 'nullable|regex:/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/', /* monetary validation */
            'project_size'  => 'nullable|regex:/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/', /* monetary validation */
            'location'      => 'required',
            'template_name' => 'required'
        ];
    }

    /**
     * Validate a user request
     *
     * @param $request
     * @return bool
     */

    private function validate( $data ){

        $rules = $this->rules();

        if (isset($data['is_template']) && !$data['is_template']) {

            unset($rules['template_name']);
        }

        $validator = \Validator::make($data, $rules);

        if ( $validator->fails() ) {

            $this->errors = $validator->errors()->all();
            $this->errorsDetail = $validator->errors()->toArray();

            return false;
        }

        return true;
    }

    public function Company() {

        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function PostedBy() {

        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function Requirements() {

        return $this->hasMany( JobRequirement::class, 'job_id', 'id');
    }

    public function JobRole() {

        return $this->belongsTo(JobRole::class, 'job_role_id', 'id');
    }

    public function setSalaryAttribute($value) {

        if ( ! empty( $value ) ) {

            $format = preg_replace("/[^0-9.]/","",$value);
            $format = number_format($format,0);
            $this->attributes['salary'] = $format;

        } else {
            $this->attributes['salary'] = null;
        }
    }

    public function setProjectSizeAttribute($value) {

        if ( ! empty( $value ) ) {

            $format = preg_replace("/[^0-9.]/","",$value);
            $format = number_format($format,0);
            $this->attributes['project_size'] = $format;

        } else {
            $this->attributes['project_size'] = null;
        }
    }

    public function getReportsToAttribute() {

        return $this->reports_to_json ? json_decode($this->reports_to_json) : [];
    }

    public function getReportsToStrAttribute() {

        $result = preg_replace("/[[\\]\"]/", " ", $this->reports_to_json);

        return str_replace(" ,", ", ", $result);
    }

    public function getJobRoleNameAttribute() {

        if ($this->JobRole) {

            return $this->JobRole->job_role_name;
        }

        return $this->title;

    }

    public function Responsibilities() {

        return $this->hasMany( JobResponsibility::class, 'job_id', 'id');
    }

    public function getCreatedAtFormattedAttribute() {

        return \Carbon\Carbon::parse($this->created_at)->format('d F Y');
    }

    public function store($data) {

        if( ! $this->validate( $data )) {

            return false;
        }

        $this->fill( $data );

        $pk = $this->primaryKey;

        if (isset($data[$pk])) {

            $this->exists = true;
        }

        if (isset($data['reports_to'])) {

            if (!empty($data['reports_to']) && is_array($data['reports_to'])) {

                $this->reports_to_json = json_encode(array_filter($data['reports_to']));

            } else {

                $this->reports_to_json = NULL;
            }
        }

        if (isset($data['job_role_id'])) {

            $this->title = null;
        }

        try{

            $this->save();

        } catch (\Exception $e){

            $this->errors[] = $e->getMessage();

            return false;
        }

        return $this;
    }

    public function JobApplicants()
    {

        return $this->belongsToMany(User::class, 'job_post_applicants', 'user_id', 'job_id')
            ->with([
                'WorkerDetail',
                'WorkerDetail.Areas',
                'WorkerDetail.Sectors',
                'WorkerDetail.Tiers',
                'Educations',
                'Tickets',
                'Skills'
            ]);
    }

    public function JobStats(){
        return $this->hasMany(JobStat::class, 'job_id', 'id');
    }

    public function getStatFavouriteAttribute(){
        return $this->JobStats->where('category','favourite')->count();
    }

    public function getStatInvitedAttribute(){
        return $this->JobStats->where('category','invited')->count();
    }

    public function getStatNewAttribute(){

        $noOfApplicants = JobApplicant::where('job_id',  $this->id)->count();
        $noOfCompanyViewedApplicants = JobStat::where(['job_id' => $this->id, 'category' => 'company_viewed_profile'])->count();
        $noOfNew = $noOfApplicants > $noOfCompanyViewedApplicants ? $noOfApplicants - $noOfCompanyViewedApplicants : 0;

        return $noOfNew;
    }

    public function getStatNotSuitableAttribute(){
        return $this->JobStats->where('category','not_suitable')->count();
    }

    public function getStatTotalAttribute(){
      //  $total = $this->stat_invited + $this->stat_not_suitable + $this->stat_favourite;

        $total = JobApplicant::where('job_id',  $this->id)->count();
        return $total;
    }

    public function getStatViewedAttribute(){
        return $this->JobStats->where('category','favourite')->count();
    }

    public function getMinExpMonthAttribute() {

        return $this->getParamsValue('min_exp_month');
    }

    public function getMinExpYearAttribute() {

        return $this->getParamsValue('min_exp_year');
    }

    /**
     * Save params as JSON
     * @param string field
     * @param string $data
     * @return boolean
     */
    public function saveParams($field, $data) {

        $params = !$this->params ? [] : json_decode($this->params, true);

        if (is_array($params)) {

            $params[$field] = $data;
            $this->params = json_encode($params);
            $this->update();

            return true;
        }

        return false;
    }

    /**
     * Get params as array
     * @param string field
     * @return string|array
     */
    public function getParamsValue( $field = NULL ) {

        $params = !$this->params ? [] : json_decode($this->params, true);

        if (is_array($params) && $field && isset($params[$field])) {

            $value = $params[$field];

            return $value ? $value : NULL;
        }

        return NULL;
    }

    public function getJobTicketsAttribute(){
        $requirements = $this->Requirements;
        if($requirements){
            $tickets = $requirements->where('title','Tickets')->first();
            if($tickets){
                $ticket_items = json_decode( json_encode( $tickets->items ) );
                if($ticket_items){
                    return $ticket_items[0]->ticket;
                }else{
                    return '';
                }

            }
            return '';
        }
    }

    public function getJobQualificationsAttribute(){
        $requirements = $this->Requirements;
        if($requirements){
            $tickets = $requirements->where('title','Qualifications')->first();
            if($tickets){
                $ticket_items = json_decode( json_encode( $tickets->items ) );
                if($ticket_items){
                    return $ticket_items[0]->course_type;
                }else{
                    return '';
                }

            }
            return '';
        }
    }
}
