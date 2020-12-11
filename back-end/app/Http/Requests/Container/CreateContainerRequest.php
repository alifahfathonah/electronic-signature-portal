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
            'signers'                             => 'array',
            'signers.*.identifier'                => 'required',
            'signers.*.identifier_type'           => 'required',
            'signers.*.country'                   => 'required_if:people.*.identifier_type,idcode',
            'signers.*.access_level'              => 'required|in:' . implode(',', $accessLevels),
            'signers.*.visual_coordinates.page'   => 'required_if:signature_type,visual|numeric|min:1',
            'signers.*.visual_coordinates.x'      => 'required_if:signature_type,visual|numeric|min:0',
            'signers.*.visual_coordinates.y'      => 'required_if:signature_type,visual|numeric|min:0',
            'signers.*.visual_coordinates.width'  => 'required_if:signature_type,visual|numeric|min:0',
            'signers.*.visual_coordinates.height' => 'required_if:signature_type,visual|numeric|min:0',
            'files'                              => 'required|array|min:1',
            'files.*.name'                       => 'required',
            'files.*.content'                    => 'required',
            'files.*.mime'                       => 'required',
        ];
    }
}
