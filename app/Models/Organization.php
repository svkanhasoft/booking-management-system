<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'organizations';

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
    protected $fillable = ['user_id', 'organization_name', 'contact_person_name', 'contact_no','address_line_1',
    'address_line_2','city','postcode','created_at', 'updated_at','start_date','end_date','plan'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];
     
    
}
