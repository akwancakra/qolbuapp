<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { CornerUpLeftIcon, EllipsisIcon, FileText, Loader2, PencilIcon, Trash2Icon } from 'lucide-vue-next';
import { DropdownMenu, DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu';
import { Separator } from '@/Components/ui/separator';
import { Button } from '@/Components/ui/button';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import { AspectRatio } from '@/Components/ui/aspect-ratio';
import { Beneficiary } from '@/types';
import { ref } from 'vue';
import { useDateFormat } from '@vueuse/core';
import { Inertia } from '@inertiajs/inertia';
import { toast } from 'vue-sonner';
import { getImageUrl, getUserDefaultImage } from '@/lib/utils';
import Loading from '@/Components/Loading.vue';

const props = defineProps<{
    beneficiary: Beneficiary
}>();

const clientLocale = ref(navigator.language || 'en-US');
const isExporting = ref(false);

const deleteBeneficiary = () => {
    Inertia.delete(route('board_member.beneficiaries.destroy', props.beneficiary.nik), {
        onSuccess: () => {
            toast.success("Berhasil menghapus data penerima manfaat");
        },
        onError: (errors) => {
            toast.error(errors);
            console.error("Error saat menghapus data:", errors);
        },
    });
};

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
            <Link :href="route('board_member.beneficiaries.index')" class="flex gap-1">
            <CornerUpLeftIcon :size="18" /> Kembali
            </Link>
        </Button>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between">
                <p class="font-semibold text-2xl tracking-tight">{{ beneficiary.name }}</p>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" class="gap-2">
                            <p>Menu</p>
                            <EllipsisIcon :size="18" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuLabel>Aksi menu</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuGroup>
                            <DropdownMenuItem @click="exportBeneficiary" class="cursor-pointer">
                                <template v-if="!isExporting">
                                    <FileText class="w-4 h-4 mr-2" />
                                    <span>Ekspor Data</span>
                                </template>
                                <template v-else>
                                    <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                                    <span>Mengunduh...</span>
                                </template>
                            </DropdownMenuItem>
                            <DropdownMenuItem as-child>
                                <Link :href="route('board_member.beneficiaries.edit', beneficiary.nik)"
                                    class="cursor-pointer flex gap-1">
                                <PencilIcon :size="18" /> Ubah
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem as-child>
                                <AlertDialog>
                                    <AlertDialogTrigger
                                        class="flex items-center gap-1.5 text-sm px-1.5 py-1 w-full rounded text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-800 dark:hovere:bg-red-700 dark:text-red-200">
                                        <Trash2Icon :size="18" />Hapus
                                    </AlertDialogTrigger>
                                    <AlertDialogContent class="p-4">
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Apakah anda yakin?</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                This action cannot be undone. This will permanently delete the
                                                selected
                                                accounts
                                                and remove their data from our servers.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Batal</AlertDialogCancel>
                                            <AlertDialogAction as-child>
                                                <Button variant="destructive"
                                                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                                    @click="deleteBeneficiary">Ya,
                                                    Hapus</Button>
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
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
