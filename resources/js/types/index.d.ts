export type User = {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role: string;
    created_at: string;
    updated_at: string;
};

export type Income = {
    id: number;
    ambassador_id: number;
    donor: string;
    transfer_date: string;
    on_behalf_of: string;
    amount: number;
    payment_method: string;
    type: string;
    created_at: string;
    updated_at: string;
    ambassador?: Ambassador;
};

export type Ambassador = {
    id: number;
    name: string;
    phone_number: string;
    code: string;
    created_at: string;
    updated_at: string;
    incomes?: Income[];
};

export type Beneficiary = {
    id: number;
    name: string;
    nickname?: string;
    birthdate?: string;
    address?: string;
    city?: string;
    province?: string;
    country?: string;
    postal_code?: string;
    photo?: string;
    phone?: string;
    email?: string;
    parent_mother?: string;
    parent_mother_phone?: string;
    parent_father?: string;
    parent_father_phone?: string;
    created_at: string;
    updated_at: string;
};

export type PaginatedUsers = {
    current_page: number;
    data: User[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
};

export type PaginatedIncomes = {
    current_page: number;
    data: Income[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
};

export type PaginationTemplate = {
    current_page: number;
    data: any[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
};

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
};
