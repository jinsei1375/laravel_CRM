<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Subtotal;

class Order extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new Subtotal);
    }

    public function scopeBetweenDate($query, $startDate = null, $endDate = null)
    {
        if (is_null($startDate) && is_null($endDate)) {
            return $query;
        } elseif (!is_null($startDate) && is_null($endDate)) {
            return $query->where('created_at', ">=", $startDate);
        } elseif (is_null($startDate) && !is_null($endDate)) {
            return $query->where('created_at', "<=", $endDate);
        } elseif (!is_null($startDate) && !is_null($endDate)) {
            return $query->where('created_at', "<=", $endDate)
            ->where('created_at', ">=", $startDate);
        }
    }
}
