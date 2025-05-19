<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    public function orderdetail()
    {
        return $this->hasMany(OrdersDetails::class, 'order_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
