<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_history';

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
    protected $fillable = ['id', 'organization_id', 'subscription_name','subscription_price', 'subscription_purchase_date', 'subscription_expire_date', 'paypal_response', 'payment_status'];
    protected $hidden = ['pseudo', 'deleted_at', 'updated_at', 'created_at'];
}
