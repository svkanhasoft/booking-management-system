<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SigneeSpecialitie extends Model
{
    //use SoftDeletes;
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

    function updateSpeciality($postData, $userId, $orgId, $isDelete = false)
    {
        //print_r($postData);exit;
        // if ($isDelete == true) {
        //     SigneeSpecialitie::where(['user_id' => $userId])->delete();
        // }
        SigneeSpecialitie::where('user_id', '=', $userId)->whereNotIn('speciality_id', $postData)->delete();
        foreach ($postData as $key => $val) {
            $objSigneeSpecialitie = SigneeSpecialitie::where(['user_id' => $userId, 'speciality_id' => $val, 'organization_id' => $orgId])->firstOrNew();;
            $objSigneeSpecialitie->speciality_id = $val;
            $objSigneeSpecialitie->user_id = $userId;
            $objSigneeSpecialitie->organization_id = $orgId;
            $objSigneeSpecialitie->save();
            $objSigneeSpecialitie = "";
        }
        return true;

        // if ($isDelete == true) {
        //     SigneeSpecialitie::where(['user_id' => $userId])->delete();
        // }
        // foreach ($postData as $key => $val) {
        //     $objSigneeSpecialitie = new SigneeSpecialitie();
        //     $objSigneeSpecialitie->speciality_id = $val;
        //     $objSigneeSpecialitie->user_id = $userId;
        //     $objSigneeSpecialitie->organization_id = $orgId;
        //     $objSigneeSpecialitie->save();
        //     $objSigneeSpecialitie = "";
        // }
        // return true;
    }

    function updateSpecialityV2($postData, $userId, $orgId, $isDelete = false)
    {

        SigneeSpecialitie::where('user_id', '=', $userId)->where('organization_id', '=', $orgId)->whereNotIn('speciality_id', $postData)->delete();
        foreach ($postData as $key => $val) {
            $objSigneeSpecialitie = SigneeSpecialitie::where(['user_id' => $userId, 'speciality_id' => $val, 'organization_id' => $orgId])->firstOrNew();;
            $objSigneeSpecialitie->speciality_id = $val;
            $objSigneeSpecialitie->user_id = $userId;
            $objSigneeSpecialitie->organization_id = $orgId;
            $objSigneeSpecialitie->save();
            $objSigneeSpecialitie = "";
        }
        return true;
    }


    public function addSpeciality($postData, $userId, $isDelete = false)
    {
        if ($isDelete == true) {
            SigneeSpecialitie::where(['user_id' => $userId])->delete();
        }
        foreach ($postData as $key => $val) {
            foreach($val['speciality'] as $key => $data)
            {
                $objSigneeSpecialitie = new SigneeSpecialitie();
                $objSigneeSpecialitie->organization_id = $val['organization_id'];
                $objSigneeSpecialitie->speciality_id = $data;
                $objSigneeSpecialitie->user_id = $userId;
                $objSigneeSpecialitie->save();
                $objSigneeSpecialitie = "";
            }
        }
        return true;
    }
}
