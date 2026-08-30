import { createBrowserRouter } from "react-router";
import { AppLayout } from "./Applayout";
import { HomePage } from "../pages/HomePage";
import { BookingLayout } from "../layouts/BookingLayout";
import { SelectServicePage } from "../pages/booking/SelectServicePage";
import { AvailabilityPage } from "../pages/booking/AvailabilityPage";
import { CustomerDetailsPage } from "../pages/booking/CustomerDetailsPage";
import { ReviewBookingPage } from "../pages/booking/ReviewBookingPage";
import { AppointmentDetailPage } from "../pages/AppointmentDetailPage";
import { NotFoundPage } from "../pages/NotFoundPage";

export const router = createBrowserRouter([
    {
        path: '/',
        element: <AppLayout />,
        children: [
            {
                index: true,
                element: <HomePage />,
            },
            {
                path: 'booking',
                element: <BookingLayout />,
                children: [
                    {
                        index: true,
                        element: <SelectServicePage />,
                    },
                    {
                        path: 'availability',
                        element: <AvailabilityPage />,
                    },
                    {
                        path: 'customer',
                        element: <CustomerDetailsPage />,
                    },
                    {
                        path: 'review',
                        element: <ReviewBookingPage />,
                    },
                ],
            },
            {
                path: 'appointments/:appointmentId',
                element: <AppointmentDetailPage />,
            },
            {
                path: '*',
                element: <NotFoundPage />,
            },
        ],
    },
]);
