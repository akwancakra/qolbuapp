<script setup lang="ts">
import { defineProps, ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { Button } from "@/Components/ui/button";
import { Input } from "@/Components/ui/input";
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
    SelectGroup,
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
    CalendarIcon,
} from "lucide-vue-next";
import { Head } from "@inertiajs/vue3";
import { BarChart } from "@/Components/ui/chart-bar";
import { Checkbox } from "@/Components/ui/checkbox";
// LOCAL COMPONENTS
import { Transaction } from "@/types";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useDateFormat } from "@vueuse/core";
import { cn, formatCurrency, formatNumber } from "@/lib/utils";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/Components/ui/popover";
import { Calendar } from "@/Components/ui/calendar";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/Components/ui/tooltip";
import { Separator } from "@/Components/ui/separator";

const props = defineProps<{
    transactions: Transaction[];
}>();

// ========== SECTION 1 ==========
// Buat reactive `value` untuk menyimpan tanggal
const value = ref<Date | null>(null);

// Gunakan Intl.DateTimeFormat untuk memformat tanggal
const df = new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'long',
});

// Fungsi untuk memformat tanggal
const formatDate = (date: Date | null) => {
    if (!date || !(date instanceof Date) || isNaN(date.getTime())) {
        return "Pilih tanggal";
    }
    try {
        return df.format(date);
    } catch (error) {
        console.error("Error formatting date:", error);
        return "Pilih tanggal";
    }
};

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
// State untuk melacak checkbox yang dipilih
const selectedTransactions = ref<number[]>([]);
const clientLocale = ref(navigator.language || "en-US");

// Computed property untuk menghitung jumlah akun yang dipilih
const selectedCount = computed(() => selectedTransactions.value.length);

// Function untuk toggle checkbox
const toggleCheckbox = (checked: boolean, id: number) => {
    if (checked) {
        selectedTransactions.value.push(id);
    } else {
        selectedTransactions.value = selectedTransactions.value.filter(
            (userId) => userId !== id
        );
    }
};

const deleteTransaction = (id: number) => {
    console.log("Delete transaction with id: " + id);
};

// Function untuk menghapus user yang dipilih
const deleteSelectedTransactions = () => {
    console.log(
        "Delete transactions with ids: " + selectedTransactions.value.join(", ")
    );
    // Implement the actual deletion logic here
    // After deletion, clear the selection
    selectedTransactions.value = [];
};

// Function untuk export transaksi
const exportTransactions = () => {
    const transactionsToExport =
        selectedTransactions.value.length > 0
            ? selectedTransactions.value
            : props.transactions.map((transaction) => transaction.id);
    // : props.transactions.data.map(transaction => transaction.id);

    console.log("Exporting transactions with ids: ", transactionsToExport);
    selectedTransactions.value = [];
};
</script>

