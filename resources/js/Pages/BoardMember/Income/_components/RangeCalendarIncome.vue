<script setup lang="ts">
import type { DateRange } from 'radix-vue'
import { Button } from '@/Components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover'
import { RangeCalendar } from '@/Components/ui/range-calendar'
import {
    CalendarDate,
    DateFormatter,
    getLocalTimeZone,
} from '@internationalized/date'
import { Calendar as CalendarIcon } from 'lucide-vue-next'
import { type Ref, ref, watch } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
    modelValue: DateRange;
    clientLocale: string;
}>();

const emit = defineEmits(["update:modelValue"]);

const df = new DateFormatter(props.clientLocale, {
    dateStyle: 'medium',
})

const value = ref({
    start: props.modelValue?.start || new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()),
    end: props.modelValue?.end || new CalendarDate(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate()).add({ days: 7 }),
}) as Ref<DateRange>

watch(value, (newValue) => {
    emit("update:modelValue", newValue);
});
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn(
                'w-full justify-start text-left font-normal overflow-hidden text-ellipsis',
                !value.start && 'text-muted-foreground',
            )">
                <CalendarIcon class="mr-2 h-4 w-4" />
                <template v-if="value.start">
                    <template v-if="value.end">
                        {{ df.format(value.start.toDate(getLocalTimeZone())) }} - {{
                            df.format(value.end.toDate(getLocalTimeZone())) }}
                    </template>

                    <template v-else>
                        {{ df.format(value.start.toDate(getLocalTimeZone())) }}
                    </template>
                </template>
                <template v-else>
                    Pick a date
                </template>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <RangeCalendar v-model="value" initial-focus :number-of-months="2" />
        </PopoverContent>
    </Popover>
</template>
