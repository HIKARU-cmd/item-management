@extends('adminlte::page')

@section('title', '購入部品編集')

@section('content_header')
    <h1>購入部品編集</h1>
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

        <div class="card card-primary">
            <form action="{{ route('itemUpdate') }}" id="edit_{{ $item->id }}" method="POST" enctype="multipart/form-data">
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
                            @if(isset($item->image))
                                <div class="d-flex">
                                    <p class="mr-4 mt-4">現在の画像</p>
                                    <a href="{{ asset($item->image) }}" data-lightbox="group" data-title="{{ $item->name }}" class="mb-3">
                                        <img src="{{ asset($item->image) }}" alt="画像" width="100">
                                    </a>
                                </div>
                            @endif
                        <input type="file" class="form-control" id="image" name="image" value="{{ $item->image ?? ''}}">
                    </div>
                </div>

                <div class="card-footer">
                    <button type="button" class="btn btn-primary" data-id="{{ $item->id }}" onclick="editPost(event, this)">変更する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <!-- Lightbox2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
@stop

@section('js')
<!-- jQuery（Lightbox2 は jQuery に依存） -->   
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Lightbox2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<script>
    // 変更確認
    function editPost(event, button){
        // 変更確認のポップアップ
        if(!confirm('本当に変更しますか？')){
            // キャンセルした場合、変更処理を止める
            event.preventDefault();
            return false;
        }
        // 変更後、変更フォームを送信
        document.getElementById('edit_' + button.dataset.id).submit()
    }
</script>
@stop
