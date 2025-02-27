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
        
        // ファイルの内容確認 "r"は読み込み専用
        $handle = fopen($file, "r");
        if(!$handle){
            return redirect('/items/add')->with('error', 'ファイルを開けません。');
        }
        
        // ヘッダー行を配列で格納
        $header = fgetcsv($handle);
        $header[0] = str_replace("\u{FEFF}", '', $header[0]);
        $lineNumber = 0;

        // csvデータの処理
        while(($row = fgetcsv($handle)) !== false ){

            // 空の行はカウント無し
            if(empty(array_filter($row))){
                continue;
            };

            // 行番号をインクリメント
            $lineNumber++;
            // csvファイルのカラムとデータを連想配列として生成、$headerをkey、$rowをvalueとして配列を生成
            $rowData = array_combine($header, $row);
            
            // csvファイルデータのバリデーション
            $validator = Validator::make($rowData, [
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
                return redirect('/items')->withErrors($validator)->with('error', $lineNumber. '行目の入力形式を確認してください。インポートできません。');
            }

            $data[] = [
                'user_id' => Auth::user()->id,
                'name' => $rowData['name'],
                'process_id' => $rowData['process_id'],
                'price' => $rowData['price'],
                'quantity' => $rowData['quantity'],
                'purchase_at' => $rowData['purchase_at'],
                'detail' => $rowData['detail'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

        }
                
        // ファイルを閉じる
        fclose($handle);

            // データをデータベースに保存
            Item::insert($data);

        return redirect('/items')->with('success', 'csvファイルを正常にインポートしました。');

    }
}
