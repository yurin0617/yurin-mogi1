<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
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


Route::middleware('auth')->group(function () {
    // 1. プロフィール画面（マイページ）
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage.show');

    // 2. プロフィール編集画面（初回設定も兼ねる）
    // 表示 (GET)
    Route::get('/mypage/profile', [ProfileController::class, 'index'])->name('profile.setup');
    // 更新処理 (POST)
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 出品画面の表示
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    // 出品データの保存（POST）
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

    // 購入画面の表示
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    // 購入確定処理
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 商品に対して「いいね」する
    Route::post('/item/{item_id}/like', [ItemController::class, 'like'])->name('like.store');
    // 「いいね」を解除する
    Route::post('/item/{item_id}/unlike', [ItemController::class, 'unlike'])->name('like.destroy');

    // ログインしている人だけがコメントできる
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');

    // 購入画面を表示する (GET)
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    // 購入処理を実行する (POST)
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 2. 配送先変更（ここを追加！）
    // 表示：/purchase/address/1 などのURL
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    // 更新：フォームの送信先
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    });
