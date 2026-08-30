import type { BookingAction, BookingDraft } from "./booking.types";

export const initialBookingDraft: BookingDraft = {
    dealershipId: null,
    serviceTypeId: null,
    requestedStartAt: null,
}

export function bookingReducer(state: BookingDraft, action: BookingAction): BookingDraft {
    switch (action.type) {
        case 'service-selection-saved':
            return {
                ...state,
                dealershipId: action.payload.dealershipId,
                serviceTypeId: action.payload.serviceTypeId,
            };
        case 'availability-saved':
            return {
                ...state,
                requestedStartAt: action.payload.requestedStartAt,
            };
        case 'booking-reset':
            return initialBookingDraft;
        default:
            return state;
    }
}