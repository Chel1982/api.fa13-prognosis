<?php

namespace App\Http\Requests\Api\V1\CommentController;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'id' => 'required|integer',
            'type' => ['required', Rule::in(['game', 'tournament'])],
            'comment' => 'required|string|min:2|max:1500'
        ];
    }
}
