<script setup lang="ts">
import { defineProps, ref, computed, watch, Ref, onMounted } from "vue";
import { Link, useForm, Head, usePage } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
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
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
} from "@/components/ui/dropdown-menu";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import {
    EllipsisIcon,
    ChevronDownIcon,
    PencilIcon,
    Trash2Icon,
    DownloadIcon,
    FileTextIcon,
    SheetIcon,
    ImageIcon,
    FilePlusIcon,
    ChevronsUpDown,
    Check,
    ReceiptTextIcon,
    DatabaseIcon,
} from "lucide-vue-next";
import { Checkbox } from "@/components/ui/checkbox";
// LOCAL COMPONENTS
import { Ambassador, PaginatedIncomes } from "@/types";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useDateFormat } from "@vueuse/core";
import { formatCurrency, formatNumber, getImageUrl } from "@/lib/utils";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
import {
    CalendarDate,
} from '@internationalized/date'
import { DateRange } from "radix-vue";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command";
import { toast } from "vue-sonner";
import { Dialog, DialogClose, DialogFooter, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Separator } from "@/components/ui/separator";
import RangeCalendarIncome from "@/Pages/BoardMember/Income/_components/RangeCalendarIncome.vue";
import TopAmbassadorTable from "@/Pages/BoardMember/Beneficiary/_components/TopAmbassadorTable.vue";
import TopDonorTable from "@/Pages/BoardMember/Beneficiary/_components/TopDonorTable.vue";
import IncomeBarChart from "@/Pages/BoardMember/Beneficiary/_components/IncomeBarChart.vue";
import IncomeLineChart from "@/Pages/BoardMember/Beneficiary/_components/IncomeLineChart.vue";
import PaginationComponent from "@/Pages/BoardMember/Income/_components/PaginationComponent.vue";
import Loading from "@/components/Loading.vue";
import { Tabs, TabsList, TabsTrigger } from "@/components/ui/tabs";

// CREATIN INTERFACES FOR DATA
interface TopAmbassador {
    ambassador_id: number;
    total_amount: number;
    ambassador: Ambassador;
}

interface TopDonor {
    donor: string;
    total_amount: number;
}

const page = usePage();
const props = defineProps<{
    incomes: PaginatedIncomes;
    incomeTotalAmount: string;
    availableTeamCodes: { [key: number]: string };
    availableYears: string[];
    availableAmbassadors: { label: string; value: string }[];
    availableMonths: { label: string; value: string }[];
    incomesInWeeks: { label: string; value: string; start: string; end: string }[];
    topTenDonors: TopDonor[];
    topTenAmbassadors: TopAmbassador[];
    chartData: any;
    filters: {
        name: string;
        team_code: string;
        time: string;
        start_range: string;
        end_range: string;
        week: string;
        month: string;
        year: string;
        chart_type: string;
        count_per_page: string;
    };
}>();

// ========== SECTION 1 ==========
const openAmbassador = ref(false);
const openTeam = ref(false);
const value = ref<DateRange>({
    start: new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()),
    end: new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()).add({ days: 7 }),
}) as Ref<DateRange>

// ===============================
// ========== SECTION 2 ==========
// ===============================
// State untuk mengontrol apakah konten utama ditampilkan atau tidak
const showContentTwo = ref(false);

// Function untuk toggle (show/hide) konten utama
const toggleContentTwo = () => {
    showContentTwo.value = !showContentTwo.value;
};

// ===============================
// ========== SECTION 3 ==========
// ===============================
// SEARCHING AND FILTERING
// Form untuk pencarian dan filter
const form = useForm({
    name: 'default',
    team_code: 'default',
    time: 'monthly',
    start_range: '',
    end_range: '',
    week: '',
    month: '',
    year: '',
    chart_type: '',
    count_per_page: '10'
});

