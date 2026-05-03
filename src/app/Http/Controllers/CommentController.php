<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $item_id)
    {
        // ここに来た時点で、バリデーションはすでに成功している！
        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $item_id,
            'comment' => $request->comment,
        ]);

        return back()->with('message', 'コメントを投稿しました');
    }
}
