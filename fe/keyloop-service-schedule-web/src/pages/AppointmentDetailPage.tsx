import { useParams } from "react-router";

export const AppointmentDetail = () => {
    const { appointmentId } = useParams();
    return (
        <section>
            <h1>Appointment detail</h1>
            <p>Appointment ID: {appointmentId}</p>
        </section>
    );
};  