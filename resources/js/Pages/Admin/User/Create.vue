<script setup lang="ts">
import { Form, Field, ErrorMessage } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { reactive, ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Button } from '@/components/ui/button';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import * as zod from 'zod';
import { CornerUpLeftIcon, Eye, EyeOff, Loader2 } from 'lucide-vue-next';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Inertia } from '@inertiajs/inertia';

const showPassword = ref(false);
const isSubmitting = ref(false);

const validationSchema = toTypedSchema(
    zod.object({
        name: zod.string().min(2, { message: 'Nama minimal 2 karakter' }),
        email: zod.string().email({ message: 'Format email tidak valid' }),
        role: zod.string().nonempty({ message: 'Tipe user harus dipilih' }),
        password: zod.string().min(8, { message: 'Password minimal 8 karakter' })
    })
);

async function onSubmit(values: any) {
    isSubmitting.value = true;
    try {
        Inertia.post(route('admin.users.store'), values, {
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    } catch (error) {
        isSubmitting.value = false;
    }
}

// Untuk preview data
const formValues = reactive({
    name: '',
    email: '',
    role: '',
    password: '',
});
</script>

<template>

    <Head>
        <title>Create User</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Create User</h1>
        </template>

        <Button class="mb-3" variant="outline">
            <Link :href="route('admin.users.index')" class="flex gap-1">
            <CornerUpLeftIcon :size="18" /> Back
            </Link>
        </Button>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="mb-3">
                <p class="font-semibold text-2xl tracking-tight">User Data</p>
                <p class="text-sm text-neutral-500">Please fill in the details below to create a new user.</p>
            </div>

            <div class="sm:flex gap-2">
                <div class="w-full sm:w-3/5">
                    <p class="font-semibold tracking-tight text-lg">Input data</p>
                    <Separator class="my-2 dark:bg-neutral-600" />

                    <Form id="userForm" :validation-schema="validationSchema" @submit="onSubmit"
                        class="grid grid-cols-2 gap-2">
                        <div>
                            <Label for="name">Name</Label>
                            <Field v-model="formValues.name" name="name" type="text" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="Name..." />
                                </template>
                            </Field>
                            <ErrorMessage name="name" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="email">Email</Label>
                            <Field v-model="formValues.email" name="email" type="email">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="example@mail.com..." />
                                </template>
                            </Field>
                            <ErrorMessage name="email" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label>Tipe User</Label>
                            <Field v-model="formValues.role" name="role" type="select">
                                <template v-slot="{ field }">
                                    <Select v-bind="field">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Pilih Tipe User" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="admin">Admin</SelectItem>
                                            <SelectItem value="pengurus">Pengurus</SelectItem>
                                            <SelectItem value="duta">Duta</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </template>
                            </Field>
                            <ErrorMessage name="role" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="password">Password</Label>
                            <Field v-model="formValues.password" name="password" type="password">
                                <template v-slot="{ field }">
                                    <div class="relative">
                                        <Input v-bind="field" :type="showPassword ? 'text' : 'password'"
                                            placeholder="Password..." />
                                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2"
                                            @click="showPassword = !showPassword">
                                            <Eye v-if="!showPassword" class="h-4 w-4 text-neutral-500" />
                                            <EyeOff v-else class="h-4 w-4 text-neutral-500" />
                                        </button>
                                    </div>
                                </template>
                            </Field>
                            <ErrorMessage name="password" class="text-red-500 text-sm" />
                        </div>

                        <Separator class="col-span-2 my-2 dark:bg-neutral-600" />

                        <div class="col-span-2 flex gap-2 justify-end w-full">
                            <Link :href="route('admin.users.index')">
                            <Button variant="outline" :disabled="isSubmitting">Batal</Button>
                            </Link>
                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button type="button" :disabled="isSubmitting">
                                        <template v-if="isSubmitting">
                                            <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                                            Menyimpan...
                                        </template>
                                        <template v-else>
                                            Buat Akun
                                        </template>
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent class="p-4">
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            The data you entered will be saved.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction as-child>
                                            <Button type="submit" form="userForm">Yes, Save</Button>
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>
                        </div>
                    </Form>
                </div>

                <div class="hidden sm:w-2/5 sm:block">
                    <p class="font-semibold tracking-tight text-lg">Preview data</p>
                    <div class="my-2 p-3 rounded-lg border border-neutral-300 dark:border-neutral-600">
                        <p>Here is the data that will be saved</p>
                        <div class="grid grid-cols-2 gap-2 my-2">
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Name</p>
                                <p class="break-words">{{ formValues.name || "Name" }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Email</p>
                                <p class="break-words">{{ formValues.email || "example@mail.com" }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Role</p>
                                <p class="break-words">{{ formValues.role || "Role" }}</p>
                            </div>
                        </div>
                        <p>If you are sure, you can save the data directly.</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
