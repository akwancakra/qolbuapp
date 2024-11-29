<script setup lang="ts">
import { LineChart } from '@/Components/ui/chart-line';
import { formatNumber } from '@/lib/utils';


defineProps<{
    chartData: any;
}>();

</script>

<template>
    <LineChart :data="chartData" index="label" :categories="['value']" :y-formatter="(tick) => {
        return typeof tick === 'number'
            ? formatNumber(tick)
            : '';
    }
        " :colors="['#58bb69']" />
</template>
