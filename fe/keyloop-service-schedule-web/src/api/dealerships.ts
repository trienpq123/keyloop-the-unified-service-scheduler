import type { Dealership } from "../features/dealerships";
import type { PaginatedApiResponse } from "../shared/api/api.types";
import { apiClient } from "./client";

export async function getDealerships(signal?: AbortSignal): Promise<Dealership[]> {
    const response = await apiClient.get<PaginatedApiResponse<Dealership>>('/dealerships', { signal });
    return response.data.data;
}