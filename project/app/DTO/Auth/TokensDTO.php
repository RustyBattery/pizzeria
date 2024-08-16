<?php

namespace App\DTO\Auth;

readonly class TokensDTO
{
    /**
     * @param string $access_token
     * @param string $refresh_token
     */
    public function __construct(
        public string $access_token,
        public string $refresh_token,
    )
    {
    }

    /**
     * @param array{access: string, refresh:string} $data
     * @return TokensDTO
     */
    public static function fromArray(array $data): TokensDTO
    {
        return new self(
            access_token: $data['access'],
            refresh_token: $data['refresh']
        );
    }
}
