<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Subtotal;
use Illuminate\Support\Carbon;

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
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('created_at', "<=", $endDate1);
        } elseif (!is_null($startDate) && !is_null($endDate)) {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('created_at', "<=", $endDate1)
            ->where('created_at', ">=", $startDate);
        }
    }
}
