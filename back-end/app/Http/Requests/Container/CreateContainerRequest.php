<?php

namespace App\Http\Requests\Container;

use App\Models\SignatureContainer;
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
        $accessLevels = [
            SignatureContainer::LEVEL_SIGNER,
            SignatureContainer::LEVEL_VIEWER,
        ];

        return [
            'people'                   => 'required|array',
            'people.*.identifier'      => 'required',
            'people.*.identifier_type' => 'required',
            'people.*.country'         => 'required',
            'people.*.access_level'    => 'required|in:' . implode(',', $accessLevels),
            'files'                    => 'required|array|min:1',
            'files.*.name'             => 'required',
            'files.*.content'          => 'required',
            'files.*.mime'             => 'required',
        ];
    }
}
