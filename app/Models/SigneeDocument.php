<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigneeDocument extends Model
{
    use HasFactory;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'signee_documents';
    
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
    protected $fillable = ['signee_id', 'key', 'file_name'];

    public function getDocument($signeeId, $key)  //get all documents while upload
    {
        $document = SigneeDocument::select(
            'key',
            'file_name',
            DB::raw('date(created_at) as date_added'),
        );
        $document->where(['signee_id' => $signeeId, 'key' => $key]);
        $res = $document->get()->toArray();
        return $res;
    }

}
