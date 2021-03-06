<?php

namespace App\Http\Requests\API\Profile;

use App\Models\User;
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
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => ['required', 'string', 'min:' . User::MIN_PASSWORD_LENGTH, 'max:255', new CurrentPasswordCheckRule],
            'password' => ['required', 'string', 'min:' . User::MIN_PASSWORD_LENGTH, 'max:255', 'confirmed', 'different:current_password'],
        ];
    }
}
