import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export const formatCurrency = (value: number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

export const formatNumber = (value: number) => {
    return new Intl.NumberFormat("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

export const parseNumber = (value: string): number => {
    return Number(value.replace(/\D/g, ""));
};

export const getImageUrl = (fileName: string) => {
    return `/storage/images/${fileName}`;
};

export const getUserDefaultImage = () => {
    return "/assets/images/user-default.jpg";
};

export const getDefaultImage = () => {
    return "/assets/images/image-default.png";
};
