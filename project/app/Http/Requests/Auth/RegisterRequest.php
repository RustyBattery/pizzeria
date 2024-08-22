<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method validated($key = null, $default = null)
 * @return array{name:string, email:string, phone:string, password:string}
 */

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|regex:/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/',
            'password' => 'required|string|min:5'
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => "The phone format must be +7(xxx)xxx-xx-xx.",
        ];
    }
}
