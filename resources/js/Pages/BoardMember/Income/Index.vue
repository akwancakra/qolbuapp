<script setup lang="ts">
import { defineProps, ref, computed, watch, Ref, onMounted } from "vue";
import { Link, useForm, Head, usePage } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
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
} from "@/Components/ui/dropdown-menu";
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
} from "@/Components/ui/alert-dialog";
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
    Loader2,
} from "lucide-vue-next";
import { BarChart } from "@/Components/ui/chart-bar";
import { Checkbox } from "@/Components/ui/checkbox";
// LOCAL COMPONENTS
import { PaginatedIncomes } from "@/types";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useDateFormat } from "@vueuse/core";
import { formatNumber } from "@/lib/utils";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/Components/ui/tooltip";
import {
    CalendarDate,
} from '@internationalized/date'
import { DateRange } from "radix-vue";
import PaginationComponent from "./_components/PaginationComponent.vue";
import RangeCalendarIncome from "./_components/RangeCalendarIncome.vue";
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/Components/ui/command";
import { Inertia } from "@inertiajs/inertia";
import { toast } from "vue-sonner";

const page = usePage();
const props = defineProps<{
    incomes: PaginatedIncomes;
    availableTeamCodes: string[];
    availableYears: string[];
    availableAmbassadors: { label: string; value: string }[];
    availableMonths: { label: string; value: string }[];
    incomesInWeeks: { label: string; value: string; start: string; end: string }[];
    filters: {
        name: string;
        teamCode: string;
        time: string;
        startRange: string;
        endRange: string;
        week: string;
        month: string;
        year: string;
    };
}>();

// ========== SECTION 1 ==========
const openAmbassador = ref(false);
const openTeam = ref(false);
const value = ref<DateRange>({
    start: new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()),
    end: new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()).add({ days: 7 }),
}) as Ref<DateRange>

// ========== SECTION 2 ==========
// State untuk mengontrol apakah konten utama ditampilkan atau tidak
const showContentTwo = ref(false);

// Function untuk toggle (show/hide) konten utama
const toggleContentTwo = () => {
    showContentTwo.value = !showContentTwo.value;
};

