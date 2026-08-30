import { Outlet } from "react-router";

export function BookingLayout() {
    return (
        <section>
            <header>
                <h1>Booking Layout</h1>

                <ol>
                    <li>Select service</li>
                    <li>Availability</li>
                    <li>Customer details</li>
                    <li>Review</li>
                </ol>
            </header>

            <Outlet />
        </section>
    )
}