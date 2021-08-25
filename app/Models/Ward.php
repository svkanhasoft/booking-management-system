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
    protected $fillable = ['trust_id', 'ward_na me', 'ward_type', 'ward_number', 'hospital_id'];
    // protected $hidden = ['pseudo', 'deleted_at', 'updated_at', 'created_at'];

    function addWard($postData, $trustId, $hospital_id, $isDelete = false)
    {
        if ($isDelete == true) {
            Ward::where(['trust_id' => $trustId])->delete();
        }

        foreach ($postData as $key => $val) {
            $val['trust_id'] = $trustId;
            $val['hospital_id'] = $hospital_id;
            Ward::create($val);
            unset($val);
        }
        return true;
    }

    function addOrUpdateWard($postData, $trustId)
    {
        foreach ($postData['ward'] as $keys => $values) {
            // dd($values);
            // exit;
            $objWards = Ward::whereNull('deleted_at')->where(['hospital_id' => $postData['hospital_id'], 'ward_name' => $values['ward_name'], 'ward_type' => $values['ward_type']])->firstOrNew();
            $objWards->ward_name = $values['ward_name'];
            $objWards->hospital_id =  $postData['hospital_id'];
            $objWards->trust_id = $trustId;
            $objWards->ward_type = $values['ward_type'];
            $objWards->ward_number = $values['ward_number'];
            $objWards->save();
            $objWards = '';
        }
    }

    public function post()
    {
        return $this->belongsTo(Trust::class);
    }
}
