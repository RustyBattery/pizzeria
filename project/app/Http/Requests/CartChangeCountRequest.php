<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartChangeCountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'count' => 'required|integer|min:1'
        ];
    }

    /**
     * @return object{count: int}
     */
    public function validatedAsObject(): object
    {
        return (object)$this->validated();
    }
}
