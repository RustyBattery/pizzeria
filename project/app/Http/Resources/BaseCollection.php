<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    public $preserveKeys = true;
    public static $wrap = 'data';

    public string $items_name = 'items';

    public function withResponse($request, $response)
    {
        $arrResponse = json_decode($response->getContent(), true);

        unset($arrResponse['links'], $arrResponse['meta']);

        $response->setContent(json_encode($arrResponse['data']));
    }
    public function toArray(Request $request): array
    {
        return [
            $this->items_name => $this->collection,
            'count' => $this->count(),
            'pagination' => [
                'current_page' => $this->currentPage(),
                'total_page' => ceil($this->total()/$this->perPage()),
                'per_page' => $this->perPage(),
            ]
        ];
    }
}
