import axios from 'axios';

const baseURL = import.meta.env.VITE_API_BASE_URL;
const domain = import.meta.env.VITE_DOMAIN_API;

if (!baseURL) {
    throw new Error('VITE_API_BASE_URL is not configured');
}

export const apiClient = axios.create({
    baseURL: `${domain}${baseURL}`,
    timeout: 10_000,
    headers: {
        Accept: 'application/json',
    },
});