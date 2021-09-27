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
    protected $fillable = ['user_id', 'speciality_id', 'organization_id'];

    function addSpeciality($postData, $userId, $isDelete = true)
    {
        //print_r($postData);exit();
        if ($isDelete == true) {
            SigneeSpecialitie::where(['user_id' => $userId])->delete();
        }
        foreach ($postData as $key => $val) {
            //print_r($val);exit();
            
            foreach($val['speciality'] as $key => $data)
            {
                $objSigneeSpecialitie = new SigneeSpecialitie();
                $objSigneeSpecialitie->organization_id = $val['organization_id'];
                $objSigneeSpecialitie->speciality_id = $data;
                $objSigneeSpecialitie->user_id = $userId;
                $objSigneeSpecialitie->save();
                $objSigneeSpecialitie = "";
            }
            //$objSigneeSpecialitie->speciality_id = $val;
            
        }
        return true;
    }
}
