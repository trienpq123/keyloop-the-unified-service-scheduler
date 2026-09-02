import {
    useReducer,
    type PropsWithChildren,
} from 'react';
import {
    bookingReducer,
    initialBookingDraft,
} from './booking.reduce';
import { BookingContext } from './booking.context';

export function BookingProvider({
    children,
}: PropsWithChildren) {
    const [state, dispatch] = useReducer(
        bookingReducer,
        initialBookingDraft,
    );

    return (
        <BookingContext.Provider value={{ state, dispatch }}>
            {children}
        </BookingContext.Provider>
    );
}