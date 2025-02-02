<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Process;
use Illuminate\Support\Carbon;

class ItemController extends Controller
{
    /**
     * 商品一覧
     */
    public function index()
    {
        // 商品一覧取得
        $items = Item::all();

        return view('item.index', compact('items'));
    }

    /**
     * 商品登録
     */
    public function add(Request $request)
    {
        // POSTリクエストのとき
        if ($request->isMethod('post')) {
            // バリデーション
            $this->validate($request,[
                'name' => 'required|max:100',
                'process_id' => 'required|exists:processes,id',
                'price' => 'required|integer|min:0',
                'quantity' => 'required|integer|min:1',
                'purchase_at' => 'required|date|before:tomorrow',
                'detail' => 'nullable|string|max:500',
                'image' => 'nullable|string',
            ]);

            $purchaseAtFormatted = Carbon::parse($request->purchase_at)->format('Y-m-d');
            // 商品登録
            Item::create([
                'user_id' => Auth::user()->id,
                'process_id' => $request->process_id,
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'purchase_at' => $purchaseAtFormatted,
                'detail' => $request->detail,
                'image' => $request->image,
            ]);
            
            return redirect('/items');
        }

        $processes = Process::all(); 
        return view('item.add', compact('processes'));
    
    }
}
