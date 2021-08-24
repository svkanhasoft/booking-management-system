<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Traning extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'traning';

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
    protected $fillable = ['id','trust_id', 'traning_name'];
    protected $hidden = ['pseudo','deleted_at','updated_at', 'created_at'];
    function addTraning($postData, $trustId,$isDelete = false){
        if($isDelete == true){
            Traning::where(['trust_id' => $trustId])->delete();
         }
        foreach($postData as $key => $val){
            $val['trust_id'] = $trustId;
            Traning::create($val);
            unset($val);
        }
        return true;
    }

    public function post()
    {
        return $this->belongsTo(Trust::class);
    }
}
