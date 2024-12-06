<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { PaginatedBeneficiaries } from '@/types';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuTrigger,
    DropdownMenuSubTrigger,
    DropdownMenuPortal,
    DropdownMenuSubContent,
    DropdownMenuRadioItem,
    DropdownMenuRadioGroup,
} from '@/components/ui/dropdown-menu';
import { AlertCircle, BookUserIcon, DatabaseIcon, DownloadIcon, EllipsisIcon, FilePlusIcon, FileTextIcon, ImageIcon, PencilIcon, Search, SheetIcon, Trash2Icon } from 'lucide-vue-next';
import { Checkbox } from '@/components/ui/checkbox';
import { useDateFormat } from '@vueuse/core';
// LOCAL CODE
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { toast } from 'vue-sonner';
import PaginationComponent from '@/Pages/BoardMember/Income/_components/PaginationComponent.vue';
import FilterBeneficiary from '@/Pages/BoardMember/Beneficiary/_components/FilterBeneficiary.vue';
import Loading from '@/components/Loading.vue';

const page = usePage();
const props = defineProps<{
    beneficiaries: PaginatedBeneficiaries;
    educationList: { label: string; value: string }[];
    statusList: { label: string; value: string }[];
    filters: {
        name: string;
        min_age: number;
        max_age: number;
        education: string;
        school_grade: string;
        shirt_size: string;
        shoe_size: number;
        gender: string;
        status: string;
        sort_by: string;
        sort_direction: string;
        count_per_page: string;
    }
}>();

// ==============================================
// ========== SEARCHING AND FIILTERING ==========
// ==============================================
const form = useForm({
    name: '',
    min_age: 0,
    max_age: 0,
    education: 'default',
    school_grade: '',
    shirt_size: 'default',
    shoe_size: 0,
    gender: 'default',
    status: 'default',
    sort_by: 'created_at',
    sort_direction: 'desc',
    count_per_page: '10'
});

const handleApplyFilter = (filters: any) => {
    console.log('Filters applied in parent:', filters);

    // Update form values with filters
    Object.assign(form, filters);

    // Fetch data with new filters
    form.get(route('ambassador.beneficiaries.index'), {
        preserveState: true,
        data: form.data(),
        onError: (errors) => {
            form.errors = errors;
        },
    });
};

// CODE FOR SEARCHING AND FIILTERING
watch(() => form.data(), (newData) => {
    form.get(route('ambassador.beneficiaries.index'), {
        preserveState: true,
        data: newData,
        onError: (errors) => {
            form.errors = errors;
        },
    });
});

onMounted(() => {
    form.name = props.filters.name;
    form.min_age = props.filters.min_age;
    form.max_age = props.filters.max_age;
    form.education = props.filters.education;
    form.school_grade = props.filters.school_grade;
    form.shirt_size = props.filters.shirt_size;
    form.shoe_size = props.filters.shoe_size;
    form.gender = props.filters.gender;
    form.status = props.filters.status;
    form.sort_by = props.filters.sort_by;
    form.sort_direction = props.filters.sort_direction;
    form.count_per_page = props.filters.count_per_page;

    const flashMessage = (page.props.flash as { message?: string })?.message;
    if (flashMessage) {
        toast.success(flashMessage);
    }
});

// ========================================
// ========== DATA TABLE SECTION ==========
// ========================================
const dropdownOpen = ref(false);
// State untuk melacak checkbox yang dipilih
const selectedBeneficiaries = ref<number[]>([]);
const clientLocale = ref(navigator.language || 'en-US');
const isExporting = ref(false);

// Computed property untuk menghitung jumlah akun yang dipilih
const selectedCount = computed(() => selectedBeneficiaries.value.length);

// Function untuk toggle checkbox
const toggleCheckbox = (checked: boolean, nik: number) => {
    if (checked) {
        selectedBeneficiaries.value.push(nik);
    } else {
        selectedBeneficiaries.value = selectedBeneficiaries.value.filter(childNIK => childNIK !== nik);
    }
};

const toggleSelectAllCheckbox = (checked: boolean) => {
    if (checked) {
        const newNiks = props.beneficiaries.data.map(beneficiary => beneficiary.nik);
        selectedBeneficiaries.value = [...new Set([...selectedBeneficiaries.value, ...newNiks])];
    } else {
        selectedBeneficiaries.value = [];
    }
}

const areAllBeneficiariesSelected = computed(() => {
    return props.beneficiaries.data.length > 0 && props.beneficiaries.data.every(beneficiary => selectedBeneficiaries.value.includes(beneficiary.nik));
});

// Function untuk export beneficiaries
const exportBeneficiaries = async (exportType: string) => {
    isExporting.value = true;
    try {
        // Gunakan fetch untuk mendapatkan file
        const response = await fetch(`/api/beneficiaries/export`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                niks: JSON.stringify(selectedBeneficiaries.value),
                type: exportType,
                filters: props.filters,
                t: new Date().getTime().toString(),
            })
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch: ${response.statusText}`);
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = `beneficiary_export.${exportType}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        console.log(`Export successful as ${exportType}`);
        selectedBeneficiaries.value = [];
        isExporting.value = false;
    } catch (error) {
        isExporting.value = false;
        console.error("Failed to export beneficiaries:", error);
        toast.error("Failed to export beneficiaries");
    }
};

// CODE FOR PAGINATION
const handlePageChange = (newPage: number) => {
    form.get(route('ambassador.beneficiaries.index', { page: newPage }), {
        preserveState: true,
    });
};
</script>

