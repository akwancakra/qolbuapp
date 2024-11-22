<script lang="ts" setup>
import { cn } from '@/lib/utils'
import { RangeCalendarRoot, type RangeCalendarRootEmits, type RangeCalendarRootProps, useForwardPropsEmits } from 'radix-vue'
import { computed, type HTMLAttributes } from 'vue'
import { RangeCalendarCell, RangeCalendarCellTrigger, RangeCalendarGrid, RangeCalendarGridBody, RangeCalendarGridHead, RangeCalendarGridRow, RangeCalendarHeadCell, RangeCalendarHeader, RangeCalendarHeading, RangeCalendarNextButton, RangeCalendarPrevButton } from '.'

const props = defineProps<RangeCalendarRootProps & { class?: HTMLAttributes['class'] }>()

const emits = defineEmits<RangeCalendarRootEmits>()

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props

    return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
    <RangeCalendarRoot v-slot="{ grid, weekDays }" :class="cn('p-3', props.class)" v-bind="forwarded">
        <RangeCalendarHeader>
            <RangeCalendarPrevButton />
            <RangeCalendarHeading />
            <RangeCalendarNextButton />
        </RangeCalendarHeader>

        <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
            <RangeCalendarGrid v-for="month in grid" :key="month.value.toString()">
                <RangeCalendarGridHead>
                    <RangeCalendarGridRow>
                        <RangeCalendarHeadCell v-for="day in weekDays" :key="day">
                            {{ day }}
                        </RangeCalendarHeadCell>
                    </RangeCalendarGridRow>
                </RangeCalendarGridHead>
                <RangeCalendarGridBody>
                    <RangeCalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`"
                        class="mt-2 w-full">
                        <RangeCalendarCell v-for="weekDate in weekDates" :key="weekDate.toString()" :date="weekDate">
                            <RangeCalendarCellTrigger :day="weekDate" :month="month.value" />
                        </RangeCalendarCell>
                    </RangeCalendarGridRow>
                </RangeCalendarGridBody>
            </RangeCalendarGrid>
        </div>
    </RangeCalendarRoot>
</template>

<!-- <script lang="ts" setup>
import { ref, computed } from "vue";
import { RangeCalendarRoot, type RangeCalendarRootEmits, type RangeCalendarRootProps, useForwardPropsEmits } from 'radix-vue'
import { RangeCalendarGrid, RangeCalendarCell, RangeCalendarHeader, RangeCalendarGridRow, RangeCalendarGridBody, RangeCalendarPrevButton, RangeCalendarNextButton, RangeCalendarHeading, RangeCalendarCellTrigger } from ".";
import { Select, SelectItem } from "../select";

const selectedYear = ref(new Date().getFullYear());
const selectedMonth = ref(new Date().getMonth() + 1); // Bulan dimulai dari 1

const months = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
];
const years = [2022, 2023, 2024, 2025];

const calendarDate = computed(() => {
    return {
        year: selectedYear.value,
        month: selectedMonth.value,
    };
});

// Update calendar ketika dropdown berubah
const handleYearChange = (year: number) => {
    selectedYear.value = year;
};

const handleMonthChange = (month: number) => {
    selectedMonth.value = month;
};

// Reset kalender ke hari ini
const resetToToday = () => {
    const today = new Date();
    selectedYear.value = today.getFullYear();
    selectedMonth.value = today.getMonth() + 1;
};
</script>

<template>
    <div>
        <RangeCalendarRoot :class="`calendar-${calendarDate.year}-${calendarDate.month}`" v-slot="{ grid, weekDays }">
            <div class="flex gap-4 mb-4">
                <Select v-model="selectedYear"
                    @change="handleYearChange(Number(($event.target as HTMLSelectElement).value))">
                    <SelectItem v-for="year in years" :key="year" :value="year.toString()">
                        {{ year }}
                    </SelectItem>
                </Select>
                <select v-model="selectedMonth"
                    @change="handleMonthChange(Number(($event.target as HTMLSelectElement).value))">
                    <SelectItem v-for="(month, index) in months" :key="index" :value="(index + 1).toString()">
                        {{ month }}
                    </SelectItem>
                </select>
            </div>

            <RangeCalendarHeader>
                <RangeCalendarPrevButton />
                <RangeCalendarHeading>{{ calendarDate.year }} - {{ months[calendarDate.month - 1] }}
                </RangeCalendarHeading>
                <RangeCalendarNextButton />
            </RangeCalendarHeader>

            <RangeCalendarGrid v-for="month in grid" :key="month.value.toString()">
                <RangeCalendarGridBody>
                    <RangeCalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`"
                        class="mt-2">
                        <RangeCalendarCell v-for="weekDate in weekDates" :key="weekDate.toString()" :date="weekDate">
                            <RangeCalendarCellTrigger :day="weekDate" :month="month.value" />
                        </RangeCalendarCell>
                    </RangeCalendarGridRow>
                </RangeCalendarGridBody>
            </RangeCalendarGrid>
        </RangeCalendarRoot>

        <button @click="resetToToday">Reset to Today</button>
    </div>
</template> -->
