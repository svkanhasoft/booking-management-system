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
     * @var array, .
     */
    protected $fillable = ['user_id', 'trust_id', 'name', 'code', 'preference_invoice_method', 'email_address', 'address_line_1', 'address_line_2', 'address_line_3', 'city', 'post_code', 'trust_portal_url', 'portal_email', 'portal_password', 'first_name', 'first_name', 'last_name', 'email_address', 'phone_number', 'client', 'department', 'contact_email_address'];
    protected $hidden = ['pseudo', 'deleted_at', 'updated_at', 'created_at'];

    public function hospital()
    {
        return $this->hasMany(Hospital::class, 'trust_id');
    }
    function ward()
    {
        // return $this->hasMany(Ward::class, 'hospital_id');
        // return  $this->hasManyThrough(Ward::class,Hospital::class,  'trust_id', 'hospital_id', 'id');
        // return $this->hasMany(Ward::class );

        return $this->hasManyThrough(
                Ward::class ,Hospital::class,
                'trust_id', // Foreign key on the hospital table...
                'hospital_id', // Foreign key on the deployments table...
                'id', // Local key on the trust table...
                'id' // Local key on the hospital table...
            );

    }

    public function training()
    {
        return $this->hasMany(Traning::class, 'trust_id');
    }


    public function getTrustById($trustId)
    {
        $result = [];
        $result = Trust::find($trustId);
        $result->hospital;
        $result->training;
        foreach ($result->hospital as $key => $value) {
            // $wardResult = Ward::where('ward.hospital_id', $value['id'])->get();
            // select('ward.*','ward_type.ward_type_name')->
            $wardResult = Ward::select('ward.*', 'ward_type.ward_type')
                ->leftJoin('ward_type',  'ward_type.id', '=', 'ward.ward_type_id')
                ->where('ward.hospital_id', $value['id'])->get();
            $result->hospital[$key]['ward'] = $wardResult;
        }
        return $result;
    }

    public function test($trustId)
    {

        $result = [];
        $result = Trust::find($trustId);
        $result->training;
        $result->hospitals;
        // $result->hospitals->take( $result->ward );
        $result->ward;
        return $result;
    }


    public function hospitals()
    {
        // return $this->hasMany(Hospital::class, "trust_id", "id"); 

        // return  $this->belongsToMany(Hospital::class, Ward::class,'hospital_id');   OLD code
        // return $this->belongsToMany(Ward::class, 'candidate_wards');

        return  $this->hasMany(Hospital::class,'trust_id');

        // return $this->morphOne(Hospital::class, 'trust_id','id','ward');
        
        // return  $this->hasManyThrough(Hospital::class, Ward::class );

        // return  $this->hasManyThrough(Ward::class ,Hospital::class,'trust_id','hospital_id','id');

        // return  $this->hasManyThrough(Hospital::class, Ward::class, 'trust_id', 'id', 'hospital_id');

        // return $this->hasManyThrough(
        //     Ward::class ,Hospital::class,
        //     'trust_id', // Foreign key on the hospital table...
        //     'hospital_id', // Foreign key on the deployments table...
        //     'id', // Local key on the trust table...
        //     'id' // Local key on the hospital table...
        // );
    }

}
