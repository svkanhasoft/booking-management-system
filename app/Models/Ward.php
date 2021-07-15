<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Ward extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ward';

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
    protected $fillable = ['trust_id', 'ward_name', 'ward_type', 'ward_number'];
    protected $hidden = ['pseudo','deleted_at','updated_at', 'created_at'];

    function addWard($postData, $trustId,$isDelete = false){
        if($isDelete == true){
           Ward::where(['trust_id' => $trustId])->delete();
        }
        foreach($postData as $key => $val){
            $val['trust_id'] = $trustId;
            Ward::create($val);
            unset($val);
        }
        return true;
    }
    
    public function post()
    {
        return $this->belongsTo(Trust::class);
    }
    
}
