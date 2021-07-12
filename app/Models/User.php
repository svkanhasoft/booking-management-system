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
        'name','email','password','first_name', 'last_name', 'email_verified_at', 'password', 
        'remember_token', 'created_at', 'updated_at', 'role', 'status', 'profile_pic', 
        'is_verified','ip_address', 'created_by', 'updated_by', 'is_deleted','parent_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'organizations.address',

        );
        $query->Join('organization_user_details as oud',  'oud.user_id', '=', 'users.id');
        $query->Join('roles',  'roles.id', '=', 'oud.role_id');
        $query->Join('designations',  'designations.id', '=', 'oud.designation_id');
        $query->leftJoin('organizations',  'organizations.user_id', '=', 'users.parent_id');
        $query->Join('users as parentUser',  'parentUser.id', '=', 'users.parent_id');
        $query->where('users.id',$userId);
        $userDetais = $query->get();
        return $userDetais;
    }

    public function SigneesDetail()
    {
        return $this->hasOne(SigneesDetail::class);
        // OR return $this->hasOne('App\Phone');
    }

}
