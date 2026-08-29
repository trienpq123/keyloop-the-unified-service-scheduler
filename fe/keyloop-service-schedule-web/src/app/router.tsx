import { createBrowserRouter } from "react-router";

const Home = () => <h1>Home Page</h1>;
const About = () => <h1>About Page</h1>;

export const router = createBrowserRouter([
    {
        path: "/",
        element: <Home />,
    },
    {
        path: "/about",
        element: <About />,
    },
]);
