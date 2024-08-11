<?php

namespace App\DTO\Base;

class FilterDTO
{
    /**
     * @param string $field
     * @param string $operator Available operators: <, >, <=, >=, =, !=
     * @param array $values
     */
    public function __construct(
        public readonly string $field,
        public readonly string $operator,
        public readonly array  $values,
    )
    {
    }

    /**
     * @param array{field:string, operator:string, values: array} $data
     * @return FilterDTO
     */
    public static function fromArray(array $data): FilterDTO
    {
        return new self(
            field: $data['field'],
            operator: $data['operator'],
            values: $data['values']
        );
    }
}
