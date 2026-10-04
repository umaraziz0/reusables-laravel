import type { Pagination } from './Pagination';

// --- Types --- //

export type ApiResponse<T> = {
  success: boolean;
  message: string;
  data: T;
};

// Success

export type ApiSuccessResponse<T> = ApiResponse<T> & {
  success: true;
};

export type PaginatedApiSuccessResponse<T> = Omit<
  ApiSuccessResponse<T[]>,
  'data'
> &
  Pagination<T>;

// Errors

export type ApiErrorResponse<T> = ApiResponse<T> & {
  success: false;
};

export type ValidationErrorResponse = ApiErrorResponse<null> & {
  errors: Record<string, string[]>;
};

export type TooManyRequestsResponse = ApiErrorResponse<null>;

// --- Constants --- //

export const HTTP_SUCCESS = 200;

export const HTTP_CREATED = 201;

export const HTTP_BAD_REQUEST = 400;

export const HTTP_UNAUTHORIZED = 401;

export const HTTP_FORBIDDEN = 403;

export const HTTP_NOT_FOUND = 404;

export const HTTP_UNPROCESSABLE_ENTITY = 422;

export const HTTP_TOO_MANY_REQUESTS = 429;

export const HTTP_SERVER_ERROR = 500;
