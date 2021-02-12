<?php

namespace App\Http\Requests\API\Profile;

use App\Rules\CurrentPasswordCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'current_password' => [
                'required',
                'string',
                'min:6',
                'max:255',
                new CurrentPasswordCheckRule
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
                'different:current_password'
            ],
        ];
    }
}
