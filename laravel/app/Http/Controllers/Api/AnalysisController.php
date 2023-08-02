<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Services\AnalysisService;
use App\Services\DecilService;

class AnalysisController extends Controller
{
	public function index(Request $request)
	{
		$subQuery = Order::BetweenDate($request->startDate, $request->endDate);
		if($request->type === 'perDay') {
				list($data, $labels, $totals) = AnalysisService::perDay($subQuery);
		} elseif($request->type === 'perMonth') {
				list($data, $labels, $totals) = AnalysisService::perMonth($subQuery);
		} elseif($request->type === 'perYear') {
				list($data, $labels, $totals) = AnalysisService::perYear($subQuery);
		} elseif($request->type === 'decil') {
			list($data, $labels, $totals) = DecilService::decil($subQuery);
		}
		return response()->json([
				'data' => $data,
				'type' => $request->type,
				'labels' => $labels,
				'totals' => $totals,
		], Response::HTTP_OK);
	}
}
