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
        transfer_date: zod.string().min(2, { message: 'Tanggal transfer minimal 2 karakter' }),
        duta: zod.string().min(2, { message: 'Nama duta minimal 2 karakter' }),
        donatur: zod.string().nonempty({ message: 'Nama donatur wajib diisi' }),
        name_of_donate: zod.string().nonempty({ message: 'Nama donasi wajib diisi' }),
        nominal: zod.number().min(1, { message: 'Nominal donasi minimal 1' }),
        method: zod.string().min(2, { message: 'Metode donasi minimal 2 karakter' }),
        type: zod.string().min(2, { message: 'Tipe donasi minimal 2 karakter' }),
    })),
    initialValues: {
        transfer_date: '',
        duta: '',
        donatur: '',
        name_of_donate: '',
        nominal: 0,
        method: '',
        type: '',
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
    // Ubahkan logika penyimpanan data di sini
});

</script>

<template>

    <Head>
        <title>Ubah Data Transferan</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Ubah Data Transferan</h1>
        </template>

        <Button class="mb-3" variant="outline">
            <Link :href="route('pengurus.transactions.index')" class="flex gap-1">
            <CornerUpLeftIcon :size="18" /> Kembali
            </Link>
        </Button>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="mb-3">
                <p class="font-semibold text-2xl tracking-tight">Data Transferan</p>
                <p class="text-sm text-neutral-500">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
            </div>

            <div class="sm:flex gap-2">
                <div class="w-full sm:w-3/5">
                    <p class="font-semibold tracking-tight text-lg">Input data</p>
                    <Separator class="my-2 dark:bg-neutral-600" />

                    <Form @submit="onSubmit" class="grid grid-cols-2 gap-2">
                        <div>
                            <Label for="transfer_date">Tanggal Transfer</Label>
                            <Input type="date" id="transfer_date" v-model="values.transfer_date" />
                            <ErrorMessage name="transfer_date" />
                        </div>

                        <div>
                            <Label for="duta">Nama Duta</Label>
                            <Input type="text" id="duta" v-model="values.duta" placeholder="Nama duta..." />
                            <ErrorMessage name="duta" />
                        </div>

                        <div>
                            <Label for="donatur">Nama Donatur</Label>
                            <Input type="text" id="donatur" v-model="values.donatur" placeholder="Nama donatur..." />
                            <ErrorMessage name="donatur" />
                        </div>

                        <div>
                            <Label for="name_of_donate">Donasi Atas Nama</Label>
                            <Input type="text" id="name_of_donate" v-model="values.name_of_donate"
                                placeholder="Donasi atas nama..." />
                            <ErrorMessage name="name_of_donate" />
                        </div>

                        <div>
                            <Label for="nominal">Nominal Donasi</Label>
                            <Input type="number" id="nominal" v-model="values.nominal"
                                placeholder="Nominal donasi..." />
                            <ErrorMessage name="nominal" />
                        </div>

                        <div>
                            <Label for="method">Metode Donasi</Label>
                            <Input type="text" id="method" v-model="values.method" placeholder="Metode donasi..." />
                            <ErrorMessage name="method" />
                        </div>

                        <div>
                            <Label for="type">Tipe Donasi</Label>
                            <Input type="text" id="type" v-model="values.type" placeholder="Tipe donasi..." />
                            <ErrorMessage name="type" />
                        </div>

                        <Separator class="col-span-2 my-2 dark:bg-neutral-600" />

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
                                    Tanggal Transfer</p>
                                <p>Tanggal Transfer</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama Duta</p>
                                <p>Nama Duta</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama Donatur</p>
                                <p>Nama Donatur</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Donasi Atar Nama</p>
                                <p>Donasi Atar Nama</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nominal</p>
                                <p>Nominal</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Metode Donasi</p>
                                <p>Metode Donasi</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Tipe Donasi</p>
                                <p>Tipe Donasi</p>
                            </div>
                        </div>
                        <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
