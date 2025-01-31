@extends('adminlte::page')

@section('title', '購入部品登録')

@section('content_header')
    <h1>購入部品登録</h1>
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
                <form method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">品名</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="名前" required>
                        </div>
                        <div class="form-group">
                            <label for="process_id">工程名</label>
                            {{-- <select class="form-control" id="process_id" name="process_id" placeholder="工程名" required>
                                @if($processes->isEmpty())
                                    <option>登録データがありません</option>
                                @else
                                    @foreach($processes as $process)
                                        <option value="{{ $process->process_id }}">{{ $process->name }}</option>
                                    @endforeach
                                @endif
                            </select> --}}
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
                            <input type="text" class="form-control" id="image" name="image" placeholder="画像" >
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">登録</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
