import { Link } from "react-router";

export const AvailabilityPage = () => {
    return (
        <section>
            <h2>Check availability</h2>

            <p>The availability form will be implemented later.</p>

            <Link to="/booking">Back</Link>
            {' | '}
            <Link to="/booking/customer">Continue</Link>
        </section>
    );
};