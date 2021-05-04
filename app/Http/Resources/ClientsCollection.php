<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resources = $this->resource->toArray();

        return [
            'data' => ClientResource::collection($this->collection),
            'links' => [
                'path' => $resources['path'],
                'firstPageUrl' => $resources['first_page_url'],
                'lastPageUrl' => $resources['last_page_url'],
                'nextPageUrl' => $resources['next_page_url'],
                'prevPageUrl' => $resources['prev_page_url']
            ],
            'meta' => [
                'currentPage' => $resources['current_page'],
                'from' => $resources['from'],
                'lastPage' => $resources['last_page'],
                'perPage' => $resources['per_page'],
                'to' => $resources['to'],
                'total' => $resources['total'],
                'count' => $this->collection->count()
            ]
        ];
    }
}