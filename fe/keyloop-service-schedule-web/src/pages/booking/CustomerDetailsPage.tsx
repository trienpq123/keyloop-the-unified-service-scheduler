import { Link } from "react-router";

export const CustomerDetailsPage = () => {
    return (
        <section>
            <h2>Customer and vehicle details</h2>

            <p>The customer form will be implemented later.</p>

            <Link to="/booking/availability">Back</Link>
            {' | '}
            <Link to="/booking/review">Continue</Link>
        </section>
    );
};