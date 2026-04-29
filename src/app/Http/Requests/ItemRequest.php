<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:20',
            'price' => 'required|integer|min:0',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'condition'   => 'required|string',
            'brand'       => 'nullable|string|max:255',
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer|exists:categories,id', // 各要素がDBにあるか確認
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => '商品名は必須です。',
            'name.min' => '20文字以内で説明してください。',
            'price.required' => '価格は必須です。',
            'price.integer' => '価格は半角数字で入力してください。',
            'price.min' => '価格は0以上を入力してください。',
            'image.required' => '商品画像を選択してください。',
            'image.image' => '画像ファイルをアップロードしてください。',
            'description.required' => '説明は必須です。',
            'description.max' => '255文字以内で説明してください。',
            'category_ids.required' => 'カテゴリーは必須です。',
            'condition.required' => '状態は必須です。',
        ];
    }
}
