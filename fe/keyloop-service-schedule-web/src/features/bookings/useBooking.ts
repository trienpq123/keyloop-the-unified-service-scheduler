import { useContext } from 'react';
import {
    BookingContext,
    type BookingContextType,
} from './booking.context';

export function useBooking(): BookingContextType {
    const context = useContext(BookingContext);

    if (!context) {
        throw new Error(
            'useBooking must be used within a BookingProvider',
        );
    }

    return context;
}