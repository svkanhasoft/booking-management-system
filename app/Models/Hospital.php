<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hospitals';

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
    protected $fillable = ['id', 'hospital_name', 'trust_id'];

    function addHospital($postData, $trustId, $isDelete = false){
        if($isDelete == true){
            Hospital::where(['trust_id' => $trustId])->delete();
         }
        foreach($postData as $key => $val){
            $val['trust_id'] = $trustId;
            Hospital::create($val);
            unset($val);
        }
        return true;
    }

    public function post()
    {
        return $this->belongsTo(Trust::class);
    }
}
