<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3'
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Beneficiary } from '@/types';
import { Button } from '@/Components/ui/button';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
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
} from '@/Components/ui/dropdown-menu';
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
} from '@/Components/ui/alert-dialog';
import { BookUserIcon, DownloadIcon, EllipsisIcon, FilePlusIcon, FileTextIcon, ImageIcon, PencilIcon, SheetIcon, Trash2Icon } from 'lucide-vue-next';
import { Checkbox } from '@/Components/ui/checkbox';
import { useDateFormat } from '@vueuse/core';
// LOCAL CODE
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps<{
    beneficiaries: Beneficiary[];
}>();

// ========== DATA TABLE SECTION ==========
// State untuk melacak checkbox yang dipilih
const selectedBeneficiaries = ref<number[]>([]);
const clientLocale = ref(navigator.language || 'en-US');

// Computed property untuk menghitung jumlah akun yang dipilih
const selectedCount = computed(() => selectedBeneficiaries.value.length);

// Function untuk toggle checkbox
const toggleCheckbox = (checked: boolean, id: number) => {
    if (checked) {
        selectedBeneficiaries.value.push(id);
    } else {
        selectedBeneficiaries.value = selectedBeneficiaries.value.filter(userId => userId !== id);
    }
};

const deleteBeneficiary = (id: number) => {
    console.log("Delete beneficiary with id: " + id);
};

// Function untuk menghapus user yang dipilih
const deleteSelectedBeneficiaries = () => {
    console.log("Delete beneficiaries with ids: " + selectedBeneficiaries.value.join(", "));
    // Implement the actual deletion logic here
    // After deletion, clear the selection
    selectedBeneficiaries.value = [];
};

// Function untuk export beneficiaries
const exportTransactions = () => {
    const beneficiariesToExport = selectedBeneficiaries.value.length > 0
        ? selectedBeneficiaries.value
        : props.beneficiaries.map(beneficiary => beneficiary.id);
    // : props.transactions.data.map(transaction => transaction.id);

    console.log("Exporting beneficiaries with ids: ", beneficiariesToExport);
    selectedBeneficiaries.value = [];
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

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">Data Penerima Manfaat</p>
                    <p class="text-sm text-neutral-500">Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                        Eveniet
                        veniam, vero omnis dolores temporibus sed?</p>
                </div>
            </div>

            <div class="grid gap-3 grid-cols-2 sm:grid-cols-4">
                <div>
                    <Label for="name">Nama Anak</Label>
                    <Input type="text" id="name" placeholder="Cari nama..." />
                </div>
                <div>
                    <Label for="age">Usia</Label>
                    <Select id="age">
                        <SelectTrigger>
                            <SelectValue placeholder="Pilih Usia" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="default">Semua</SelectItem>
                                <SelectItem value="one">4-8 Tahun</SelectItem>
                                <SelectItem value="two">9-11 Tahun</SelectItem>
                                <SelectItem value="three">12-15 Tahun</SelectItem>
                                <SelectItem value="four">16-18 Tahun</SelectItem>
                                <SelectItem value="five">19-21 Tahun</SelectItem>
                                <SelectItem value="other">>21 Tahun</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="education">Pendidikan</Label>
                    <Select id="education">
                        <SelectTrigger>
                            <SelectValue placeholder="Pilih Pendidikan" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="default">Semua</SelectItem>
                                <SelectItem value="tk">TK (Taman Kanak-kanak)</SelectItem>
                                <SelectItem value="sd">SD (Sekolah Dasar)</SelectItem>
                                <SelectItem value="smp">SMP (Sekolah Menengah Pertama)</SelectItem>
                                <SelectItem value="sma">SMA (Sekolah Menengah Atas)</SelectItem>
                                <SelectItem value="university">Universitas</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Label for="status">Status</Label>
                    <Select id="status">
                        <SelectTrigger>
                            <SelectValue placeholder="Pilih Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="default">Semua</SelectItem>
                                <SelectItem value="partherless">Yatim</SelectItem>
                                <SelectItem value="motherless">Piatu</SelectItem>
                                <SelectItem value="orphan">Yatim Piatu</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </section>

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
                                    <Link :href="route('pengurus.beneficiaries.create')"
                                        class="flex items-center gap-1">
                                    <FilePlusIcon :size="18" />
                                    <p>Tambah penerima manfaat</p>
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSub>
                                    <DropdownMenuSubTrigger class="gap-1">
                                        <DownloadIcon :size="18" />
                                        <p>Export penerima manfaat</p>
                                    </DropdownMenuSubTrigger>
                                    <DropdownMenuPortal>
                                        <DropdownMenuSubContent>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportTransactions">
                                                <FileTextIcon :size="18" /> Export PDF
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportTransactions">
                                                <SheetIcon :size="18" /> Export Excel
                                            </DropdownMenuItem>
                                            <DropdownMenuItem class="cursor-pointer gap-1" @click="exportTransactions">
                                                <ImageIcon :size="18" />Export JPG
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
                                                <AlertDialogAction @click="deleteSelectedBeneficiaries">Ya, Hapus
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
                    <TableCaption>List dari data penerima manfaat Qolbu App.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead></TableHead>
                            <TableHead>#</TableHead>
                            <TableHead class="min-w-[120px]">NIK</TableHead>
                            <TableHead class="min-w-[120px]">Nama</TableHead>
                            <TableHead class="min-w-[120px]">RT/RW</TableHead>
                            <TableHead class="min-w-[120px]">JK</TableHead>
                            <TableHead class="min-w-[170px]">TTL</TableHead>
                            <TableHead>Pendidikan</TableHead>
                            <TableHead class="min-w-[120px]">Ayah</TableHead>
                            <TableHead class="min-w-[120px]">Ibu</TableHead>
                            <TableHead>No. Telp</TableHead>
                            <TableHead>No. Akte Kematian</TableHead>
                            <TableHead>Keterangan</TableHead>
                            <TableHead>Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(beneficiary, index) in beneficiaries" :key="index">
                            <TableCell class="p-3">
                                <Checkbox :id="`transaction-${beneficiary.id}`"
                                    :checked="selectedBeneficiaries.includes(beneficiary.id)"
                                    @update:checked="(checked) => toggleCheckbox(checked, beneficiary.id)" />
                            </TableCell>
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>317219138129</TableCell>
                            <TableCell>{{ beneficiary.name }}</TableCell>
                            <TableCell>003/001 Sukup Latest</TableCell>
                            <TableCell>Laki-laki</TableCell>
                            <TableCell>Bandung, {{ useDateFormat(beneficiary.birthdate, 'DD MMM YYYY', {
                                locales:
                                    clientLocale
                            }) }}</TableCell>
                            <TableCell>SD</TableCell>
                            <TableCell>{{ beneficiary.parent_father }}</TableCell>
                            <TableCell>{{ beneficiary.parent_mother }}</TableCell>
                            <TableCell>087789892020</TableCell>
                            <TableCell>AK/29/TOT/KENT</TableCell>
                            <TableCell>Y-Team</TableCell>
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
                                            <Link :href="route('pengurus.beneficiaries.show', beneficiary.id)"
                                                class="cursor-pointer flex gap-1">
                                            <BookUserIcon :size="18" /> Detail
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('pengurus.beneficiaries.edit', beneficiary.id)"
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
                                                                @click="deleteBeneficiary(beneficiary.id)">Ya,
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
