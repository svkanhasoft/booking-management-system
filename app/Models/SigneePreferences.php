<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SigneePreferences extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'signee_preference';

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
    protected $fillable = ['user_id', 'monday_day', 'monday_night', 'tuesday_day', 'tuesday_night', 'wednesday_day', 'wednesday_night', 'thursday_day', 'thursday_night', 'friday_day', 'friday_night', 'saturday_day', 'saturday_night', 'sunday_day', 'sunday_night', 'no_of_shift', 'is_travel',];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function addOrUpdatePreference($postData, $userId)
    {
            $objSigneePreference = SigneePreferences::where(['user_id' => $userId])->firstOrNew();
            $objSigneePreference->user_id = $userId;
            $objSigneePreference->monday_day = $postData['monday_day'];
            $objSigneePreference->monday_night = $postData['monday_night'];
            $objSigneePreference->tuesday_day = $postData['tuesday_day'];
            $objSigneePreference->tuesday_night = $postData['tuesday_night'];
            $objSigneePreference->wednesday_day = $postData['wednesday_day'];
            $objSigneePreference->wednesday_night = $postData['wednesday_night'];
            $objSigneePreference->thursday_day = $postData['thursday_day'];
            $objSigneePreference->thursday_night = $postData['thursday_night'];
            $objSigneePreference->friday_day = $postData['friday_day'];
            $objSigneePreference->friday_night = $postData['friday_night'];
            $objSigneePreference->saturday_day = $postData['saturday_day'];
            $objSigneePreference->saturday_night = $postData['saturday_night'];
            $objSigneePreference->sunday_day = $postData['sunday_day'];
            $objSigneePreference->sunday_night = $postData['sunday_night'];
            $objSigneePreference->no_of_shift = $postData['no_of_shift'];
            $objSigneePreference->is_travel = $postData['is_travel'];
            $objSigneePreference->save();
            $objSigneePreference = '';
            return true;
    }

}
