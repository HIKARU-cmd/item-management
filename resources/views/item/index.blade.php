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

{{-- 購入部品検索機能 --}}
<div class="text-right mt-5 mb-5" style="font-size: 1.3rem;">
    <form action="{{ route('itemSearch') }}" method="GET">
        <label for="search">購入部品検索</label>
        <input type="text" id="search" name="keyword" value="{{ $keyword->name ?? '' }}">
        <input type="submit" value="検索">
    </form>
    <a class="btn btn-primary mt-3" style="font-size: 1.5rem;" href="{{route('item')}}" role="button">全購入部品表示</a>
</div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">購入部品一覧</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm">
                        <div class="input-group-append">
                            <a href="{{ url('items/add') }}" class="btn btn-default" style="font-size: 1.5rem;">購入部品登録</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->process->name ?? '未分類'}}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->purchase_at)->format('Y-m-d') }}</td>
                                <td>{{ $item->detail }}</td>
                                <td>{{ $item->image }}</td>
                                <td>
                                    <div class="d-flex">
                                        {{-- 編集ボタン --}}
                                        <a href="items/itemEdit/{{ $item->id }}"><button type="button" class="btn btn-sm btn-primary mr-2">編集</button></a>
                                        
                                        {{-- 削除ボタン --}}
                                        <form action="items/itemDelete/{{ $item->id }}" id="delete_{{ $item->id }}" method="POST">
                                            @csrf   
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger mr-2" data-id="{{ $item->id }}" onclick="deletePost(this,event)">削除</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')
<script>
    function deletePost(e){
        // 削除確認のポップアップ
        if(!confirm('本当に削除しますか？')){
            // キャンセルした場合、削除処理を止める
            event.preventDefault();
            return false;
        }
        // 確認語削除フォームを送信
        document.getElementById('delete_' + e.dataset.id).submit()
    }
</script>
@stop
