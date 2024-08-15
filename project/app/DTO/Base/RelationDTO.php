<?php

namespace App\DTO\Base;

readonly class RelationDTO
{
    /**
     * @param string $name
     * @param string $type
     * @param string $model
     */
    public function __construct(
        public string $name,
        public string $type,
        public string $model,
    )
    {
    }

    /**
     * @param array{name:string, type: string, model: string} $data
     * @return RelationDTO
     */
    public static function fromArray(array $data): RelationDTO
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            model: $data['model'],
        );
    }
}
