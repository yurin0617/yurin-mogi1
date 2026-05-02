<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        // プロフィールデータを持ってマイページを表示
        return view('profile.show', compact('user'));
    }

    public function index()
    {
        $user = Auth::user();
        // プロフィールが存在すればその名前、なければUserテーブルの名前を初期値にする
        $defaultName = $user->profile->name ?? $user->name;

        return view('profile.setup', compact('user', 'defaultName'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        // 画像の保存処理
        $imagePath = $user->profile->image_path ?? null; // 現在の画像パスを取得
        if ($request->hasFile('image_path')) {
            // もし既に画像が設定されていたら、古いファイルを削除してサーバーを綺麗に保つ
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            // 新しい画像を storage/app/public/profiles に保存
            $imagePath = $request->file('image_path')->store('profiles', 'public');
        }
        // Userテーブルの名前も更新する場合
        $user->update(['name' => $request->name]);
        // プロフィールの更新または作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
                'image_path' => $imagePath, // DBに保存されるパス
            ]
        );

        return redirect('/')->with('message', 'プロフィールを更新しました');
    }
}
