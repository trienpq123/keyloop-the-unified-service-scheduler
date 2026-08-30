import { Link } from "react-router";

export const HomePage = () => {
    return (
        <section>
            <h1>Keyloop Service Scheduler</h1>
            <p>Book your vehicle service online.</p>

            <Link to="/booking">Start booking</Link>
        </section>
    )
};