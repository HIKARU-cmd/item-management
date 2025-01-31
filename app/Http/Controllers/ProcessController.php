<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Process;

class ProcessController extends Controller
{
    //  工程名一覧
    public function index(){

        // 工程名一覧を取得
        $processes = Process::all();
        return view('process/add', compact('processes'));
    }

    // 工程名を追加
    public function add(Request $request){

    // POSTリクエストの時
    if($request->isMethod('post')){
        // バリデーション
        $request->validate([
            'name' => 'required'
        ]);

        Process::create([
            'name' => $request->name,
        ]);
    }
        return redirect('/processes');
    }

}
