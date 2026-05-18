// ─── Backend-aligned API response wrappers ───────────────────────────────────

/**
 * Standard API response from the backend ApiResponse trait.
 * successResponse() → { success: true, message: string, data: T }
 */
export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

/**
 * Paginated API response from paginatedResponse().
 * Includes a meta block with pagination info.
 */
export interface PaginatedApiResponse<T> {
  success: boolean;
  message: string;
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: {
      first: string | null;
      last: string | null;
      prev: string | null;
      next: string | null;
    };
  };
}

// ─── Domain Interfaces ───────────────────────────────────────────────────────

export interface User {
  id: number;
  name: string;
  email: string;
  is_admin: boolean;
  department_id: number | null;
  role_id: number | null;
}

export interface Department {
  id: number;
  name: string;
}

export interface Role {
  id: number;
  name: string;
}

export interface Application {
  id: number;
  name: string;
  url: string;
  icon: string | null;
  category: string | null;
  description: string | null;
  created_at?: string;
  updated_at?: string;
}

/**
 * Row shape returned by the view_user_application_access DB view
 * via GET /api/dashboard.
 */
export interface UserAppAccess {
  application_id: number;
  name: string;
  url: string;
  icon: string | null;
  category: string | null;
}

// ─── Access Rule payload ──────────────────────────────────────────────────────

/** Valid entity types for the AccessRuleController */
export type AccessEntityType = 'department' | 'role' | 'user';

/** Shape returned by GET /api/access-rules/{type}/{id} */
export interface AccessRulePayload {
  entity_type: AccessEntityType;
  entity_id: number;
  assigned_applications: Application[];
  all_applications: Application[];
}

/** Body expected by PUT /api/access-rules/{type}/{id} */
export interface SyncAccessPayload {
  application_ids: number[];
}
