<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Speciality extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'specialities';

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
    protected $fillable = ['user_id', 'speciality_name'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];

    public function addOrUpdateSpeciality($postData, $userId)
    {
        //print_r($postData);exit();
        //$signeeidArray = array_column($postData['speciality'], 'id');
        SigneeSpecialitie::where('user_id', '=', $userId)->whereNotIn('speciality_id', $postData)->delete();
        foreach ($postData as $keys => $values) {
            //print_r($values);exit();
            if (!empty($values)) {
                $res = SigneeSpecialitie::withTrashed()->where(['speciality_id' => $values, 'user_id' => $userId])->restore();
                $objSpeciality = SigneeSpecialitie::where(['speciality_id' => $values, 'user_id' => $userId])->firstOrNew();
                //print_r($objSpeciality);exit();
                $objSpeciality->speciality_id = $values;
                $objSpeciality->user_id = $userId;
                $objSpeciality->save();
                $objSpeciality = '';
            }
        }
        return true;
    }
    
}
