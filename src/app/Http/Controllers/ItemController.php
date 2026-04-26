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
}
