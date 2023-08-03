<?php 

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RFMService
{
  public static function rfm($subQuery, $rfmPrms)
  {
    //RFM分析
    $subQuery = $subQuery->groupBy('id')
    ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase, created_at');

    $subQuery = DB::table($subQuery)
    ->groupBy('customer_id')
    ->selectRaw('customer_id, customer_name, max(created_at) as recentDate, datediff(now(), max(created_at)) as recency, count(customer_id) as frequency, sum(totalPerPurchase) as monetary');
  
    // $rfmPrms = [
    //     14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000
    // ];

    $subQuery = DB::table($subQuery)
    ->selectRaw('customer_id, customer_name, recentDate, recency, frequency, monetary, 
    case 
        when recency < ? then 5
        when recency < ? then 4
        when recency < ? then 3
        when recency < ? then 2
        else 1 end as r,
    case 
        when ? <= frequency then 5
        when ? <= frequency then 4
        when ? <= frequency then 3
        when ? <= frequency then 2
        else 1 end as f,
    case 
        when ? <= monetary then 5
        when ? <= monetary then 4
        when ? <= monetary then 3
        when ? <= monetary then 2
        else 1 end m
    ', $rfmPrms);

    // dd($subQuery);

    Log::debug($subQuery->get());

    $totals = DB::table($subQuery)->count();
    $rCount = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'r')
        ->selectRaw('ranks.rank as r, count(r)')
        ->groupBy('ranks.rank')
        ->orderBy('r', 'desc')
        ->pluck('count(r)');

    Log::debug($rCount);

    $fCount = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'f')
        ->selectRaw('ranks.rank as f, count(f)')
        ->groupBy('ranks.rank')
        ->orderBy('f', 'desc')
        ->pluck('count(f)');

    $mCount = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'm')
        ->selectRaw('ranks.rank as m, count(m)')
        ->groupBy('ranks.rank')
        ->orderBy('m', 'desc')
        ->pluck('count(m)');

    $eachCount = [];
    $rank = 5;

    for($i = 0; $i < 5; $i++) {
        array_push($eachCount, [
            'rank' => $rank, 
            'r' => $rCount[$i], 
            'f' => $fCount[$i], 
            'm' => $mCount[$i], 
        ]);
        $rank--;
    }
    // dd($total, $eachCount, $rCount, $fCount, $mCount);

    $data = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'r')
        ->selectRaw('concat("r_", ranks.rank) as rRank, 
        count(case when f = 5 then 1 end) as f_5,
        count(case when f = 4 then 1 end) as f_4,
        count(case when f = 3 then 1 end) as f_3,
        count(case when f = 2 then 1 end) as f_2,
        count(case when f = 1 then 1 end) as f_1')
        ->groupBy('rank')
        ->orderBy('rRank', 'desc')
        ->get();

    // dd($data);

    return [$data, $totals, $eachCount];

  }
}