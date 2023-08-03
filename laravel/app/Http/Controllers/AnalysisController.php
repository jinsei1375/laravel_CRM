<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index()
    {
        $startDate = '2023-08-01';
        $endDate = '2023-08-31';

        //RFM分析
        $subQuery = Order::BetweenDate($startDate, $endDate)
        ->groupBy('id')
        ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase, created_at');

        $subQuery = DB::table($subQuery)
        ->groupBy('customer_id')
        ->selectRaw('customer_id, customer_name, max(created_at) as recentDate, datediff(now(), max(created_at)) as recency, count(customer_id) as frequency, sum(totalPerPurchase) as monetary');
        

        $rfmPrms = [
            14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000
        ];

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

        // dd($subQuery->get());

        $total = DB::table($subQuery)->count();
        $rCount = DB::table($subQuery)
        ->rightJoin('ranks', 'ranks.rank', '=', 'r')
        ->selectRaw('ranks.rank as r, count(r)')
        ->groupBy('ranks.rank')
        ->orderBy('r', 'desc')
        ->pluck('count(r)');
        // dd($rCount->get());

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

        return Inertia::render('Analysis');
    }

    public function rfm()
    {
        $startDate = '2022-09-01';
        $endDate = '2023-08-31';
        //RFM分析
        $subQuery = Order::BetweenDate($startDate, $endDate)
        ->groupBy('id')
        ->selectRaw('id, customer_id, customer_name, SUM(subtotal) as totalPerPurchase, created_at');

        $subQuery = DB::table($subQuery)
        ->groupBy('customer_id')
        ->selectRaw('customer_id, customer_name, max(created_at) as recentDate, datediff(now(), max(created_at)) as recency, count(customer_id) as frequency, sum(totalPerPurchase) as monetary');
        

        $rfmPrms = [
            14, 28, 60, 90, 7, 5, 3, 2, 300000, 200000, 100000, 30000
        ];

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

        $total = DB::table($subQuery)->count();
        $rCount = DB::table($subQuery)
        ->groupBy('r')
        ->selectRaw('r, count(r)')
        ->orderBy('r', 'desc')
        ->pluck('count(r)');

        $fCount = DB::table($subQuery)
        ->groupBy('f')
        ->selectRaw('f, count(f)')
        ->orderBy('f', 'desc')
        ->pluck('count(f)');

        $mCount = DB::table($subQuery)
        ->groupBy('m')
        ->selectRaw('m, count(m)')
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
        ->groupBy('r')
        ->selectRaw('concat("r_", r) as rRank, 
        count(case when f = 5 then 1 end) as f_5,
        count(case when f = 4 then 1 end) as f_4,
        count(case when f = 3 then 1 end) as f_3,
        count(case when f = 2 then 1 end) as f_2,
        count(case when f = 1 then 1 end) as f_1')
        ->orderBy('rRank', 'desc')
        ->get();

        // dd($data);
    }

    public function decil() 
    {
        $startDate = '2022-08-01';
        $endDate = '2022-08-31';

        $subQuery = Order::BetweenDate($startDate, $endDate)
        ->groupBy('id')
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

        // dd($data);
    }
}
