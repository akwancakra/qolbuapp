<template>
    <div v-if="active" class="p-2 bg-white shadow-md rounded-md text-sm">
        <!-- Tampilkan hari dan tanggal -->
        <div class="font-semibold">{{ formattedDate }}</div>
        <!-- Tampilkan pendapatan dengan format Rupiah -->
        <div>Total: <span class="font-semibold">{{ formattedTotal }}</span></div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps({
    payload: {
        type: Array,
        required: true,
    },
    active: {
        type: Boolean,
        required: true,
    },
})

const formattedDate = ref('')
const formattedTotal = ref('')

// Watch for changes in payload and active to update the tooltip content
watch([() => props.payload, () => props.active], ([payload, active]) => {
    if (active && payload && payload.length) {
        const dataPoint = payload[0].payload

        // Ambil hari dan tanggal dari data
        const day = dataPoint.day
        const date = dataPoint.date

        // Update formatted date (contoh: "17 Sen")
        formattedDate.value = `${date} ${day.substring(0, 3)}`

        // Format total pendapatan ke dalam Rupiah
        const total = dataPoint.pendapatan
        formattedTotal.value = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`
    }
})
</script>
