<script setup lang="ts">
import * as zod from 'zod';
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Form, Field, ErrorMessage } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import { computed, onMounted, ref } from 'vue';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Separator } from '@/Components/ui/separator';
import { Button } from '@/Components/ui/button';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import { AlertCircle, Check, ChevronsUpDown, CornerUpLeftIcon, Loader2, Save } from 'lucide-vue-next';
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/Components/ui/command";
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';
import { formatCurrency, getImageUrl } from '@/lib/utils';
import { toast } from 'vue-sonner';
import { useDateFormat } from '@vueuse/core';
import { Ambassador, Income } from '@/types';

const props = defineProps<{
    income: Income,
    ambassador: Ambassador,
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
        proof: zod.any().refine((file) => {
            if (!file) return true;
            const acceptedFormats = ['image/jpeg', 'image/png', 'application/pdf'];
            return file.size <= 1048576 && acceptedFormats.includes(file.type);
        }, { message: 'Bukti transfer harus berupa file jpg, jpeg, png, atau pdf dan maksimal 1MB' }),
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
    proof: null as File | null
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
        proof: null as File | null
    });
}

function initializeForm(income: Income, ambassador: Ambassador) {
    Object.assign(formValues, {
        transfer_date: useDateFormat(income.transfer_date, 'YYYY-MM-DD').value,
        ambassador: `${ambassador.code} ${ambassador.name}`,
        donor: income.donor,
        on_behalf_of: income.on_behalf_of,
        amount: parseInt(income.amount.toString()),
        payment_method: income.payment_method,
        type: income.type,
    });
}

