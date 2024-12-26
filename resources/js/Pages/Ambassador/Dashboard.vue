<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { HeartHandshakeIcon, ReceiptTextIcon, SquareUserIcon } from 'lucide-vue-next';
import { User } from '@/types';

const props = defineProps<{
    user: User;
}>();

</script>

<template>

    <Head>
        <title>Dashboard Duta</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Dashboard Duta</h1>
        </template>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="mb-3">
                <p class="font-semibold text-2xl tracking-tight">Selamat Datang, {{ props?.user?.name ?? 'Kamu' }}</p>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Kamu dapat langsung memilih 3 menu dibawah.</p>
            </div>
        </section>

        <section class="grid grid-cols-1 gap-2 sm:grid-cols-3">
            <Link :href="route('ambassador.incomes.index')"
                class="bg-white border border-neutral-300 rounded-lg p-3 hover:border-green-500 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:border-green-600">
            <ReceiptTextIcon :size="32" class="mb-3" />
            <p class="font-semibold tracking-tight text-lg sm:text-xl">Daftar Pendapatan</p>
            </Link>
            <Link :href="route('ambassador.beneficiaries.index')"
                class="bg-white border border-neutral-300 rounded-lg p-3 hover:border-green-500 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:border-green-600">
            <HeartHandshakeIcon :size="32" class="mb-3" />
            <p class="font-semibold tracking-tight text-lg sm:text-xl">Daftar Penerima Manfaat</p>
            </Link>
            <Link :href="route('profile.edit')"
                class="bg-white border border-neutral-300 rounded-lg p-3 hover:border-green-500 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:border-green-600">
            <SquareUserIcon :size="32" class="mb-3" />
            <p class="font-semibold tracking-tight text-lg sm:text-xl">Profile</p>
            </Link>
        </section>
    </DashboardLayout>
</template>
