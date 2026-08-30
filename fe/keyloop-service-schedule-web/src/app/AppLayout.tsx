import { Link, Outlet } from "react-router";

export function AppLayout() {
    return (
        <div>
            <header>
                <nav>
                    <Link to="/">Keyloop Scheduler</Link>
                    <Link to="/booking">Booking a service</Link>
                </nav>
            </header>

            <main>
                <Outlet />
            </main>

            <footer>
                <p>© 2025 Keyloop Scheduler. All rights reserved.</p>
            </footer>
        </div>
    )
}