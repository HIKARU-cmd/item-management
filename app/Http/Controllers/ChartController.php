<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Process;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request){

        $latestYear = Item::where('user_id', auth()->id())
        ->selectRaw('YEAR(purchase_at) as year')
        ->orderBy('year', 'desc')
        ->value('year'); // 最初の year の値を取得
        
        // 選択された年を取得(デフォルトでは今年を取得)
        $selectedYear = $request->input('year', $latestYear ?? Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);

        // 選択or入力された年がBDの存在するか確認
        $exists = Item::where('user_id', auth()->id())
            ->whereYear('purchase_at', $selectedYear)
            ->exists();

        if($selectedMonth < 1 || $selectedMonth > 12) {
            return redirect('/chart');
        }

        $months = [];
        for($i=1; $i<=12; $i++){
            $months[sprintf('%04d-%02d', $selectedYear, $i)] = 0;
        }

        // 選択された年の購入データを取得

        $rawData = Item::where('user_id', auth()->id())
            ->selectRaw("DATE_FORMAT(purchase_at, '%Y-%m') as month, COALESCE(SUM(quantity * price), 0) as total")
            ->whereYear('purchase_at', $selectedYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month'); // ['YYYY-MM' => total] の配列として取得
        
        foreach($rawData as $month => $total){
            $months[$month] = $total;
        }

        $monthlyData = [];
        foreach($months as $month => $total){
            $monthlyData[] = ['month' => $month, 'total' => $total];
        }
        
        $years = Item::where('user_id', auth()->id())
            ->selectRaw("YEAR(purchase_at) as year")
            ->distinct() // 重複を排除
            ->orderBy('year', 'desc')
            ->pluck('year'); // 配列として返す

        // 工程別グラフのデータ取得
        $processData = Item::where('items.user_id', auth()->id())
            ->selectRaw('process_id, COALESCE(SUM(quantity * price), 0) as total')
            ->join('processes', 'items.process_id', '=', 'processes.id')
            ->whereYear('purchase_at', $selectedYear)
            ->whereMonth('purchase_at', $selectedMonth)
            ->groupBy('process_id')
            ->orderBy('total', 'desc')
            ->pluck('total', 'process_id'); 

        $processes = Process::where('user_id', auth()->id())
            ->whereIn('id', $processData->keys())
            ->get()
            ->pluck('name', 'id');
            
        $processChartData = $processData->map(function($total, $process_id) use ($processes) {
            return ['process' => $processes[$process_id], 'total' => $total];
        })->values();
        
        return view('home',compact('selectedYear', 'selectedMonth', 'monthlyData', 'years', 'exists', 'processChartData'));

    }
    
}