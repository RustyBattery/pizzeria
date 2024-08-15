<?php

namespace App\DTO\Base;

readonly class PaginationDTO
{
    /**
     * @param int $per_page
     * @param int $current_page
     */
    public function __construct(
        public int $per_page,
        public int $current_page,
    )
    {
    }

    /**
     * @param array{per_page:int, current_page:int} $data
     * @return PaginationDTO
     */
    public static function fromArray(array $data): PaginationDTO
    {
        return new self(
            per_page: $data['per_page'],
            current_page: $data['current_page'],
        );
    }
}
