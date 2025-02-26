<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Process;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * 部品一覧
     */
    public function index()
    {
        // 部品一覧取得
        $items = Item::where('user_id', auth()->id())->get();

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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // 購入日を年月日までの表示としている
            $purchaseAtFormatted = Carbon::parse($request->purchase_at)->format('Y-m-d');

            $image_base64 = null;
            if($request->hasFile('image')){
                // ファイルを`storage/app/public/img/` に保存
                $image = $request->file('image');
                // DBにはstorage/img/sample.jpgの形で保存
                $image_base64 = base64_encode(file_get_contents($image->getRealPath()));
            }

            // 商品登録
            Item::create([
                'user_id' => Auth::user()->id,
                'process_id' => $request->process_id,
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'purchase_at' => $purchaseAtFormatted,
                'detail' => $request->detail,
                'image' => $image_base64,
            ]);
            
            return redirect('/items')->with('success', '登録されました。');
        }

        $processes = Process::where('user_id', auth()->id())->get(); 
        return view('item.add', compact('processes'));
    
    }

    /**
     * 部品一覧編集
     */
    public function itemEdit(Request $request){

        $item = Item::find($request->id);
        $processes = Process::where('user_id', auth()->id())->get(); 
        
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $purchaseAtFormatted = Carbon::parse($request->purchase_at)->format('Y-m-d');
        
        $item = Item::find($request->id);

        if(!$item){
            return redirect('/items')->with('error', '指定されたアイテムが見つかりません。');
        }

        if($request->hasfile('image')){
            $image = $request->file('image');
            $item->image = base64_encode(file_get_contents($image->getRealPath()));
        } else {
            $item->image = $request->current_image;
        }

        $item->name = $request->name;
        $item->process_id = $request->process_id;
        $item->price = $request->price;
        $item->quantity = $request->quantity;
        $item->purchase_at = $request->purchase_at;
        $item->detail = $request->detail;
        $item->save();

        return redirect('/items')->with('success', '編集が成功しました。');
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
     * 一括削除
     */
    public function bulkDelete(Request $request){
        // 選択されたIDの取得(配列で取得)
        $ids = $request->input('ids');
        // チェックボックスが選択されていない場合の処理
        if($ids === null){
            return redirect('/items')->with('error', '削除選択されていません。');
        }

        foreach($ids as $id){
            $this->itemDelete(new Request(['id' => $id]));
        }

        return redirect('/items')->with('success', '削除が成功しました。');

    }


    /**
     * 工程名検索
     */
    public function itemSearch(Request $request){

        $keyword = $request->input('keyword');

        $query = Item::where('user_id', auth()->id())
            ->where(function ($query) use ($keyword) {
                $query->where ('name', 'LIKE', "%{$keyword}%")
                ->orwhere('price', 'LIKE', "%{$keyword}%")
                ->orwhere('quantity', 'LIKE', "%{$keyword}%")
                ->orwhere('purchase_at', 'LIKE', "%{$keyword}%")
                ->orwhere('detail', 'LIKE', "%{$keyword}%")
                ->orwherehas('process', function($q) use ($keyword){
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            });

        $items = $query->get();

        return view('item/index', compact('keyword', 'items'));

    }

}