onMounted(() => {
    form.name = props.filters.name;
    form.team_code = props.filters.team_code;
    form.time = props.filters.time;
    form.start_range = props.filters.start_range;
    form.end_range = props.filters.end_range;
    form.week = props.filters.week;
    form.month = props.filters.month;
    form.year = props.filters.year;
    form.chart_type = props.filters.chart_type;
    form.count_per_page = props.filters.count_per_page;

    const flashMessage = (page.props.flash as { message?: string })?.message;
    if (flashMessage) {
        toast.success(flashMessage);
    }
});

watch(() => value.value, (newValue) => {
    if (newValue.start && newValue.end) {
        form.start_range = newValue.start.toString();
        form.end_range = newValue.end.toString();
    }
}, { deep: true });

watch(() => form.data(), (newData) => {
    form.get(route('ambassador.incomes.index'), {
        preserveState: true,
        data: newData,
    });
});

// ========================================
// ========== DATA TABLE SECTION ==========
// ========================================
const dropdownOpen = ref(false);
const selectedIncomes = ref<number[]>([]);
const clientLocale = ref(navigator.language || "en-US");
const isExporting = ref(false);

// Update the related functions
const selectedCount = computed(() => selectedIncomes.value.length);

const toggleCheckbox = (checked: boolean, id: number) => {
    if (checked) {
        selectedIncomes.value.push(id);
    } else {
        selectedIncomes.value = selectedIncomes.value.filter(
            (incomeId) => incomeId !== id
        );
    }
};

const toggleSelectAllCheckbox = (checked: boolean) => {
    if (checked) {
        const newIds = props.incomes.data.map(income => income.id);
        selectedIncomes.value = [...new Set([...selectedIncomes.value, ...newIds])];
    } else {
        selectedIncomes.value = [];
    }
}

const areAllBeneficiariesSelected = computed(() => {
    return props.incomes.data.length > 0 && props.incomes.data.every(income => selectedIncomes.value.includes(income.id));
})

