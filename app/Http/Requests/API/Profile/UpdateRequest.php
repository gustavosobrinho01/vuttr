<?php

namespace App\Http\Requests\API\Profile;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                'min:3',
                'max:255',
                Rule::unique((new User)->getTable())->ignore(auth()->id())
            ],
        ];
    }
}
