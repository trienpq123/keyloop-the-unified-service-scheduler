export type ServiceType = {
    id: number;
    name: string;
    duration_minutes: number;
};

export type Dealership = {
    id: number;
    name: string;
    timezone: string;
    service_types: readonly ServiceType[];
};
