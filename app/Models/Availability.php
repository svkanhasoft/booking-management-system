<?php

namespace App\Models;

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
    protected $fillable = ['user_id', 'day_name', 'shift_id','is_selected'];

    function addAvailability($postData,$userId)
    {
        // print_r($postData);
        // exit;
        foreach ($postData['sunday'] as $key => $available) {
            // print_r($available);
            // exit;
            //   $objAvailability = new Availability();
            // $objAvailability = Availability::where('shift_id', $available['shift_id'])->firstOrNew();
            $objAvailability = Availability::where(['user_id' => $userId, 'day_name' => 'sunday', 
            'shift_id' => $available['shift_id'] ])->firstOrNew();
            $objAvailability->user_id = $userId;
            $objAvailability->is_selected = $available['is_selected'];
            $objAvailability->day_name = 'sunday';
            $objAvailability->shift_id = $available['shift_id'];
            $objAvailability->save();
            $objAvailability = '';
        }
       return true;
    }
}
