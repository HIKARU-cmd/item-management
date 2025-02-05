@extends('adminlte::page')

@section('title', '購入部品編集')

@section('content_header')
    <h1>購入部品編集</h1>
@stop

@section('content')
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

            <div class="card card-primary">
                <form action="{{ route('itemUpdate') }}" id="edit_{{ $item->id }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $item->id }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">品名</label>
                            <input type="text" class="form-control" value="{{ $item->name }}" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="process_id">工程名</label>
                            <select class="form-control" id="process_id" name="process_id" required>
                                @foreach($processes as $process)
                                    <option value="{{ $process->id }}" @if($process->id == $item->process->id) selected @endif>{{ $process->name ?? '未分類' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">単価</label>
                            <input type="text" class="form-control" value="{{ $item->price }}" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity">数量</label>
                            <input type="text" class="form-control" value="{{ $item->quantity }}" id="quantity" name="quantity" required>
                        </div>

                        <div class="form-group">
                            <label for="purchase_at">購入日</label>
                            <input type="date" class="form-control" value="{{ \Carbon\Carbon::parse($item->purchase_at)->format('Y-m-d') }}" id="purchase_at" name="purchase_at" required>
                        </div>

                        <div class="form-group">
                            <label for="detail">詳細</label>
                            <input type="text" class="form-control" value="{{ $item->detail }}" id="detail" name="detail">
                        </div>
                        <div class="form-group">
                            <label for="image">画像</label>
                            <input type="text" class="form-control" value="{{ $item->image }}" id="image" name="image">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" data-id="{{ $item->id }}" onclick="editPost(this,event)">変更する</button>
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
    function editPost(e){
        // 変更確認のポップアップ
        if(!confirm('購入部品一覧も変更されますが、本当に変更しますか？')){
            // キャンセルした場合、変更処理を止める
            event.preventDefault();
            return false;
        }
        // 変更後、変更フォームを送信
        document.getElementById('edit_' + e.dataset.id).submit()
    }
</script>

@stop
