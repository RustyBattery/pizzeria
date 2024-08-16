<?php

namespace App\DTO\Base;

readonly class SortDTO
{
    /**
     * @param string $field
     * @param string $order_by asc|desc
     */
    public function __construct(
        public string $field,
        public string $order_by,
    )
    {
    }

    /**
     * @param array{field:string, order_by: string} $data
     * @return SortDTO
     */
    public static function fromArray(array $data): SortDTO
    {
        return new self(
            field: $data['field'],
            order_by: $data['order_by'],
        );
    }

}
