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
            'signature_type'                     => 'required|in:crypto,visual',
            'people'                             => 'array',
            'people.*.identifier'                => 'required',
            'people.*.identifier_type'           => 'required',
            'people.*.country'                   => 'required_if:people.*.identifier_type,idcode',
            'people.*.access_level'              => 'required|in:' . implode(',', $accessLevels),
            'people.*.visual_coordinates.page'   => 'required_if:signature_type,visual|numeric|min:1',
            'people.*.visual_coordinates.x'      => 'required_if:signature_type,visual|numeric|min:0',
            'people.*.visual_coordinates.y'      => 'required_if:signature_type,visual|numeric|min:0',
            'people.*.visual_coordinates.width'  => 'required_if:signature_type,visual|numeric|min:0',
            'people.*.visual_coordinates.height' => 'required_if:signature_type,visual|numeric|min:0',
            'files'                              => 'required|array|min:1',
            'files.*.name'                       => 'required',
            'files.*.content'                    => 'required',
            'files.*.mime'                       => 'required',
        ];
    }
}
