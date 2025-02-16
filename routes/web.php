<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

// ログイン必須のルーティング
Route::middleware('auth')->group(function(){

    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::prefix('items')->group(function () {
        Route::get('/', [App\Http\Controllers\ItemController::class, 'index'])->name('item');
        Route::get('/add', [App\Http\Controllers\ItemController::class, 'add']);
        Route::post('/add', [App\Http\Controllers\ItemController::class, 'add']);
        Route::get('/search', [App\Http\Controllers\ItemController::class, 'itemSearch'])->name('itemSearch');
        Route::post('/update', [App\Http\Controllers\ItemController::class, 'update'])->name('itemUpdate');
        Route::get('/itemEdit/{id}', [App\Http\Controllers\ItemController::class, 'itemEdit']);
        Route::get('/itemDelete/{id}', function(){
            return redirect('/items')->with('error', '不正な操作です');
        });
        Route::delete('/itemDelete/{id}', [App\Http\Controllers\ItemController::class, 'itemDelete']);
        Route::get('/bulkDelete', function(){
            return redirect('/items')->with('error', '不正な操作です');
        });
        Route::delete('/bulkDelete', [App\Http\Controllers\ItemController::class, 'bulkDelete'])->name('bulkDelete');
    });

    Route::post('/csvImport', [App\Http\Controllers\ImportController::class, 'csvImport'])->name('csvImport');

    Route::get('/csvExport', [App\Http\Controllers\ExportController::class, 'csvExport'])->name('csvExport');
    
    Route::prefix('processes')->group(function () {
        Route::get('/', [App\Http\Controllers\ProcessController::class, 'index'])->name('process');
        Route::post('/', [App\Http\Controllers\ProcessController::class, 'add']);
        Route::get('/add', [App\Http\Controllers\ProcessController::class, 'add']);
        Route::get('/search', [App\Http\Controllers\ProcessController::class, 'processSearch'])->name('processSearch');
        Route::post('/update', [App\Http\Controllers\ProcessController::class, 'update'])->name('processUpdate');
        Route::get('/processEdit/{id}', [App\Http\Controllers\ProcessController::class, 'processEdit'])->name('processEdit');
        Route::get('/processDelete/{id}', function(){
            return redirect('/processes')->with('error', '不正な操作です');
        });
        Route::delete('/processDelete/{id}', [App\Http\Controllers\ProcessController::class, 'processDelete'])->name('processDelete');
    });
    
});