async function onSubmit(values: any) {
    isSubmitting.value = true;
    try {
        let ambassador = props.availableAmbassadors.find((ambassador) => ambassador.label === formValues.ambassador)?.value;

        Inertia.post(route('board_member.incomes.update', props.income.id), {
            ...values,
            amount: Number(values.amount),
            ambassador: Number(ambassador),
        }, {
            onError: (errors) => {
                console.error("Error dari backend:", errors);

                if (errors.message) {
                    toast.error(errors.message);
                }
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    } catch (error) {
        isSubmitting.value = false;
    }
}

// Add a computed property to handle file preview
const filePreviewUrl = computed(() => {
    return formValues.proof ? URL.createObjectURL(formValues.proof) : null;
});

onMounted(() => {
    initializeForm(props.income, props.ambassador);
});
</script>

<template>

    <Head>
        <title>Ubah Data Pendapatan</title>
    </Head>

    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold tracking-tight">Ubah Data Pendapatan</h1>
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
                            <Label for="transfer_date">Tanggal Transfer
                                <span class="text-xs text-red-500">*wajib diisi</span>
                            </Label>
                            <Field v-model="formValues.transfer_date" name="transfer_date" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="date" />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="transfer_date" />
                        </div>

                        <div>
                            <Label for="ambassador">Duta
                                <span class="text-xs text-red-500">*wajib dipilih</span>
                            </Label>
                            <Field v-model="formValues.ambassador" name="ambassador" type="text"
                                :rules="{ required: true }">
                                <Popover v-model:open="openAmbassador">
                                    <PopoverTrigger as-child>
                                        <Button variant="outline" role="combobox" :aria-expanded="openAmbassador"
                                            class="w-full justify-between">
                                            {{ formValues.ambassador ? availableAmbassadors.find((ambassador) =>
                                                ambassador.label
                                                ===
                                                formValues.ambassador)?.label || 'Semua'
                                                : 'Pilih Duta...' }}
                                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="md:w-auto lg:w-[300px] p-0">
                                        <Command v-model="formValues.ambassador">
                                            <CommandInput placeholder="Cari Duta..." />
                                            <CommandEmpty>Tidak ada duta ditemukan.</CommandEmpty>
                                            <CommandList>
                                                <CommandGroup>
                                                    <CommandItem v-for="ambassador in availableAmbassadors"
                                                        :key="ambassador.value" :value="ambassador.label"
                                                        @select="openAmbassador = false"
                                                        class="overflow-hidden text-ellipsis">
                                                        <Check :class="[
                                                            'mr-2 h-4 w-4',
                                                            formValues.ambassador === ambassador.label ? 'opacity-100' : 'opacity-0',
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
                            <Label for="donor">Nama donatur
                                <span class="text-xs text-red-500">*wajib diisi</span>
                            </Label>
                            <Field v-model="formValues.donor" name="donor" type="text" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="Nama donatur..." />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="donor" />
                        </div>

                        <div>
                            <Label for="on_behalf_of">Donasi Atas Nama
                                <span class="text-xs text-red-500">*wajib diisi</span>
                            </Label>
                            <Field v-model="formValues.on_behalf_of" name="on_behalf_of" type="text"
                                :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" placeholder="Donasi atas nama..." />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="on_behalf_of" />
                        </div>

                        <div>
                            <Label for="amount">Jumlah donasi
                                <span class="text-xs text-red-500">*wajib diisi</span>
                            </Label>
                            <Field v-model="formValues.amount" name="amount" type="number" :rules="{ required: true }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="number" />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="amount" />
                        </div>

                        <div>
                            <!-- cSpell:ignore Metode Donasi Pilih Tipe Pemayaran mandiri Mandiri lainnya Lainnya -->
                            <Label for="payment_method">Metode Donasi
                                <span class="text-xs text-red-500">*wajib dipilih</span>
                            </Label>
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
                            <Label for="type">Tipe Donasi
                                <span class="text-xs text-red-500">*wajib dipilih</span>
                            </Label>
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

                        <div>
                            <Label for="proof">Bukti Transfer</Label>
                            <Field v-model="formValues.proof" name="proof" type="file" :rules="{ required: false }">
                                <template v-slot="{ field }">
                                    <Input v-bind="field" type="file" class="dark:!text-white" />
                                </template>
                            </Field>
                            <ErrorMessage class="text-red-500 text-sm" name="proof" />
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
                                        <p class="first-letter:uppercase">{{ formValues.ambassador ?
                                            (availableAmbassadors.find(ambassador => ambassador.label ===
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
                                        <p class="first-letter:uppercase">{{ formatCurrency(formValues.amount) || '-' }}
                                        </p>
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

                                    <div class="col-span-2">
                                        <p
                                            class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 mb-1">
                                            Bukti Pembayaran <span class="text-xs">(opsional)</span>
                                        </p>
                                        <div
                                            v-if="formValues.proof && formValues.proof.type !== 'application/pdf' && filePreviewUrl">
                                            <img :src="filePreviewUrl" alt="Bukti Transfer"
                                                class="max-w-full max-h-56 h-auto rounded-lg" />
                                        </div>
                                        <div
                                            v-else-if="formValues.proof && formValues.proof.type === 'application/pdf'">
                                            <p>Bukti telah diunggah</p>
                                        </div>
                                        <div v-else>
                                            <div v-if="income.proof">
                                                <img :src="getImageUrl(`proof/${income.proof}`)" alt="Bukti Transfer"
                                                    class="max-w-full max-h-56 h-auto rounded-lg" />
                                            </div>
                                            <div v-else>
                                                <p>Tidak ada bukti yang diunggah</p>
                                            </div>
                                        </div>
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
                                    (availableAmbassadors.find(ambassador => ambassador.label ===
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
                                <p class="first-letter:uppercase">{{ formatCurrency(formValues.amount) || '-' }}</p>
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

                            <div class="col-span-2">
                                <p
                                    class="font-semibold text-sm tracking-tight text-neutral-600 dark:text-neutral-400 mb-1">
                                    Bukti Pembayaran <span class="text-xs">(opsional)</span>
                                </p>
                                <div
                                    v-if="formValues.proof && formValues.proof.type !== 'application/pdf' && filePreviewUrl">
                                    <img :src="filePreviewUrl" alt="Bukti Transfer"
                                        class="max-w-full max-h-56 h-auto rounded-lg" />
                                </div>
                                <div v-else-if="formValues.proof && formValues.proof.type === 'application/pdf'">
                                    <p>Bukti telah diunggah</p>
                                </div>
                                <div v-else>
                                    <div v-if="income.proof">
                                        <img :src="getImageUrl(`proof/${income.proof}`)" alt="Bukti Transfer"
                                            class="max-w-full max-h-56 h-auto rounded-lg" />
                                    </div>
                                    <div v-else>
                                        <p>Tidak ada bukti yang diunggah</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p>Apabila anda sudah yakin anda dapat langsung menyimpan data</p>
                    </div>
                </div>
            </div>
        </section>
    </DashboardLayout>
</template>
