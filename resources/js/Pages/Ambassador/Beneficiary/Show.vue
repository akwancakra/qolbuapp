<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { CornerUpLeftIcon, FileText, Loader2 } from 'lucide-vue-next';
import { Separator } from '@/Components/ui/separator';
import { Button } from '@/Components/ui/button';
import { AspectRatio } from '@/Components/ui/aspect-ratio';
import { Beneficiary } from '@/types';
import { ref } from 'vue';
import { useDateFormat } from '@vueuse/core';
import { getImageUrl, getUserDefaultImage } from '@/lib/utils';
import Loading from '@/Components/Loading.vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    beneficiary: Beneficiary
}>();

const clientLocale = ref(navigator.language || 'en-US');
const isExporting = ref(false);

const exportBeneficiary = async () => {
    isExporting.value = true;
    try {
        // Use fetch to get the file
        const response = await fetch(`/api/beneficiaries/export/${props.beneficiary.nik}`);

        if (!response.ok) {
            throw new Error(`Failed to fetch: ${response.statusText}`);
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = `beneficiary_export.pdf`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        console.log(`Export successful as pdf`);
        isExporting.value = false;
    } catch (error) {
        isExporting.value = false;
        console.error("Failed to export beneficiary:", error);
        toast.error("Failed to export beneficiary");
    }
};
</script>

<template>

    <Head>
        <title>Detail Penerima Manfaat</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Detail Penerima Manfaat</h1>
        </template>

        <template v-if="isExporting">
            <Loading message="Mengunduh data..." />
        </template>

        <Button class="mb-3" variant="outline">
            <Link :href="route('ambassador.beneficiaries.index')" class="flex gap-1">
            <CornerUpLeftIcon :size="18" /> Kembali
            </Link>
        </Button>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between">
                <p class="font-semibold text-2xl tracking-tight">{{ beneficiary.name }}</p>
                <Buton variant="outline" @click="exportBeneficiary">
                    <template v-if="!isExporting">
                        <FileText class="w-4 h-4 mr-2" />
                        <span>Ekspor Data</span>
                    </template>
                    <template v-else>
                        <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                        <span>Mengeksport...</span>
                    </template>
                </Buton>
            </div>

            <Separator class="my-3 dark:bg-neutral-600" />

            <div>
                <p class="font-semibold text-lg tracking-tight mb-3">Biodata Penerima Manfaat</p>

                <div class="max-w-[250px] lg:max-w-[300px]">
                    <AspectRatio :ratio="6 / 7">
                        <img :src="beneficiary.photo ? getImageUrl(`beneficiaries/${beneficiary.photo}`) : getUserDefaultImage()"
                            alt="Image" class="rounded-md object-cover w-full h-full" draggable="false">
                    </AspectRatio>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 my-3">
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">NIK</p>
                        <p>{{ beneficiary.nik }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Nama</p>
                        <p>{{ beneficiary.name }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Jenis Kelamin</p>
                        <p>{{ beneficiary.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Tempat Lahir</p>
                        <p>{{ beneficiary.place_of_birth }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Tanggal Lahir</p>
                        <p>{{ useDateFormat(beneficiary.date_of_birth, 'DD MMM YYYY', {
                            locales:
                                clientLocale
                        }) }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">RT/RW</p>
                        <p>{{ beneficiary.neighborhood_unit }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Ukuran Baju</p>
                        <p>{{ beneficiary.shirt_size }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Ukuran Sepatu</p>
                        <p>{{ beneficiary.shoe_size }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Pendidikan
                            Terakhir</p>
                        <p>{{ beneficiary.last_education }}, Kelas {{ beneficiary.school_grade }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Status</p>
                        <p>{{ beneficiary.status }}</p>
                    </div>
                    <div>
                        <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">No. Telp</p>
                        <p>{{ beneficiary.phone_number }}</p>
                    </div>
                </div>

                <div>
                    <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Keterangan</p>
                    <p>{{ beneficiary.description }}</p>
                </div>
            </div>

            <Separator class="my-5 dark:bg-neutral-600" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <p class="font-semibold text-lg tracking-tight mb-3">Data Ayah</p>
                    <div class="grid grid-cols-2 gap-2 my-3">
                        <div class="max-w-[150px] lg:max-w-[250px] col-span-2">
                            <AspectRatio :ratio="6 / 7">
                                <img :src="beneficiary.father_photo ? getImageUrl(`beneficiaries/${beneficiary.father_photo}`) : getUserDefaultImage()"
                                    alt="Image" class="rounded-md object-cover w-full h-full" draggable="false">
                            </AspectRatio>
                        </div>
                        <div>
                            <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Ayah</p>
                            <p>{{ beneficiary.father }}</p>
                        </div>
                        <div>
                            <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Akta Kematian
                                Ayah
                            </p>
                            <p>{{ beneficiary.father_death_certificate_number }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="font-semibold text-lg tracking-tight mb-3">Data Ibu</p>
                    <div class="grid grid-cols-2 gap-2 my-3">
                        <div class="max-w-[150px] lg:max-w-[250px] col-span-2">
                            <AspectRatio :ratio="6 / 7">
                                <img :src="beneficiary.mother_photo ? getImageUrl(`beneficiaries/${beneficiary.mother_photo}`) : getUserDefaultImage()"
                                    alt="Image" class="rounded-md object-cover w-full h-full" draggable="false">
                            </AspectRatio>
                        </div>
                        <div>
                            <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Ibu</p>
                            <p>{{ beneficiary.mother }}</p>
                        </div>
                        <div>
                            <p class="-mb-1 text-sm font-semibold text-neutral-600 dark:text-neutral-400">Akta Kematian
                                Ibu
                            </p>
                            <p>{{ beneficiary.mother_death_certificate_number }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
