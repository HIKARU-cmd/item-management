@extends('adminlte::page')

@section('title', '購入部品登録')

@section('content_header')
    <h1>購入部品登録</h1>
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

{{-- CSVファイルインポート --}}
<form action="{{ route('csvImport') }}" method="POST" id="import" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
        <div class="form-group">
            <label for="csvFile">CSVファイル選択</label>
            <input type="file" id="csvFile" name="csvFile" class="form-control">
        </div>
        <button type="button" class="btn btn-success" data-id="import" onclick="registerPost(this)">CSVインポート</button>
    </div>
</form>

{{-- 購入部品登録 --}}
<div class="row">
    <div class="col-md-10">

        <div class="card card-primary">
            <form method="POST" id="register" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">品名</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="名前" required>
                    </div>


                    <div class="form-group">
                        <label for="process_id">工程名</label>
                        <select class="form-control" id="process_id" name="process_id" placeholder="工程名" required>
                            @if($processes->isEmpty())
                                <option>登録データがありません。登録フォームより登録してください。</option>
                            @else
                                <option value="">選択してください</option>
                                @foreach($processes as $process)
                                    <option value="{{ $process->id }}">{{ $process->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="price">単価</label>
                        <input type="text" class="form-control" id="price" name="price" placeholder="単価" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">数量</label>
                        <input type="text" class="form-control" id="quantity" name="quantity" placeholder="数量" required>
                    </div>

                    <div class="form-group">
                        <label for="purchase_at">購入日</label>
                        <input type="date" class="form-control" id="purchase_at" name="purchase_at" placeholder="購入日" required>
                    </div>

                    <div class="form-group">
                        <label for="detail">詳細</label>
                        <input type="text" class="form-control" id="detail" name="detail" placeholder="詳細説明" >
                    </div>
                    <div class="form-group">
                        <label for="image">画像</label>
                        <input type="file" class="form-control" id="image" name="image" placeholder="画像" >
                    </div>
                </div>

                <div class="card-footer">
                    <button type="button" class="btn btn-primary" data-id="register" onclick="registerPost(this)">登録</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
@stop

@section('js')
<script>
    // 登録確認
    function registerPost(button){
        // 変更確認のポップアップ
        if(!confirm('登録しますか？')){
            // キャンセルした場合、変更処理を止める
            return false;
        }
        let formId = button.getAttribute('data-id');
        let form = document.getElementById(formId);

        // 変更後、変更フォームを送信
        if(form){
            form.submit();
        } else {
            alert('エラー:フォームが見つかりません。');
        }
    }
</script>
@stop