<template>

    <Head>
        <title>Penerima Manfaat</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Penerima Manfaat</h1>
        </template>

        <template v-if="isExporting">
            <Loading message="Mengekspor data..." />
        </template>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">Data Penerima Manfaat</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Daftar orang yang telah terdata untuk
                        dapat
                        disalurkan kebermanfaatan</p>
                </div>
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1 md:flex-none">
                    <Label for="name">Nama Anak</Label>
                    <div class="relative">
                        <Search class="absolute left-2 top-3 h-4 w-4 text-muted-foreground" />
                        <Input id="name" class="pl-8 w-full md:w-[300px]" v-model="form.name"
                            placeholder="Cari nama..." />
                    </div>
                </div>

                <FilterBeneficiary :educationList="educationList" :statusList="statusList"
                    @apply-filter="handleApplyFilter" :filters="filters" />
            </div>
        </section>

        <template v-if="Object.keys(form.errors).length > 0">
            <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
                <Alert variant="destructive" class="col-span-2">
                    <AlertCircle class="w-4 h-4" />
                    <AlertTitle>Data yang dikirim terdapat kesalahan:</AlertTitle>
                    <AlertDescription>
                        <ul class="list-inside list-disc">
                            <li v-for="(message, field) in form.errors" :key="field">
                                {{ message }}
                            </li>
                        </ul>
                    </AlertDescription>
                </Alert>
            </section>
        </template>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="mb-3 flex items-center justify-between">
                <p v-if="selectedCount > 0" class="font-semibold tracking-tight text-lg">({{ selectedCount }}) penerima
                    manfaat
                    dipilih
                </p>
                <p v-else class="font-semibold tracking-tight text-lg">
                    Data Penerima Manfaat
                </p>
                <div class="flex gap-2">
                    <DropdownMenu :open="dropdownOpen" @update:open="dropdownOpen = $event">
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
                                <DropdownMenuSub>
                                    <DropdownMenuSubTrigger class="gap-1">
                                        <DatabaseIcon :size="18" />
                                        <p>Tampil data</p>
                                    </DropdownMenuSubTrigger>
                                    <DropdownMenuPortal>
                                        <DropdownMenuSubContent>
                                            <DropdownMenuRadioGroup v-model="form.count_per_page">
                                                <DropdownMenuRadioItem value="0" class="cursor-pointer gap-1">
                                                    <FileTextIcon :size="18" /> Semua data
                                                </DropdownMenuRadioItem>
                                                <DropdownMenuRadioItem value="10" class="cursor-pointer gap-1">
                                                    <FileTextIcon :size="18" /> 10 data
                                                </DropdownMenuRadioItem>
                                                <DropdownMenuRadioItem value="20" class="cursor-pointer gap-1">
                                                    <FileTextIcon :size="18" /> 20 data
                                                </DropdownMenuRadioItem>
                                                <DropdownMenuRadioItem value="30" class="cursor-pointer gap-1">
                                                    <FileTextIcon :size="18" /> 30 data
                                                </DropdownMenuRadioItem>
                                                <DropdownMenuRadioItem value="40" class="cursor-pointer gap-1">
                                                    <FileTextIcon :size="18" /> 40 data
                                                </DropdownMenuRadioItem>
                                                <DropdownMenuRadioItem value="50" class="cursor-pointer gap-1">
                                                    <FileTextIcon :size="18" /> 50 data
                                                </DropdownMenuRadioItem>
                                            </DropdownMenuRadioGroup>
                                        </DropdownMenuSubContent>
                                    </DropdownMenuPortal>
                                </DropdownMenuSub>
                                <DropdownMenuSub>
                                    <DropdownMenuSubTrigger class="gap-1">
                                        <DownloadIcon :size="18" />
                                        <p>Export penerima manfaat</p>
                                    </DropdownMenuSubTrigger>
                                    <DropdownMenuPortal>
                                        <DropdownMenuSubContent>
                                            <DropdownMenuItem class="cursor-pointer gap-1"
                                                @click="exportBeneficiaries('pdf');">
                                                <FileTextIcon :size="18" /> Export PDF
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1"
                                                @click="exportBeneficiaries('xlsx');">
                                                <SheetIcon :size="18" /> Export Excel
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1"
                                                @click="exportBeneficiaries('jpeg');">
                                                <ImageIcon :size="18" />Export JPG
                                            </DropdownMenuItem>
                                        </DropdownMenuSubContent>
                                    </DropdownMenuPortal>
                                </DropdownMenuSub>
                            </DropdownMenuGroup>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>

            <div>
                <Table>
                    <TableCaption class="mb-3">List dari data penerima manfaat Qolbu App.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="p-3">
                                <Checkbox :checked="areAllBeneficiariesSelected"
                                    @update:checked="toggleSelectAllCheckbox" />
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
                            <TableHead>Detail</TableHead>
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
                                    @update:checked="(checked: boolean) => toggleCheckbox(checked, beneficiary.nik)" />
                            </TableCell>
                            <TableCell>
                                {{ (beneficiaries.current_page - 1) * beneficiaries.per_page + index + 1 }}
                            </TableCell>
                            <TableCell>{{ beneficiary.nik }}</TableCell>
                            <TableCell>
                                <Link :href="route('ambassador.beneficiaries.show', beneficiary.nik)"
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
                                <Button size="icon" variant="outline">
                                    <Link :href="route('ambassador.beneficiaries.show', beneficiary.nik)"
                                        class="cursor-pointer flex gap-1">
                                    <BookUserIcon :size="18" />
                                    </Link>
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <PaginationComponent :pagination="props.beneficiaries" @page-change="handlePageChange" />
            </div>
        </section>
    </DashboardLayout>
</template>
