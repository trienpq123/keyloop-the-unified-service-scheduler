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