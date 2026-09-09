import type {
    ApiSuccessResponse,
} from '../shared/api/api.types';
import { apiClient } from '../shared/api/client';
import type { Appointment } from '../features/appointments';

export async function getAppointment(
    appointmentId: number,
    signal?: AbortSignal,
): Promise<Appointment> {
    const response = await apiClient.get<
        ApiSuccessResponse<Appointment>
    >(`/appointments/${appointmentId}`, {
        signal,
    });

    return response.data.data;
}