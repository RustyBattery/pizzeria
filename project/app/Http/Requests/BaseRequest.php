<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filters' => 'nullable',
            'filters.*.field' => 'required_unless:filters,null',
            'filters.*.operator' => 'required_unless:filters,null|in:=,!=,>,<,<=,>=',
            'filters.*.values' => 'required_unless:filters,null',
            'search' => 'nullable',
            'search.fields' => 'required_unless:search,null',
            'search.fields.*' => 'required_unless:search,null',
            'search.value' => 'required_unless:search,null',
            'sort' => 'nullable',
            'sort.field' => 'required_unless:sort,null',
            'sort.order_by' => 'required_unless:sort,null|in:asc,desc',
            'pagination' => 'nullable',
            'pagination.per_page' => 'required_unless:pagination,null|integer',
            'pagination.current_page' => 'required_unless:pagination,null|integer',
        ];
    }
}
