<script setup lang="ts">
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { formatCurrency } from '@/lib/utils';

interface TopDonor {
    donor: string;
    total_amount: number;
}

defineProps<{
    topDonors: TopDonor[];
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
            <TableRow v-if="topDonors[0]" class="bg-yellow-100 font-semibold dark:bg-yellow-500 dark:text-yellow-100">
                <TableCell class="py-1.5">1</TableCell>
                <TableCell class="py-1.5">{{ topDonors[0].donor }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(topDonors[0].total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-if="topDonors[1]" class="bg-gray-100 font-semibold dark:bg-gray-500 dark:text-gray-100">
                <TableCell class="py-1.5">2</TableCell>
                <TableCell class="py-1.5">{{ topDonors[1].donor }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(topDonors[1].total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-if="topDonors[2]"
                class="bg-yellow-700 text-white font-semibold dark:bg-yellow-800 dark:text-yellow-100 hover:text-white hover:bg-yellow-800">
                <TableCell class="py-1.5">3</TableCell>
                <TableCell class="py-1.5">{{ topDonors[2].donor }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(topDonors[2].total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-for="(ambassador, index) in topDonors.slice(3)">
                <TableCell class="py-1.5">{{ index + 4 }}</TableCell>
                <TableCell class="py-1.5">{{ ambassador.donor }}</TableCell>
                <TableCell class="py-1.5">{{ formatCurrency(ambassador.total_amount) }}</TableCell>
            </TableRow>
            <TableRow v-for="i in 10 - topDonors.length">
                <TableCell class="py-1.5">{{ i + topDonors.length }}</TableCell>
                <TableCell class="py-1.5">-</TableCell>
                <TableCell class="py-1.5">-</TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
