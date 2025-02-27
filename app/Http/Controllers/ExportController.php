<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    //DBに保存されたitemsテーブルをcsv形式でエクスポート
    public function csvExport(){
        
        $fileName = 'item_list.csv';
        $response = new StreamedResponse(function(){
            $handle = fopen('php://output', 'w');

            // BOMを追加（UTF-8 BOM）
            fwrite($handle, "\xEF\xBB\xBF");
            
            //ヘッダーを追加
            fputcsv($handle, ["name", "process_id","process_name", "price", "quantity", "purchase_at", "detail", "image", "created_at"]);
            
            // データ取得
            $items = Item::where('user_id', auth()->id())->get();
            
            foreach($items as $item){
                fputcsv($handle, [
                    $item->name,
                    $item->process->id,
                    $item->process->name,
                    $item->price,
                    $item->quantity,
                    $item->purchase_at,
                    $item->detail,
                    $item->image ? "画像あり" : "画像なし",
                    $item->created_at
                ]);
            }
            
            fclose($handle);
            
        });
        
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;

    }
}
