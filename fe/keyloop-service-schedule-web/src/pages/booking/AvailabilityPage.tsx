import { Link, useLocation } from "react-router";

type AvailabilityLocationService = {
    dealershipId: number;
    serviceTypeId: number;
}

export const AvailabilityPage = () => {
    const location = useLocation();
    const selection = location.state as AvailabilityLocationService;

    if (!selection) {
        return (
            <section>
                <h2>Check availability</h2>
                <p>Invalid selection. Please go back and select a dealership and service type.</p>
                <Link to="/booking">Back</Link>
            </section>
        );
    }

    return (
        <section>
            <h2>Check availability</h2>

            <p>Dealership ID: {selection.dealershipId}</p>
            <p>Service type ID: {selection.serviceTypeId}</p>

            <Link to="/booking">Back</Link>
            {' | '}
            <Link to="/booking/customer">Continue</Link>
        </section>
    );
};