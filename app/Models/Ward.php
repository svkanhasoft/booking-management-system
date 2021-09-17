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
    // protected $fillable = ['ward_type_id', 'ward_name', 'ward_number', 'hospital_id'];
    // protected $hidden = ['deleted_at', 'updated_at', 'created_at'];
    protected $fillable = ['ward_type_id', 'ward_name','trust_id', 'ward_number', 'hospital_id'];
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

    function addWard($postData, $trustId, $hospital_id, $isDelete = false)
    {
        foreach ($postData as $key => $val) {
            if (isset($val['ward_name']) && !empty($val['ward_number'])) {
                $val['hospital_id'] = $hospital_id;
                $val['trust_id'] = $trustId;
                Ward::create($val);
                unset($val);
            }
        }
        return true;
    }

    // function addWard($postData, $trustId, $hospital_id, $isDelete = false)
    // {
    //     print_r($postData);exit();
    //     foreach ($postData as $key => $val) {
    //         if (isset($val['ward_name']) && !empty($val['ward_number'])) {
    //             $val['hospital_id'] = $hospital_id;
    //             Ward::create($val);
    //             unset($val);
    //         }
    //     }
    //     return true;
    // }

    function addOrUpdateWard($postData, $trustId, $hospitalId)
    {
        $wardidArray = array_column($postData['ward'], 'id');
        // dd($wardidArray);
        // exit;
        // echo $postData['id'];exit;

        if (!empty($postData['ward'])) {
            $objBookingMatchDelete = Ward::where('hospital_id', '=', $hospitalId)->whereNotIn('id', $wardidArray)->delete();
            foreach ($postData['ward'] as $keys => $values) {
               // print_r($values);exit();
                // $objWards = Ward::whereNull('deleted_at')->where(['hospital_id' => $postData['id'], 'ward_name' => $values['ward_name'], 'ward_type_id' => $values['ward_type_id']])->firstOrNew();                
                if (isset($values['ward_name']) && !empty($values['ward_number'])) {
                    if (isset($values['id']) && $values['id'] > 0) {
                        //echo "in if";exit();
                        $objWards = Ward::where(['id' => $values['id']])->firstOrNew();
                    } else {
                        //echo "in else";exit();
                        $objWards = new Ward();
                    }
                    $objWards->trust_id = $trustId;
                    $objWards->ward_name = $values['ward_name'];
                    $objWards->hospital_id =  $hospitalId;
                    $objWards->ward_type_id = $values['ward_type_id'];
                    $objWards->ward_number = $values['ward_number'];
                    $objWards->save();
                    $objWards = '';
                }
            }
        }
    }

    public function post()
    {
        return $this->belongsTo(Trust::class);
    }


    public function ward() {
        // return $this->belongsTo(Hospital::class);
        return $this->hasManyThrough(Hospital::class, Ward::class);
    }

}
