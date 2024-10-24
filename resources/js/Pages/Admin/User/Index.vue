<script setup lang="ts">
import { defineProps, ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
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
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
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
import { Ellipsis, ChevronDown, Pencil, Trash2 } from 'lucide-vue-next';
import { Head } from '@inertiajs/vue3';
import { Checkbox } from '@/Components/ui/checkbox';
// LOCAL COMPONENTS
import { User } from '@/types';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

defineProps<{
    users: User[]; // props users yang berisi array dari User
}>();

// ========== SECTION 3 ==========
// State untuk melacak checkbox yang dipilih
const selectedUsers = ref<number[]>([]);

// Computed property untuk menghitung jumlah akun yang dipilih
const selectedCount = computed(() => selectedUsers.value.length);

// Function untuk toggle checkbox
const toggleCheckbox = (checked: boolean, id: number) => {
    if (checked) {
        selectedUsers.value.push(id);
    } else {
        selectedUsers.value = selectedUsers.value.filter(userId => userId !== id);
    }
};

const deleteUser = (id: number) => {
    console.log("Delete user with id: " + id);
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
        <title>Daftar Akun</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Daftar Akun</h1>
        </template>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="font-semibold text-2xl tracking-tight">QolbuApp - Akun</p>
                    <p class="text-sm text-neutral-500">Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                        Eveniet
                        veniam, vero omnis dolores temporibus sed?</p>
                </div>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button size="icon" variant="outline">
                            <Ellipsis :size="18" />
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
                    <Label for="type">Tipe Peran</Label>
                    <Select>
                        <SelectTrigger>
                            <SelectValue placeholder="Pilih Tipe Peran" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem value="admin">Admin</SelectItem>
                                <SelectItem value="pengurus">Pengurus</SelectItem>
                                <SelectItem value="duta">Duta</SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </section>

        <section class="bg-white p-3 rounded-lg dark:bg-neutral-800">
            <!-- <p class="font-semibold text-2xl tracking-tight">Overview Minggu 1 Sept</p> -->
            <div v-if="selectedCount > 0" class="mb-3 flex items-center justify-between">
                <p class="font-semibold tracking-tight text-lg">({{ selectedCount }}) akun dipilih</p>
                <AlertDialog>
                    <AlertDialogTrigger as-child>
                        <Button variant="destructive" size="icon">
                            <Trash2 :size="18" />
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
                            <TableHead>Role</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users" :key="user.id">
                            <TableCell class="p-3">
                                <!-- <input type="checkbox" :id="`user-${user.id}`"
                                    :checked="selectedUsers.includes(user.id)" @change="toggleCheckbox(user.id)"
                                    class="cursor-pointer w-4 h-4 rounded-md checked:bg-blue-500" /> -->
                                <Checkbox :id="`user-${user.id}`" :checked="selectedUsers.includes(user.id)"
                                    @update:checked="(checked) => toggleCheckbox(checked, user.id)" />
                            </TableCell>
                            <TableCell class="font-medium p-3">{{ user.name }}</TableCell>
                            <TableCell class="p-3 first-letter:uppercase">{{ user.role }}</TableCell>
                            <TableCell class="p-3">{{ user.email }}</TableCell>
                            <TableCell class="p-3">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button size="icon" variant="outline">
                                            <Ellipsis :size="18" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuLabel>Aksi</DropdownMenuLabel>
                                        <DropdownMenuSeparator />
                                        <!-- Link untuk mengedit user -->
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('users.edit', user.id)"
                                                class="cursor-pointer flex gap-1">
                                            <Pencil :size="18" /> Ubah
                                            </Link>
                                        </DropdownMenuItem>
                                        <!-- Item untuk hapus dengan background merah -->
                                        <DropdownMenuItem as-child>
                                            <AlertDialog>
                                                <AlertDialogTrigger
                                                    class="flex items-center gap-1.5 text-sm px-1.5 py-1 w-full rounded text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-800 dark:hovere:bg-red-700 dark:text-red-200">
                                                    <Trash2 :size="18" />Hapus
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
