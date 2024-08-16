<?php

namespace App\DTO\Base;

readonly class FilterDTO
{
    /**
     * @param string $field
     * @param string $operator Available operators: <, >, <=, >=, =, !=
     * @param array $values
     */
    public function __construct(
        public string $field,
        public string $operator,
        public array  $values,
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
