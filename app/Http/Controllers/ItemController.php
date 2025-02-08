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
     * 部品一覧
     */
    public function index()
    {
        // 部品一覧取得
        $items = Item::all();

        return view('item.index', compact('items'));
    }

    /**
     * 部品登録
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

        /**
     * 部品一覧編集
     */
    public function itemEdit(Request $request){

        $item = Item::find($request->id);
        $processes = Process::all(); 
        
        if ($item === null) {
            return redirect('/items')->with('error', '指定されたアイテムが見つかりません。');
        }
        return view('item.edit', compact('item', 'processes'));
    }
        /**
     * 部品一覧更新
     */
    public function update(Request $request){
        $this->validate($request,[
            'id' => 'required|exists:items,id',
            'name' => 'required|max:100',
            'process_id' => 'required|exists:processes,id',
            'price' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:1',
            'purchase_at' => 'required|date|before:tomorrow',
            'detail' => 'nullable|string|max:500',
            'image' => 'nullable|string',
        ]);
        
        $purchaseAtFormatted = Carbon::parse($request->purchase_at)->format('Y-m-d');
        
        $item = Item::find($request->id);
        $item->name = $request->name;
        $item->process_id = $request->process_id;
        $item->price = $request->price;
        $item->quantity = $request->quantity;
        $item->purchase_at = $request->purchase_at;
        $item->detail = $request->detail;
        $item->image = $request->image;
        $item->save();

        return redirect('/items');
    }

        /**
     * 部品一覧削除
     */
    public function itemDelete(Request $request){
        $item = Item::find($request->id);

        if(!$item){
            return redirect('/items')->with('error', '商品が見つかりません。');
        }

        $item->delete();
        return redirect('/items');
    }

                    /**
     * 工程名検索
     */
    public function itemSearch(Request $request){
        $keyword = $request->input('keyword');
        $query = Item::query();
        $query->where('name', 'LIKE', "%{$keyword}%")
            ->orwhere('price', 'LIKE', "%{$keyword}%")
            ->orwhere('quantity', 'LIKE', "%{$keyword}%")
            ->orwhere('purchase_at', 'LIKE', "%{$keyword}%")
            ->orwhere('detail', 'LIKE', "%{$keyword}%")
            ->orwherehas('process', function($q) use ($keyword){
                $q->where('name', 'LIKE', "%{$keyword}%");
            });
        $items = $query->get();
        dd($items);

        return view('item/index', compact('keyword', 'items'));

    }

}
