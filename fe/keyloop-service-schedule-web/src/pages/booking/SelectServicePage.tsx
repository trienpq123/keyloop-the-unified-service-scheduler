import { useNavigate } from "react-router";
import type { Dealership } from "../../types/api";
import { useState, type ChangeEvent } from "react";

const dealerships = [
    {
        id: 1,
        name: 'Ho Chi Minh Dealership',
        timezone: 'Asia/Ho_Chi_Minh',
        service_types: [
            {
                id: 1,
                name: 'Oil change',
                duration_minutes: 30,
            },
            {
                id: 2,
                name: 'Brake inspection',
                duration_minutes: 60,
            },
        ],
    },
    {
        id: 2,
        name: 'Da Nang Dealership',
        timezone: 'Asia/Ho_Chi_Minh',
        service_types: [
            {
                id: 3,
                name: 'General maintenance',
                duration_minutes: 90,
            },
        ],
    },
] satisfies readonly Dealership[];

export const SelectServicePage = () => {
    const navigate = useNavigate();

    const [dealershipId, setDealershipId] = useState<number | null>(null);
    const [serviceTypeId, setServiceTypeId] = useState<number | null>(null);

    const selectedDealership = dealerships.find(d => d.id === dealershipId) ?? null;
    const availableServices = selectedDealership?.service_types ?? [];

    const selectedService = availableServices.find(s => s.id === serviceTypeId) ?? null;

    const canContinue = selectedDealership !== null && selectedService !== null;

    function handleDealershipChange(event: ChangeEvent<HTMLSelectElement>) {
        const value = event.target.value;
        setDealershipId(value === '' ? null : Number(value));

        setServiceTypeId(null);
    }

    function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (!selectedDealership || !selectedService) {
            return;
        }

        if (canContinue) {
            navigate('/booking/availability', {
                state: {
                    dealershipId: selectedDealership.id,
                    serviceTypeId: selectedService.id,
                }
            });
        }
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

                <select value={serviceTypeId ?? ''} onChange={(event) => {
                    const value = event.target.value;
                    setServiceTypeId(value === '' ? null : Number(value));
                }}>
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