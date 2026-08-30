import { Link } from "react-router";

export const NotFoundPage = () => {
    return (
        <section>
            <h1>404 — Page not found</h1>
            <Link to="/">Return home</Link>
        </section>
    );
};