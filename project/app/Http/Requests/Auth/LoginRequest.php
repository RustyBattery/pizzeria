<?php

namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;


/**
 * @method array validated($key = null, $default = null)
 * @return array{email: string, password: string}
 */
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
     * @return object{email: string, password: string}
     */
    public function validatedAsObject(): object
    {
        return (object)$this->validated();
    }
}
