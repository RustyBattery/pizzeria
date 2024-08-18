<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'address' => 'required|string',
            'comment' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ];
    }
}
