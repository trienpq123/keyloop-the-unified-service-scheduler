import type {
    ApiSuccessResponse,
    Appointment,
} from '../types/api';
import { apiClient } from './client';

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