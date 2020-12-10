<?php

namespace App\Http\Requests\Container;

use Illuminate\Foundation\Http\FormRequest;

class CreateContainerRequest extends FormRequest
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
            'files'           => 'required|array|min:1',
            'files.*.name'    => 'required',
            'files.*.content' => 'required',
            'files.*.mime'    => 'required',
        ];
    }
}
