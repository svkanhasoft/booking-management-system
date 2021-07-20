<?php

namespace App\Models;

use Hamcrest\Arrays\IsArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'availability';

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
    protected $fillable = ['user_id', 'day_name', 'shift_id', 'is_selected'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    function addAvailability($postData, $userId)
    {
        foreach ($postData as $key => $available) {
            if (is_array($available)) {
                foreach ($postData[$key] as $keys => $values) {
                    $objAvailability = Availability::where([ 'user_id' => $userId, 'day_name' =>  $key,
                        'shift_id' => $values['shift_id'] ])->firstOrNew();
                    $objAvailability->user_id = $userId;
                    $objAvailability->is_selected = $values['is_selected'];
                    $objAvailability->day_name = $key;
                    $objAvailability->shift_id = $values['shift_id'];
                    $objAvailability->save();
                    $objAvailability = '';
                }
            }
        }
        return true;
    }

    function getAvailability($userId)
    {
        $res = Availability::where(['user_id' => $userId])->groupBy('day_name')->get()->toArray();
        $result = [];
        foreach ($res as $key => $val) {
            $result[$val['day_name']]  = Availability::where(['day_name' => $val['day_name']])->get()->toArray();
        }
        return $result;
    }
}
