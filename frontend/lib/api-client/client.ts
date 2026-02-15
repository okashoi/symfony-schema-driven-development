import createClient from 'openapi-fetch';

import type { paths } from './generated/api';

const BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL;

if (!BASE_URL) {
  throw new Error('NEXT_PUBLIC_API_BASE_URL environment variable is not set');
}

export const apiClient = createClient<paths>({
  baseUrl: BASE_URL,
  credentials: 'include',
  headers: {
    'Content-Type': 'application/json',
  },
});
