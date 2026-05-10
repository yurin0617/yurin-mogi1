<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Like;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // --- ① 認証・プロフィールの交通整理 ---
        if (auth()->check()) {
            $user = auth()->user();

            // 1. メール認証がまだなら、認証案内画面へ（Fortify標準のルート名を使用）
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // 2. プロフィール設定（住所）がまだなら、設定画面へ
            // ルート名 'profile.setup' を使用
            if (empty($user->profile->address)) {
                return redirect()->route('profile.setup');
            }
        }

        // --- ② タブの初期値設定 ---
        $tab = $request->query('tab');
        if (!$tab) {
            // ログイン中なら 'mylist'、未ログインなら 'all' をデフォルトにする
            $tab = auth()->check() ? 'mylist' : 'all';
        }

        // --- ③ 商品データの取得ロジック ---
        // 自分以外の出品 ＆ 最新順
        $query = Item::where('user_id', '!=', auth()->id())->latest();

        // 検索キーワードがある場合
        $keyword = $request->query('keyword');
        if ($keyword) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        // マイリストタブの場合の絞り込み
        if ($tab === 'mylist') {
            if (auth()->check()) {
                $query->whereHas('likes', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            } else {
                // 未ログインなら何も表示しない
                $query->whereRaw('1 = 0');
            }
        }

        $items = $query->get();

        return view('index', compact('items', 'tab', 'keyword'));
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

        return redirect('/?tab=all')->with('success', '出品が完了しました！');
    }

    // いいね
    public function like($item_id)
    {
        Like::create([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
        ]);

        return back(); // 元の画面に戻る
    }
    // いいね解除
    public function unlike($item_id)
    {
        Like::where('user_id', auth()->id())->where('item_id', $item_id)->delete();

        return back();
    }
}
