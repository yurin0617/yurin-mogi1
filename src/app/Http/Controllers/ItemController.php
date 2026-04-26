<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        // Itemモデルから全データを取得
        $items = Item::all();
        // 'index' という名前のビュー（画面）に、取得した $items を渡す
        return view('index', compact('items'));
    }
    public function show($id)
    {
        // 指定されたIDの商品を取得。なければ自動で404エラー
        $item = Item::findOrFail($id);

        // items.show ビューへデータを渡す
        return view('items.show', compact('item'));
    }
}
