@extends('adminlte::page')

@section('title', '購入部品一覧')

@section('content_header')
    <h1>購入部品一覧</h1>
@stop

@section('content')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-primary">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex align-items-center justify-content-between my-4">
    {{-- csvエクスポート --}}
    <div class="ml-4">
        <a href="{{ route('csvExport') }}" class="btn btn-success" style="font-size: 1.3rem;">csvエクスポート</a>
    </div>
    {{-- 購入部品検索機能 --}}
    <div class="mr-4" style="font-size: 1.3rem;">
        <form action="{{ route('itemSearch') }}" method="GET">
            <label for="search">購入部品検索</label>
            <input type="text" id="search" name="keyword" value="{{ $keyword->name ?? '' }}">
            <input type="submit" value="検索">
        </form>
        <a class="btn btn-success mt-3 " style="font-size: 1.3rem;" href="{{route('item')}}" role="button">購入部品を全て表示する</a>
    </div>
</div>

{{-- 登録部品一覧表示 --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header ">
                <h3 class="card-title">購入部品一覧</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm">
                        <div class="input-group-append">
                            <a href="{{ url('items/add') }}" class="btn btn-secondary" style="font-size: 1.5rem;">購入部品登録</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('bulkDelete') }}" id="bulkDelete" method="POST">
            @csrf
            @method('DELETE')
            <div class="card-body table-responsive p-0 ">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>品名</th>
                            <th>工程名</th>
                            <th>単価</th>
                            <th>数量</th>
                            <th>購入日</th>
                            <th>詳細</th>
                            <th>画像</th>
                            <th>操作</th>
                            <th>
                                <button form="bulkDelete" type="button" class="btn btn-sm btn-danger mr-2" onclick="deletePost(this)">削除</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->process->name ?? '未分類'}}</td>
                            <td>{{ $item->price }} 円</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->purchase_at)->format('Y-m-d') }}</td>
                            <td>{{ $item->detail }}</td>
                            <td>
                                @if($item->image)
                                <a href="data:image/jpeg;base64,{{ $item->image }}" data-lightbox="group" data-title="{{ $item->name }}">
                                    <img src="data:image/jpeg;base64,{{ $item->image }}" alt="画像" width="100">
                                </a>
                                @else
                                <p>画像なし</p>
                                @endif
                            </td>
                            <td>
                                {{-- 編集ボタン --}}
                                <div class="d-flex justify-content-center">
                                    <a href="items/itemEdit/{{ $item->id }}" class="btn btn-sm btn-primary">編集</a>
                                </div>
                            </td>
                            <td>
                                {{-- 一括削除チェックボックス --}}
                                <dvi class="d-flex justify-content-center">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" style="cursor: pointer; transform: scale(1.5);">
                                </dvi>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <!-- Lightbox2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
    <style>
        .table td, .table th {
            text-align: center; /* 水平方向の中央揃え */
            vertical-align: middle; /* 垂直方向の中央揃え */
        }
    </style>
@stop

@section('js')
    <!-- jQuery（Lightbox2 は jQuery に依存） -->   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Lightbox2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    {{-- 削除確認 --}}
    <script>
        function deletePost(button){
            // 削除確認のポップアップ
            if(!confirm('本当に削除しますか？')){
                // キャンセルした場合、削除処理を止める
                return false;
            }
            // 確認語削除フォームを送信
            let form = document.getElementById('bulkDelete');
            if(form){
                form.submit();
            } else {
                console.error('削除フォームが見つかりません。')
            }
        }
    </script>
@stop
