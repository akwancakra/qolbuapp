import { cva, type VariantProps } from "class-variance-authority";

export { default as Alert } from "./Alert.vue";
export { default as AlertDescription } from "./AlertDescription.vue";
export { default as AlertTitle } from "./AlertTitle.vue";

export const alertVariants = cva(
    "relative w-full rounded-lg border p-4 [&>svg~*]:pl-7 [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground",
    {
        variants: {
            variant: {
                default: "bg-background text-foreground",
                destructive:
                    "border-destructive/50 text-destructive dark:border-destructive [&>svg]:text-destructive dark:text-red-600 dark:border-red-600 dark:[&>svg]:text-red-600",
                info: "border-cyan-600 text-cyan-600 [&>svg]:text-cyan-600 dark:border-cyan-500 dark:text-cyan-500 dark:[&>svg]:text-cyan-500",
                warning:
                    "border-yellow-600 text-yellow-600 [&>svg]:text-yellow-600 dark:border-yellow-500 dark:text-yellow-500 dark:[&>svg]:text-yellow-500",
            },
        },
        defaultVariants: {
            variant: "default",
        },
    }
);

export type AlertVariants = VariantProps<typeof alertVariants>;
