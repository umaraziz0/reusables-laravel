export type PaginationLink = {
  url: string | null;
  label: string;
  active: boolean;
};

export type PaginationLinks = {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
};

export type PaginationMeta = {
  current_page: number;
  from: number | null;
  last_page: number;
  links: Array<PaginationLink>;
  path: string;
  per_page: number;
  to: number | null;
  total: number;
};

export type Pagination<T> = {
  data: Array<T>;
  links: PaginationLinks;
  meta: PaginationMeta;
};

/** Laravel's `LengthAwarePaginator` serialized directly, without an API resource. */
export type LengthAwarePaginator<T> = PaginationMeta & {
  data: Array<T>;
  first_page_url: string;
  last_page_url: string;
  next_page_url: string | null;
  prev_page_url: string | null;
};
