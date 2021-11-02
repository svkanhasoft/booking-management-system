<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hospitals';

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
    protected $fillable = ['id', 'hospital_name', 'trust_id'];
    protected $hidden = ['pseudo', 'deleted_at', 'updated_at', 'created_at'];

    function addHospital($postData, $trustId, $isDelete = false)
    {
        if ($isDelete == true) {
            Hospital::where(['trust_id' => $trustId])->delete();
        }
        foreach ($postData as $key => $val) {
            $val['trust_id'] = $trustId;
            Hospital::create($val);
            unset($val);
        }
        return true;
    }

    public function post()
    {
        return $this->belongsTo(Trust::class);
    }

    public function addUpdateHospital($postData)
    {
        $hospitalidArray = array_column($postData['hospital'], 'id');
        if (!empty($postData['hospital'])) {
            $hospitals = Hospital::where('trust_id', '=', $postData['id'])->whereNotIn('id', $hospitalidArray)->delete();
            foreach ($postData['hospital'] as $keys => $values) {
                if (isset($values['id'])) {
                    $hospitalId = $values['id'];
                    $objHospital = Hospital::where(['id' => $values['id'], 'trust_id' => $postData['id']])->firstOrNew();
                    $objHospital->hospital_name = $values['hospital_name'];
                    $objHospital->trust_id = $postData['id'];
                    $objHospital->save();
                    $objHospital = '';
                } else {
                    $objHospital = new Hospital();
                    $objHospital->hospital_name = $values['hospital_name'];
                    $objHospital->trust_id = $postData['id'];
                    $objHospital->save();
                    $hospitalId =  $objHospital['id'];
                    // $objHospital = '';
                }

                $objWard = new Ward();
                $wardResult = $objWard->addOrUpdateWard($values, $postData['id'], $hospitalId);
            }
        }
    }

    public function trust()
    {
        return $this->belongsTo(Trust::class);
    }
    
    public function ward()
    {
        return  $this->belongsToMany( Ward::class,'hospital_id');
        // return $this->hasMany(Ward::class , 'hospital_id');
        
    }

    public function booking()
    {
        return $this->hasMany(Booking::class , 'booking_id');
    }
   
}
