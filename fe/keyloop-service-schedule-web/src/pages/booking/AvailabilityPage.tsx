import { Link } from "react-router";
import { useBookings } from "../../features/bookings/BookingContext";

export const AvailabilityPage = () => {
    const { state } = useBookings();
    console.log(state);

    if (!state.dealershipId || !state.serviceTypeId) {
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

            <p>Dealership ID: {state.dealershipId}</p>
            <p>Service type ID: {state.serviceTypeId}</p>

            <Link to="/booking">Back</Link>
            {' | '}
            <Link to="/booking/customer">Continue</Link>
        </section>
    );
};