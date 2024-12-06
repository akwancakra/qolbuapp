<script setup lang="ts">
import { defineProps, ref, computed, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
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
import { Ellipsis, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { Head } from '@inertiajs/vue3';
import { Checkbox } from '@/components/ui/checkbox';
// LOCAL COMPONENTS
import { PaginatedUsers } from '@/types';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { toast } from 'vue-sonner';
import { onMounted } from 'vue';

const page = usePage();
defineProps<{
    users: PaginatedUsers; // props users yang berisi array dari User
    filters: {
        search: string,
        role: string
    }
}>();

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
    Inertia.delete(route('admin.users.destroy', id), {
        onSuccess: () => {
            toast.success("Berhasil menghapus akun");
        },
        onError: (errors) => {
            toast.error(errors);
            console.error("Error saat menghapus akun:", errors);
        },
    });
};

// Function untuk menghapus user yang dipilih
const deleteSelectedUsers = () => {
    Inertia.post(route('admin.users.destroy-multiple'), {
        ids: selectedUsers.value
    }, {
        preserveState: true,
        onSuccess: () => {
            selectedUsers.value = [];
            toast.success("Berhasil menghapus beberapa akun");
        },
        onError: (errors) => {
            toast.error(errors);
            console.error("Error saat menghapus beberapa akun:", errors);
        },
    });
};

// Form untuk pencarian dan filter
const form = useForm({
    search: '',
    role: 'default',
});

// Watch untuk mengirimkan permintaan pencarian dan filter
watch(() => form.data(), (newData) => {
    form.get(route('admin.users.index'), { preserveState: true });
    form.search = newData.search;
    form.role = newData.role;
});

onMounted(() => {
    const flashMessage = (page.props.flash as { message?: string })?.message;
    if (flashMessage) {
        toast.success(flashMessage);
    }
});
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
                        <DropdownMenuLabel>Aksi</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem as-child>
                            <Link :href="route('admin.users.create')" class="cursor-pointer flex gap-1">
                            <Plus :size="18" /> Tambah Akun
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div class="grid gap-3 grid-cols-2 sm:grid-cols-4">
                <div>
                    <Label for="name">Name</Label>
                    <Input type="text" id="name" v-model="form.search" placeholder="Cari nama..." />
                </div>
                <div>
                    <Label>Tipe User</Label>
                    <Select v-model="form.role" @update:model-value="form.role = $event">
                        <SelectTrigger>
                            <SelectValue :placeholder="form.role || 'Pilih Tipe User'" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="default">Semua</SelectItem>
                            <SelectItem value="admin">Admin</SelectItem>
                            <SelectItem value="pengurus">Pengurus</SelectItem>
                            <SelectItem value="duta">Duta</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </section>

        <section class="bg-white p-3 rounded-lg dark:bg-neutral-800">
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
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell class="p-3">
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
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('admin.users.edit', user)"
                                                class="cursor-pointer flex gap-1">
                                            <Pencil :size="18" /> Ubah
                                            </Link>
                                        </DropdownMenuItem>
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
