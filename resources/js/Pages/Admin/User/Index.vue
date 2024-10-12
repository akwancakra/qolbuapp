<script setup lang="ts">
import { defineProps, ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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
} from '@/components/ui/alert-dialog';
import { Ellipsis, ChevronDown, Pencil, Trash2 } from 'lucide-vue-next';
import { Head } from '@inertiajs/vue3';
import { BarChart } from '@/components/ui/chart-bar';
import { Checkbox } from '@/Components/ui/checkbox';
// LOCAL COMPONENTS
import CustomChartTooltip from './_components/CustomChartTooltip.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
    users: {
        type: Array,
    },
});

// SECTION 2
// State untuk mengontrol apakah konten utama ditampilkan atau tidak
const showContentTwo = ref(true);

// Function untuk toggle (show/hide) konten utama
const toggleContentTwo = () => {
    showContentTwo.value = !showContentTwo.value;
};

// CHART DATA
const data = [
    { day: '17 Sen', pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: '18 Sel', pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: '19 Rab', pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: '20 Kam', pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: '21 Jum', pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: '22 Sab', pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
    { day: '23 Min', pendapatan: Math.floor(Math.random() * 2000000) + 500000 },
];

// SECTION 3
// State untuk melacak checkbox yang dipilih
const selectedUsers = ref<number[]>([]);

// Computed property untuk menghitung jumlah akun yang dipilih
const selectedCount = computed(() => selectedUsers.value.length);

// Function untuk toggle checkbox
const toggleCheckbox = (id: number) => {
    const index = selectedUsers.value.indexOf(id);
    if (index > -1) {
        selectedUsers.value.splice(index, 1);
    } else {
        selectedUsers.value.push(id);
    }
};

// Function untuk menghapus user yang dipilih
const deleteSelectedUsers = () => {
    console.log("Delete users with ids: " + selectedUsers.value.join(", "));
    // Implement the actual deletion logic here
    // After deletion, clear the selection
    selectedUsers.value = [];
};
</script>

<template>

    <Head>
        <title>Dashboard User</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Dashboard User</h1>
        </template>

        <section class="bg-white p-3 rounded-lg mb-3">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">Transferan Minggu 1 Sept</p>
                    <p class="text-sm text-neutral-500">Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                        Eveniet
                        veniam, vero omnis dolores temporibus sed?</p>
                </div>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button size="icon" variant="outline">
                            <Ellipsis size="18" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuLabel>My Account</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem>Profile</DropdownMenuItem>
                        <DropdownMenuItem>Billing</DropdownMenuItem>
                        <DropdownMenuItem>Team</DropdownMenuItem>
                        <DropdownMenuItem>Subscription</DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div class="grid gap-3 grid-cols-2 sm:grid-cols-4">
                <div>
                    <Label for="name">Name</Label>
                    <Input type="text" id="name" />
                </div>
                <div>
                    <Label for="type">Tipe Waktu</Label>
                    <Select>
                        <SelectTrigger>
                            <SelectValue placeholder="Select a fruit" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="weekly">Per-minggu</SelectItem>
                                <SelectItem value="monthly">Per-bulan</SelectItem>
                                <SelectItem value="yearly">Per-tahun</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </section>

        <section class="bg-white p-3 rounded-lg mb-3">
            <div :class="['flex justify-between items-center'], showContentTwo ? 'mb-3' : ''">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">Overview Minggu 1 Sept</p>
                    <p class="text-sm text-neutral-500">
                        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Eveniet veniam, vero omnis dolores
                        temporibus
                        sed?
                    </p>
                </div>

                <!-- Button untuk toggle show/hide konten -->
                <Button size="icon" variant="outline" @click="toggleContentTwo">
                    <ChevronDown :class="{ 'rotate-180': !showContentTwo }" size="18" />
                </Button>
            </div>

            <!-- Transition untuk smooth animasi -->
            <transition name="slide-fade">
                <!-- SHOW HIDE DISINI -->
                <div v-if="showContentTwo" class="grid gap-2 grid-cols-2 md:grid-cols-4">
                    <div class="col-span-2">
                        <BarChart :data="data" index="day" :categories="['pendapatan']" :y-formatter="(tick) => {
                            return typeof tick === 'number'
                                ? `Rp ${new Intl.NumberFormat('id-ID').format(tick)}`
                                : '';
                        }" :colors="['#58bb69']" />
                    </div>
                    <div class="flex justify-center items-center bg-neutral-200">
                        <p>Data 1</p>
                    </div>
                    <div class="flex justify-center items-center bg-neutral-200">
                        <p>Data 2</p>
                    </div>
                </div>
            </transition>
        </section>

        <section class="bg-white p-3 rounded-lg">
            <!-- <p class="font-semibold text-2xl tracking-tight">Overview Minggu 1 Sept</p> -->
            <div v-if="selectedCount > 0" class="mb-3 flex items-center justify-between">
                <p class="font-semibold tracking-tight text-lg">({{ selectedCount }}) akun dipilih</p>
                <AlertDialog>
                    <AlertDialogTrigger as-child>
                        <Button variant="destructive" size="icon">
                            <Trash2 size="18" />
                        </Button>
                    </AlertDialogTrigger>
                    <AlertDialogContent class="p-4">
                        <AlertDialogHeader>
                            <AlertDialogTitle>Apakah anda yakin?</AlertDialogTitle>
                            <AlertDialogDescription>
                                This action cannot be undone. This will permanently delete the selected accounts
                                and remove their data from our servers.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel>Batal</AlertDialogCancel>
                            <AlertDialogAction @click="deleteSelectedUsers">Ya, Hapus</AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>

            <div>
                <Table>
                    <TableCaption>List dari data akun Qolbu App.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead>#</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users" :key="user.id">
                            <TableCell class="p-3">
                                <input type="checkbox" :id="`user-${user.id}`"
                                    :checked="selectedUsers.includes(user.id)" @change="toggleCheckbox(user.id)"
                                    class="cursor-pointer w-4 h-4 rounded-md checked:bg-blue-500" />
                                <!-- <Checkbox :id="`user-${user.id}`" :checked="selectedUsers.includes(user.id)"
                                    @change="toggleCheckbox(user.id)" /> -->
                            </TableCell>
                            <TableCell class="font-medium p-3">{{ user.name }}</TableCell>
                            <TableCell class="p-3">{{ user.email }}</TableCell>
                            <TableCell class="p-3">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button size="icon" variant="outline">
                                            <Ellipsis size="18" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>Aksi</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <!-- Link untuk mengedit user -->
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('users.edit', user.id)"
                                                class="cursor-pointer flex gap-1">
                                            <Pencil size="18" /> Ubah
                                            </Link>
                                        </DropdownMenuItem>
                                        <!-- Item untuk hapus dengan background merah -->
                                        <DropdownMenuItem as-child>
                                            <AlertDialog>
                                                <AlertDialogTrigger
                                                    class="flex items-center gap-1.5 text-sm px-1.5 py-1 w-full text-red-600 bg-red-50 hover:bg-red-100">
                                                    <Trash2 size="18" />Hapus
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
                                                                @click="deleteUser(user.id)">Ya,
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
            </div>
        </section>
    </DashboardLayout>
</template>

<script lang="ts">
export default {
    props: {
        users: Array,
    },
    methods: {
        deleteUser(id) {
            // this.$inertia.delete(route('users.destroy', id));
            console.log("Delete user with id: " + id);
        },
    },
};
</script>
