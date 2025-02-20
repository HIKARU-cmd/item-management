<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function index(){
        // 月毎の購入データを取得
        $monthlyData = Item::selectRaw("DATE_FORMAT(purchase_at, '%Y-%m') as month, COALESCE(SUM(quantity * price), 0) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('home',compact('monthlyData'));

    }
    
}
