<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CompteCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'success' => true,
            'data' => $this->collection,
            'pagination' => [
                'currentPage' => $this->currentPage(),
                'totalPages' => $this->lastPage(),
                'totalItems' => $this->total(),
                'itemsPerPage' => $this->perPage(),
                'hasNext' => $this->hasMorePages(),
                'hasPrevious' => $this->currentPage() > 1,
            ],
            'links' => [
                'self' => $request->fullUrl(),
                'next' => $this->nextPageUrl(),
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
            ],
        ];
    }
}
