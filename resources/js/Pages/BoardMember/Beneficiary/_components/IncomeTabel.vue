<script setup lang="ts">
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/Components/ui/dropdown-menu';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Link } from '@inertiajs/vue3';
import { BookUserIcon, EllipsisIcon, PencilIcon, Trash2Icon } from 'lucide-vue-next';
import { PaginatedBeneficiaries } from '@/types';
import PaginationComponent from '../../Income/_components/PaginationComponent.vue';
import { computed, Ref, ref } from 'vue';
import { useDateFormat } from '@vueuse/core';
import { Inertia } from '@inertiajs/inertia';
import { toast } from 'vue-sonner';
import { FormType } from '../Index.vue';

const props = defineProps < {
    beneficiaries: PaginatedBeneficiaries;
    selectedBeneficiaries: number[] as Ref<number>;
    form: Ref<FormType>;
} > ();

// const selectedBeneficiaries = ref<number[]>([]);
const clientLocale = ref(navigator.language || 'en-US');

// Function untuk toggle checkbox
const toggleCheckbox = (checked: boolean, nik: number) => {
    if (checked) {
        props.selectedBeneficiaries.value.push(nik);
    } else {
        props.selectedBeneficiaries.value = props.selectedBeneficiaries.value.filter(childNIK => childNIK !== nik);
    }
};

const toggleSelectAllCheckbox = (checked: boolean) => {
    if (checked) {
        const newNiks = props.beneficiaries.data.map(beneficiary => beneficiary.nik);
        props.selectedBeneficiaries.value = [...new Set([...props.selectedBeneficiaries.value, ...newNiks])];
    } else {
        props.selectedBeneficiaries.value = [];
    }
}

const areAllBeneficiariesSelected = computed(() => {
    return props.beneficiaries.data.every(beneficiary => props.selectedBeneficiaries.value.includes(beneficiary.nik));
});

const deleteBeneficiary = (nik: number) => {
    props.selectedBeneficiaries.value = [];

    Inertia.delete(route('board_member.beneficiaries.destroy', nik), {
        onSuccess: () => {
            toast.success("Berhasil menghapus data penerima manfaat");
        },
        onError: (errors) => {
            toast.error(errors);
            console.error("Error saat menghapus data:", errors);
            // alert("Gagal menghapus data!");
        },
    });
};

// CODE FOR PAGINATION
const handlePageChange = (newPage: number) => {
    props.form.get(route('board_member.beneficiaries.index', { page: newPage }), {
        preserveState: true,
    });
};
</script>

<template>
    <Table>
        <TableCaption class="mb-3">List dari data penerima manfaat Qolbu App.</TableCaption>
        <TableHeader>
            <TableRow>
                <TableHead class="p-3">
                    <Checkbox :checked="areAllBeneficiariesSelected" @update:checked="toggleSelectAllCheckbox" />
                </TableHead>
                <TableHead>#</TableHead>
                <TableHead class="min-w-[120px]">NIK</TableHead>
                <TableHead class="min-w-[120px]">Nama</TableHead>
                <!-- <TableHead class="min-w-[120px]">RT/RW</TableHead> -->
                <TableHead class="min-w-[120px]">JK</TableHead>
                <TableHead class="min-w-[170px]">TTL</TableHead>
                <TableHead>Pendidikan</TableHead>
                <TableHead class="min-w-[120px]">Ayah</TableHead>
                <TableHead class="min-w-[120px]">Ibu</TableHead>
                <TableHead>No. Telp</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Aksi</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <!-- IF DATA IS EMPTY -->
            <TableRow v-if="beneficiaries.data.length === 0">
                <TableCell class="p-3" :colspan="11">
                    <p class="text-center text-neutral-500 dark:text-neutral-400">
                        Tidak ada data penerima manfaat yang ditemukan.
                    </p>
                </TableCell>
            </TableRow>
            <!-- IF DATA IS EXIST -->
            <TableRow v-else v-for="(beneficiary, index) in beneficiaries.data" :key="index">
                <TableCell class="p-3">
                    <Checkbox :id="`transaction-${beneficiary.nik}`"
                        :checked="selectedBeneficiaries.includes(beneficiary.nik)"
                        @update:checked="(checked) => toggleCheckbox(checked, beneficiary.nik)" />
                </TableCell>
                <TableCell>{{ index + 1 }}</TableCell>
                <TableCell>{{ beneficiary.nik }}</TableCell>
                <TableCell>
                    <Link :href="route('board_member.beneficiaries.show', beneficiary.nik)"
                        class="hover:underline hover:text-primary">
                    {{ beneficiary.name }}
                    </Link>
                </TableCell>
                <!-- <TableCell>{{ beneficiary.neighborhood_unit }}</TableCell> -->
                <TableCell>{{ beneficiary.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</TableCell>
                <TableCell>
                    {{ beneficiary.place_of_birth }},
                    {{ useDateFormat(beneficiary.date_of_birth, 'DD MMM YYYY', {
                        locales:
                            clientLocale
                    }) }}
                </TableCell>
                <TableCell>{{ beneficiary.last_education }}, Kelas {{ beneficiary.school_grade }}
                </TableCell>
                <TableCell>{{ beneficiary.father }}</TableCell>
                <TableCell>{{ beneficiary.mother }}</TableCell>
                <TableCell>{{ beneficiary.phone_number }}</TableCell>
                <TableCell>{{ beneficiary.status }}</TableCell>
                <TableCell class="p-3">
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button size="icon" variant="outline">
                                <EllipsisIcon :size="18" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuLabel>Aksi</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem as-child>
                                <Link :href="route('board_member.beneficiaries.show', beneficiary.nik)"
                                    class="cursor-pointer flex gap-1">
                                <BookUserIcon :size="18" /> Detail
                                </Link>
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
                                                This action cannot be undone. This will permanently delete
                                                your
                                                account
                                                and remove your data from our servers.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Batal</AlertDialogCancel>
                                            <AlertDialogAction as-child>
                                                <Button variant="destructive"
                                                    class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                                    @click="deleteBeneficiary(beneficiary.nik)">Ya,
                                                    Hapus</Button>
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>

    <PaginationComponent :pagination="props.beneficiaries" @page-change="handlePageChange" />
</template>
