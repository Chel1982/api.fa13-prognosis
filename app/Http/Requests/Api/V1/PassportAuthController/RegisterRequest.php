<?php

namespace App\Http\Requests\Api\V1\PassportAuthController;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
