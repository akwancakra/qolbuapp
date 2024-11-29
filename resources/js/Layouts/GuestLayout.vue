<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Toaster } from 'vue-sonner';

defineProps({
    containerClass: {
        type: String,
        default: null,
    },
});

// ========= DARK MODE VARIABLES =========
// State to track dark mode
const isDarkMode = ref(false);
// Fungsi untuk toggle mode
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark')
        localStorage.setItem('theme', 'dark')
    } else {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('theme', 'light')
    }
}

// Ambil tema dari localStorage saat halaman dimuat
onMounted(() => {
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme === 'dark') {
        isDarkMode.value = true
        document.documentElement.classList.add('dark')
    } else {
        isDarkMode.value = false
        document.documentElement.classList.remove('dark')
    }
})
</script>

<template>
    <div :class="['flex min-h-screen bg-neutral-100 dark:bg-neutral-900', { 'dark': isDarkMode }]">
        <div :class="`${containerClass ?? 'max-w-7xl'} w-full m-auto sm:py-4 sm:px-3 lg:px-4`">
            <slot />
        </div>
    </div>

    <Toaster position="bottom-right" :theme="isDarkMode ? 'dark' : 'light'" richColors />
</template>
