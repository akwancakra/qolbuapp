<script setup lang="ts">
import { Pagination, PaginationList } from "@/components/ui/pagination";
import { Button } from "@/components/ui/button";
import { computed, ref } from "vue";
import { PaginationTemplate } from "@/types";

const props = defineProps<{
    pagination: PaginationTemplate;
}>();

const emit = defineEmits(["page-change"]);

// Responsiveness
const isMobile = ref(window.innerWidth <= 768);

window.addEventListener("resize", () => {
    isMobile.value = window.innerWidth <= 768;
});

// Proses link pagination
const processedLinks = computed(() => {
    const totalPages = props.pagination.last_page;
    const currentPage = props.pagination.current_page;

    // Batas halaman di sekitar current_page
    const siblingCount = isMobile.value ? 1 : 2;
    const range = [];


    // Mobile: tampilkan max 3 halaman di tengah
    if (isMobile.value) {
        // Selalu tambahkan halaman pertama jika total halaman > 1
        if (totalPages > 1) {
            range.push({ type: "page", value: 1 });
        }

        // Tambahkan ellipsis jika halaman awal tidak dekat dengan current page
        if (currentPage > 3) {
            range.push({ type: "ellipsis" });
        }

        // Tambahkan halaman di sekitar current page
        const startPage = Math.max(2, currentPage - siblingCount);
        const endPage = Math.min(totalPages, currentPage + siblingCount);

        for (let i = startPage; i <= endPage; i++) {
            // Hindari menambahkan halaman 1 lagi
            if (i !== 1) {
                range.push({ type: "page", value: i });
            }
        }

        // Tambahkan ellipsis dan halaman terakhir jika diperlukan
        if (currentPage < totalPages - 1) {
            if (currentPage < totalPages - 2) {
                range.push({ type: "ellipsis" });
            }
            // Tambahkan halaman terakhir hanya jika belum ada
            if (range[range.length - 1].value !== totalPages) {
                range.push({ type: "page", value: totalPages });
            }
        }
    } else {
        // Desktop: tampilkan lebih banyak halaman
        const startPage = Math.max(1, currentPage - siblingCount);
        const endPage = Math.min(totalPages, currentPage + siblingCount);

        // Tambahkan halaman awal
        if (startPage > 1) {
            range.push({ type: "page", value: 1 });
            if (startPage > 2) {
                range.push({ type: "ellipsis" });
            }
        }

        // Tambahkan halaman di tengah
        for (let i = startPage; i <= endPage; i++) {
            range.push({ type: "page", value: i });
        }

        // Tambahkan halaman akhir
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                range.push({ type: "ellipsis" });
            }
            range.push({ type: "page", value: totalPages });
        }
    }

    return range;
});

// Navigasi ke halaman tertentu
const goToPage = (page: number) => {
    if (page >= 1 && page <= props.pagination.last_page) {
        emit("page-change", page);
    }
};
</script>

<template>
    <Pagination class="w-full mt-5 flex justify-center">
        <PaginationList class="flex items-center gap-1">
            <!-- Tombol Previous -->
            <Button :disabled="pagination.current_page === 1" @click="goToPage(pagination.current_page - 1)"
                variant="outline">
                &laquo;
            </Button>

            <!-- Links (angka dan ellipsis) -->
            <template v-for="(item, index) in processedLinks" :key="index">
                <template v-if="item.type === 'page'">
                    <Button :variant="pagination.current_page === item.value ? 'default' : 'outline'"
                        @click="goToPage(item.value as number)">
                        {{ item.value }}
                    </Button>
                </template>
                <template v-else>
                    <Button variant="outline" size="icon" class="cursor-default w-fit px-0.5">
                        ...
                    </Button>
                </template>
            </template>

            <!-- Tombol Next -->
            <Button :disabled="pagination.current_page === pagination.last_page"
                @click="goToPage(pagination.current_page + 1)" variant="outline">
                &raquo;
            </Button>
        </PaginationList>
    </Pagination>
</template>
