import { useEffect, useState } from "react";

export function AppointmentsPage() {

    useEffect(() => {
        // TODO: Fetch appointments from API

    }, []);

    return (
        <section>
            <h2>Appointments</h2>

            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Customer Name</th>
                        <th>Service Type</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Appointment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>Oil Change</td>
                        <td>2025-10-15</td>
                        <td>10:00 AM</td>
                        <td>Confirmed</td>
                    </tr>
                </tbody>
            </table>
        </section>
    );
}