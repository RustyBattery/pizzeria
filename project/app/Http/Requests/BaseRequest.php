<?php

namespace App\Http\Requests;

use App\DTO\Base\FilterDTO;
use App\DTO\Base\PaginationDTO;
use App\DTO\Base\SearchDTO;
use App\DTO\Base\SortDTO;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{

    /**
     * @return string[]
     */
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

    /**
     * @param $key
     * @param $default
     * @return array{filters: array<FilterDTO>, search: SearchDTO|null, sort: SortDTO|null, pagination: PaginationDTO|null}
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        $filters = [];
        foreach ($data['filters'] ?? [] as $filter) {
            $filters[] = FilterDTO::fromArray($filter);
        }

        return [
            'filters' => $filters,
            'search' => isset($data['search']) ? SearchDTO::fromArray($data['search']) : null,
            'sort' => isset($data['sort']) ? SortDTO::fromArray($data['sort']) : null,
            'pagination' => isset($data['pagination']) ? PaginationDTO::fromArray($data['pagination']) : null,
        ];
    }
}
