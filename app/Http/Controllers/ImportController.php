<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

        // DBに保存できるヘッダーカラムか確認
        $expectedHeader = ['name', 'process_id', 'price', 'quantity', 'purchase_at', 'detail'];
        if($header !== $expectedHeader) {
            // ヘッダーカラムがあっていない場合、CSVを閉じる
            fclose($handle);
            return redirect('/items/add')->with('error', 'CSVのフォーマットが正しくありません。ヘッダーカラムを"name, process_id, price, quantity, purchase_at, detail" にして下さい。');
        }

        $batchSize = 1000; // 一度に保存するデータ
        $data = [];
        $lineNumber = 0;
        
        // csvデータの処理
        while(($row = fgetcsv($handle)) !== false ){

            // 行番号をインクリメント
            $lineNumber++;

            // 空の行はカウント無し
            if(empty(array_filter($row))){
                continue;
            };
            
            // ヘッダーの要素数をカウント
            $columnCount = count($expectedHeader);

            // 入力データがヘッダーの要素数より多い場合
            if(count($row) > $columnCount) {
                fclose($handle);
                return redirect('/items/add')->with('error', $lineNumber. '行目の入力形式を確認してください。ヘッダーの要素数と合っていないため、インポートできません。');
            }

            // 入力データがヘッダーの要素数より少ない場合
            if(count($row) < $columnCount){
                // データに空があった場合は、nullを入れる。不正な入力の場合、後のバリデーションで処理を止める
                $row = array_pad($row, 6, null);
            }

            // csvファイルのカラムとデータを連想配列として生成、$headerをkey、$rowをvalueとして配列を生成
            $rowData = array_combine($header, $row);
            
            // csvファイルデータのバリデーション
            $validator = Validator::make($rowData, [
                'name' => 'required|max:100',
                'process_id' => [
                'required',
                    Rule::exists('processes', 'id')->where(function ($query) {
                        $query->where('user_id', Auth::id()); // ログインユーザーが所有する processes.id のみ
                        }),
                    ],
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

            // 1000件ごとにDBへ保存
            if(count($data) >= $batchSize){
                Item::insert($data);
                $data = [];
            }

        }

        // データをデータベースに保存
        if(!empty($data)){
            Item::insert($data);
        }
                
        // ファイルを閉じる
        fclose($handle);

        return redirect('/items')->with('success', 'csvファイルを正常にインポートしました。');

    }
}
