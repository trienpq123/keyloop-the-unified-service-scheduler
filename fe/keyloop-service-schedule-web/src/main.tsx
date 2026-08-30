import './index.css'
import { RouterProvider } from 'react-router'
import { router } from './app/router'
import React from 'react'
import ReactDOM from 'react-dom/client'

const rootElement = document.getElementById("root")
if (!rootElement) {
  throw new Error("Root element not found")
}

ReactDOM.createRoot(rootElement).render(
  <React.StrictMode>
    <RouterProvider router={router} />
  </React.StrictMode>
);    