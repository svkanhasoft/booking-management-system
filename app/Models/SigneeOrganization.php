<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SigneeOrganization extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'signee_organization';

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
    protected $fillable = ['user_id', 'status','organization_id'];

    function addOrganisation($postData, $userId, $orgId, $isDelete = false)
    {
      // print_r($postData);exit();
        if ($isDelete == true) {
            SigneeOrganization::where(['user_id' => $userId])->delete();
        }
        foreach ($postData as $key => $val) {
            //print_r($val);exit();
            $objSigneeOrganization = new SigneeOrganization();
            $objSigneeOrganization->organization_id = $val['organization_id'];
            $objSigneeOrganization->user_id = $userId;
            $objSigneeOrganization->status = "NEWSIGNUP";
            $objSigneeOrganization->save();
            $objSigneeOrganization = "";
        }
        return true;
    }
}
