<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    // 購入画面を表示
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        // 1. 売り切れチェック
        if ($item->purchase) {
            return redirect('/')->with('error', 'この商品は既に売り切れています。');
        }

        // 2. 自分が出品した商品かチェック（ここを追加！）
        if ($item->user_id === Auth::id()) {
            return redirect()->route('item.show', $item->id)->with('error', '自分の出品した商品は購入できません。');
        }

        return view('purchase.index', compact('item'));
    }

    // 購入を確定する処理（ここが一番難しいところ！）
    public function store(Request $request, $item_id)
    {
        // 1. 今ログインしているユーザー情報を取得
        $user = auth()->user();

        // 2. そのユーザーに紐づくプロフィール情報を取得
        $profile = $user->profile;

        // もしプロフィールがなかったら（nullだったら）
        if (!$profile) {
            return redirect()->route('profile.edit')
                ->with('error', '先にプロフィール情報（住所）を登録してください。');
        }

        // 3. 購入テーブルに保存（プロフィールからバトンを渡す）
        Purchase::create([
            'user_id'              => $user->id,
            'item_id'              => $item_id,
            'payment_method'       => $request->payment_method,
            'shipping_postal_code' => $profile->postal_code, // $user直下ではなく$profileから！
            'shipping_address'     => $profile->address,
            'shipping_building'    => $profile->building,
        ]);

        return redirect('/?tab=all')->with('message', 'ご購入ありがとうございました');
    }

    // 住所変更画面を表示
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $profile = auth()->user()->profile;

        return view('purchase.address', compact('item', 'profile'));
    }

    // 住所変更を保存（更新）する
    public function updateAddress(AddressRequest $request, $item_id)
    {
        $user = auth()->user();

        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // 2. 「購入画面」に戻る！ここがBの答えですね
        return redirect()->route('purchase.show', $item_id);
    }
}
