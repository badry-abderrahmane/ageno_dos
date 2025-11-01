import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface Organization {
    org_name: string;
    org_footer?: string;
    org_modality?: string;
    org_bank?: string;
    org_logo?: string;
    org_color?: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: string;
    organization: Organization
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Client {
    id: number;
    name: string;
    ice: string;
    created_at: string;
    updated_at: string;
}

export interface Invoice {
    id: number;
    total: string;
    status: 'not_paid' | 'paid';
    client_id: string;
    client: Client;
    user_id: string;
}

interface Supplier {
  id: number;
  name: string;
  phone?: string;
  address?: string;
  email?: string;
  contact?: string;
  category?: string;
}

interface ProductCategory { id: number, name: string }
interface Product {
  id: number;
  name: string;
  ref: string;
  price: string;
  supplier_price: string;
  product_category_id: number;
  supplier_id: number;
  supplier: Supplier;
  product_category: ProductCategory;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface InertiaLink {
    /** The URL for the link (or null for the '...' separator or disabled links). */
    url: string | null;

    /** The display text for the link (e.g., '1', '2', '...', 'Next &raquo;'). */
    label: string;

    /** A boolean indicating if this link represents the currently active page. */
    active: boolean;
}
