@extends('adminlte::page')

@section('title', '工程名登録')

@section('content_header')
    <h1>工程名登録及び一覧表示</h1>
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
        </div>
    </div>

{{-- 工程名登録フォーム --}}
    <div class="card card-primary">
        <form method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="name">工程名登録</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="工程名" required>
                </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">登録</button>
            </div>
        </form>
    </div>

{{-- 工程名一覧表示 --}}
    <div class="col-12 mt-5" >
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
                                <td>編集 削除</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop

@section('css')
@stop

@section('js')
@stop
