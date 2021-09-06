<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SigneeSpecialitie extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'signee_speciality';

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
    protected $fillable = ['user_id', 'speciality_id'];

    function addSpeciality($postData, $userId, $isDelete = false)
    {
        if ($isDelete == true) {
            SigneeSpecialitie::where(['user_id' => $userId])->delete();
        }
        foreach ($postData as $key => $val) {
          //  print_r($val);exit();
            $objSigneeSpecialitie = new SigneeSpecialitie();
            $objSigneeSpecialitie->speciality_id = $val['id'];
            $objSigneeSpecialitie->user_id = $userId;
            $objSigneeSpecialitie->save();
            $objSigneeSpecialitie = "";
        }
        return true;
    }
}
