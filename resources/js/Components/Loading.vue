<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next';
import { defineProps } from 'vue';

const props = defineProps({
    message: {
        type: String,
        default: 'Memuat...'
    }
});
</script>

<template>
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm">
        <div className="flex flex-col items-center space-y-4 text-center">
            <Loader2 :class="'h-8 w-8 animate-spin text-primary'" />
            <p :class="'text-lg font-medium text-foreground'">{{ props.message }}</p>
        </div>
    </div>
</template>
