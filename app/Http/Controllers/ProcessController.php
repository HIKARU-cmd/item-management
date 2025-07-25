<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Process;
use Illuminate\Support\Facades\Auth;

class ProcessController extends Controller
{
    //  工程名一覧
    public function index(){

        // 工程名一覧を取得
        $userId = auth()->id();
        $processes = Process::where('user_id', $userId)->get();
        return view('process/add', compact('processes'));
    }

    // 工程名を追加
    public function add(Request $request){
        // POSTリクエストの時
        if($request->isMethod('post')){
            // バリデーション
            $request->validate([
                'name' => 'required|max:100'
            ]);

            Process::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
            ]);
            return redirect('/processes')->with('success', '登録されました。');
        }
        return redirect('/processes');
    }

            /**
     * 工程名一覧編集
     */
    public function processEdit(Request $request){

        $process = Process::find($request->id);
        
        if ($process === null) {
            return redirect('/processes')->with('error', '指定されたアイテムが見つかりません。');
        }
        return view('process.edit', compact( 'process'));
    }
    
            /**
     * 工程名一覧更新
     */
    public function update(Request $request){

        $this->validate($request,[
            'id' => 'required|exists:processes,id',
            'name' => 'required|max:100',
        ]);

        $process = Process::find($request->id);
        $process->name = $request->name;
        $process->save();

        return redirect('/processes')->with('success', '編集が成功しました。');
    }

            /**
     * 工程名、部品一覧削除
     */
    public function processDelete(Request $request){
        $process = Process::find($request->id);
        
        if(!$process){
            return redirect('/processes')->with('error', '見つかりません。');
        }
        
        $process->items()->delete();
        $process->delete();
        return redirect('/processes')->with('success', '削除が成功しました。');
    }

                /**
     * 工程名検索
     */
    public function processSearch(Request $request){
        $keyword = $request->input('keyword');

        $query = Process::where('user_id', auth()->id())
            ->where('name', 'LIKE', "%{$keyword}%");
            

        $processes = $query->get();

        return view('process/add', compact('keyword', 'processes'));

    }

}
