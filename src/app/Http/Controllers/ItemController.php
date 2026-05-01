<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Category;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        // Itemモデルから全データを取得
        $items = Item::latest()->get();
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

    public function create()
    {
        $categories = Category::all(); // 全カテゴリー取得
        return view('items.create', compact('categories'));
    }

    // storeメソッドの引数を変更
    public function store(ItemRequest $request)
    {
        $validated = $request->validated();

        // 1. 画像保存（パス取得）
        $validated['image_path'] = $request->file('image')->store('images', 'public');
        $validated['user_id'] = auth()->id();

        // 2. 不要な「category_ids」を抜き出す（createでエラーにならないようにするため）
        // 配列の「値」だけ取り出し、キーを消す処理
        $categoryIds = $validated['category_ids'];
        unset($validated['category_ids']); // ここで元の配列から削除

        // 3. 商品本体を作成
        $item = Item::create($validated);

        // 4. カテゴリーを紐付け
        $item->categories()->attach($categoryIds);

        return redirect('/')->with('success', '出品が完了しました！');
    }
}
