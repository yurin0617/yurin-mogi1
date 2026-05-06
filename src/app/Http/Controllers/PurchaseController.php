<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    // 購入画面を表示
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = auth()->user();

        // 1. 売り切れチェック
        if ($item->purchase) {
            return redirect('/')->with('error', 'この商品は既に売り切れています。');
        }

        // 2. 自分が出品した商品かチェック（ここを追加！）
        if ($item->user_id === Auth::id()) {
            return redirect()->route('item.show', $item->id)->with('error', '自分の出品した商品は購入できません。');
        }
        // 3. 住所の準備 (新機能) ---
        // セッションに新しい住所があればそれを使い、なければプロフィールの住所を使う
        // ※ $user->profile が存在することを前提としています
        $display_address = [
            'postal_code' => session('new_address.postal_code') ?? $user->profile->postal_code ?? '',
            'address'     => session('new_address.address')     ?? $user->profile->address ?? '',
            'building'    => session('new_address.building')    ?? $user->profile->building ?? '',
        ];

        return view('purchase.index', compact('item', 'display_address'));
    }

    // 購入を確定する処理（ここが一番難しいところ！）
    public function store(PurchaseRequest $request, $item_id)
    {
        // 1. 今ログインしているユーザー情報を取得
        $user = auth()->user();

        // 2. そのユーザーに紐づくプロフィール情報を取得
        $profile = $user->profile;

        // 1. プロフィールが万が一なかった時のガード（残しておくのが安心！）
        if (!$profile && !session()->has('new_address')) {
            return redirect()->route('profile.edit')
                ->with('error', '配送先情報が見つかりません。');
        }

        // 2. 実際に保存する住所を決定（セッション優先 ＞ DB）
        $postal_code = session('new_address.postal_code') ?? $profile->postal_code;
        $address     = session('new_address.address')     ?? $profile->address;
        $building    = session('new_address.building')    ?? $profile->building;

        // 3. 購入テーブルに保存（決定した変数 $address などを使う！）
        Purchase::create([
            'user_id'              => $user->id,
            'item_id'              => $item_id,
            'payment_method'       => $request->payment_method,
            'shipping_postal_code' => $postal_code,
            'shipping_address'     => $address,
            'shipping_building'    => $building,
        ]);

        // 💡 ここでお掃除！
        session()->forget('new_address');

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
        // 1. 入力内容をセッションに一時保存する
        // 'new_address' という名前の箱に入れておくイメージ
        session(['new_address' => $request->only(['postal_code', 'address', 'building'])]);

        // 2. 購入画面に戻る
        return redirect()->route('purchase.show', $item_id);
    }
}
