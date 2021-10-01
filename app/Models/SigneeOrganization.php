<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

    function addOrganisation($postData, $userId, $orgId, $isDelete = true)
    {
    //print_r(Auth::user()->parent_id);exit();
        if ($isDelete == true) {
            SigneeOrganization::where(['user_id' => $userId, 'organization_id'=> Auth::user()->parent_id])->delete();
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
