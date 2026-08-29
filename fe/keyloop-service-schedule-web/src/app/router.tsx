import React from "react";
import ReactDOM from "react-dom/client";
import { createBrowserRouter, RouterProvider } from "react-router";

// 1. Define placeholder components (or import your own pages)
const Home = () => <h1>Home Page</h1>;
const About = () => <h1>About Page</h1>;

// 2. Define the route paths and structural hierarchy 
const router = createBrowserRouter([
    {
        path: "/",
        element: <Home />,
    },
    {
        path: "/about",
        element: <About />,
    },
]);

// 3. Render the application inside the provider
ReactDOM.createRoot(document.getElementById("root") as HTMLElement).render(
    <React.StrictMode>
        <RouterProvider router={router} />
    </React.StrictMode>
);