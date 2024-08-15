<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method
 */
class CartChangeCountRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'count' => 'required|integer|min:1'
        ];
    }

    /**
     * @param $key
     * @param $default
     * @return object{count: int}
     */
    public function validated($key = null, $default = null): object
    {
        return (object)[
            'count' => parent::validated()['count'],
        ];
    }
}
