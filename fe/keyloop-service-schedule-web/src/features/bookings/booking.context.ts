import {
    createContext,
    type Dispatch,
} from 'react';
import type {
    BookingAction,
    BookingDraft,
} from './booking.types';

export type BookingContextType = {
    state: BookingDraft;
    dispatch: Dispatch<BookingAction>;
};

export const BookingContext =
    createContext<BookingContextType | null>(null);