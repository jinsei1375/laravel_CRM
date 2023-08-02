<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;

class DecilService
{
  public static function decil($subQuery)
  {
    $subQuery = $subQuery->groupBy('id')
        ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase');

        $subQuery = DB::table($subQuery)
    ->groupBy('customer_id')
    ->selectRaw('customer_id, customer_name, sum(totalPerPurchase) as total')
    ->orderBy('total', 'desc');

    DB::statement('set @row_num = 0;');
    $subQuery = DB::table($subQuery)
        ->selectRaw('@row_num:= @row_num+1 as row_num, customer_id, customer_name, total');

    $count = DB::table($subQuery)->count();
    $total = DB::table($subQuery)->selectRaw('sum(total) as total')->get();
    $total = $total[0]->total;

    $decil = ceil($count / 10);

    $bindValue = [];
    $tempValue = 0;
    for($i = 1; $i <= 10; $i++) {
        array_push($bindValue, 1 + $tempValue);
        $tempValue += $decil;
        array_push($bindValue, 1 + $tempValue);
    }

    DB::statement('set @row_num = 0;');
    $subQuery = DB::table($subQuery)
    ->selectRaw('row_num, customer_id, customer_name, total, 
    case
        when ? <= row_num and row_num < ? then 1
        when ? <= row_num and row_num < ? then 2
        when ? <= row_num and row_num < ? then 3
        when ? <= row_num and row_num < ? then 4
        when ? <= row_num and row_num < ? then 5
        when ? <= row_num and row_num < ? then 6
        when ? <= row_num and row_num < ? then 7
        when ? <= row_num and row_num < ? then 8
        when ? <= row_num and row_num < ? then 9
        when ? <= row_num and row_num < ? then 10
    end as decil
    ', $bindValue);

    $subQuery = DB::table($subQuery)
    ->groupBy('decil')
    ->selectRaw('decil, round(avg(total)) as average, sum(total) as totalPerGroup');

    DB::statement("set @total = ${total};");
    $data = DB::table($subQuery)
    ->selectRaw('decil, average, totalPerGroup, round(100 * totalPerGroup / @total, 1) as totalRatio')->get();

    $labels = $data->pluck('decil');
    $totals = $data->pluck('totalPerGroup');

    return [$data, $labels, $totals];

  }
}