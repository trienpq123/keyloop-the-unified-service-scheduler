import { useNavigate } from "react-router";
import type { Dealership } from "../../types/api";
import { useEffect, useState, type ChangeEvent } from "react";
import { getDealerships } from "../../api/dealerships";
import axios from "axios";
import { useBooking } from "../../features/bookings/useBooking";

export const SelectServicePage = () => {
    const navigate = useNavigate();
    const { state, dispatch } = useBooking();
    const [dealerships, setDealerships] = useState<Dealership[]>([]);
    const [loadStatus, setLoadStatus] = useState<'loading' | 'success' | 'error'>('loading');
    const [loadError, setLoadError] = useState<string | null>(null);
    const [reloadKey, setReloadKey] = useState(0);
    const [dealershipId, setDealershipId] = useState<number | null>(state.dealershipId);
    const [serviceTypeId, setServiceTypeId] = useState<number | null>(state.serviceTypeId);

    useEffect(() => {
        const controller = new AbortController();

        async function loadDealerships() {
            try {
                const result = await getDealerships(controller.signal);

                setDealerships(result);
                setLoadError(null);
                setLoadStatus('success');
            } catch (error) {
                if (controller.signal.aborted) {
                    return;
                }

                setLoadStatus('error');
                setLoadError(
                    axios.isAxiosError(error)
                        ? 'Failed to load dealerships. Please try again.'
                        : 'An unexpected error occurred. Please try again.',
                );
            }
        }
        loadDealerships();

        return () => {
            controller.abort();
        }
    }, [reloadKey]);

    const selectedDealership = dealerships.find(d => d.id === dealershipId) ?? null;
    const availableServices = selectedDealership?.service_types ?? [];

    const selectedService = availableServices.find(s => s.id === serviceTypeId) ?? null;

    const canContinue = selectedDealership !== null && selectedService !== null;

    function handleDealershipChange(event: ChangeEvent<HTMLSelectElement>) {
        const value = event.target.value;
        setDealershipId(value === '' ? null : Number(value));

        setServiceTypeId(null);
    }

    function handleServiceChange(event: ChangeEvent<HTMLSelectElement>) {
        const value = event.target.value;
        setServiceTypeId(value === '' ? null : Number(value));
    }

    function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (canContinue) {
            dispatch({
                type: 'service-selection-saved',
                payload: {
                    dealershipId: selectedDealership.id,
                    serviceTypeId: selectedService.id,
                }
            });
            navigate('/booking/availability');
        }
    }

    if (loadStatus === 'loading') {
        return (
            <section>
                <h2>Select dealership and service</h2>
                <p>Loading dealerships...</p>
            </section>
        );
    }

    if (loadStatus === 'error') {
        return (
            <section>
                <h2>Select dealership and service</h2>
                <p role="alert">{loadError}</p>

                <button
                    type="button"
                    onClick={() => setReloadKey((current) => current + 1)}
                >
                    Retry
                </button>
            </section>
        );
    }

    return (
        <section>
            <h2>Select dealership and service</h2>

            <form onSubmit={handleSubmit}>
                <select value={dealershipId ?? ''} onChange={handleDealershipChange}>
                    <option value="">Select a dealership</option>
                    {dealerships.map(dealership => (
                        <option key={dealership.id} value={dealership.id}>
                            {dealership.name}
                        </option>
                    ))}
                </select>

                <select
                    value={serviceTypeId ?? ''}
                    onChange={handleServiceChange}
                    disabled={!selectedDealership}
                >
                    <option value="">Select a service</option>
                    {availableServices.map(service => (
                        <option key={service.id} value={service.id}>
                            {service.name}
                        </option>
                    ))}
                </select>

                {selectedDealership && selectedService && (
                    <p>
                        Selected: {selectedDealership.name} - {selectedService.name}
                        Duration: {selectedService.duration_minutes} minutes
                    </p>
                )}

                <button type="submit" disabled={!canContinue}>
                    Continue
                </button>
            </form>
        </section>
    )
};