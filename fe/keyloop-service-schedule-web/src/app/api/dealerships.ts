import type { PaginatedApiResponse } from
    '../shared/api/api.types';
import { apiClient } from '../shared/api/client';
import type { Dealership } from '../types/api';

export async function getDealerships(
    signal?: AbortSignal,
): Promise<Dealership[]> {
    const response = await apiClient.get<
        PaginatedApiResponse<Dealership>
    >('/dealerships', {
        signal,
    });

    return response.data.data;
}