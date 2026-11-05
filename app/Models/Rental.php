<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'customer_name', 'days', 'total_price', 'status'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}