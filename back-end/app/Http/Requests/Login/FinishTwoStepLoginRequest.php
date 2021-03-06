<?php

namespace App\Http\Requests\Login;

use Illuminate\Foundation\Http\FormRequest;

class FinishTwoStepLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => 'required',
        ];
    }
}
