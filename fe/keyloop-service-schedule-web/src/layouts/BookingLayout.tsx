import { Outlet } from "react-router";
import { BookingProvider } from "../features/bookings/BookingContext";

export function BookingLayout() {
    return (
        <BookingProvider>
            <section>
                <header>
                    <h1>Book a vehicle service</h1>

                    <ol>
                        <li>Select service</li>
                        <li>Availability</li>
                        <li>Customer details</li>
                        <li>Review</li>
                    </ol>
                </header>

                <Outlet />
            </section>
        </BookingProvider>
    )
}