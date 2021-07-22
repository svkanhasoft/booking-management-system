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

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'first_name', 'last_name', 'email_verified_at', 'password',
        'remember_token', 'created_at', 'updated_at', 'role', 'status', 'profile_pic',
        'is_verified', 'ip_address', 'created_by', 'updated_by', 'is_deleted', 'parent_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'created_by', 'updated_by', 'remember_token',
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
            'oud.contact_number',
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
            'organizations.contact_no',
            'organizations.address_line_1',
            'organizations.address_line_2',
        );
        $query->Join('organization_user_details as oud',  'oud.user_id', '=', 'users.id');
        $query->Join('roles',  'roles.id', '=', 'oud.role_id');
        $query->Join('designations',  'designations.id', '=', 'oud.designation_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        $query->where('users.id', $userId);
        $userDetais = $query->get();
        return $userDetais;
    }

    public function fetchStaflist($userId = null)
    {
        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.role',
            'users.email',
            'oud.contact_number',
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
            'organizations.contact_no',
            'organizations.address_line_1',
            'organizations.address_line_2',
        );
        $query->Join('organization_user_details as oud',  'oud.user_id', '=', 'users.id');
        $query->leftJoin('roles',  'roles.id', '=', 'oud.role_id');
        $query->Join('designations',  'designations.id', '=', 'oud.designation_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        $query->where('users.parent_id', $userId);
        $query->where('users.role', "STAFF");
        $userDetais = $query->simplePaginate(15);
        return $userDetais;
    }

    public function getSigneeDetails($userId = null)
    {

        $query = User::select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'oud.contact_number',
            'oud.designation_id',
            'users.parent_id',
            'parentUser.first_name as org_first_name',
            'parentUser.last_name as org_last_name',
            'parentUser.last_name as org_last_name',
            'parentUser.email  as org_email',
            'organizations.organization_name',
            'organizations.contact_person_name',
            'organizations.contact_no',
            'organizations.address_line_1',
            'organizations.address_line_2',
            'signees_detail.candidate_id',
            'signees_detail.address_line_1',
            'signees_detail.address_line_2',
            'signees_detail.address_line_3',
            'signees_detail.city',
            'signees_detail.post_code',
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
        // OR return $this->hasOne('App\Phone');
    }
    public function Organization()
    {
        return $this->hasOne(Organization::class);
        // OR return $this->hasOne('App\Phone');
    }
}
