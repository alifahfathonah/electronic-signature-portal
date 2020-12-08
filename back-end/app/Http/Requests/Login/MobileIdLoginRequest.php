<?php

namespace App\Http\Requests\Login;

use Illuminate\Foundation\Http\FormRequest;

class MobileIdLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idcode' => 'required|size:11',
            'phone'  => 'required|min:6|max:15|startsWith:+372,+370',
        ];
    }
}
