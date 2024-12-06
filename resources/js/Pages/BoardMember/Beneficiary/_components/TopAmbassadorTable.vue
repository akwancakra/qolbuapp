<script setup lang="ts">
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatCurrency } from '@/lib/utils';
import { Ambassador } from '@/types';

interface TopAmbassador {
    ambassador_id: number;
    total_amount: number;
    ambassador: Ambassador;
}

defineProps<{
    topAmbassadors: TopAmbassador[];
}>();

</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead class="h-8">#</TableHead>
                <TableHead class="h-8">Nama</TableHead>
                <TableHead class="h-8">Nominal</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-if="topAmbassadors[0]"
                class="bg-yellow-100 font-semibold dark:bg-yellow-500 dark:text-yellow-100">
                <TableCell class="py-1.5">1</TableCell>
                <TableCell class="py-1.5">{{ topAmbassadors[0].ambassador.name }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(topAmbassadors[0].total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-if="topAmbassadors[1]" class="bg-gray-100 font-semibold dark:bg-gray-500 dark:text-gray-100">
                <TableCell class="py-1.5">2</TableCell>
                <TableCell class="py-1.5">{{ topAmbassadors[1].ambassador.name }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(topAmbassadors[1].total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-if="topAmbassadors[2]"
                class="bg-yellow-700 text-white font-semibold dark:bg-yellow-800 dark:text-yellow-100 hover:text-white hover:bg-yellow-800">
                <TableCell class="py-1.5">3</TableCell>
                <TableCell class="py-1.5">{{ topAmbassadors[2].ambassador.name }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(topAmbassadors[2].total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-for="(ambassador, index) in topAmbassadors.slice(3)">
                <TableCell class="py-1.5">{{ index + 4 }}</TableCell>
                <TableCell class="py-1.5">{{ ambassador.ambassador.name }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(ambassador.total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-for="i in 10 - topAmbassadors.length">
                <TableCell class="py-1.5">{{ i + topAmbassadors.length }}</TableCell>
                <TableCell class="py-1.5">-</TableCell>
                <TableCell class="py-1.5">-</TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
