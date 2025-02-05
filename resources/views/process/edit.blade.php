@extends('adminlte::page')

@section('title', '工程名編集')

@section('content_header')
    <h1>工程名編集</h1>
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
                <form action="{{ route('processUpdate') }}" id="edit_{{ $process->id }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $process->id }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">工程名</label>
                            <input type="text" class="form-control" value="{{ $process->name }}" id="name" name="name" required>
                        </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" data-id="{{ $process->id }}" onclick="editPost(this,event)">変更する</button>
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
