<?php

namespace App\Http\Requests\Login;

use Illuminate\Foundation\Http\FormRequest;

class SmartIdLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'country' => 'in:EE,LV,LT',
            'idcode' => 'required|size:11',
        ];
    }
}
