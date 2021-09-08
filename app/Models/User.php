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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','name', 'email', 'password', 'first_name', 'last_name', 'email_verified_at', 'password', 'remember_token',
        'created_at', 'updated_at', 'role', 'status', 'profile_pic', 'password_change', 'password_change', 'last_login_date', 'is_deleted',
        'parent_id', 'postcode', 'city', 'address_line_2', 'address_line_1', 'contact_number'
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
                    ->orWhere('users.email',  'LIKE', "%$keyword%")
                    ->orWhere('roles.role_name',  'LIKE', "%$keyword%")
                    ->orWhere('designations.designation_name',  'LIKE', "%$keyword%");
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

    public function getSigneeDetails($userId = null)
    {

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
            'users.city',
            'users.postcode',
            'signees_detail.nationality',
            'signees_detail.date_of_birth',
            'signees_detail.mobile_number',
            'signees_detail.phone_number',
        );
        $query->leftJoin('signees_detail',  'signees_detail.user_id', '=', 'users.id');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        $query->leftJoin('organization_user_details as oud',  'oud.user_id', '=', 'users.parent_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->where('users.id', $userId);
        $userDetais = $query->first();
        return $userDetais;
    }

    public function SigneesDetail()
    {
        return $this->hasOne(SigneesDetail::class);
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
                ->cc('shaileshv.kanhasoft@gmail.com')
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
                ->cc('maulik.kanhasoft@gmail.com')
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
            'organizations.user_id',
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

    public function getSignee($userId = null)
    {
        $perPage = Config::get('constants.pagination.perPage');
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.parent_id',
            'signees_detail.candidate_id',
            'signees_detail.phone_number',
            'signees_detail.mobile_number',
            'signees_detail.date_of_birth',
            'signees_detail.nationality',
            //'signees_detail.candidate_referred_from',
            DB::raw('candidate_referred_froms.name AS candidate_referred_from'),
            'signees_detail.nmc_dmc_pin',
            'signee_organization.status',
            'signee_speciality.speciality_id',
            DB::raw('GROUP_CONCAT( specialities.speciality_name SEPARATOR ", ") AS speciality_name'),
        );
        $query->Join('signee_organization', 'signee_organization.user_id', '=', 'users.id');
        $query->Join('signees_detail', 'signees_detail.user_id', '=', 'users.id');
        $query->leftJoin('signee_speciality', 'signee_speciality.user_id', '=', 'users.id');
        $query->leftJoin('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query->leftJoin('candidate_referred_froms', 'candidate_referred_froms.id', '=', 'signees_detail.candidate_referred_from');
        $query->where('signee_organization.organization_id', $userId);
        $query->where('users.role', "SIGNEE");
        $query->whereNull(['signee_speciality.deleted_at','specialities.deleted_at']);
        $query->groupBy('signee_organization.user_id');
        // $query->groupBy('signee_speciality.user_id');
        return $query->latest('users.created_at')->paginate($perPage);
    }
    
    public function speciality()
    {
        return $this->hasMany(Speciality::class, 'user_id');
    }

    public function getSigneeById($userId = null)
    {
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
        

        $query2 = SigneeSpecialitie::select(
            'specialities.id',
            'specialities.speciality_name',
        );
        $query2->Join('specialities', 'specialities.id', '=', 'signee_speciality.speciality_id');
        $query2->where('signee_speciality.user_id', $userId);

        $result = [];
        $userSpec = $query2->get();
        $result = $userDetais;
        $result->speciality = $userSpec;
        //$result = $userSpec;
        //$result = array_push($userDetais, $userSpec);
        
        return $result;
    }
}
