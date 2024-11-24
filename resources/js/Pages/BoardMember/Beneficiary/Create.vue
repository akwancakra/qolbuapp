<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { ErrorMessage, Field, Form } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { ref, reactive } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/Components/ui/input';
import { Separator } from '@/Components/ui/separator';
import { Button } from '@/components/ui/button';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import * as zod from 'zod';
import { AlertCircle, CornerUpLeftIcon, Loader2, Save } from 'lucide-vue-next';
import { Inertia } from '@inertiajs/inertia';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';

defineProps<{
    educationList: { label: string; value: string }[];
    statusList: { label: string; value: string }[];
}>();

const isSubmitting = ref(false);

const validationSchema = toTypedSchema(
    zod.object({
        nik: zod.number().min(1, { message: 'NIK wajib diisi' }),
        place_of_birth: zod.string().min(1, { message: 'Tempat lahir wajib diisi' }),
        date_of_birth: zod.string().refine((val) => !isNaN(Date.parse(val)), { message: 'Tanggal lahir wajib diisi' }),
        name: zod.string().min(1, { message: 'Nama wajib diisi' }),
        neighborhood_unit: zod.string().optional(),
        gender: zod.string().min(1, { message: 'Jenis kelamin wajib diisi' }),
        last_education: zod.string().min(1, { message: 'Pendidikan terakhir wajib diisi' }),
        school_grade: zod.string().min(1, { message: 'Kelas sekolah wajib diisi' }),
        shirt_size: zod.string().optional(),
        shoe_size: zod.number().optional(),
        father: zod.string().min(1, { message: 'Nama ayah wajib diisi' }),
        mother: zod.string().min(1, { message: 'Nama ibu wajib diisi' }),
        father_death_certificate_number: zod.string().optional(),
        mother_death_certificate_number: zod.string().optional(),
        phone_number: zod.string().min(1, { message: 'Nomor telepon wajib diisi' }).max(15, { message: 'Nomor telepon tidak boleh lebih dari 15 karakter' }),
        status: zod.string().min(1, { message: 'Status wajib diisi' }),
        description: zod.string().optional(),
    })
);

const formValues = reactive({
    nik: 0,
    place_of_birth: '',
    date_of_birth: '',
    name: '',
    neighborhood_unit: '',
    gender: '',
    last_education: '',
    school_grade: '',
    father: '',
    mother: '',
    shirt_size: '',
    shoe_size: 0,
    father_death_certificate_number: '',
    mother_death_certificate_number: '',
    phone_number: '',
    status: '',
    description: '',
});

function resetForm() {
    Object.assign(formValues, {
        nik: 0,
        place_of_birth: '',
        date_of_birth: '',
        name: '',
        neighborhood_unit: '',
        gender: '',
        last_education: '',
        school_grade: '',
        father: '',
        mother: '',
        shirt_size: '',
        shoe_size: 0,
        father_death_certificate_number: '',
        mother_death_certificate_number: '',
        phone_number: '',
        status: '',
        description: '',
    });
}

