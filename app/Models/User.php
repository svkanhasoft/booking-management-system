<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Laravel\Cashier\Billable;
use DB;
use App\Models\Organization;
use App\Models\OrganizationUserDetail;
use App\Models\Role;
use Config;
use Illuminate\Http\Request;
use Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'email', 'password', 'first_name', 'last_name', 'email_verified_at', 'password', 'remember_token',
        'created_at', 'updated_at', 'role', 'status', 'profile_pic', 'password_change', 'password_change', 'last_login_date', 'is_deleted',
        'parent_id', 'postcode', 'city', 'address_line_2', 'address_line_1', 'contact_number','device_id','platform'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'deleted_at', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_password_change' => 'boolean',
    ];

    public function getOrganizationDetails($userId = null)
    {
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.role',
            'users.email',
            'users.contact_number',
            'oud.role_id',
            'roles.role_name',
            'oud.designation_id',
            'designations.designation_name',
            'users.parent_id',
            'parentUser.first_name as org_first_name',
            'parentUser.last_name as org_last_name',
            'parentUser.last_name as org_last_name',
            'parentUser.email  as org_email',
            'organizations.organization_name',
            'organizations.contact_person_name',
            'users.contact_number',
            'users.address_line_1',
            'users.address_line_2',
        );
        $query->Join('organization_user_details as oud',  'oud.user_id', '=', 'users.id');
        $query->Join('roles',  'roles.id', '=', 'oud.role_id');
        $query->Join('designations',  'designations.id', '=', 'oud.designation_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        //$query->where('users.parent_id', $userId);
        $query->where('users.id', $userId);
        $userDetais = $query->get();
        return $userDetais;
    }

    public function fetchStaflist(Request $request, $userId = null)
    {
        $keyword = $request->get('search');
        //print_r($keyword);exit();
        $perPage = Config::get('constants.pagination.perPage');
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.role',
            'users.email',
            'users.contact_number',
            'oud.role_id',
            'roles.role_name',
            'oud.designation_id',
            'users.parent_id',
            'designations.designation_name',
            'parentUser.first_name as org_first_name',
            'parentUser.last_name as org_last_name',
            'parentUser.last_name as org_last_name',
            'parentUser.email  as org_email',
            'organizations.organization_name',
            'organizations.contact_person_name',
            'users.contact_number',
            'users.address_line_1',
            'users.address_line_2',
        );
        $query->Join('organization_user_details as oud',  'oud.user_id', '=', 'users.id');
        $query->leftJoin('roles',  'roles.id', '=', 'oud.role_id');
        $query->Join('designations',  'designations.id', '=', 'oud.designation_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        if (!empty($keyword)) {
            $query->where(function ($query2) use ($keyword) {
                $query2->where('users.email', 'like',  "%$keyword%")
                    ->orWhere('users.first_name', 'like',  "%$keyword%")
                    ->orWhere('users.last_name',  'LIKE', "%$keyword%")
                    ->orWhere('users.contact_number',  'LIKE', "%$keyword%");
            });
        }
        $query->where('users.parent_id', $userId);
        $query->where('users.role', "STAFF");
        $res =  $query->latest('users.created_at')->paginate($perPage);
        return $res;
    }

    public function getStafById($userId = null)
    {
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.email_verified_at',
            'users.role',
            'users.status',
            'users.password_change',
            'users.postcode',
            'users.city',
            'users.address_line_2',
            'users.address_line_1',
            'users.contact_number',
            'users.last_login_date',
            'users.parent_id',
            'users.contact_number',
            'oud.role_id',
            'oud.designation_id',
            'designations.designation_name',
            'roles.role_name',
        );
        $query->Join('organization_user_details as oud',  'oud.user_id', '=', 'users.id');
        $query->leftJoin('roles',  'roles.id', '=', 'oud.role_id');
        $query->leftJoin('designations',  'designations.id', '=', 'oud.designation_id');
        $query->where('users.id', $userId);
        $query->where('users.role', "STAFF");
        $userDetais = $query->first();
        return $userDetais;
    }

    public function getSigneeDetails($userId = null, $orgId = null)
    {
        //print_r($orgId);exit();
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.contact_number',
            'oud.designation_id',
            'users.parent_id',
            'parentUser.first_name as org_first_name',
            'parentUser.last_name as org_last_name',
            'parentUser.last_name as org_last_name',
            'parentUser.email  as org_email',
            'organizations.organization_name',
            'organizations.contact_person_name',
            'users.contact_number',
            'users.address_line_1',
            'users.address_line_2',
            'signees_detail.candidate_id',
            'users.address_line_1',
            'users.address_line_2',
            'users.password_change as is_password_change',
            'users.city',
            'users.postcode',

            'signees_detail.nationality',
            'signees_detail.nmc_dmc_pin',

            'signees_detail.date_of_birth',
            'signee_organization.status'
        );
        $query->leftJoin('signees_detail',  'signees_detail.user_id', '=', 'users.id');
        $query->leftJoin('signee_organization',  'signee_organization.user_id', '=', 'users.id');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        $query->leftJoin('organization_user_details as oud',  'oud.user_id', '=', 'users.parent_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->where('signee_organization.organization_id', $orgId);
        $query->where('users.id', $userId);
        $userDetais = $query->first();
        //print_r($userDetais);exit();
        return $userDetais;
    }

    public function SigneesDetail()
    {
        return $this->hasOne(SigneesDetail::class);
    }
    public function stafdetails()
    {
        return  $this->hasOneThrough(OrganizationUserDetail::class, Designation::class,'id','designation_id');
        // return $this->hasOne(OrganizationUserDetail::class);
        // return  $this->belongsToMany(Designation::class, OrganizationUserDetail::class,'designation_id');
    }
    public function designation()
    {
        return  $this->hasOneThrough(OrganizationUserDetail::class, Designation::class,'id','designation_id');
        // return $this->hasOne(Designation::class);
    }
    public function Organization()
    {
        return $this->hasOne(Organization::class);
    }
    public function sendForgotEmail($request)
    {
        $user = User::where('email', $request->all('email'))->first();
        if (isset($user) && !empty($user)) {
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'forgot',
                'subject' => 'Booking Management System: Forgot Password',
                'data' => $user,
            ];
            $emailRes = \Mail::to($user['email'])
                ->cc('maulik.kanhasoft@gmail.com')
                ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));
            return true;
        } else {
            return false;
        }
    }

    public function sendRegisterEmail($request)
    {
        $user = User::where('email', $request->all('email'))->first();
        $randPassword =  $this->RandomString();
        $user->password = $randPassword;
        if (isset($user) && !empty($user)) {
            $userObj = User::find($user->id);
            $userObj->password = Hash::make($randPassword);
            $userObj->save();

            $details = [
                'mailTitle' => 'register',
                'subject' => 'Booking Management System: Registration Done!',
                'data' => $user,
            ];
            $emailRes = \Mail::to($user['email'])
                //->cc('maulik.kanhasoft@gmail.com')
                ->bcc('suresh.kanhasoft@gmail.com')
                ->send(new \App\Mail\SendSmtpMail($details));
            return true;
        } else {
            return false;
        }
    }

    public function RandomString()
    {
        return substr(str_shuffle(str_repeat("0123456789szABCDEFGHIJUVWXYZ", 8)), 0, 8);
    }


    public function getOrganizationById($userId = null)
    {
        $query = User::select(
            'users.id',
            'users.*',
            'organizations.organization_name',
            'organizations.contact_person_name',
            'organizations.plan',
            //'organizations.user_id',
            'organizations.start_date',
            'organizations.end_date',
            'users.contact_number',
            'users.address_line_1',
            'users.address_line_2',
        );
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.id');
        $query->where('users.id', $userId);
        $userDetais = $query->first();
        return $userDetais;
    }

    public function getSignee(Request $request, $userId = null)
    {
        //print_r($userId);exit();
        $keyword = $request->get('search');
        $perPage = 40;
        //$perPage = Config::get('constants.pagination.perPage');
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.contact_number',
            'users.parent_id',
            //'signee_organization.organization_id as parent_id',
            'users.status as signee_status',
            'signees_detail.candidate_id',
            'signees_detail.date_of_birth',
            'signees_detail.nationality',
            DB::raw('candidate_referred_froms.name AS candidate_referred_from'),
            'signees_detail.nmc_dmc_pin',
            'signee_organization.status as compliance_status',
            'signee_speciality.speciality_id',
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $query->leftJoin('signee_organization', 'signee_organization.user_id', '=', 'users.id');
        $query->Join('signees_detail', 'signees_detail.user_id', '=', 'users.id');
        $query->leftJoin('signee_speciality', 'signee_speciality.user_id', '=', 'users.id');
        $query->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query->leftJoin('candidate_referred_froms', 'candidate_referred_froms.id', '=', 'signees_detail.candidate_referred_from');
        // $query->whereIn('signee_organization.organization_id', array(40 ,107));
        $query->where('users.role', "SIGNEE");
        //$query->where('users.parent_id', $userId);
        if(Auth::user()->role == 'ORGANIZATION'){
            //print_r($userId);exit();
            $org = SigneeOrganization::where('organization_id', $userId)->get()->toArray();
            $userIdArray = array_column($org, 'user_id');
            //print_r($userIdArray);exit();

            $signee = User::select('id')->where(['parent_id' => Auth::user()->id])->get()->toArray();
            $signeeIdArray = array_column($signee, 'id');
            $signeeIdArray[] = Auth::user()->id;
            $mainArray = array_merge($userIdArray, $signeeIdArray);
            //$query->where('signee_organization.organization_id', $userId);
            $query->where('signee_organization.organization_id', $userId);
            $query->whereIn('users.id', $mainArray);
        }
        else{
            //print_r(Auth::user()->parent_id);exit();
            // $org = SigneeOrganization::where('organization_id', Auth::user()->parent_id)->get()->toArray();
            // $signeeIdArray = array_column($org, 'user_id');
           // print_r($signeeIdArray);exit();
            // $query->whereIn('users.id', array($signeeIdArray))->whereIn('users.parent_id', array(Auth::user()->id, Auth::user()->parent_id));
            // $a = $query->get()->toArray();
            // print_r($a);exit();
            //$query->whereIn('users.parent_id', Auth::user()->id,  Auth::user()->parent_id);
            $query->whereIn('signee_organization.organization_id', array(Auth::user()->id, Auth::user()->parent_id));
        }
        if (!empty($keyword)) {
            $query->where(function ($query2) use ($keyword) {
                $query2->where('users.email', 'like',  "%$keyword%")
                    ->orWhere('users.first_name', 'like',  "%$keyword%")
                    ->orWhere('users.last_name',  'LIKE', "%$keyword%")
                    ->orWhere('users.contact_number', 'LIKE', "%$keyword%");
            });
        }
        $query->whereNull(['signee_speciality.deleted_at']);
        // $query->whereNull(['signee_speciality.deleted_at','specialities.deleted_at']);

        $query->whereNull(['signee_speciality.deleted_at', 'specialities.deleted_at']);
        $query->whereNull(['signee_organization.deleted_at', 'specialities.deleted_at']);

        $query->groupBy('signee_organization.user_id');
        // $query->groupBy('signee_speciality.user_id');
        return $query->latest('users.created_at')->paginate($perPage);
    }

    public function speciality()
    {
        return $this->hasMany(Speciality::class, 'user_id');
    }
    public function documents()
    {
        return $this->hasMany(SigneeDocument::class, 'signee_id');
    }
    // public function passport()
    // {
    //     return $this->hasMany(SigneeDocument::class, 'signee_id');
    // }
    // public function immuninisation_records()
    // {
    //     return $this->hasMany(SigneeDocument::class, 'signee_id');
    // }

    public function getSigneeById($userId = null)
    {
       // print_r(Auth::user()->id);exit();
        $query = User::select(
            'users.id as user_id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.contact_number',
            'oud.designation_id',
            'users.parent_id',
            'parentUser.first_name as org_first_name',
            'parentUser.last_name as org_last_name',
            'parentUser.last_name as org_last_name',
            'parentUser.email  as org_email',
            'organizations.organization_name',
            'organizations.contact_person_name',
            'users.contact_number',
            'users.address_line_1',
            'users.address_line_2',
            'signees_detail.candidate_id',
            'users.address_line_1',
            'users.address_line_2',
            'users.city',
            'users.postcode',
            'signees_detail.nationality',
            'signees_detail.date_of_birth',
            // 'signees_detail.mobile_number',
            // 'signees_detail.phone_number',
            'signees_detail.candidate_referred_from',
            DB::raw('candidate_referred_froms.name AS candidate_referred_name'),
            'signees_detail.nmc_dmc_pin',
            DB::raw('date(users.created_at) AS date_registered'),
            //DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $query->leftJoin('signees_detail',  'signees_detail.user_id', '=', 'users.id');
        //$query->leftJoin('signee_speciality', 'signee_speciality.user_id', '=', 'users.id');
        //$query->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query->leftJoin('candidate_referred_froms', 'candidate_referred_froms.id', '=', 'signees_detail.candidate_referred_from');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        $query->leftJoin('organization_user_details as oud',  'oud.user_id', '=', 'users.parent_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->where('users.id', $userId);
        $userDetais = $query->first();

        //query for speciality
        $query2 = SigneeSpecialitie::select(
            //'specialities.id',
            //'specialities.speciality_name',
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $query2->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query2->where('signee_speciality.user_id', $userId);
        $userSpec = $query2->get()->toArray();
        //print_r($userSpec);exit();
        //query for passport documents
        $query3 = SigneeDocument::select(
            'file_name'
        );
        $query3->where('signee_id', $userId);
        $query3->where('key', '=','passport');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query3->where('organization_id', Auth::user()->id);
        }else{
            $query3->where('organization_id', Auth::user()->parent_id);
        }
        $userPassportDocs = $query3->get()->toArray();

        //query for immunisation_records documents
        $query4 = SigneeDocument::select(
            'file_name'
        );
        $query4->where('signee_id', $userId);
        $query4->where('key', '=','immunisation_records');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query4->where('organization_id', Auth::user()->id);
        }else{
            $query4->where('organization_id', Auth::user()->parent_id);
        }
        $userIRDocs = $query4->get()->toArray();
        // print_r($userIRDocs);exit();

        //query for training_certificates documents
        $query5 = SigneeDocument::select(
            'file_name'
        );
        $query5->where('signee_id', $userId);
        $query5->where('key', '=','training_certificates');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query5->where('organization_id', Auth::user()->id);
        }else{
            $query5->where('organization_id', Auth::user()->parent_id);
        }
        // $query5->where('organization_id', Auth::user()->id);
        $userTCDocs = $query5->get()->toArray();

        //query for nursing_certificates documents
        $query6 = SigneeDocument::select(
            'file_name'
        );
        $query6->where('signee_id', $userId);
        $query6->where('key', '=','nursing_certificates');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query6->where('organization_id', Auth::user()->id);
        }else{
            $query6->where('organization_id', Auth::user()->parent_id);
        }
        //$query6->where('organization_id', Auth::user()->id);
        $userNCDocs = $query6->get()->toArray();

        //query for professional_indemnity_insurance documents
        $query7 = SigneeDocument::select(
            'file_name'
        );
        $query7->where('signee_id', $userId);
        $query7->where('key', '=','professional_indemnity_insurance');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query7->where('organization_id', Auth::user()->id);
        }else{
            $query7->where('organization_id', Auth::user()->parent_id);
        }
        //$query7->where('organization_id', Auth::user()->id);
        $userPIIDocs = $query7->get()->toArray();

        //query for nmc_statement documents
        $query8 = SigneeDocument::select(
            'file_name'
        );
        $query8->where('signee_id', $userId);
        $query8->where('key', '=','nmc_statement');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query8->where('organization_id', Auth::user()->id);
        }else{
            $query8->where('organization_id', Auth::user()->parent_id);
        }
        //$query8->where('organization_id', Auth::user()->id);
        $userNMCDocs = $query8->get()->toArray();

        //query for dbs_disclosure_certificate documents
        $query9 = SigneeDocument::select(
            'file_name'
        );
        $query9->where('signee_id', $userId);
        $query9->where('key', '=','dbs_disclosure_certificate');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query9->where('organization_id', Auth::user()->id);
        }else{
            $query9->where('organization_id', Auth::user()->parent_id);
        }
        //$query9->where('organization_id', Auth::user()->id);
        $userDDCDocs = $query9->get()->toArray();

        //query for cv documents
        $query10 = SigneeDocument::select(
            'file_name'
        );
        $query10->where('signee_id', $userId);
        $query10->where('key', '=','cv');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query10->where('organization_id', Auth::user()->id);
        }else{
            $query10->where('organization_id', Auth::user()->parent_id);
        }
        //$query10->where('organization_id', Auth::user()->id);
        $userCVDocs = $query10->get()->toArray();

        //query for employment documents
        $query11 = SigneeDocument::select(
            'file_name'
        );
        $query11->where('signee_id', $userId);
        $query11->where('key', '=','employment');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query11->where('organization_id', Auth::user()->id);
        }else{
            $query11->where('organization_id', Auth::user()->parent_id);
        }
        //$query11->where('organization_id', Auth::user()->id);
        $userEmpDocs = $query11->get()->toArray();

        //query for address_proof documents
        $query12 = SigneeDocument::select(
            'file_name'
        );
        $query12->where('signee_id', $userId);
        $query12->where('key', '=','address_proof');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query12->where('organization_id', Auth::user()->id);
        }else{
            $query12->where('organization_id', Auth::user()->parent_id);
        }
        //$query12->where('organization_id', Auth::user()->id);
        $userAPDocs = $query12->get()->toArray();

        //query for passport_photo documents
        $query13 = SigneeDocument::select(
            'file_name'
        );
        $query13->where('signee_id', $userId);
        $query13->where('key', '=','passport_photo');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query13->where('organization_id', Auth::user()->id);
        }else{
            $query13->where('organization_id', Auth::user()->parent_id);
        }
        //$query13->where('organization_id', Auth::user()->id);
        $userPPDocs = $query13->get()->toArray();

        //query for proof_of_ni documents
        $query14 = SigneeDocument::select(
            'file_name'
        );
        $query14->where('signee_id', $userId);
        $query14->where('key', '=','proof_of_ni');
        if(Auth::user()->role == 'ORGANIZATION'){
            $query14->where('organization_id', Auth::user()->id);
        }else{
            $query14->where('organization_id', Auth::user()->parent_id);
        }
        //$query14->where('organization_id', Auth::user()->id);
        $userNIDocs = $query14->get()->toArray();

        $result = [];
        $result = $userDetais;
        $result->speciality = $userSpec;
        $result->documents = array('passport'=>$userPassportDocs);
        $result->documents += array('immunisation_records'=>$userIRDocs);
        $result->documents += array('training_certificates'=>$userTCDocs);
        $result->documents += array('nursing_certificates'=>$userNCDocs);
        $result->documents += array('professional_indemnity_insurance'=>$userPIIDocs);
        $result->documents += array('nmc_statement'=>$userNMCDocs);
        $result->documents += array('dbs_disclosure_certificate'=>$userDDCDocs);
        $result->documents += array('cv'=>$userCVDocs);
        $result->documents += array('employment'=>$userEmpDocs);
        $result->documents += array('address_proof'=>$userAPDocs);
        $result->documents += array('passport_photo'=>$userPPDocs);
        $result->documents += array('proof_of_ni'=>$userNIDocs);
        //$result->passport = $userPassportDocs;
        return $result;
    }
}
