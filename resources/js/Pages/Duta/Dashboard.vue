<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

</script>

<template>

    <Head>
        <title>Dashboard Duta</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Dashboard Duta</h1>
        </template>

        <section class="bg-white p-3 rounded-lg mb-3">
            <div class="mb-3">
                <p class="font-semibold text-2xl tracking-tight">Dashboard Duta</p>
                <p class="text-sm text-neutral-500">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eveniet
                    veniam,
                    vero omnis dolores temporibus sed?</p>
            </div>
        </section>
    </DashboardLayout>
</template>
