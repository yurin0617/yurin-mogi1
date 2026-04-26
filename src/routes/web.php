<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// トップページ（/）にアクセスしたら、ItemControllerのindexメソッドを呼ぶ
Route::get('/', [ItemController::class, 'index']);
// {item} とすると、Laravelは自動でIDを探してくれます
Route::get('/item/{item}', [ItemController::class, 'show'])->where('item', '[0-9]+')->name('item.show');