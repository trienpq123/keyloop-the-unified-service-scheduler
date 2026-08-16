<?php

declare(strict_types=1);

namespace App\Catalog\Queries;

use App\Models\Dealership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListActiveDealerships
{
    /** @return LengthAwarePaginator<int, Dealership> */
    public function execute(int $perPage): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Dealership> */
        return Dealership::query()
            ->where('is_active', true)
            ->with([
                'technicians' => static fn ($query) => $query
                    ->where('is_active', true)
                    ->with(['serviceTypes' => static fn ($query) => $query->where('is_active', true)]),
            ])
            ->orderBy('name')
            ->paginate($perPage, ['id', 'name', 'timezone']);
    }
}
