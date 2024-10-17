export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role: string;
    created_at: string;
    updated_at: string;
}

export interface Transaction {
    id: number;
    duta: string;
    donatur: string;
    transfer_date: string;
    nominal: number;
    metode: string;
    jenis: string;
    timestamps: string;
    created_at: string;
    updated_at: string;
}

export interface Beneficiary {
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
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>
> = T & {
    auth: {
        user: User;
    };
};