// CHART DATA
const data = [
    { day: "17 Sen", pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: "18 Sel", pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: "19 Rab", pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: "20 Kam", pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: "21 Jum", pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: "22 Sab", pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: "23 Min", pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
];

// ========== SECTION 3 ==========
// SEARCHING AND FILTERING
// Form untuk pencarian dan filter
const form = useForm({
    name: 'default',
    teamCode: 'default',
    time: 'monthly',
    startRange: '',
    endRange: '',
    week: '',
    month: '',
    year: '',
});

onMounted(() => {
    form.name = props.filters.name;
    form.teamCode = props.filters.teamCode;
    form.time = props.filters.time;
    form.startRange = props.filters.startRange;
    form.endRange = props.filters.endRange;
    form.week = props.filters.week;
    form.month = props.filters.month;
    form.year = props.filters.year;

    const flashMessage = (page.props.flash as { message?: string })?.message;
    if (flashMessage) {
        toast.success(flashMessage);
    }
});

watch(() => value.value, (newValue) => {
    if (newValue.start && newValue.end) {
        form.startRange = newValue.start.toString();
        form.endRange = newValue.end.toString();
    }
}, { deep: true });

watch(() => form.data(), (newData) => {
    form.get(route('board_member.incomes.index'), {
        preserveState: true,
        data: newData,
    });

    console.log(newData);
});

// Replace selectedTransactions with selectedIncomes
const selectedIncomes = ref<number[]>([]);
const clientLocale = ref(navigator.language || "en-US");

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

const deleteIncome = (id: number) => {
    Inertia.delete(route('board_member.incomes.destroy', id), {
        onSuccess: () => {
            toast.success("Berhasil menghapus pendapatan");
        },
        onError: (errors) => {
            toast.error(errors);
            console.error("Error saat menghapus pendapatan:", errors);
        },
    });
};

// Function untuk menghapus income yang dipilih
const deleteSelectedIncomes = () => {
    Inertia.post(route('board_member.incomes.destroy-multiple'), {
        ids: selectedIncomes.value
    }, {
        preserveState: true,
        onSuccess: () => {
            selectedIncomes.value = [];
            toast.success("Berhasil menghapus beberapa pendapatan");
        },
        onError: (errors) => {
            toast.error(errors);
            console.error("Error saat menghapus beberapa pendapatan:", errors);
        },
    });
};

const exportIncomes = () => {
    const incomesToExport =
        selectedIncomes.value.length > 0
            ? selectedIncomes.value
            : props.incomes.data.map((income) => income.id);

    console.log("Exporting incomes with ids: ", incomesToExport);
    selectedIncomes.value = [];
};

// CODE FOR PAGINATION
const handlePageChange = (newPage: number) => {
    form.get(route('board_member.incomes.index', { page: newPage }), {
        preserveState: true,
    });
};
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

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">
                        Pendapatan Minggu 1 Sept
                    </p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Lorem ipsum, dolor sit amet consectetur adipisicing
                        elit. Eveniet veniam, vero omnis dolores temporibus sed?
                    </p>
                </div>
            </div>

            <div class="grid gap-3 grid-cols-2 md:grid-cols-4">
                <!-- Nama Duta -->
                <div>
                    <!-- example label: "Saodah"
                    example value: 2 -->
                    <Label for="team">Duta</Label>
                    <Popover v-model:open="openAmbassador">
                        <PopoverTrigger as-child>
                            <Button variant="outline" role="combobox" :aria-expanded="openAmbassador"
                                class="w-full justify-between">
                                {{ form.name ? availableAmbassadors.find((ambassador) => ambassador.label ===
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
                                {{ form.teamCode
                                    ? (form.teamCode === 'default' ? 'Semua' : form.teamCode)
                                    : 'Pilih Tim...' }}
                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="md:w-auto lg:w-[300px] p-0">
                            <Command v-model="form.teamCode">
                                <CommandInput placeholder="Cari Tim..." />
                                <CommandEmpty>Tidak ada tim ditemukan.</CommandEmpty>
                                <CommandList>
                                    <CommandGroup>
                                        <CommandItem value="default" @select="openTeam = false">
                                            <Check :class="[
                                                'mr-2 h-4 w-4',
                                                form.teamCode === 'default' ? 'opacity-100' : 'opacity-0',]" />
                                            Semua
                                        </CommandItem>
                                        <CommandItem v-for="team in availableTeamCodes" :key="team" :value="team"
                                            @select="openTeam = false" class="overflow-hidden text-ellipsis">
                                            <Check :class="[
                                                'mr-2 h-4 w-4',
                                                form.teamCode === team ? 'opacity-100' : 'opacity-0',
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
                        Overview Minggu 1 Sept
                    </p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Lorem ipsum, dolor sit amet consectetur adipisicing
                        elit. Eveniet veniam, vero omnis dolores temporibus sed?
                    </p>
                </div>

                <!-- Button untuk toggle show/hide konten -->
                <Button size="icon" variant="outline" @click="toggleContentTwo" class="min-w-10">
                    <ChevronDownIcon :class="{ 'rotate-180': !showContentTwo }" :size="18" />
                </Button>
            </div>

            <div class="my-3 border border-neutral-300 p-2 rounded-lg dark:border-neutral-600">
                <p class="text-sm text-neutral-500 -mb-1 dark:text-neutral-400">Total transferan pada minggu 1 sept</p>
                <p class="font-semibold text-2xl tracking-tight break-words">Rp32.000.000</p>
            </div>

            <!-- Transition untuk smooth animasi -->
            <transition name="slide-fade">
                <!-- SHOW HIDE DISINI -->
                <div v-if="showContentTwo" class="grid gap-2 lg:grid-cols-2">
                    <div class="p-2 border border-neutral-300 rounded-lg dark:border-neutral-700">
                        <p class="text-center font-semibold tracking-tight">Top 10 Duta - Minggu 1 Sept</p>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="h-8">#</TableHead>
                                    <TableHead class="h-8">Nama</TableHead>
                                    <TableHead class="h-8">Nominal</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow class="bg-yellow-100 font-semibold dark:bg-yellow-500 dark:text-yellow-100">
                                    <TableCell class="py-1.5">1</TableCell>
                                    <TableCell class="py-1.5">Josephine Lily</TableCell>
                                    <TableCell class="py-1.5">Rp10.000.000</TableCell>
                                </TableRow>
                                <TableRow class="bg-gray-100 font-semibold dark:bg-gray-500 dark:text-gray-100">
                                    <TableCell class="py-1.5">2</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow
                                    class="bg-yellow-700 text-white font-semibold dark:bg-yellow-800 dark:text-yellow-100 hover:text-white hover:bg-yellow-800">
                                    <TableCell class="py-1.5">3</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">4</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">5</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">6</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">7</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">8</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">9</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">10</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div class="p-2 border border-neutral-300 rounded-lg dark:border-neutral-700">
                        <p class="text-center font-semibold tracking-tight">Top 10 Donatur - Minggu 1 Sept</p>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="h-8">#</TableHead>
                                    <TableHead class="h-8">Nama</TableHead>
                                    <TableHead class="h-8">Nominal</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow class="bg-yellow-100 font-semibold dark:bg-yellow-500 dark:text-yellow-100">
                                    <TableCell class="py-1.5">1</TableCell>
                                    <TableCell class="py-1.5">Josephine Lily</TableCell>
                                    <TableCell class="py-1.5">Rp10.000.000</TableCell>
                                </TableRow>
                                <TableRow class="bg-gray-100 font-semibold dark:bg-gray-500 dark:text-gray-100">
                                    <TableCell class="py-1.5">2</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow
                                    class="bg-yellow-700 text-white font-semibold dark:bg-yellow-800 dark:text-yellow-100 hover:text-white hover:bg-yellow-800">
                                    <TableCell class="py-1.5">3</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">4</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">5</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">6</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">7</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">8</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">9</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                                <TableRow>
                                    <TableCell class="py-1.5">10</TableCell>
                                    <TableCell class="py-1.5">Andreas Thon</TableCell>
                                    <TableCell class="py-1.5">Rp9.500.000</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div>
                        <BarChart :data="data" index="day" :categories="['pendapatan']" :y-formatter="(tick) => {
                            return typeof tick === 'number'
                                ? `Rp ${new Intl.NumberFormat(
                                    'id-ID'
                                ).format(tick)}`
                                : '';
                        }
                            " :colors="['#58bb69']" />
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
                                <DropdownMenuItem>
                                    <Link :href="route(
                                        'board_member.incomes.create'
                                    )
                                        " class="flex items-center gap-1">
                                    <FilePlusIcon :size="18" />
                                    <p>Tambah transaksi</p>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSub>
                                    <DropdownMenuSubTrigger class="gap-1">
                                        <DownloadIcon :size="18" />
                                        <p>Export transaksi</p>
                                    </DropdownMenuSubTrigger>
                                    <DropdownMenuPortal>
                                        <DropdownMenuSubContent>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportIncomes">
                                                <FileTextIcon :size="18" />
                                                Export PDF
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportIncomes">
                                                <SheetIcon :size="18" /> Export
                                                Excel
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportIncomes">
                                                <ImageIcon :size="18" />Export
                                                JPG
                                            </DropdownMenuItem>
                                        </DropdownMenuSubContent>
                                    </DropdownMenuPortal>
                                </DropdownMenuSub>
                                <DropdownMenuItem v-if="selectedCount > 0" as-child>
                                    <AlertDialog>
                                        <AlertDialogTrigger
                                            class="flex items-center gap-1.5 text-sm px-1.5 py-1 w-full rounded text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-800 dark:hovere:bg-red-700 dark:text-red-200">
                                            <Trash2Icon :size="18" />Hapus
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>Apakah anda yakin?</AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    Tindakan ini akan menghapus pendapatan yang dipilih secara permanen.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                                <AlertDialogAction as-child>
                                                    <Button variant="destructive"
                                                        class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                                        @click="deleteSelectedIncomes
                                                            ">Ya,
                                                        Hapus</Button>
                                                </AlertDialogAction>
                                                <!-- <AlertDialogAction @click="deleteSelectedIncomes">
                                                    Ya, Hapus
                                                </AlertDialogAction> -->
                                            </AlertDialogFooter>
                                        </AlertDialogContent>
                                    </AlertDialog>
                                </DropdownMenuItem>
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
                            <TableHead></TableHead>
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
                            <TableHead>Aksi</TableHead>
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
                                    @update:checked="(checked) => toggleCheckbox(checked, income.id)" />
                            </TableCell>
                            <TableCell>{{ index + 1 }}</TableCell>
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
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button size="icon" variant="outline">
                                            <EllipsisIcon :size="18" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>Aksi</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <!-- Link untuk mengedit user -->
                                        <DropdownMenuItem as-child>
                                            <Link :href="route(
                                                'board_member.incomes.edit',
                                                income.id
                                            )
                                                " class="cursor-pointer flex gap-1">
                                            <PencilIcon :size="18" /> Ubah
                                            </Link>
                                        </DropdownMenuItem>
                                        <!-- Item untuk hapus dengan background merah -->
                                        <DropdownMenuItem as-child>
                                            <AlertDialog>
                                                <AlertDialogTrigger
                                                    class="flex items-center gap-1.5 text-sm px-1.5 py-1 w-full rounded text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-800 dark:hovere:bg-red-700 dark:text-red-200">
                                                    <Trash2Icon :size="18" />Hapus
                                                </AlertDialogTrigger>
                                                <AlertDialogContent>
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>Apakah anda yakin?</AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            Tindakan ini akan menghapus pendapatan secara permanen.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Batal</AlertDialogCancel>
                                                        <AlertDialogAction as-child>
                                                            <Button variant="destructive"
                                                                class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                                                @click="
                                                                    deleteIncome(
                                                                        income.id
                                                                    )
                                                                    ">Ya,
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

                <!-- <pre>{{ incomes }}</pre> -->
                <PaginationComponent :pagination="props.incomes" @page-change="handlePageChange" />
            </div>
        </section>
    </DashboardLayout>
</template>
