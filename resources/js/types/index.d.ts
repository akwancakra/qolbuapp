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
    proof?: string;
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
    nik: number;
    place_of_birth: string;
    date_of_birth: string;
    name: string;
    neighborhood_unit?: string;
    gender: string;
    last_education?: string;
    school_grade?: string;
    photo?: string;
    father?: string;
    father_photo?: string;
    mother?: string;
    mother_photo?: string;
    shirt_size?: string;
    shoe_size?: string;
    father_death_certificate_number?: string;
    mother_death_certificate_number?: string;
    phone_number?: string;
    status?: string;
    description?: string;
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

export type PaginatedBeneficiaries = {
    current_page: number;
    data: Beneficiary[];
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
