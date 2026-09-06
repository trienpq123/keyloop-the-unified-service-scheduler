import { useState, useEffect } from "react";
import { Link, useParams } from "react-router";
import type { Appointment } from "../types/api";
import axios from "axios";
import { getAppointment } from "../api/appointments";

type AppointmentLoadState =
    | { status: 'loading' }
    | { status: 'success'; appointment: Appointment }
    | { status: 'error'; message: string };

const dateTimeFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'short',
});

export const AppointmentDetailPage = () => {
    const { appointmentId: appointmentIdParam } = useParams();

    const appointmentId = Number(appointmentIdParam);
    const isValidAppointmentId = !Number.isNaN(appointmentId) && appointmentId > 0;

    const [loadState, setLoadState] = useState<AppointmentLoadState>({ status: 'loading' });

    useEffect(() => {
        if (!isValidAppointmentId) {
            setLoadState({ status: 'error', message: 'Invalid appointment ID' });
            return;
        }

        // TODO: Fetch appointment data
        const abortController = new AbortController();
        async function loadAppointment() {
            setLoadState({ status: 'loading' });

            try {
                const appointment = await getAppointment(
                    appointmentId,
                    abortController.signal,
                );

                setLoadState({
                    status: 'success',
                    appointment,
                });
            } catch (error) {
                if (abortController.signal.aborted) {
                    return;
                }

                if (
                    axios.isAxiosError(error) &&
                    error.response?.status === 404
                ) {
                    setLoadState({
                        status: 'error',
                        message: 'Appointment not found.',
                    });

                    return;
                }

                setLoadState({
                    status: 'error',
                    message:
                        'Failed to load appointment. Please try again.',
                });
            }
        }
        loadAppointment();

        return () => {
            abortController.abort();
        };
    }, [isValidAppointmentId]);
    if (loadState.status === 'loading') {
        return (
            <section>
                <h1>Appointment detail</h1>
                <p>Loading...</p>
            </section>
        );
    }
    if (loadState.status === 'error') {
        return (
            <section>
                <h1>Appointment detail</h1>
                <p>{loadState.message}</p>
            </section>
        );
    }
    const { appointment } = loadState;

    return (
        <section>
            <header>
                <h1>Appointment #{appointment.id}</h1>
                <p>Status: {appointment.status}</p>
            </header>

            <dl>
                <dt>Customer ID</dt>
                <dd>{appointment.customer_id}</dd>

                <dt>Vehicle ID</dt>
                <dd>{appointment.vehicle_id}</dd>

                <dt>Dealership ID</dt>
                <dd>{appointment.dealership_id}</dd>

                <dt>Service type ID</dt>
                <dd>{appointment.service_type_id}</dd>

                <dt>Technician ID</dt>
                <dd>{appointment.technician_id}</dd>

                <dt>Service bay ID</dt>
                <dd>{appointment.service_bay_id}</dd>

                <dt>Start time</dt>
                <dd>
                    {dateTimeFormatter.format(
                        new Date(appointment.start_at),
                    )}
                </dd>

                <dt>End time</dt>
                <dd>
                    {dateTimeFormatter.format(
                        new Date(appointment.end_at),
                    )}
                </dd>
            </dl>

            {appointment.status === 'cancelled' && (
                <section>
                    <h2>Cancellation</h2>

                    <p>
                        {appointment.cancellation_reason ??
                            'No cancellation reason provided.'}
                    </p>
                </section>
            )}

            <Link to="/">Return home</Link>
        </section>
    );
};  