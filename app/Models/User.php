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
//use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
        'parent_id', 'postcode', 'city', 'address_line_2', 'address_line_1', 'contact_number', 'device_id', 'platform', 'created_by', 'updated_by',
        'subscription_name', 'subscription_purchase_date', 'subscription_expire_date'
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
            // 'users.status as profile_status',
            'signee_organization.profile_status',
            'users.password_change as is_password_change',
            'users.city',
            'users.postcode',
            'users.profile_pic',
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
        return  $this->hasOneThrough(OrganizationUserDetail::class, Designation::class, 'id', 'designation_id');
    }
    public function designation()
    {
        return  $this->hasOneThrough(OrganizationUserDetail::class, Designation::class, 'id', 'designation_id');
    }
    public function Organization()
    {
        return $this->hasOne(Organization::class);
    }
    public function sendForgotEmail($request)
    {
        $user = User::where('status', 'Active')->where('email', $request->all('email'))->first();
        if (isset($user) && !empty($user)) {
            $details = [
                'title' => '',
                'body' => 'Hello ',
                'mailTitle' => 'forgot',
                'subject' => 'Pluto: Forgot Password',
                'data' => $user,
            ];
            $emailRes = \Mail::to($user['email'])->send(new \App\Mail\SendSmtpMail($details));
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
                'subject' => 'Pluto: Registration Done!',
                'data' => $user,
            ];
            $emailRes = \Mail::to($user['email'])->send(new \App\Mail\SendSmtpMail($details));
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
        // print_r($userId);exit();
        $keyword = $request->get('search');
        $perPage = Config::get('constants.pagination.perPage');
        //$perPage = 70;
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
            DB::raw('GROUP_CONCAT(DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $query->leftJoin('signee_organization', 'signee_organization.user_id', '=', 'users.id');
        $query->Join('signees_detail', 'signees_detail.user_id', '=', 'users.id');
        $query->leftJoin('signee_speciality', 'signee_speciality.user_id', '=', 'users.id');
        $query->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query->leftJoin('candidate_referred_froms', 'candidate_referred_froms.id', '=', 'signees_detail.candidate_referred_from');
        // $query->whereIn('signee_organization.organization_id', array(40 ,107));
        $query->where('users.role', "SIGNEE");
        //$query->where('users.parent_id', $userId);
        if (Auth::user()->role == 'ORGANIZATION') {
            //echo "123";exit;
            $staffList = SigneeOrganization::where('organization_id', Auth::user()->id)->get()->toArray();
            //print_r($staffList);exit;

            $staffIdArray = array_column($staffList, 'user_id');
            //print_r($staffIdArray);exit;


            //$signeeList = SigneeOrganization::whereIn(['organization_id' => $staffIdArray])->get()->toArray();
            //$signeeIdArray = array_column($signeeList, 'id');
            //print_r($signeeIdArray);exit;
            $query->whereIn('users.id', $staffIdArray);
        } else {

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
        //print_r($query->toSql());exit();
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

    public function getSigneeById($userId = null)
    {
        //print_r($userId);exit();
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
            // 'users.status as signee_status',
            'signee_organization.profile_status as signee_status',
            'signee_organization.status as compliance_status',
            'users.postcode',
            'signees_detail.nationality',
            //'signees_detail.date_of_birth',
            DB::raw('DATE_FORMAT(signees_detail.date_of_birth, "%d-%m-%Y")AS date_of_birth'),
            // 'signees_detail.mobile_number',
            // 'signees_detail.phone_number',
            'signees_detail.candidate_referred_from',
            DB::raw('candidate_referred_froms.name AS candidate_referred_name'),
            'signees_detail.nmc_dmc_pin',
            DB::raw('DATE_FORMAT(users.created_at, "%d-%m-%Y") AS date_registered'),
            //DB::raw('date(users.created_at) AS date_registered'),
            //DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $query->leftJoin('signees_detail',  'signees_detail.user_id', '=', 'users.id');
        //$query->leftJoin('signee_speciality', 'signee_speciality.user_id', '=', 'users.id');
        //$query->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query->leftJoin('candidate_referred_froms', 'candidate_referred_froms.id', '=', 'signees_detail.candidate_referred_from');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        $query->leftJoin('organization_user_details as oud',  'oud.user_id', '=', 'users.parent_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->leftJoin('signee_organization',  'signee_organization.user_id', '=', 'users.id');

        if (Auth::user()->role == 'ORGANIZATION') {
            //print(Auth::user()->id);exit;
            $query->where('signee_organization.organization_id', Auth::user()->id);
        } else {
            //print(Auth::user()->role);exit;
            $query->where('signee_organization.organization_id', Auth::user()->parent_id);
        }

        $query->where('users.id', $userId);
        //$query->where('signee_organization.organization_id', Auth::user()->id);
        $userDetais = $query->first();
        //query for speciality
        $query2 = SigneeSpecialitie::select(
            //'specialities.id as speciality_id',
            // 'specialities.speciality_name',
            DB::raw('GROUP_CONCAT( DISTINCT specialities.id SEPARATOR ", ") AS speciality_id'),
            DB::raw('GROUP_CONCAT( DISTINCT specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $query2->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query2->where('signee_speciality.user_id', $userId);
        $userSpec = $query2->get()->first();

        //query for passport documents
        $query3 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query3->where('signee_id', $userId);
        $query3->where('key', '=', 'passport');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query3->where('organization_id', Auth::user()->id);
        } else {
            $query3->where('organization_id', Auth::user()->parent_id);
        }
        $userPassportDocs = $query3->get()->toArray();

        //query for immunisation_records documents
        $query4 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query4->where('signee_id', $userId);
        $query4->where('key', '=', 'immunisation_records');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query4->where('organization_id', Auth::user()->id);
        } else {
            $query4->where('organization_id', Auth::user()->parent_id);
        }
        $userIRDocs = $query4->get()->toArray();


        //query for training_certificates documents
        $query5 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query5->where('signee_id', $userId);
        $query5->where('key', '=', 'training_certificates');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query5->where('organization_id', Auth::user()->id);
        } else {
            $query5->where('organization_id', Auth::user()->parent_id);
        }

        $userTCDocs = $query5->get()->toArray();

        //query for nursing_certificates documents
        $query6 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query6->where('signee_id', $userId);
        $query6->where('key', '=', 'nursing_certificates');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query6->where('organization_id', Auth::user()->id);
        } else {
            $query6->where('organization_id', Auth::user()->parent_id);
        }

        $userNCDocs = $query6->get()->toArray();

        //query for professional_indemnity_insurance documents
        $query7 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query7->where('signee_id', $userId);
        $query7->where('key', '=', 'professional_indemnity_insurance');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query7->where('organization_id', Auth::user()->id);
        } else {
            $query7->where('organization_id', Auth::user()->parent_id);
        }

        $userPIIDocs = $query7->get()->toArray();

        //query for nmc_statement documents
        $query8 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query8->where('signee_id', $userId);
        $query8->where('key', '=', 'nmc_statement');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query8->where('organization_id', Auth::user()->id);
        } else {
            $query8->where('organization_id', Auth::user()->parent_id);
        }

        $userNMCDocs = $query8->get()->toArray();

        //query for dbs_disclosure_certificate documents
        $query9 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query9->where('signee_id', $userId);
        $query9->where('key', '=', 'dbs_disclosure_certificate');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query9->where('organization_id', Auth::user()->id);
        } else {
            $query9->where('organization_id', Auth::user()->parent_id);
        }

        $userDDCDocs = $query9->get()->toArray();

        //query for cv documents
        $query10 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query10->where('signee_id', $userId);
        $query10->where('key', '=', 'cv');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query10->where('organization_id', Auth::user()->id);
        } else {
            $query10->where('organization_id', Auth::user()->parent_id);
        }

        $userCVDocs = $query10->get()->toArray();

        //query for employment documents
        $query11 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query11->where('signee_id', $userId);
        $query11->where('key', '=', 'employment');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query11->where('organization_id', Auth::user()->id);
        } else {
            $query11->where('organization_id', Auth::user()->parent_id);
        }

        $userEmpDocs = $query11->get()->toArray();

        //query for address_proof documents
        $query12 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query12->where('signee_id', $userId);
        $query12->where('key', '=', 'address_proof');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query12->where('organization_id', Auth::user()->id);
        } else {
            $query12->where('organization_id', Auth::user()->parent_id);
        }

        $userAPDocs = $query12->get()->toArray();

        //query for passport_photo documents
        $query13 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query13->where('signee_id', $userId);
        $query13->where('key', '=', 'passport_photo');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query13->where('organization_id', Auth::user()->id);
        } else {
            $query13->where('organization_id', Auth::user()->parent_id);
        }

        $userPPDocs = $query13->get()->toArray();

        //query for proof_of_ni documents
        $query14 = SigneeDocument::select(
            'file_name',
            'document_status',
            'expire_date',
            'id',
        );
        $query14->where('signee_id', $userId);
        $query14->where('key', '=', 'proof_of_ni');
        if (Auth::user()->role == 'ORGANIZATION') {
            $query14->where('organization_id', Auth::user()->id);
        } else {
            $query14->where('organization_id', Auth::user()->parent_id);
        }

        $userNIDocs = $query14->get()->toArray();

        $signeePref = SigneePreferences::where('user_id', $userId)->first();
        //print_r($signeePref);exit;

        $result = [];
        $result = $userDetais;
        // print_r( explode (',',$userSpec->speciality_id));exit;
        $userSpec->speciality_id =  array_map('intval', explode(',', $userSpec->speciality_id));
        $result->speciality = $userSpec;
        $result->documents = array('passport' => $userPassportDocs);
        $result->documents += array('immunisation_records' => $userIRDocs);
        $result->documents += array('training_certificates' => $userTCDocs);
        $result->documents += array('nursing_certificates' => $userNCDocs);
        $result->documents += array('professional_indemnity_insurance' => $userPIIDocs);
        $result->documents += array('nmc_statement' => $userNMCDocs);
        $result->documents += array('dbs_disclosure_certificate' => $userDDCDocs);
        $result->documents += array('cv' => $userCVDocs);
        $result->documents += array('employment' => $userEmpDocs);
        $result->documents += array('address_proof' => $userAPDocs);
        $result->documents += array('passport_photo' => $userPPDocs);
        $result->documents += array('proof_of_ni' => $userNIDocs);
        $result->preferences = $signeePref;
        return $result;
    }

    public function organizations()
    {
        // return  $this->hasManyThrough(SigneeOrganization::class, User::class );
        // return  $this->hasManyThrough(SigneeOrganization::class, User::class,'id','user_id','id');

        return  $this->hasManyThrough(User::class, SigneeOrganization::class, 'user_id', 'id');  // Working

        // return  $this->belongsToMany( User::class,SigneeOrganization::class,'user_id','id');  // Working

        // return  $this->morphMany( SigneeOrganization::class,'specialitys','user_id');

        // return $this->hasMany(SigneeOrganization::class,'user_id');
        return $this->hasMany(SigneeSpecialitie::class, 'user_id');
    }

    public function specialitys($id)
    {
        return  Speciality::where(['user_id', $id])->get()->toArray();
    }


    public function getDashboard($dayName, $year = '')
    {
        $today = Carbon::now()->format('Y-m-d');

        $month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $usermcount =   $userArr =  [];

        if ($dayName == 'today') {
            return  User::where('role', 'ORGANIZATION')->where('created_at', '=', $today)->count();
        } else if ($dayName == 'week') {
            return  User::where('role', 'ORGANIZATION')->where('created_at', '>=', Carbon::now()->subDays(7)->format('Y-m-d'))->count();
        } else if ($dayName == 'month') {
            return  User::where('role', 'ORGANIZATION')->where('created_at', '>=', Carbon::now()->subMonth(1)->format('Y-m-d'))->count();
        } else if ($dayName == 'year') {
            return  User::where('role', 'ORGANIZATION')->whereYear('created_at', date('Y'))->count();
        } else if ($dayName == 'block_user') {
            return  User::where('role', 'ORGANIZATION')->where('status', 'Inactive')->count();
        } else if ($dayName == 'total_user') {
            return  User::where('role', 'ORGANIZATION')->count();
        } else if ($dayName == 'monthly_details') {
            $users = User::select('id', 'created_at')->whereYear('created_at', $year)->where('role', 'ORGANIZATION')->get()->groupBy(function ($date) {
                // $users = User::select('id', 'created_at')->whereYear('created_at', date('Y'))->where('role', 'ORGANIZATION')->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });
            $userArr[] = array("Element", "Monthly User Details", array('role' => "style"));
            foreach ($users as $key => $value) {
                $usermcount[(int)$key] = count($value);
            }
            for ($i = 1; $i <= 12; $i++) {
                $userArr[] = array($month[$i - 1], (!empty($usermcount[$i])) ? $usermcount[$i] : 0, $this->bgcolor());
                // $userArr[] = array('label' => $month[$i - 1], 'y' => (!empty($usermcount[$i])) ? $usermcount[$i] : 0);
            }
            return $userArr;
        } else if ($dayName == 'yearly_details') {
            $users = User::select('id', 'created_at')->where('role', 'ORGANIZATION')->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y');
            });
            $userArr[] = array("Element", "Yearly User Details", array('role' => "style"));
            foreach ($users as $key => $value) {
                $usermcount[(int)$key] = count($value);
            }
            foreach ($usermcount as $key => $value) {
                $userArr[] = array("$key", $value, $this->bgcolor());
                // $userArr[] = array('label' => $key, 'y' => $value);
            }
            return $userArr;
        }
    }

    public function bgcolor()
    {
        // return "color: #" . dechex(rand(0,10000000));
        $res = array("color: #184a7b");
        // $res = array("color: #bc8c99", "color: #9261be", "color: #55fbf8", "color: #c54094", "color: #6b3223", "color: #2b0b95", "color: #74952e", "color: #1f5ada", "color: #deadd0", "color: #2a4453", "color: #e6d21a", "color: #1d3151", "color: #cdd53c", "color: #3da117", "color: #adf9e9");
        $key =  array_rand($res);
        return $res[$key];
        // return "color: #" . dechex(rand(0x000000, 0xFFFFFF));
    }
}
