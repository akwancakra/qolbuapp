<script setup>
import { ref, computed, watchEffect } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    HomeIcon,
    UsersIcon,
    FolderIcon,
    CalendarIcon,
    InboxIcon,
    ChartBarIcon,
    Menu,
    LogOut,
} from 'lucide-vue-next';

// Sidebar expansion state
const isExpanded = ref(true);

// Toggle sidebar visibility
const toggleSidebar = () => {
    isExpanded.value = !isExpanded.value;
};

// Menu items
const menuItems = [
    { name: 'Dashboard', icon: HomeIcon },
    { name: 'Team', icon: UsersIcon },
    { name: 'Projects', icon: FolderIcon },
    { name: 'Calendar', icon: CalendarIcon },
    { name: 'Documents', icon: InboxIcon },
    { name: 'Reports', icon: ChartBarIcon },
];

// Detect if the viewport is tablet size (<= 768px)
const isTablet = ref(window.innerWidth <= 768);

watchEffect(() => {
    const updateIsTablet = () => {
        isTablet.value = window.innerWidth <= 768;
    };

    window.addEventListener('resize', updateIsTablet);

    // Cleanup event listener on unmount
    return () => {
        window.removeEventListener('resize', updateIsTablet);
    };
});
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div v-if="!isTablet" :class="[
            'flex flex-col h-full bg-white transition-all duration-300 border-r border-neutral-300 ease-in-out',
            isExpanded ? 'w-64' : 'w-20'
        ]">
            <!-- Logo -->
            <div class="flex items-center justify-center h-20">
                <h1 class="text-2xl font-semibold tracking-tight" v-if="!isExpanded">QA</h1>
                <h1 class="text-2xl font-semibold tracking-tight" v-else>Qolbu App</h1>
            </div>

            <!-- Nav Items -->
            <nav class="flex-1 overflow-y-auto">
                <ul class="p-3 space-y-2">
                    <li v-for="item in menuItems" :key="item.name">
                        <Button variant="ghost" :class="[
                            'w-full justify-start hover:bg-neutral-200 transition-colors duration-200',
                            isExpanded ? 'px-4' : 'px-2'
                        ]">
                            <component :is="item.icon" :class="['h-5 w-5', isExpanded ? 'mr-4' : 'mx-auto']" />
                            <span v-if="isExpanded" class="truncate">{{ item.name }}</span>
                        </Button>
                    </li>
                </ul>
            </nav>

            <!-- Footer Logout -->
            <div class="p-3">
                <div
                    :class="['flex justify-between items-center', isExpanded ? 'p-2 border border-neutral-200 rounded-lg' : '']">
                    <div v-if="isExpanded">
                        <p class="font-semibold tracking-tight -mb-1">Nama User</p>
                        <p class="text-sm text-neutral-400">Role User</p>
                    </div>
                    <Button variant="outline" :size="[isExpanded ? 'icon' : 'default']">
                        <LogOut size="18" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow">
                <div :class="['p-3 flex items-center', isTablet ? 'gap-3' : 'gap-2']">
                    <!-- Sheet Component -->
                    <Sheet v-if="isTablet">
                        <SheetTrigger as-child>
                            <Button size="icon" variant="outline">
                                <Menu size="18" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="p-3 flex flex-col h-full">
                            <SheetHeader class="my-5">
                                <SheetTitle class="font-semibold tracking-tight">Qolbu App</SheetTitle>
                            </SheetHeader>
                            <ul class="space-y-2">
                                <li v-for="item in menuItems" :key="item.name">
                                    <Button variant="ghost"
                                        class="w-full justify-start hover:bg-neutral-200 transition-colors duration-200">
                                        <component :is="item.icon"
                                            :class="['h-5 w-5', isExpanded ? 'mr-4' : 'mx-auto']" />
                                        <span v-if="isExpanded" class="truncate">{{ item.name }}</span>
                                    </Button>
                                </li>
                            </ul>
                            <SheetFooter class="mt-auto">
                                <div
                                    :class="['flex justify-between items-center', isExpanded ? 'p-2 border border-neutral-200 rounded-lg' : '']">
                                    <div v-if="isExpanded">
                                        <p class="font-semibold tracking-tight -mb-1">Nama User</p>
                                        <p class="text-sm text-neutral-400">Role User</p>
                                    </div>
                                    <Button variant="outline" :size="[isExpanded ? 'icon' : 'default']">
                                        <LogOut size="18" />
                                    </Button>
                                </div>
                            </SheetFooter>
                        </SheetContent>
                    </Sheet>

                    <Button v-if="!isTablet" variant="outline" size="icon" @click="toggleSidebar">
                        <ChevronLeftIcon v-if="isExpanded" size="18" />
                        <ChevronRightIcon v-else size="18" />
                    </Button>
                    <slot name="header">
                        <h1 class="text-xl font-semibold tracking-tight">Header</h1>
                    </slot>
                </div>
            </header>
            <main>
                <div class="max-w-7xl mx-auto py-4 px-2 sm:px-3 lg:px-4">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
