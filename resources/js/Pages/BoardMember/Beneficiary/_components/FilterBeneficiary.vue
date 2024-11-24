<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { Filter, Search, X } from 'lucide-vue-next'
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/Components/ui/sheet'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/Components/ui/select'
import { Separator } from '@/Components/ui/separator'
import { toTypedSchema } from '@vee-validate/zod'
import * as zod from 'zod';

const props = defineProps<{
    educationList: { label: string; value: string }[];
    statusList: { label: string; value: string }[];
    filters: {
        name: string;
        min_age: number;
        max_age: number;
        education: string;
        school_grade: string;
        shirt_size: string;
        shoe_size: number;
        gender: string;
        status: string;
        sort_by: string;
        sort_direction: string;
    }
}>();
const emit = defineEmits(['apply-filter']);

// CODE FOR VALIDATING DATAS
const validationSchema = toTypedSchema(
    zod.object({
        name: zod.string(),
        min_age: zod.number().nonnegative(),
        max_age: zod.number().nonnegative(),
        education: zod.string(),
        school_grade: zod.string(),
        status: zod.string(),
        sort_by: zod.string(),
        sort_direction: zod.string(),
    }).refine(data => data.min_age <= data.max_age, {
        message: "Min umur tidak boleh lebih dari max umur",
        path: ["min_age"],
    }).refine(data => data.max_age >= data.min_age, {
        message: "Max umur tidak boleh kurang dari min umur",
        path: ["max_age"],
    })
);

// CODE FOR FILTERING BENEFICIARY
const open = ref(false);
const filters = ref({
    name: '',
    min_age: 0,
    max_age: 0,
    education: 'default',
    school_grade: '',
    shirt_size: '',
    gender: 'default',
    shoe_size: 0,
    status: 'default',
    sort_by: 'created_at',
    sort_direction: 'desc',
})

const handleReset = () => {
    filters.value = {
        name: '',
        min_age: 0,
        max_age: 0,
        education: 'default',
        school_grade: '',
        shirt_size: '',
        gender: 'default',
        shoe_size: 0,
        status: 'default',
        sort_by: 'created_at',
        sort_direction: 'desc',
    }

    applyFilter();
}

const applyFilter = () => {
    try {
        // Validasi data menggunakan validationSchema
        validationSchema.parse(filters.value);

        // Jika validasi berhasil, lanjutkan proses
        open.value = false;
        emit('apply-filter', filters.value);
    } catch (error) {
        // Tangani kesalahan validasi
        if (error instanceof zod.ZodError) {
            const messages = error.errors.map((err) => `${err.path.join('.')} - ${err.message}`);
            alert(`Validation Error:\n${messages.join('\n')}`);
        } else {
            alert('Unexpected error occurred during validation');
        }
    }
};

onMounted(() => {
    filters.value.name = props.filters.name;
    filters.value.min_age = props.filters.min_age;
    filters.value.max_age = props.filters.max_age;
    filters.value.education = props.filters.education || 'default';
    filters.value.school_grade = props.filters.school_grade;
    filters.value.shirt_size = props.filters.shirt_size;
    filters.value.gender = props.filters.gender || 'default';
    filters.value.shoe_size = props.filters.shoe_size;
    filters.value.status = props.filters.status || 'default';
    filters.value.sort_by = props.filters.sort_by;
    filters.value.sort_direction = props.filters.sort_direction;
});
</script>

<template>
    <Sheet :open="open" @update:open="open = $event">
        <SheetTrigger asChild>
            <Button variant="outline" size="lg" class="px-4">
                <Filter class="h-4 w-4" />
                Filter
            </Button>
        </SheetTrigger>
        <SheetContent side="left">
            <SheetHeader>
                <SheetTitle class="flex items-center justify-between">
                    Filter Data
                    <!-- <Button variant="ghost" size="icon" class="rounded-full" @click="handleReset">
                        <X class="h-4 w-4" />
                    </Button> -->
                </SheetTitle>
            </SheetHeader>
            <Separator class="col-span-2 my-4 dark:bg-neutral-600" />
            <!-- <SheetDescription> -->
            <div class="flex flex-col gap-2">
                <!-- Nama Anak -->
                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium">Nama Anak</label>
                    <div class="relative">
                        <Search class="absolute left-2 top-3 h-4 w-4 text-muted-foreground" />
                        <Input id="name" placeholder="Cari nama..." class="pl-8" v-model="filters.name" />
                    </div>
                </div>

                <!-- Min & Max Umur -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="min_age" class="text-sm font-medium">Min. Umur</label>
                        <Input id="min_age" type="number" min="0" max="100" placeholder="cth: 5"
                            v-model="filters.min_age" />
                    </div>
                    <div class="space-y-2">
                        <label for="max_age" class="text-sm font-medium">Max. Umur</label>
                        <Input id="max_age" type="number" min="0" max="100" placeholder="cth: 16"
                            v-model="filters.max_age" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Pendidikan -->
                    <div class="space-y-2">
                        <label for="education" class="text-sm font-medium">Pendidikan</label>
                        <Select v-model="filters.education" @update:model-value="filters.education = $event">
                            <SelectTrigger id="education">
                                <SelectValue placeholder="Pilih Pendidikan" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="default">Semua</SelectItem>
                                <SelectItem v-for="education in educationList" :key="education.value"
                                    :value="education.value">{{ education.label }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="space-y-2">
                        <label for="school_grade" class="text-sm font-medium">Kelas</label>
                        <Input id="school_grade" type="number" min="0" max="14" placeholder="cth: 3"
                            v-model="filters.school_grade" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Baju -->
                    <div class="space-y-2">
                        <label for="shirt_size" class="text-sm font-medium">Uk. Baju</label>
                        <Input id="shirt_size" type="text" placeholder="cth: M" v-model="filters.shirt_size" />
                    </div>

                    <!-- Sepatu -->
                    <div class="space-y-2">
                        <label for="shoe_size" class="text-sm font-medium">Uk. Sepatu</label>
                        <Input id="shoe_size" type="number" min="0" max="50" placeholder="cth: 36"
                            v-model="filters.shoe_size" />
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="space-y-2">
                    <label for="status" class="text-sm font-medium">Jenis Kelamin</label>
                    <Select v-model="filters.gender" @update:model-value="filters.gender = $event">
                        <SelectTrigger id="gender">
                            <SelectValue placeholder="Pilih Jenis Kelamin" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="default">Semua</SelectItem>
                            <SelectItem value="male">Laki-laki</SelectItem>
                            <SelectItem value="female">Perempuan</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label for="status" class="text-sm font-medium">Status</label>
                    <Select v-model="filters.status" @update:model-value="filters.status = $event">
                        <SelectTrigger id="status">
                            <SelectValue placeholder="Pilih Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="default">Semua</SelectItem>
                            <SelectItem v-for="status in statusList" :key="status.value" :value="status.value">{{
                                status.label }}</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Apply Filter Button -->
                <div class="flex gap-2 mt-4">
                    <Button class="w-full" variant="outline" type="reset" @click="handleReset">Reset</Button>
                    <Button class="w-full" @click="applyFilter">Terapkan Filter</Button>
                </div>
            </div>
            <!-- </SheetDescription> -->
        </SheetContent>
    </Sheet>
</template>
