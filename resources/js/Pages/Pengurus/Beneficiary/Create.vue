<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { ErrorMessage, Field, Form, useField, useForm } from 'vee-validate';
// import { z } from 'zod';
import { toFormValidator, toTypedSchema } from '@vee-validate/zod';
import { ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/Components/ui/input';
import { Separator } from '@/Components/ui/separator';
import { Button } from '@/components/ui/button';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import * as zod from 'zod';
import { CornerUpLeftIcon } from 'lucide-vue-next';

// Define Zod schema for validation
// const schema = z.object({
//     name: z.string().min(2, { message: 'Nama minimal 2 karakter' }),
//     nickname: z.string().min(2, { message: 'Nama panggilan minimal 2 karakter' }),
//     birthdate: z.string({ message: 'Tanggal lahir wajib diisi' }),
//     email: z.string().email({ message: 'Format email tidak valid' })
// });

// Prepare form validation schema and errors
const { handleSubmit, resetForm, errors, values } = useForm({
    validationSchema: toTypedSchema(zod.object({
        name: zod.number().min(2, { message: 'Nama minimal 2 karakter' }),
        nickname: zod.string().min(2, { message: 'Nama panggilan minimal 2 karakter' }),
        birthdate: zod.string().nonempty({ message: 'Tanggal lahir wajib diisi' }),
        email: zod.string().email({ message: 'Format email tidak valid' })
    })),
    initialValues: {
        name: 0,
        nickname: '',
        birthdate: '',
        email: '',
    }
});

// Reactive data to preview inputs
// const formData: any = ref({
//     name: '',
//     nickname: '',
//     birthdate: '',
//     email: '',
// });

// Form submit handler
// values: typeof formData.value
// const submitForm = () => {
//     // Action when form is submitted
//     console.log('Form Submitted:', );
// };
const onSubmit = handleSubmit((values) => {
    console.log('Form Submitted:', values);
    // Tambahkan logika penyimpanan data di sini
});

</script>

<template>

    <Head>
        <title>Tambah Penerima Manfaat</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Tambah Penerima Manfaat</h1>
        </template>

        <Button class="mb-3" variant="outline">
            <Link :href="route('pengurus.beneficiaries.index')" class="flex gap-1">
            <CornerUpLeftIcon :size="18" /> Kembali
            </Link>
        </Button>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="mb-3">
                <p class="font-semibold text-2xl tracking-tight">Data Penerima Manfaat</p>
                <p class="text-sm text-neutral-500">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
            </div>

            <div class="sm:flex gap-2">
                <div class="w-full sm:w-3/5">
                    <p class="font-semibold tracking-tight text-lg">Input data</p>
                    <Separator class="my-2 dark:bg-neutral-600" />

                    <Form @submit="onSubmit" class="grid grid-cols-2 gap-2">
                        <div>
                            <Label for="name">Nama</Label>
                            <Input id="name" name="name" type="name" v-model="values.name" placeholder="Nama..." />
                            <ErrorMessage name="name" />
                            <!-- <span class="text-red-500 text-sm">{{ errors.name }}</span> -->
                        </div>

                        <div>
                            <Label for="nickname">Nama Panggilan</Label>
                            <Input type="text" id="nickname" v-model="values.nickname"
                                placeholder="Nama Panggilan..." />
                            <ErrorMessage name="nickname" />
                            <!-- <span class="text-red-500 text-sm">{{ errors.nickname }}</span> -->
                        </div>

                        <div>
                            <Label for="birthdate">Tgl Lahir</Label>
                            <Input type="date" id="birthdate" v-model="values.birthdate" />
                            <ErrorMessage name="birthdate" />
                            <!-- <span class="text-red-500 text-sm">{{ errors.birthdate }}</span> -->
                        </div>

                        <div>
                            <Label for="email">Email</Label>
                            <Input type="email" id="email" v-model="values.email" placeholder="example@mail.com..." />
                            <ErrorMessage name="email" />
                            <!-- <span class="text-red-500 text-sm">{{ errors.email }}</span> -->
                        </div>

                        <Separator class="col-span-2 my-2 dark:bg-neutral-600" />

                        <div class="w-full col-span-2 sm:hidden">
                            <p class="font-semibold tracking-tight text-lg">Preview data</p>
                            <div class="my-2 p-3 rounded-lg border border-neutral-300 dark:border-neutral-600">
                                <p>Berikut adalah data yang akan disimpan</p>
                                <div class="grid grid-cols-2 gap-2 my-2">
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Nama</p>
                                        <p>Contoh</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Nama
                                            Panggilan</p>
                                        <p>Contoh</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Tgl Lahir
                                        </p>
                                        <p>Contoh</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Email</p>
                                        <p>Contoh</p>
                                    </div>
                                </div>
                                <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                            </div>
                        </div>

                        <div class="col-span-2 flex gap-2 justify-end w-full">
                            <Button variant="outline" type="reset" @click="resetForm">Batal</Button>
                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button>Simpan</Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent class="p-4">
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Apakah anda yakin?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            Data yang anda masukkan akan disimpan.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Batal</AlertDialogCancel>
                                        <AlertDialogAction as-child>
                                            <Button type="submit">Ya, Simpan</Button>
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
                        <p>Berikut adalah data yang akan disimpan</p>
                        <div class="grid grid-cols-2 gap-2 my-2">
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama</p>
                                <p>Contoh</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama
                                    Panggilan</p>
                                <p>Contoh</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Tgl Lahir
                                </p>
                                <p>Contoh</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Email</p>
                                <p>Contoh</p>
                            </div>
                        </div>
                        <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
