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