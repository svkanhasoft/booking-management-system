<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trust extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trusts';

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
    protected $fillable = ['user_id', 'trust_id', 'name', 'code', 'preference_invoide_mathod', 'email_address', 'address_line_1', 'address_line_2', 'address_line_3', 'city', 'post_code', 'trust_portal_url', 'portal_email', 'portal_password', 'first_name', 'first_name', 'last_name', 'email_address', 'phone_number', 'client', 'department','contact_email_address'];
    protected $hidden = ['pseudo','deleted_at','updated_at', 'created_at'];
   
    public function hospital()
    {
        return $this->hasMany(Hospital::class, 'trust_id');
    }
    function ward()
    {
        return $this->hasMany(Ward::class, 'trust_id');
    }
    public function training()
    {
        return $this->hasMany(Traning::class, 'trust_id');
    }
    

    public function getTrustById($trustId)
    {
        $query = Trust::select(
            'trusts.*',
            'hospitals.hospital_name',
            'ward.ward_name',
            'ward.ward_type',
            'ward.ward_number',
            'traning.traning_name',
        );
        $query->leftJoin('hospitals',  'hospitals.trust_id', '=', 'trusts.id');
        $query->leftJoin('ward',  'ward.trust_id', '=', 'trusts.id');
        $query->leftJoin('traning',  'traning.trust_id', '=', 'trusts.id');
        $query->where('trusts.id', $trustId);
        $trusts = $query->get();

        $subArray = [];
            foreach ($trusts as $key => $trust) {

                $subArray[$key] = $trust;
                $subQuery = Hospital::select(
                    'hospitals.id',
                    'hospitals.hospital_name',
                );
                $subQuery->leftJoin('trusts', 'trusts.id', '=' ,'hospitals.trust_id');
                $subQuery->where('trusts.id', $trustId);
                $res = $subQuery->get()->toArray();
                $subArray[$key]['hospital'] = $res;


                foreach($res as $key => $ward)
                {
                    $subArray[$key] = $ward;
                    $query = Ward::select(
                        'ward.ward_name',
                        'ward.ward_type',
                        'ward.ward_number',
                        'ward.hospital_id'
                    );

                    //$query->leftJoin('ward', 'ward.hospital_id', '=' ,'hospitals.id');
                    $query->leftJoin('hospitals', 'ward.hospital_id', '=' ,'hospitals.id');
                    $query->where('ward.trust_id', $trustId);
                    $res2 = $query->get()->toArray();
                    $subArray[$key]['ward'] = $res2;
                }
                break;
            }

            // $subSubArray = [];
            // foreach ($res as $key => $ward) {

            //     $subArray[$key] = $ward;
            //     $subSubQuery = Ward::select(
            //         'ward.ward_name',
            //         'ward.ward_type',
            //         'ward.ward_number'
            //     );
            //     $subSubQuery->leftJoin('trusts', 'trusts.id', 'ward.trust_id');
            //     $subSubQuery->leftJoin('hospitals', 'hospitals.id', 'ward.hospital_id');
            //     $subSubQuery->where('ward.trust_id', $trustId);
            //     $res = $subSubQuery->get()->toArray();
            //     $subSubArray[$key]['ward'] = $res;
            // }
            // return $subSubArray;
            return $subArray;
            
    }
}
