<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rental_rate_per_day', 'stock'];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}