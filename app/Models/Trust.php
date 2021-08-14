<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trust extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trusts';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'trust_id', 'name', 'code', 'preference_invoide_mathod', 'email_address', 'address_line_1', 'address_line_2', 'address_line_3', 'city', 'post_code', 'trust_portal_url', 'portal_email', 'portal_password', 'first_name', 'first_name', 'last_name', 'email_address', 'phone_number', 'client', 'department','contact_email_address'];
    protected $hidden = ['pseudo','deleted_at','updated_at', 'created_at'];
   
    function ward(){
        return $this->hasMany(Ward::class, 'trust_id');
    }
    public function training()
    {
        return $this->hasMany(Traning::class, 'trust_id');
    }
}
