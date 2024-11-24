<script setup lang="ts">
import * as zod from 'zod';
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Form, Field, ErrorMessage } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { onMounted, ref, watch } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/Components/ui/input';
import { Separator } from '@/Components/ui/separator';
import { Button } from '@/components/ui/button';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import { AlertCircle, Check, ChevronsUpDown, CornerUpLeftIcon, Loader2, Save } from 'lucide-vue-next';
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/Components/ui/command";
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useDateFormat } from '@vueuse/core';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Ambassador, Income } from '@/types';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';

defineProps<{
    availableAmbassadors: { label: string; value: number }[];
    paymentMethods: { label: string; value: string }[];
    donationTypes: { label: string; value: string }[];
}>();

// Define variables for ambassador combo box
const openAmbassador = ref(false);
const isSubmitting = ref(false);

const validationSchema = toTypedSchema(
    zod.object({
        transfer_date: zod.string().refine((val) => !isNaN(Date.parse(val)), { message: 'Tanggal donasi wajib diisi' }),
        ambassador: zod.string().min(1, { message: 'Duta wajib dipilih' }),
        donor: zod.string().min(1, { message: 'Nama donatur minimal 1 karakter' }),
        on_behalf_of: zod.string().min(1, { message: 'Donasi atas nama minimal 1 karakter' }),
        amount: zod.number().min(3, { message: 'Nominal donasi minimal 3 karakter' }),
        payment_method: zod.string().min(2, { message: 'Metode donasi minimal 2 karakter' }),
        type: zod.string().min(2, { message: 'Tipe donasi minimal 2 karakter' }),
    })
);

// Untuk preview data
const formValues = reactive({
    transfer_date: '',
    ambassador: '',
    donor: '',
    on_behalf_of: '',
    amount: 0,
    payment_method: '',
    type: '',
});

function resetForm() {
    Object.assign(formValues, {
        transfer_date: '',
        ambassador: '',
        donor: '',
        on_behalf_of: '',
        amount: 0,
        payment_method: '',
        type: '',
    });
}

