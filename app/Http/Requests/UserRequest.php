<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UserRequest extends FormRequest
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
            'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
            'avatar' => 'mimes:jpeg,bmp,png,gif|dimensions:min_width=208,min_height=208',
        ];
    }

    public function messages()
    {
        return [
            'avatar.mimes' => '個人照片必須是 jpeg, bmp, png, gif 格式的圖片',
            'avatar.dimensions' => '圖片的清晰度不夠，寬和高需要 208px 以上',
            'name.unique' => '用戶名已被佔用，請重新填寫',
            'name.regex' => '用戶名只支持英文、數字、減號(-)、底線(_)',
            'name.between' => '用戶名必須介於3到25個字',
            'name.required' => '用戶名必須填寫',
        ];
    }
}
