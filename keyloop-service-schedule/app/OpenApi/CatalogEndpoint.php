<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

final class CatalogEndpoint
{
    #[OA\Get(
        path: '/api/v1/dealerships',
        operationId: 'listDealerships',
        summary: 'List active dealerships with their currently bookable service types',
        parameters: [
            new OA\QueryParameter(name: 'page', schema: new OA\Schema(type: 'integer', minimum: 1, default: 1)),
            new OA\QueryParameter(name: 'per_page', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 20)),
        ],
        tags: ['Catalog'],
        responses: [
            new OA\Response(response: 200, description: 'Paginated active dealerships and their offered service types.'),
            new OA\Response(response: 422, description: 'Pagination query parameters are invalid.'),
        ],
    )]
    public function dealerships(): void {}
}
