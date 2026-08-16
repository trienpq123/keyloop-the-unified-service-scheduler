<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Catalog\Queries\ListActiveDealerships;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListDealershipsRequest;
use App\Http\Resources\DealershipResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

final class CatalogController extends Controller
{
    public function __construct(
        private readonly ListActiveDealerships $listActiveDealerships,
    ) {}

    public function dealerships(ListDealershipsRequest $request): JsonResponse
    {
        $dealerships = $this->listActiveDealerships->execute($request->perPage());
        $data = $dealerships->getCollection()
            ->map(static fn ($dealership): array => (new DealershipResource($dealership))->resolve($request))
            ->all();

        return ApiResponse::paginated($data, $dealerships);
    }
}
