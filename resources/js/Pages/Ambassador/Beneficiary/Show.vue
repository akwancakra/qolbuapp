<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Separator } from '@/Components/ui/separator';
import { DropdownMenu, DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu';
import { CornerUpLeftIcon, EllipsisIcon, PencilIcon, Trash2Icon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import { Beneficiary } from '@/types';
import { ref } from 'vue';
import { useDateFormat } from '@vueuse/core';
import { AspectRatio } from '@/Components/ui/aspect-ratio';

const props = defineProps<{
    beneficiary: Beneficiary
}>();

const clientLocale = ref(navigator.language || 'en-US');

</script>

<template>

    <Head>
        <title>Detail Penerima Manfaat</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Detail Penerima Manfaat</h1>
        </template>

        <Button class="mb-3" variant="outline">
            <Link :href="route('duta.beneficiaries.index')" class="flex gap-1">
            <CornerUpLeftIcon :size="18" /> Kembali
            </Link>
        </Button>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div>
                <p class="font-semibold text-2xl tracking-tight">{{ beneficiary.name }}</p>
            </div>

            <Separator class="my-3 dark:bg-neutral-600" />

            <div>
                <p class="font-semibold text-lg tracking-tight mb-3">Biodata Penerima Manfaat</p>

                <div class="max-w-[250px] lg:max-w-[300px]">
                    <AspectRatio :ratio="6 / 7">
                        <img :src="`/storage/images/beneficiaries/${beneficiary.photo}`" alt="Image"
                            class="rounded-md object-cover w-full h-full" draggable="false">
                    </AspectRatio>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 my-3">
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Nama</p>
                        <p>{{ beneficiary.name }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Nama Panggilan</p>
                        <p>{{ beneficiary.nickname }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Tanggal Lahir</p>
                        <p>{{ useDateFormat(beneficiary.birthdate, 'DD MMM YYYY', {
                            locales:
                                clientLocale
                        }) }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Kota</p>
                        <p>{{ beneficiary.city }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Provinsi</p>
                        <p>{{ beneficiary.province }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Negara</p>
                        <p>{{ beneficiary.country }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Kode Pos</p>
                        <p>{{ beneficiary.postal_code }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">No Handphone</p>
                        <p>{{ beneficiary.phone }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Email</p>
                        <p>{{ beneficiary.email }}</p>
                    </div>
                </div>
            </div>

            <Separator class="my-5 dark:bg-neutral-600" />

            <div>
                <p class="font-semibold text-lg tracking-tight mb-3">Data Orang Tua</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 my-3">
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Ayah</p>
                        <p>{{ beneficiary.parent_father }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Ibu</p>
                        <p>{{ beneficiary.parent_mother }}</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
