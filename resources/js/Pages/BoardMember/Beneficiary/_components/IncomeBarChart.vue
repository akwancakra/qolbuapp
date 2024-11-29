<script setup lang="ts">
import { BarChart } from '@/Components/ui/chart-bar';
import { formatNumber } from '@/lib/utils';


defineProps<{
    chartData: any;
}>();

</script>

<template>
    <BarChart :data="chartData" index="label" :categories="['value']" :y-formatter="(tick) => {
        return typeof tick === 'number'
            ? formatNumber(tick)
            : '';
    }
        " :colors="['#58bb69']" />
</template>