async function onSubmit(values: any) {
    isSubmitting.value = true;
    try {
        // console.log(values);
        Inertia.post(route('board_member.beneficiaries.store'), values, {
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
        // isSubmitting.value = false;
    } catch (error) {
        isSubmitting.value = false;
    }
}
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
                    <Form id="beneficiaryForm" @submit="onSubmit" :validation-schema="validationSchema"
                        class="grid grid-cols-2 gap-2" v-slot="{ errors }">
                        <div class="col-span-2">
                            <p class="font-semibold tracking-tight text-lg">Data Anak</p>
                            <Separator class="my-2 dark:bg-neutral-600" />
                        </div>

                        <template v-if="Object.keys(errors).length">
                            <Alert variant="destructive" class="col-span-2">
                                <AlertCircle class="w-4 h-4" />
                                <AlertTitle>Tolong perbaiki data berikut:</AlertTitle>
                                <AlertDescription>
                                    <ul class="list-inside list-disc">
                                        <li v-for="(message, field) in errors" :key="field">
                                            {{ message }}
                                        </li>
                                    </ul>
                                </AlertDescription>
                            </Alert>
                        </template>

                        <div>
                            <Label for="nik">No NIK</Label>
                            <Field v-model="formValues.nik" name="nik" type="number" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="number" placeholder="cth: 3307xxxxxxxx..." />
                                </template>
                            </Field>
                            <ErrorMessage name="nik" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="name">Nama</Label>
                            <Field v-model="formValues.name" name="name" type="text" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: Jasuki..." />
                                </template>
                            </Field>
                            <ErrorMessage name="name" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="gender">Jenis Kelamin</Label>
                            <Field v-model="formValues.gender" name="gender" type="select" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Select v-bind="field" v-model="formValues.gender"
                                        @update:model-value="formValues.gender = $event">
                                        <SelectTrigger>
                                            <SelectValue :placeholder="formValues.gender || 'Pilih Jenis Kelamin'" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="male">Laki-laki</SelectItem>
                                            <SelectItem value="female">Perempuan</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </template>
                            </Field>
                            <ErrorMessage name="gender" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="place_of_birth">Tempat Lahir</Label>
                            <Field v-model="formValues.place_of_birth" name="place_of_birth" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: Jakarta..." />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="place_of_birth" />
                        </div>

                        <div>
                            <Label for="date_of_birth">Tanggal Lahir</Label>
                            <Field v-model="formValues.date_of_birth" name="date_of_birth" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="date" />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="date_of_birth" />
                        </div>

                        <div>
                            <Label for="neighborhood_unit">RT/RW</Label>
                            <Field v-model="formValues.neighborhood_unit" name="neighborhood_unit" type="text">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: 07/02..." />
                                </template>
                            </Field>
                            <ErrorMessage name="neighborhood_unit" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="last_education">Pendidikan Terakhir</Label>
                            <Field v-model="formValues.last_education" name="last_education" type="select">
                                <template v-slot="{ field }">
                                    <Select v-bind="field" v-model="formValues.last_education"
                                        @update:model-value="formValues.last_education = $event">
                                        <SelectTrigger>
                                            <SelectValue
                                                :placeholder="formValues.last_education || 'Pilih Pendidikan Terakhir'" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="education in educationList" :key="education.value"
                                                :value="education.value">{{ education.label }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </template>
                            </Field>
                            <ErrorMessage name="last_education" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="school_grade">Kelas</Label>
                            <Field v-model="formValues.school_grade" name="school_grade" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: 6..." />
                                </template>
                            </Field>
                            <ErrorMessage name="school_grade" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="shirt_size">Ukuran Baju</Label>
                            <Field v-model="formValues.shirt_size" name="shirt_size" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: M..." />
                                </template>
                            </Field>
                            <ErrorMessage name="shirt_size" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="shoe_size">Ukuran Sepatu</Label>
                            <Field v-model="formValues.shoe_size" name="shoe_size" type="number">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="number" placeholder="cth: 36..." />
                                </template>
                            </Field>
                            <ErrorMessage name="shoe_size" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="status">Status</Label>
                            <Field v-model="formValues.status" name="status" type="select" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Select v-bind="field" v-model="formValues.status"
                                        @update:model-value="formValues.status = $event">
                                        <SelectTrigger>
                                            <SelectValue :placeholder="formValues.status || 'Pilih Status'" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="status in statusList" :key="status.value"
                                                :value="status.value">{{ status.label }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </template>
                            </Field>
                            <ErrorMessage name="status" class="text-red-500 text-sm" />
                        </div>

                        <div class="col-span-2 mt-3">
                            <p class="font-semibold tracking-tight text-lg">Data Orang Tua</p>
                            <Separator class="my-2 dark:bg-neutral-600" />
                        </div>

                        <div>
                            <Label for="father">Nama Ayah</Label>
                            <Field v-model="formValues.father" name="father" type="text" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: Supriyadi..." />
                                </template>
                            </Field>
                            <ErrorMessage name="father" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="mother">Nama Ibu</Label>
                            <Field v-model="formValues.mother" name="mother" type="text" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: Ngadinah..." />
                                </template>
                            </Field>
                            <ErrorMessage name="mother" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="father_death_certificate_number">No. Akta Kematian Ayah</Label>
                            <Field v-model="formValues.father_death_certificate_number"
                                name="father_death_certificate_number" type="text" :rules="{ required: false }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: 12/INV/XXXX..." />
                                </template>
                            </Field>
                            <ErrorMessage name="father_death_certificate_number" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="mother_death_certificate_number">No. Akta Kematian Ibu</Label>
                            <Field v-model="formValues.mother_death_certificate_number"
                                name="mother_death_certificate_number" type="text" :rules="{ required: false }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: 92/INV/XXXX..." />
                                </template>
                            </Field>
                            <ErrorMessage name="mother_death_certificate_number" class="text-red-500 text-sm" />
                        </div>

                        <div>
                            <Label for="phone_number">No. Telepon</Label>
                            <Field v-model="formValues.phone_number" name="phone_number" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="cth: 0812345678..." />
                                </template>
                            </Field>
                            <ErrorMessage name="phone_number" class="text-red-500 text-sm" />
                        </div>

                        <div class="col-span-2">
                            <Label for="description">Deskripsi</Label>
                            <Field v-model="formValues.description" name="description" type="textarea"
                                :rules="{ required: false }">
                                <template v-slot="{ field }">
                                    <Textarea v-bind="field" placeholder="cth: Jasuki anak yang baik..." />
                                </template>
                            </Field>
                            <ErrorMessage name="description" class="text-red-500 text-sm" />
                        </div>

                        <div class="block col-span-2 sm:w-2/5 sm:hidden">
                            <p class="font-semibold tracking-tight text-lg">Preview data</p>
                            <div class="my-2 p-3 rounded-lg border border-neutral-300 dark:border-neutral-600">
                                <p>Berikut adalah data yang akan disimpan</p>
                                <div class="grid grid-cols-2 gap-2 my-2">
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Nama</p>
                                        <p>{{ formValues.name || 'Nama' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Jenis Kelamin</p>
                                        <p>{{ formValues.gender || 'Jenis Kelamin' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Tempat Lahir</p>
                                        <p>{{ formValues.place_of_birth || 'Tempat Lahir' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Tanggal Lahir</p>
                                        <p>{{ formValues.date_of_birth || 'Tanggal Lahir' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            RT/RW</p>
                                        <p>{{ formValues.neighborhood_unit || 'RT/RW' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Pendidikan Terakhir</p>
                                        <p>{{ formValues.last_education || 'Pendidikan Terakhir' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Status</p>
                                        <p>{{ formValues.status || 'Status' }}</p>
                                    </div>
                                    <Separator class="col-span-2 my-2 dark:bg-neutral-600" />
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Nama Ayah</p>
                                        <p>{{ formValues.father || 'Nama Ayah' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Nama Ibu</p>
                                        <p>{{ formValues.mother || 'Nama Ibu' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            No. Akta Kematian Ayah</p>
                                        <p>{{ formValues.father_death_certificate_number || 'No. Akta Kematian Ayah' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            No. Akta Kematian Ibu</p>
                                        <p>{{ formValues.mother_death_certificate_number || 'No. Akta Kematian Ibu' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            No. Telepon</p>
                                        <p>{{ formValues.phone_number || 'No. Telepon' }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Deskripsi</p>
                                        <p>{{ formValues.description || 'Deskripsi' }}</p>
                                    </div>
                                </div>
                                <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                            </div>
                        </div>

                        <Separator class="col-span-2 my-2 dark:bg-neutral-600" />

                        <div class="col-span-2 flex gap-2 justify-end w-full">
                            <Button variant="outline" type="reset" @click="resetForm"
                                :disabled="isSubmitting">Batal</Button>
                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button type="button" :disabled="isSubmitting">
                                        <template v-if="isSubmitting">
                                            <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                                            Menyimpan...
                                        </template>
                                        <template v-else>
                                            Simpan
                                            <Save class="w-4 h-4 ml-1" />
                                        </template>
                                    </Button>
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
                                            <Button type="submit" form="beneficiaryForm">Ya, Simpan</Button>
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
                                    NIK</p>
                                <p>{{ formValues.nik || 'NIK' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama</p>
                                <p>{{ formValues.name || 'Nama' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Jenis Kelamin</p>
                                <p>{{ formValues.gender || 'Jenis Kelamin' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Tempat Lahir</p>
                                <p>{{ formValues.place_of_birth || 'Tempat Lahir' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Tanggal Lahir</p>
                                <p>{{ formValues.date_of_birth || 'Tanggal Lahir' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    RT/RW</p>
                                <p>{{ formValues.neighborhood_unit || 'RT/RW' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Pendidikan Terakhir</p>
                                <p>{{ formValues.last_education || 'Pendidikan Terakhir' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Kelas</p>
                                <p>{{ formValues.school_grade || 'Kelas' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Ukuran Baju</p>
                                <p>{{ formValues.shirt_size || 'Ukuran Baju' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Ukuan Sepatu</p>
                                <p>{{ formValues.shoe_size || 'Ukuan Sepatu' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Status</p>
                                <p>{{ formValues.status || 'Status' }}</p>
                            </div>
                            <Separator class="col-span-2 my-2 dark:bg-neutral-600" />
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama Ayah</p>
                                <p>{{ formValues.father || 'Nama Ayah' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama Ibu</p>
                                <p>{{ formValues.mother || 'Nama Ibu' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    No. Akta Kematian Ayah</p>
                                <p>{{ formValues.father_death_certificate_number || 'No. Akta Kematian Ayah' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    No. Akta Kematian Ibu</p>
                                <p>{{ formValues.mother_death_certificate_number || 'No. Akta Kematian Ibu' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    No. Telepon</p>
                                <p>{{ formValues.phone_number || 'No. Telepon' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Deskripsi</p>
                                <p>{{ formValues.description || 'Deskripsi' }}</p>
                            </div>
                        </div>
                        <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
