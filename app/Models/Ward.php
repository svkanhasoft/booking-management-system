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
    protected $fillable = ['trust_id', 'ward_type_id', 'ward_name', 'ward_number', 'hospital_id'];
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

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

    function addOrUpdateWard($postData, $trustId, $hospitalId)
    {
        $wardidArray = array_column($postData['ward'], 'id');
        // dd($wardidArray);
        // exit;
        // echo $postData['id'];exit;
        $objBookingMatchDelete = Ward::where('trust_id', '=', $trustId)
            ->whereNotIn('id', $wardidArray)->delete();
        // dd( $objBookingMatchDelete);
        if (!empty($postData['ward'])) {
            foreach ($postData['ward'] as $keys => $values) {
                // $objWards = Ward::whereNull('deleted_at')->where(['hospital_id' => $postData['id'], 'ward_name' => $values['ward_name'], 'ward_type_id' => $values['ward_type_id']])->firstOrNew();
                if (isset($values['id']) && $values['id'] > 0) {
                    $objWards = Ward::where(['id' => $values['id']])->firstOrNew();
                } else {
                    $objWards = new Ward();
                }
                $objWards->ward_name = $values['ward_name'];
                $objWards->hospital_id =  $hospitalId;
                $objWards->trust_id = $trustId;
                $objWards->ward_type_id = $values['ward_type_id'];
                $objWards->ward_number = $values['ward_number'];
                $objWards->save();
                $objWards = '';
            }
        }
    }

    public function post()
    {
        return $this->belongsTo(Trust::class);
    }
}
