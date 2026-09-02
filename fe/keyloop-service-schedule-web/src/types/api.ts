export type ServiceType = {
    id: number;
    name: string;
    duration_minutes: number;
};

export type Dealership = {
    id: number;
    name: string;
    timezone: string;
    service_types: readonly ServiceType[];
};

export type PaginationMeta = {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
};

export type ApiMeta = {
    request_id: string;
};

export type ApiSuccessResponse<T> = {
    success: true;
    data: T;
    meta: ApiMeta;
};

export type PaginatedApiResponse<T> = {
    success: true;
    data: T[];
    meta: ApiMeta & {
        pagination: PaginationMeta;
    };
}
