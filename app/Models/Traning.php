<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPUnit\Framework\isNull;

class Traning extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'traning';

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
    protected $fillable = ['id', 'trust_id', 'training_name'];
    protected $hidden = ['pseudo', 'deleted_at', 'updated_at', 'created_at'];
    function addTraning($postData, $trustId, $isDelete = false)
    {
        foreach ($postData as $key => $val) {
            if (!empty($val['training_name'])) {
                $val['trust_id'] = $trustId;
                Traning::create($val);
                unset($val);
            }
        }
        return true;
    }

    public function updateTraning($postData)
    {
        $signeeidArray = array_column($postData['training'], 'id');
        $objBookingMatchDelete = Traning::where('trust_id', '=', $postData['id'])->whereNotIn('id', $signeeidArray)->delete();
        foreach ($postData['training'] as $keys => $values) {
            if (isset($values['id'])) {
                $objTraning = Traning::where(['id' => $values['id']])->firstOrNew();
            } else {
                $objTraning = new Traning();
            }
            $objTraning->training_name = $values['training_name'];
            $objTraning->trust_id = $postData['id'];
            $objTraning->save();
            $objTraning = '';
        }
        return true;
    }

    public function post()
    {
        return $this->belongsTo(Trust::class);
    }
}
