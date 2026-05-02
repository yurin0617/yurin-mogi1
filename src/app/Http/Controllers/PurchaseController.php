<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入画面を表示
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 既に売り切れていたらトップへ戻す
        if ($item->purchase) {
            return redirect('/')->with('error', 'この商品は既に売り切れています。');
        }

        return view('purchase.index', compact('item'));
    }

    // 購入を確定する処理（ここが一番難しいところ！）
    public function store(Request $request, $item_id)
    {
        // 1. バリデーション（支払い方法や住所が空でないか）
        // 2. Purchasesテーブルに保存
        // 3. 完了後、トップページや完了画面へリダイレクト
    }
}
