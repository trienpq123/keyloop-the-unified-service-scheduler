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

export type AppointmentStatus = 'confirmed' | 'cancelled';

export type Appointment = {
    id: number;
    status: AppointmentStatus;
    customer_id: number;
    vehicle_id: number;
    dealership_id: number;
    service_type_id: number;
    technician_id: number;
    service_bay_id: number;
    start_at: string;
    end_at: string;
    cancelled_at: string | null;
    cancellation_reason: string | null;
};