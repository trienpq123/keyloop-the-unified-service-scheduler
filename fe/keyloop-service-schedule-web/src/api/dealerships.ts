import type { Dealership, PaginatedApiResponse } from "../types/api";
import { apiClient } from "./client";

export async function getDealerships(signal?: AbortSignal): Promise<Dealership[]> {
    const response = await apiClient.get<PaginatedApiResponse<Dealership>>('/dealerships', { signal });
    return response.data.data;
}