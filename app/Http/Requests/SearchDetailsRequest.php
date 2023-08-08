<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\CustomValidationErrorMessage;
use Illuminate\Foundation\Http\FormRequest;

class SearchDetailsRequest extends FormRequest
{
    use CustomValidationErrorMessage;

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
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180'
        ];
    }
}
