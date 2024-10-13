<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>

    <Head title="Profile" />

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Profile</h1>
        </template>

        <div>
            <div class="max-w-7xl mx-auto space-y-6 py-4 px-2 sm:px-3 lg:px-4">
                <div class="bg-white p-3 shadow dark:bg-neutral-800 sm:rounded-lg sm:p-4">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                        class="max-w-7xl" />
                </div>

                <div class="bg-white p-3 shadow dark:bg-neutral-800 sm:rounded-lg sm:p-4">
                    <UpdatePasswordForm class="max-w-7xl" />
                </div>

                <div class="bg-white p-3 shadow dark:bg-neutral-800 sm:rounded-lg sm:p-4">
                    <DeleteUserForm class="max-w-7xl" />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
