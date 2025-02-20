<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(Request $request){

        // 選択された年を取得(デフォルトでは今年を取得)
        $selectedYear = $request->input('year', Carbon::now()->year);

        // 選択or入力された年がBDの存在するか確認
        $exists = Item::whereYear('purchase_at', $selectedYear)->exists();

        $months = [];
        for($i=1; $i<=12; $i++){
            $months[sprintf('%04d-%02d', $selectedYear, $i)] = 0;
        }

        // 選択された年の購入データを取得
        $rawData = Item::selectRaw("DATE_FORMAT(purchase_at, '%Y-%m') as month, COALESCE(SUM(quantity * price), 0) as total")
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
        
        $years = Item::selectRaw("YEAR(purchase_at) as year")
        ->distinct() // 重複を排除
        ->orderBy('year', 'desc')
        ->pluck('year'); // 配列として返す
        
        return view('home',compact('selectedYear', 'monthlyData', 'years', 'exists'));

    }
    
}
