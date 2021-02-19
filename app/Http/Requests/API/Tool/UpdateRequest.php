<?php

namespace App\Http\Requests\API\Tool;

use App\Models\Tool;
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
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'link' => ['required', 'url', 'min:3', 'max:255', Rule::unique((new Tool())->getTable())->where('user_id', auth()->id())->ignore($this->tool->id)],
            'description' => ['required', 'string', 'min:3', 'max:1000'],
            'tags' => ['required', 'array', 'min:1'],
        ];
    }
}
