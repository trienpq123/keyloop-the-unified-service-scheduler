import { createContext, useContext, useReducer, type PropsWithChildren } from "react";
import type { BookingAction, BookingDraft } from "./booking.types"
import { bookingReducer, initialBookingDraft } from "./booking.reduce";

type BookingContextType = {
    state: BookingDraft;
    dispatch: React.Dispatch<BookingAction>;
}

const BookingContext = createContext<BookingContextType | null>(null);

export function BookingProvider({ children }: PropsWithChildren) {
    const [state, dispatch] = useReducer(bookingReducer, initialBookingDraft);
    return (
        <BookingContext.Provider value={{ state, dispatch }}>
            {children}
        </BookingContext.Provider>
    )
}

export function useBookings(): BookingContextType {
    const context = useContext(BookingContext);
    console.log(context);
    if (!context) {
        throw new Error("useBookings must be used within a BookingProvider");
    }
    return context;
}
