<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SigneesDetail extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'signees_detail';

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
    protected $fillable = ['user_id', 'candidate_id', 'phone_number', 'mobile_number', 'date_of_birth', 'nationality', 'candidate_referred_from', 'date_registered','cv','nmc_dmc_pin'];
    // protected $fillable = ['user_id', 'candidate_id', 'phone_number', 'mobile_number', 'address_line_1', 'address_line_2', 'address_line_3', 'city', 'post_code', 'date_of_birth', 'nationality', 'candidate_referred_from', 'date_registered','cv','nmc_dmc_pin'];

    public function user()
    {
        return $this->belongsTo(User::class);
        // OR return $this->belongsTo('App\User');
    }
}
