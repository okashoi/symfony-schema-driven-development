import { ApiRequestClientError } from './error/ApiRequestClientError';

export const customFetch = async <T>(url: string, options?: RequestInit): Promise<T> => {
  const baseUrl = process.env.NEXT_PUBLIC_API_BASE_URL;
  if (!baseUrl) {
    throw new Error('NEXT_PUBLIC_API_BASE_URL environment variable is not set');
  }

  const response = await fetch(`${baseUrl}${url}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...options?.headers,
    },
  });

  const body = await response.json().catch(() => undefined);

  if (!response.ok) {
    if (response.status >= 500) {
      throw new Error(`Server error: ${response.status}`);
    }
    throw new ApiRequestClientError(response.status, body);
  }

  return body as T;
};

export default customFetch;
