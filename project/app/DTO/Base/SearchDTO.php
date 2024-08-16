<?php

namespace App\DTO\Base;

readonly class SearchDTO
{
    /**
     * @param array $fields
     * @param string $value
     */
    public function __construct(
        public array  $fields,
        public string $value,
    )
    {
    }

    /**
     * @param array{fields:array, value:string} $data
     * @return SearchDTO
     */
    public static function fromArray(array $data): SearchDTO
    {
        return new self(
            fields: $data['fields'],
            value: $data['value']
        );
    }
}
