<?php

namespace App\Models;

use App\Models\Booking;

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
    protected $fillable = ['ward_type_id', 'ward_name', 'ward_number', 'hospital_id'];
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];

    function addWard($postData, $trustId, $hospital_id, $isDelete = false)
    {
        foreach ($postData as $key => $val) {
            if (isset($val['ward_name'])) { // remove by shailesh on 25 May feedback
            // if (isset($val['ward_name']) && !empty($val['ward_number'])) {
                $val['hospital_id'] = $hospital_id;
                //$val['trust_id'] = $trustId;
                Ward::create($val);
                unset($val);
            }
        }
        return true;
    }

    function addOrUpdateWard($postData, $trustId, $hospitalId)
    {
        $wardidArray = array_column($postData['ward'], 'id');

        if (!empty($postData['ward'])) {
            /** Below code  is used for delete ward and check in booking table.
             * if record are available and date id >= today then this record are not deleted.
             * Before change anything on this cade discuss with me */
            $wardArray = Ward::where('hospital_id', '=', $hospitalId)->whereNotIn('id', $wardidArray)->get()->toArray();
            $wards = array_map(function ($e) {
                return is_object($e) ? $e->id : $e['id'];
            }, $wardArray);
            $bookingWard = Booking::whereIn('ward_id', $wards)->where('date', '>=', date('Y-m-d'))->get()->toArray();
            $deleteWard = array_map(function ($e) {
                return is_object($e) ? $e->ward_id : $e['ward_id'];
            }, $bookingWard);
            $diffArray =   array_diff($wards, $deleteWard);
            $objBookingMatchDelete = Ward::where('hospital_id', '=', $hospitalId)->whereIn('id', $diffArray)->delete();

            // $objBookingMatchDelete = Ward::where('hospital_id', '=', $hospitalId)->whereNotIn('id', $wardidArray)->delete();
            foreach ($postData['ward'] as $keys => $values) {
                if (isset($values['ward_name'])) {
                // if (isset($values['ward_name']) && !empty($values['ward_number'])) {  // remove by shailesh on 25 May feedback
                    if (isset($values['id']) && $values['id'] > 0) {
                        $objWards = Ward::where(['id' => $values['id']])->firstOrNew();
                    } else {
                        $objWards = new Ward();
                    }
                    //$objWards->trust_id = $trustId;
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

    public function hospital()
    {
        return  $this->belongsToMany(Hospital::class, 'hospital_id');
        // return $this->hasMany(Ward::class , 'hospital_id');
    }
}
