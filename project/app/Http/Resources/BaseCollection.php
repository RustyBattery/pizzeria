<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use JsonException;
use ReflectionClass;

class BaseCollection extends ResourceCollection
{
    public bool $preserveKeys = true;
    public static $wrap = 'data';

    public string $items_name = 'items';

    /**
     * @throws JsonException
     */
    public function withResponse($request, $response): void
    {
        $arrResponse = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        unset($arrResponse['links'], $arrResponse['meta']);

        $response->setContent(json_encode($arrResponse['data'], JSON_THROW_ON_ERROR));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $response = [
            $this->items_name => $this->collection,
            'count' => $this->count(),
        ];
        try {
            $pagination = [
                'current_page' => $this->currentPage(),
                'total_page' => ceil($this->total()/$this->perPage()),
                'per_page' => $this->perPage() ?? $this->count(),
            ];
            $response['pagination'] = $pagination;
        } catch (Exception $e){
            Log::info($e->getMessage(), ['exception' => $e]);
        }
        return $response;
    }
}
