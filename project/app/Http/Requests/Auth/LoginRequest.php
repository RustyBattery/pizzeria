<?php

namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|exists:users,email',
            'password' => 'required|string'
        ];
    }

    /**
     * @param $key
     * @param $default
     * @return array{email: string, password: string}
     */
    public function validated($key = null, $default = null): array
    {
        return parent::validated();
    }
}
