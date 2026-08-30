export type BookingDraft = {
    dealershipId: number | null;
    serviceTypeId: number | null;
    requestedStartAt: string | null;
}

export type SaveServiceSelectionAction = {
    type: 'service-selection-saved';
    payload: {
        dealershipId: number;
        serviceTypeId: number;
    }
}

export type SaveAvailabilityAction = {
    type: 'availability-saved';
    payload: {
        requestedStartAt: string;
    }
}

export type ResetBookingAction = {
    type: 'booking-reset';
}

export type BookingAction =
    | SaveServiceSelectionAction
    | SaveAvailabilityAction
    | ResetBookingAction;