async function onSubmit(values: any) {
    isSubmitting.value = true;
    try {
        Inertia.post(route('board_member.incomes.store'), {
            ...values,
            amount: Number(values.amount),
            ambassador: Number(values.ambassador),
        }, {
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    } catch (error) {
        isSubmitting.value = false;
    }
}

watch(() => formValues.ambassador, (newValues) => {
    formValues.ambassador = newValues.toString();
}, { deep: true });
</script>

<template>

    <Head>
        <title>Tambah Data Pendapatan</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Tambah Data Pendapatan</h1>
        </template>

        <Button class="mb-3" variant="outline">
            <Link :href="route('board_member.incomes.index')" class="flex gap-1">
            <CornerUpLeftIcon :size="18" /> Kembali
            </Link>
        </Button>

        <section class="bg-white p-3 rounded-lg mb-3 dark:bg-neutral-800">
            <div class="mb-3">
                <p class="font-semibold text-2xl tracking-tight">Data Pendapatan</p>
                <p class="text-sm text-neutral-500">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
            </div>

            <div class="sm:flex gap-2">
                <div class="w-full sm:w-3/5">
                    <p class="font-semibold tracking-tight text-lg">Input data</p>
                    <Separator class="my-2 dark:bg-neutral-600" />

                    <Form id="editIncomeForm" :validation-schema="validationSchema" @submit="onSubmit"
                        class="grid grid-cols-2 gap-2" v-slot="{ errors }">
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
                            <Label for="transfer_date">Tanggal Transfer</Label>
                            <Field v-model="formValues.transfer_date" name="transfer_date" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="date" />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="transfer_date" />
                        </div>

                        <div>
                            <Label for="team">Duta</Label>
                            <Field v-model="formValues.ambassador" name="ambassador" type="text"
                                :rules="{ required: true }">
                                <Popover v-model:open="openAmbassador">
                                    <PopoverTrigger as-child>
                                        <Button variant="outline" role="combobox" :aria-expanded="openAmbassador"
                                            class="w-full justify-between">
                                            {{ formValues.ambassador ?
                                                (availableAmbassadors.find(ambassador => ambassador.value.toString() ===
                                                    formValues.ambassador)?.label) : 'Pilih Duta' }}
                                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="md:w-auto lg:w-[300px] p-0">
                                        <Command v-model="formValues.ambassador">
                                            <CommandInput placeholder="Cari Duta..." :value="formValues.ambassador ?
                                                (availableAmbassadors.find(ambassador => ambassador.value.toString() ===
                                                    formValues.ambassador)?.label) : '-'" />
                                            <CommandEmpty>Tidak ada duta ditemukan.</CommandEmpty>
                                            <CommandList>
                                                <CommandGroup>
                                                    <CommandItem v-for="ambassador in availableAmbassadors"
                                                        :key="ambassador.value" :value="ambassador.value"
                                                        @select="openAmbassador = false"
                                                        class="overflow-hidden text-ellipsis">
                                                        <Check :class="[
                                                            'mr-2 h-4 w-4',
                                                            formValues.ambassador === ambassador.value.toString() ? 'opacity-100' : 'opacity-0',
                                                        ]" />
                                                        {{ ambassador.label }}
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>
                            </Field>
                        </div>

                        <div>
                            <Label for="donor">Nama donatur</Label>
                            <Field v-model="formValues.donor" name="donor" type="text" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="Nama donatur..." />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="donor" />
                        </div>

                        <div>
                            <Label for="on_behalf_of">Donasi Atas Nama</Label>
                            <Field v-model="formValues.on_behalf_of" name="on_behalf_of" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="Donasi atas nama..." />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="on_behalf_of" />
                        </div>

                        <div>
                            <Label for="amount">Jumlah donasi</Label>
                            <Field v-model="formValues.amount" name="amount" type="number" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="number" />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="amount" />
                        </div>

                        <div>
                            <!-- cSpell:ignore Metode Donasi Pilih Tipe Pemayaran mandiri Mandiri lainnya Lainnya -->
                            <Label for="payment_method">Metode Donasi</Label>
                            <Field v-model="formValues.payment_method" name="payment_method" type="select"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Select v-bind="field" v-model="formValues.payment_method"
                                        @update:model-value="formValues.payment_method = $event">
                                        <SelectTrigger>
                                            <SelectValue
                                                :placeholder="formValues.payment_method || 'Pilih Tipe Pemayaran'" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="method in paymentMethods" :key="method.value"
                                                :value="method.value">{{ method.label }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="payment_method" />
                        </div>

                        <div>
                            <Label for="type">Tipe Donasi</Label>
                            <Field v-model="formValues.type" name="type" type="select" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Select v-bind="field" v-model="formValues.type"
                                        @update:model-value="formValues.type = $event">
                                        <SelectTrigger>
                                            <SelectValue :placeholder="formValues.type || 'Pilih Tipe Donasi'" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="type in donationTypes" :key="type.value"
                                                :value="type.value">{{ type.label }}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="type" />
                        </div>

                        <div class="block mt-3 col-span-2 sm:w-2/5 sm:hidden">
                            <p class="font-semibold tracking-tight text-lg">Preview data</p>
                            <div class="my-2 p-3 rounded-lg border border-neutral-300 dark:border-neutral-600">
                                <p>Berikut adalah data yang akan disimpan</p>
                                <div class="grid grid-cols-2 gap-2 my-2">
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Tanggal Transfer</p>
                                        <p class="first-letter:uppercase">{{ formValues.transfer_date || '-' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Nama Duta</p>
                                        <p class="first-letter:uppercase">{{ formValues.ambassador || '-' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Nama Donatur</p>
                                        <p class="first-letter:uppercase">{{ formValues.donor || '-' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Donasi Atas Nama</p>
                                        <p class="first-letter:uppercase">{{ formValues.on_behalf_of || '-' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Jumlah Donasi</p>
                                        <p class="first-letter:uppercase">{{ formValues.amount || '-' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Metode Donasi</p>
                                        <p class="uppercase">{{ formValues.payment_method || '-' }}</p>
                                    </div>
                                    <div>
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                            Tipe Donasi</p>
                                        <p class="first-letter:uppercase">{{ formValues.type || '-' }}</p>
                                    </div>
                                </div>
                                <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                            </div>
                        </div>

                        <Separator class="col-span-2 my-2 dark:bg-neutral-600" />

                        <div class="col-span-2 flex gap-2 justify-end w-full">
                            <Button variant="outline" type="reset" @click="resetForm"
                                :disabled="isSubmitting">Reset</Button>
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
                                            <Button type="submit" form="editIncomeForm">Ya, Simpan</Button>
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
                                <p class="first-letter:uppercase">{{ formValues.transfer_date || '-' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama Duta</p>
                                <p class="first-letter:uppercase">{{ formValues.ambassador ?
                                    (availableAmbassadors.find(ambassador => ambassador.value.toString() ===
                                        formValues.ambassador)?.label) : '-' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Nama Donatur</p>
                                <p class="first-letter:uppercase">{{ formValues.donor || '-' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Donasi Atas Nama</p>
                                <p class="first-letter:uppercase">{{ formValues.on_behalf_of || '-' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Jumlah Donasi</p>
                                <p class="first-letter:uppercase">{{ formValues.amount || '-' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Metode Donasi</p>
                                <p class="uppercase">{{ formValues.payment_method || '-' }}</p>
                            </div>
                            <div>
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 -mb-1">
                                    Tipe Donasi</p>
                                <p class="first-letter:uppercase">{{ formValues.type || '-' }}</p>
                            </div>
                        </div>
                        <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