<template>

    <Head>
        <title>Pendapatan Transferan</title>
    </Head>

    <DashboardLayout :containerClass="'max-w-screen-2xl'">
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">
                Pendapatan Transferan
            </h1>
        </template>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">
                        Transferan Minggu 1 Sept
                    </p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Lorem ipsum, dolor sit amet consectetur adipisicing
                        elit. Eveniet veniam, vero omnis dolores temporibus sed?
                    </p>
                </div>
            </div>

            <div class="grid gap-3 grid-cols-2 md:grid-cols-4">
                <div>
                    <Label for="name">Nama Duta</Label>
                    <Input type="text" id="name" placeholder="Cari nama duta..." />
                </div>
                <div>
                    <Label for="type">Tim</Label>
                    <Select>
                        <SelectTrigger>
                            <SelectValue placeholder="Pilih Tim" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="default">Semua</SelectItem>
                                <SelectItem value="one">Tim 99</SelectItem>
                                <SelectItem value="two">Tim 98</SelectItem>
                                <SelectItem value="three">Tim 97</SelectItem>
                                <SelectItem value="four">Tim 96</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="type">Tipe Waktu</Label>
                    <Select>
                        <SelectTrigger>
                            <SelectValue placeholder="Pilih Waktu" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="default">Default (Per-bulan)</SelectItem>
                                <SelectItem value="weekly">Per-minggu</SelectItem>
                                <SelectItem value="monthly">Per-bulan</SelectItem>
                                <SelectItem value="yearly">Per-tahun</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="type">Tanggal</Label>
                    <Popover>
                        <PopoverTrigger as-child>
                            <Button variant="outline" :class="cn(
                                'w-full justify-start text-left font-normal',
                                !value && 'text-muted-foreground'
                            )">
                                <CalendarIcon class="mr-2 h-4 w-4" />
                                {{ formatDate(value) }}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-auto p-0">
                            <Calendar v-model="value" :initial-focus="true" />
                        </PopoverContent>
                    </Popover>
                </div>
            </div>
        </section>

        <!-- <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div>
                <p class="font-semibold text-2xl tracking-tight">
                    Keseluruhan Per-Bulan
                </p>
                <p class="text-sm text-neutral-500">
                    Lorem ipsum, dolor sit amet consectetur adipisicing
                    elit. Eveniet veniam, vero omnis dolores temporibus sed?
                </p>
            </div>
        </section> -->

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
                        <!-- <ul class="list-inside list-decimal">
                            <li class="font-semibold text-lg">
                                Josephine Lily - Rp10.000.000
                            </li>
                            <li>Andreas Thon - Rp10.000.000</li>
                            <li>Wocky Minu - Rp10.000.000</li>
                            <li>Borodin Dmitry - Rp10.000.000</li>
                            <li>Josephine Lily - Rp10.000.000</li>
                            <li>Andreas Thon - Rp10.000.000</li>
                            <li>Wocky Minu - Rp10.000.000</li>
                            <li>Borodin Dmitry - Rp10.000.000</li>
                            <li>Josephine Lily - Rp10.000.000</li>
                            <li>Andreas Thon - Rp10.000.000</li>
                        </ul> -->
                    </div>
                    <!-- <div class="flex justify-center items-center bg-neutral-200">
                        <p>Data 2</p>
                    </div> -->
                </div>
            </transition>

            <!-- <Separator class="my-3" /> -->
        </section>

        <section class="bg-white p-3 rounded-lg dark:bg-neutral-800">
            <div>

            </div>

            <div class="mb-3 flex items-center justify-between">
                <p v-if="selectedCount > 0" class="font-semibold tracking-tight text-lg">
                    ({{ selectedCount }}) transaksi dipilih
                </p>
                <p v-else class="font-semibold tracking-tight text-lg">
                    Data Transaksi
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
                                        'pengurus.transactions.create'
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
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportTransactions">
                                                <FileTextIcon :size="18" />
                                                Export PDF
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportTransactions">
                                                <SheetIcon :size="18" /> Export
                                                Excel
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportTransactions">
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
                                        <AlertDialogContent class="p-4">
                                            <AlertDialogHeader>
                                                <AlertDialogTitle>Apakah anda
                                                    yakin?</AlertDialogTitle>
                                                <AlertDialogDescription>
                                                    This action cannot be
                                                    undone. This will
                                                    permanently delete the
                                                    selected accounts and remove
                                                    their data from our servers.
                                                </AlertDialogDescription>
                                            </AlertDialogHeader>
                                            <AlertDialogFooter>
                                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                                <AlertDialogAction @click="deleteSelectedTransactions
                                                    ">Ya, Hapus
                                                </AlertDialogAction>
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
                    <TableCaption>List dari data transaksi Qolbu App.</TableCaption>
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
                        <TableRow v-for="(transaction, index) in transactions" :key="index"
                            class="odd:bg-neutral-100 even:bg-white dark:even:bg-neutral-700 dark:odd:bg-neutral-800">
                            <TableCell class="p-3">
                                <Checkbox :id="`transaction-${transaction.id}`" :checked="selectedTransactions.includes(
                                    transaction.id
                                )
                                    " @update:checked="(checked) =>
                                        toggleCheckbox(
                                            checked,
                                            transaction.id
                                        )
                                        " />
                            </TableCell>
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger>{{
                                            useDateFormat(
                                                transaction.created_at,
                                                "DD MMM YYYY",
                                                {
                                                    locales: clientLocale,
                                                }
                                            )
                                        }}</TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{
                                                useDateFormat(
                                                    transaction.created_at,
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
                                                transaction.transfer_date,
                                                "DD MMM YYYY",
                                                {
                                                    locales: clientLocale,
                                                }
                                            )
                                        }}</TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{
                                                useDateFormat(
                                                    transaction.transfer_date,
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
                            <TableCell>{{ transaction.duta }}</TableCell>
                            <TableCell>{{ transaction.donatur }}</TableCell>
                            <TableCell>{{ transaction.metode }}</TableCell>
                            <TableCell>{{ transaction.jenis }}</TableCell>
                            <TableCell>{{
                                formatNumber(transaction.nominal)
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber(transaction.nominal * 0.2)
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber(transaction.nominal - (transaction.nominal * 0.2))
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber((transaction.nominal - (transaction.nominal * 0.2)) * 0.5)
                                }}</TableCell>
                            <TableCell>{{
                                formatNumber((transaction.nominal - (transaction.nominal * 0.2)) - ((transaction.nominal
                                    - (transaction.nominal * 0.2)) * 0.5))
                            }}</TableCell>
                            <TableCell>{{
                                formatNumber(((transaction.nominal - (transaction.nominal * 0.2)) -
                                    ((transaction.nominal
                                        - (transaction.nominal * 0.2)) * 0.5)) * 0.2)
                            }}</TableCell>
                            <TableCell>{{
                                formatNumber((transaction.nominal - (transaction.nominal * 0.2)) - ((transaction.nominal
                                    - (transaction.nominal * 0.2)) * 0.5) - (((transaction.nominal - (transaction.nominal *
                                        0.2)) -
                                        ((transaction.nominal
                                            - (transaction.nominal * 0.2)) * 0.5)) * 0.2))
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
                                                'pengurus.transactions.edit',
                                                transaction.id
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
                                                <AlertDialogContent class="p-4">
                                                    <AlertDialogHeader>
                                                        <AlertDialogTitle>Apakah anda
                                                            yakin?</AlertDialogTitle>
                                                        <AlertDialogDescription>
                                                            This action cannot
                                                            be undone. This will
                                                            permanently delete
                                                            your account and
                                                            remove your data
                                                            from our servers.
                                                        </AlertDialogDescription>
                                                    </AlertDialogHeader>
                                                    <AlertDialogFooter>
                                                        <AlertDialogCancel>Batal</AlertDialogCancel>
                                                        <AlertDialogAction as-child>
                                                            <Button variant="destructive"
                                                                class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                                                                @click="
                                                                    deleteTransaction(
                                                                        transaction.id
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

                <!-- <Pagination v-slot="{ page }" :total="transactions.total" :sibling-count="1" show-edges
                    :default-page="transactions.current_page">
                    <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                        <PaginationFirst />
                        <PaginationPrev />

                        <template v-for="(item, index) in items">
                            <PaginationListItem v-if="item.type === 'page'" :key="index" :value="item.value" as-child>
                                <Button class="w-10 h-10 p-0" :variant="item.value === page ? 'default' : 'outline'">
                                    {{ item.value }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsisIcon v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext />
                        <PaginationLast />
                    </PaginationList>
                </Pagination> -->
            </div>
        </section>
    </DashboardLayout>
</template>
