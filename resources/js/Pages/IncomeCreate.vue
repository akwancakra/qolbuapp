<script setup lang="ts">
import * as zod from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { computed, reactive, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { toast } from 'vue-sonner';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { ErrorMessage, Field, Form } from 'vee-validate';
import { Label } from '@/Components/ui/label';
import { Button } from '@/Components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Input } from '@/Components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover';
import { AlertCircleIcon, CheckIcon, ChevronsUpDownIcon, CornerUpLeftIcon, Loader2Icon, SaveIcon } from 'lucide-vue-next';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/Components/ui/command';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';
import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/Components/ui/number-field';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/Components/ui/alert-dialog';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps<{
    availableAmbassadors: { label: string; value: string }[];
    paymentMethods: { label: string; value: string }[];
    donationTypes: { label: string; value: string }[];
}>();

// ==========================================
// FORM HANDLING
// ==========================================
const isSubmitting = ref(false);
const openAmbassador = ref(false);

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

async function onSubmit(values: any) {
    isSubmitting.value = true;
    try {
        let ambassador = props.availableAmbassadors.find((ambassador) => ambassador.label === formValues.ambassador)?.value;

        Inertia.post(route('incomes.store'), {
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

</script>

<template>

    <Head>
        <title>Tambah Data Pendapatan</title>
    </Head>

    <GuestLayout :containerClass="'sm:max-w-xl'">
        <Card
            className="mx-auto rounded-md bg-white border border-neutral-300 px-3 py-2 sm:px-6 sm:py-4 dark:border-neutral-600 dark:bg-neutral-800">
            <CardHeader>
                <CardTitle className="text-2xl tracking-tight font-semibold text-center">Form Data Pendapatan
                </CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <Form id="addIncomeForm" :validation-schema="validationSchema" @submit="onSubmit" className="space-y-4"
                    v-slot="{ errors }">
                    <template v-if="Object.keys(errors).length">
                        <Alert variant="destructive" class="col-span-2">
                            <AlertCircleIcon class="w-4 h-4" />
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

                    <div className="space-y-2">
                        <Label htmlFor="transfer_date">Tanggal Transfer</Label>
                        <Field v-model="formValues.transfer_date" name="transfer_date" type="text"
                            :rules="{ required: true }">
                            <template v-slot="{ field }">
                                <Input v-bind="field" type="date" />
                            </template>
                        </Field>
                        <ErrorMessage class="text-red-500 text-sm" name="transfer_date" />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="ambassador">Duta</Label>
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
                                        <ChevronsUpDownIcon class="ml-2 h-4 w-4 shrink-0 opacity-50" />
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
                                                    <CheckIcon :class="[
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

                    <div className="space-y-2">
                        <Label htmlFor="donor">Donatur</Label>
                        <Field v-model="formValues.donor" name="donor" type="text" :rules="{ required: true }">
                            <template v-slot="{ field }">
                                <Input v-bind="field" placeholder="cth: Rahmat" />
                            </template>
                        </Field>
                        <ErrorMessage class="text-red-500 text-sm" name="donor" />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="on_behalf_of">Atas Nama</Label>
                        <Field v-model="formValues.on_behalf_of" name="on_behalf_of" type="text"
                            :rules="{ required: true }">
                            <template v-slot="{ field }">
                                <Input v-bind="field" placeholder="cth: Joni atau -" />
                            </template>
                        </Field>
                        <ErrorMessage class="text-red-500 text-sm" name="on_behalf_of" />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="amount">Nominal</Label>
                        <Field v-model="formValues.amount" name="amount" type="number" :rules="{ required: true }">
                            <template v-slot="{ field }">
                                <NumberField v-bind="field" name="amount" id="balance" :min="0" :default-value="0"
                                    :format-options="{
                                        style: 'currency',
                                        currency: 'IDR',
                                        currencyDisplay: 'code',
                                        currencySign: 'accounting',
                                        maximumFractionDigits: 0,
                                    }" :step="10000">
                                    <NumberFieldContent>
                                        <NumberFieldDecrement />
                                        <NumberFieldInput />
                                        <NumberFieldIncrement />
                                    </NumberFieldContent>
                                </NumberField>
                            </template>
                        </Field>
                        <ErrorMessage class="text-red-500 text-sm" name="amount" />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="payment_method">Metode Pembayaran</Label>
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

                    <div className="space-y-2">
                        <Label htmlFor="type">Tipe Donasi</Label>
                        <Field v-model="formValues.type" name="type" type="select" :rules="{ required: true }">
                            <template v-slot="{ field }">
                                <Select v-bind="field" v-model="formValues.type"
                                    @update:model-value="formValues.type = $event">
                                    <SelectTrigger>
                                        <SelectValue :placeholder="formValues.type || 'Pilih Tipe Donasi'" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="type in donationTypes" :key="type.value" :value="type.value">
                                            {{ type.label }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </template>
                        </Field>
                        <ErrorMessage class="text-red-500 text-sm" name="type" />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="proof">Bukti Pendapatan</Label>
                        <Field v-model="formValues.proof" name="proof" type="file" :rules="{ required: false }">
                            <template v-slot="{ field }">
                                <Input v-bind="field" type="file" class="dark:!text-white" />
                            </template>
                        </Field>
                        <ErrorMessage class="text-red-500 text-sm" name="proof" />

                        <div>
                            <div
                                v-if="formValues.proof && formValues.proof.type !== 'application/pdf' && filePreviewUrl">
                                <img :src="filePreviewUrl" alt="Bukti Transfer"
                                    class="max-w-full max-h-56 h-auto rounded-lg" />
                            </div>
                            <div v-else-if="formValues.proof && formValues.proof.type === 'application/pdf'">
                                <p>Bukti telah diunggah</p>
                            </div>
                            <div v-else>
                                <p>Tidak ada bukti yang diunggah</p>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-2">
                        <AlertDialog>
                            <AlertDialogTrigger as-child>
                                <Button class="w-full" type="button" :disabled="isSubmitting">
                                    <template v-if="isSubmitting">
                                        <Loader2Icon class="w-4 h-4 mr-2 animate-spin" />
                                        Menyimpan...
                                    </template>
                                    <template v-else>
                                        Simpan
                                        <SaveIcon class="w-4 h-4 ml-1" />
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
                                        <Button type="submit" form="addIncomeForm">Ya, Simpan</Button>
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </GuestLayout>
</template>
