import { Outlet } from "react-router";

export function AppointmentLayout() {
    return (
        <section>
            <header>
                <h1>Appointment Management</h1>

                <ol>
                    <li>View appointments</li>
                    <li>Manage appointments</li>
                    <li>Cancel appointments</li>
                    <li>Reschedule appointments</li>
                </ol>
            </header>

            <Outlet />
        </section>
    )
}
