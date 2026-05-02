<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:20',
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'], // 000-0000形式
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpeg,png|max:2048', // 画像バリデーション
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号は000-0000の形式で入力してください',
            'address.required' => '住所を入力してください',
            'image_path.image' => '指定されたファイルが画像ではありません',
            'image_path.max' => '画像サイズは2MB以内でアップロードしてください',
        ];
    }
}
