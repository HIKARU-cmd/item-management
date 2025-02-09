<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    //csvファイルインポート
    public function csvImport(Request $request){

        // バリデーション
        $request->validate([
            'csvFile' => 'required|mimes:csv,txt'
        ]);
        
        // csvファイルを取得
        $file = $request->file('csvFile');
        
        // ファイルの内容確認
        $handle = fopen($file, "r");
        if(!$handle){
            return redirect('/items/add')->with('error', 'ファイルを開けません。');
        }
        
        // ヘッダー行を読み込み
        $header = fgetcsv($handle);
        $header[0] = str_replace("\u{FEFF}", '', $header[0]);
        
        // csvデータの処理
        while(($row = fgetcsv($handle)) !== false ){
            // csvファイルのカラムとデータを連想配列として格納
            $data = array_combine($header, $row);
            
            // csvファイルデータのバリデーション
            $validator = Validator::make($data, [
                'name' => 'required|max:100',
                'process_id' => 'required|exists:processes,id',
                'price' => 'required|integer|min:0',
                'quantity' => 'required|integer|min:1',
                'purchase_at' => 'required|date|before:tomorrow',
                'detail' => 'nullable|string|max:500',
            ]);

            // バリデーション失敗時のエラー表示
            if ($validator->fails()) {
                fclose($handle);
                return redirect('/items')->withErrors($validator);
            }

            // データをデータベースに保存
            Item::create([
                'user_id' => Auth::user()->id,
                'name' => $data['name'],
                'process_id' => $data['process_id'],
                'price' => $data['price'],
                'quantity' => $data['quantity'],
                'purchase_at' => $data['purchase_at'],
                'detail' => $data['detail'] ?? null,
            ]);
        }

        // ファイルを閉じる
        fclose($handle);

        return redirect('/items')->with('success', 'csvファイルを正常にインポートしました。');

    }
}
