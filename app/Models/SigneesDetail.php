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
    protected $fillable = ['user_id', 'candidate_id', 'date_of_birth', 'nationality', 'candidate_referred_from', 'date_registered','cv','nmc_dmc_pin'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