const exportIncomes = async (exportType: string) => {
    isExporting.value = true;
    try {
        // Gunakan fetch untuk mendapatkan file
        const response = await fetch(`/api/incomes/export`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                ids: JSON.stringify(selectedIncomes.value),
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
        a.download = `income_export.${exportType}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        console.log(`Export successful as ${exportType}`);
        selectedIncomes.value = [];
        isExporting.value = false;
    } catch (error) {
        isExporting.value = false;
        console.error("Failed to export incomes:", error);
        toast.error("Failed to export incomes");
    }
};

// CODE FOR PAGINATION
const handlePageChange = (newPage: number) => {
    form.get(route('ambassador.incomes.index', { page: newPage }), {
        preserveState: true,
    });
};

const secondaryTitlePage = computed(() => {
    switch (form.time) {
        case 'custom':
            if (form.start_range && form.end_range) {
                const startDate = new Date(form.start_range);
                const endDate = new Date(form.end_range);
                const startFormat = useDateFormat(startDate, "DD MMM", { locales: clientLocale }).value;
                const endFormat = useDateFormat(endDate, "DD MMM YYYY", { locales: clientLocale }).value;

                if (startDate.getFullYear() === endDate.getFullYear()) {
                    if (startDate.getMonth() === endDate.getMonth()) {
                        return `${useDateFormat(startDate, "DD", { locales: clientLocale }).value} - ${endFormat}`;
                    }
                    return `${startFormat} - ${useDateFormat(endDate, "DD MMM YYYY", { locales: clientLocale }).value}`;
                }
                return `${startFormat} - ${endFormat}`;
            }
            return 'Kustom';
        case 'weekly':
            return form.week ? props.incomesInWeeks.find((week) => week.value === form.week)?.label : 'Mingguan';
        case 'monthly':
            return form.month && form.year ? `${props.availableMonths.find((month) => month.value === form.month)?.label} ${form.year}` : 'Bulanan';
        case 'yearly':
            return form.year ? `Year ${form.year}` : 'Tahunan';
        default:
            return 'Keseluruhan';
    }
});
</script>

<template>

    <Head>
        <title>Daftar Pendapatan</title>
    </Head>

    <DashboardLayout :containerClass="'max-w-screen-2xl'">
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">
                Daftar Pendapatan
            </h1>
        </template>

        <template v-if="isExporting">
            <Loading message="Mengekspor data..." />
        </template>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">
                        Pendapatan {{ secondaryTitlePage }}
                    </p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Daftar pendapatan yang telah ada pada range terpilih
                    </p>
                </div>
            </div>

            <div class="grid gap-3 grid-cols-2 md:grid-cols-4">
                <!-- Duta -->
                <div>
                    <Label for="team">Duta</Label>
                    <Popover v-model:open="openAmbassador">
                        <PopoverTrigger as-child>
                            <Button variant="outline" role="combobox" :aria-expanded="openAmbassador"
                                class="w-full justify-between">
                                {{ form.name ? props.availableAmbassadors.find((ambassador) => ambassador.label ===
                                    form.name)?.label || 'Semua'
                                    : 'Pilih Duta...' }}
                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="md:w-auto lg:w-[300px] p-0">
                            <Command v-model="form.name">
                                <CommandInput placeholder="Cari Duta..." />
                                <CommandEmpty>Tidak ada duta ditemukan.</CommandEmpty>
                                <CommandList>
                                    <CommandGroup>
                                        <CommandItem value="default" @select="openAmbassador = false">
                                            <Check :class="[
                                                'mr-2 h-4 w-4',
                                                form.name === 'default' ? 'opacity-100' : 'opacity-0',]" />
                                            Semua
                                        </CommandItem>
                                        <CommandItem v-for="ambassador in availableAmbassadors" :key="ambassador.value"
                                            :value="ambassador.label" @select="openAmbassador = false"
                                            class="overflow-hidden text-ellipsis">
                                            <Check :class="[
                                                'mr-2 h-4 w-4',
                                                form.name === ambassador.label ? 'opacity-100' : 'opacity-0',
                                            ]" />
                                            {{ ambassador.label }}
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                </div>

                <!-- Tim -->
                <div>
                    <Label for="team">Tim</Label>
                    <Popover v-model:open="openTeam">
                        <PopoverTrigger as-child>
                            <Button variant="outline" role="combobox" :aria-expanded="openTeam"
                                class="w-full justify-between">
                                {{ form.team_code
                                    ? (form.team_code === 'default' ? 'Semua' : form.team_code)
                                    : 'Pilih Tim...' }}
                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="md:w-auto lg:w-[300px] p-0">
                            <Command v-model="form.team_code">
                                <CommandInput placeholder="Cari Tim..." />
                                <CommandEmpty>Tidak ada tim ditemukan.</CommandEmpty>
                                <CommandList>
                                    <CommandGroup>
                                        <CommandItem value="default" @select="openTeam = false">
                                            <Check :class="[
                                                'mr-2 h-4 w-4',
                                                form.team_code === 'default' ? 'opacity-100' : 'opacity-0',]" />
                                            Semua
                                        </CommandItem>
                                        <CommandItem v-for="team in availableTeamCodes" :key="team" :value="team"
                                            @select="openTeam = false" class="overflow-hidden text-ellipsis">
                                            <Check :class="[
                                                'mr-2 h-4 w-4',
                                                form.team_code === team ? 'opacity-100' : 'opacity-0',
                                            ]" />
                                            {{ team }}
                                        </CommandItem>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                </div>

                <!-- Tipe Waktu -->
                <div>
                    <Label for="time">Tipe Waktu</Label>
                    <Select v-model="form.time">
                        <SelectTrigger>
                            <SelectValue :placeholder="form.time || 'Pilih Tipe Waktu'" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="default">Semua Data</SelectItem>
                            <SelectItem value="custom">Kustom</SelectItem>
                            <SelectItem value="weekly">Per-minggu</SelectItem>
                            <SelectItem value="monthly">Per-bulan</SelectItem>
                            <SelectItem value="yearly">Per-tahun</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Input Berdasarkan Tipe -->
                <div v-if="form.time && form.time !== 'default'">
                    <Label for="dateRange">Tanggal</Label>
                    <!-- Custom -->
                    <div v-if="form.time === 'custom'">
                        <RangeCalendarIncome v-model="value" :clientLocale="'id'" />
                    </div>

                    <!-- Per-minggu -->
                    <div v-if="form.time === 'weekly'">
                        <Select v-model="form.week">
                            <SelectTrigger>
                                <SelectValue :placeholder="form.week || 'Pilih Minggu'" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="week in props.incomesInWeeks" :key="week.value" :value="week.value">
                                    {{ week.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Per-bulan -->
                    <div v-if="form.time === 'monthly'" class="flex gap-2">
                        <Select v-model="form.year">
                            <SelectTrigger>
                                <SelectValue :placeholder="form.year || 'Pilih Tahun'" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="year in availableYears" :key="year" :value="year">
                                    {{ year }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Select v-model="form.month">
                            <SelectTrigger>
                                <SelectValue :placeholder="form.month || 'Pilih Bulan'" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="month in availableMonths" :key="month.value" :value="month.value">
                                    {{ month.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Per-tahun -->
                    <div v-if="form.time === 'yearly'">
                        <Select v-model="form.year">
                            <SelectTrigger>
                                <SelectValue :placeholder="form.year || 'Pilih Tahun'" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="year in availableYears" :key="year" :value="year">
                                    {{ year }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div :class="[
                'flex justify-between',
                { 'mb-3': showContentTwo },
            ]">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">
                        Overview {{ secondaryTitlePage }}
                    </p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Berikut adalah data yang telah diproses menjadi tabel dan chart
                    </p>
                </div>

                <!-- Button untuk toggle show/hide konten -->
                <Button size="icon" variant="outline" @click="toggleContentTwo" class="min-w-10">
                    <ChevronDownIcon :class="{ 'rotate-180': !showContentTwo }" :size="18" />
                </Button>
            </div>

            <div class="my-3 border border-neutral-300 p-2 rounded-lg dark:border-neutral-600">
                <p class="text-sm text-neutral-500 -mb-1 dark:text-neutral-400">Total transferan pada {{
                    secondaryTitlePage }}
                </p>
                <p class="font-semibold text-2xl tracking-tight break-words">{{ formatCurrency(incomeTotalAmount) }}</p>
            </div>

            <!-- Transition untuk smooth animasi -->
            <transition name="slide-fade">
                <!-- SHOW HIDE DISINI -->
                <div v-if="showContentTwo" class="grid gap-2 lg:grid-cols-2">
                    <div class="p-2 border border-neutral-300 rounded-lg dark:border-neutral-700">
                        <p class="text-center font-semibold tracking-tight break-words">Top 10 Duta - {{
                            secondaryTitlePage }}
                        </p>
                        <TopAmbassadorTable :top-ambassadors="topTenAmbassadors" />
                    </div>
                    <div class="p-2 border border-neutral-300 rounded-lg break-words dark:border-neutral-700">
                        <p class="text-center font-semibold tracking-tight">Top 10 Donatur - {{ secondaryTitlePage }}
                        </p>
                        <TopDonorTable :top-donors="topTenDonors" />
                    </div>
                    <div class="col-span-2 my-3">
                        <p class="text-sm font-semibold mb-1">Tipe Chart</p>
                        <Tabs default-value="daily" v-model="form.chart_type" @update-value="form.chart_type = $event">
                            <TabsList>
                                <TabsTrigger value="daily">
                                    Harian
                                </TabsTrigger>
                                <TabsTrigger value="monthly">
                                    Bulanan
                                </TabsTrigger>
                                <TabsTrigger value="yearly">
                                    Tahunan
                                </TabsTrigger>
                            </TabsList>
                        </Tabs>
                    </div>
                    <div>
                        <IncomeBarChart :chart-data="chartData" />
                    </div>
                    <div>
                        <IncomeLineChart :chart-data="chartData" />
                    </div>
                </div>
            </transition>

            <!-- <Separator class="my-3" /> -->
        </section>

        <section class="bg-white p-3 rounded-lg dark:bg-neutral-800">
            <div>

            </div>

            <div class="mb-3 flex items-center justify-between">
                <p v-if="selectedCount > 0" class="font-semibold tracking-tight text-lg">
                    ({{ selectedCount }}) pendapatan dipilih
                </p>
                <p v-else class="font-semibold tracking-tight text-lg">
                    Data Pendapatan
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
                                <DropdownMenuItem>
                                    <Link :href="route(
                                        'ambassador.incomes.create'
                                    )
                                        " class="flex items-center gap-1">
                                    <FilePlusIcon :size="18" />
                                    <p>Tambah transaksi</p>
                                    </Link>
                                </DropdownMenuItem>
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
                                        <p>Export transaksi</p>
                                    </DropdownMenuSubTrigger>
                                    <DropdownMenuPortal>
                                        <DropdownMenuSubContent>
                                            <DropdownMenuItem class="cursor-pointer gap-1"
                                                @click="exportIncomes('pdf');">
                                                <FileTextIcon :size="18" />
                                                Export PDF
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1"
                                                @click="exportIncomes('xlsx');">
                                                <SheetIcon :size="18" /> Export
                                                Excel
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1"
                                                @click="exportIncomes('jpeg');">
                                                <ImageIcon :size="18" />Export
                                                JPG
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
                    <TableCaption v-if="incomes.data.length > 0" class="mb-3">List dari data transaksi Qolbu App.
                    </TableCaption>
                    <TableHeader>
                        <TableRow>
                            <Label for="checkAll">
                                <TableHead class="p-3">
                                    <Checkbox id="checkAll" :checked="areAllBeneficiariesSelected"
                                        @update:checked="toggleSelectAllCheckbox" />
                                </TableHead>
                            </Label>
                            <TableHead>#</TableHead>
                            <TableHead class="min-w-[120px]">Tgl Submit</TableHead>
                            <TableHead class="min-w-[120px]">Tgl Transfer</TableHead>
                            <TableHead class="min-w-[150px]">Duta</TableHead>
                            <TableHead class="min-w-[150px]">Donatur</TableHead>
                            <TableHead class="min-w-[120px]">Metode</TableHead>
                            <TableHead>Jenis</TableHead>
                            <TableHead>Nominal</TableHead>
                            <TableHead>20%</TableHead>
                            <TableHead>Saldo</TableHead>
                            <TableHead>Yayasan</TableHead>
                            <TableHead>Total</TableHead>
                            <TableHead>FKY</TableHead>
                            <TableHead>Hak</TableHead>
                            <TableHead>Bukti</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <!-- IF DATA IS EMPTY -->
                        <TableRow v-if="incomes.data.length === 0">
                            <TableCell class="p-3" :colspan="15">
                                <p class="text-center text-neutral-500 dark:text-neutral-400">
                                    Tidak ada data pendapatan yang ditemukan.
                                </p>
                            </TableCell>
                        </TableRow>
                        <!-- IF DATA IS EXIST -->
                        <TableRow v-else v-for="(income, index) in incomes.data" :key="index"
                            class="odd:bg-neutral-100 even:bg-white dark:even:bg-neutral-700 dark:odd:bg-neutral-800">
                            <TableCell class="p-3">
                                <Checkbox :id="`income-${income.id}`" :checked="selectedIncomes.includes(income.id)"
                                    @update:checked="(checked: boolean) => toggleCheckbox(checked, income.id)" />
                            </TableCell>
                            <TableCell>
                                {{ (incomes.current_page - 1) * incomes.per_page + index + 1 }}
                            </TableCell>
                            <TableCell>
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger>{{
                                            useDateFormat(
                                                income.created_at,
                                                "DD MMM YYYY",
                                                {
                                                    locales: clientLocale,
                                                }
                                            )
                                        }}</TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{
                                                useDateFormat(
                                                    income.created_at,
                                                    "HH:MM:ss, DD MMM YYYY",
                                                    {
                                                        locales: clientLocale,
                                                    }
                                                )
                                            }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </TableCell>
                            <TableCell>
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger>{{
                                            useDateFormat(
                                                income.transfer_date,
                                                "DD MMM YYYY",
                                                {
                                                    locales: clientLocale,
                                                }
                                            )
                                        }}</TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{
                                                useDateFormat(
                                                    income.transfer_date,
                                                    "HH:MM:ss, DD MMM YYYY",
                                                    {
                                                        locales: clientLocale,
                                                    }
                                                )
                                            }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </TableCell>
                            <TableCell>{{ income.ambassador?.name }}</TableCell>
                            <TableCell>{{ income.donor }}</TableCell>
                            <TableCell>{{ income.payment_method }}</TableCell>
                            <TableCell>{{ income.type }}</TableCell>
                            <TableCell>{{
                                formatNumber(income.amount)
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber(income.amount * 0.2)
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber(income.amount - (income.amount * 0.2))
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber((income.amount - (income.amount * 0.2)) * 0.5)
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber((income.amount - (income.amount * 0.2)) - ((income.amount
                                    - (income.amount * 0.2)) * 0.5))
                            }}</TableCell>
                            <TableCell>{{
                                formatNumber(((income.amount - (income.amount * 0.2)) -
                                    ((income.amount
                                        - (income.amount * 0.2)) * 0.5)) * 0.2)
                            }}</TableCell>
                            <TableCell>{{
                                formatNumber((income.amount - (income.amount * 0.2)) - ((income.amount
                                    - (income.amount * 0.2)) * 0.5) - (((income.amount - (income.amount *
                                        0.2)) -
                                        ((income.amount
                                            - (income.amount * 0.2)) * 0.5)) * 0.2))
                            }}</TableCell>
                            <TableCell class="p-3">
                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button size="icon" variant="outline" :disabled="income.proof === null">
                                            <ReceiptTextIcon :size="18" />
                                        </Button>
                                    </DialogTrigger>
                                    <DialogScrollContent>
                                        <DialogHeader>
                                            <DialogTitle>Bukti Pendapatan</DialogTitle>
                                        </DialogHeader>
                                        <div>
                                            <Separator class="my-2 dark:bg-neutral-600" />
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <p
                                                        class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                                        Duta</p>
                                                    <p>{{ income.ambassador?.name }}</p>
                                                </div>
                                                <div>
                                                    <p
                                                        class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                                        Donatur</p>
                                                    <p>{{ income.donor }}</p>
                                                </div>
                                                <div>
                                                    <p
                                                        class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                                        Atas Nama</p>
                                                    <p>{{ income.on_behalf_of }}</p>
                                                </div>
                                                <div>
                                                    <p
                                                        class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                                        Nominal</p>
                                                    <p>{{ formatCurrency(income.amount) }}</p>
                                                </div>
                                                <div class="col-span-2">
                                                    <p
                                                        class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 mb-1">
                                                        Bukti Pendapatan</p>
                                                    <div v-if="income.proof">
                                                        <img :src="getImageUrl(`proof/${income.proof}`)"
                                                            class="w-full rounded-lg" />
                                                    </div>
                                                    <div v-else>
                                                        <p>Tidak unggah bukti pembayaran</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <DialogFooter>
                                            <DialogClose>
                                                <Button variant="outline">
                                                    Tutup
                                                </Button>
                                            </DialogClose>
                                        </DialogFooter>
                                    </DialogScrollContent>
                                </Dialog>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <!-- <pre>{{ incomes }}</pre> -->
                <PaginationComponent :pagination="props.incomes" @page-change="handlePageChange" />
            </div>
        </section>
    </DashboardLayout>
</template>
