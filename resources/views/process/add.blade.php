@extends('adminlte::page')

@section('title', '工程名登録')

@section('content_header')
    <h1>工程名登録及び一覧表示</h1>
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

<div class="row">
    <div class="col-md-10">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

{{-- 工程名登録フォーム --}}
<div class="card card-primary" style="font-size: 1.3rem;">
    <form method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <h3><label for="name">工程名登録</label></h3>
                <input type="text" class="form-control" id="name" name="name" placeholder="工程名" required>
            </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">登録</button>
        </div>
    </form>
</div>

{{-- 工程名検索機能 --}}
<div class="text-right mt-3" style="font-size: 1.3rem;">
    <form action="{{ route('processSearch') }}" method="GET">
        <label for="search">工程名検索</label>
        <input type="text" id="search" name="keyword" value="{{ $keyword->name ?? '' }}">
        <input type="submit" value="検索">
    </form>
    <a class="btn btn-success mt-3" style="font-size: 1.3rem;" href="{{route('process')}}" role="button">工程名を全て表示</a>
</div>

{{-- 工程名一覧表示 --}}
<div class="col-12 mt-5">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">工程名一覧</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>工程名</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($processes as $process)
                        <tr>
                            <td>{{ $process->id }}</td>
                            <td>{{ $process->name }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    {{-- 編集ボタン --}}
                                    <a href="{{ route('processEdit', $process->id) }}"><button type="button" class="btn btn-sm btn-primary mr-2">編集</button></a>
                                    
                                    {{-- 削除ボタン --}}
                                    <form action="{{ route('processDelete', $process->id) }}" id="delete_{{ $process->id }}" method="POST">
                                        @csrf   
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger mr-2" data-id="{{ $process->id }}" onclick="deletePost(this)">削除</button>
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

@stop

@section('css')
<style>
    .table td, .table th {
        text-align: center; /* 水平方向の中央揃え */
        vertical-align: middle; /* 垂直方向の中央揃え */
    }
</style>

@stop

@section('js')
<script>
    function deletePost(button){
        // 削除確認のポップアップ
        if(!confirm('購入部品一覧に登録されているレコードも全て削除されますが、本当に削除しますか？')){
            // キャンセルした場合、削除処理を止める
            return false;
        }
        // 確認語削除フォームを送信
        document.getElementById('delete_' + button.dataset.id).submit()
    }
</script>

@stop
