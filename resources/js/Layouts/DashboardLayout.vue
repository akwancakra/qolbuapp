<script setup>
import { ref, computed, watchEffect, onMounted } from 'vue';
import { Toaster } from 'vue-sonner'
import { Button } from '@/Components/ui/button';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Sheet,
    SheetContent,
    SheetFooter,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/Components/ui/sheet';
import {
    ChevronRightIcon,
    HomeIcon,
    UsersIcon,
    ReceiptTextIcon,
    HeartHandshakeIcon,
    SquareUserIcon,
    MenuIcon,
    LogOutIcon,
    SunIcon,
    MoonIcon,
    SidebarIcon,
} from 'lucide-vue-next';

const props = defineProps({
    containerClass: {
        type: String,
        default: null,
    },
});

// Ambil data user dari props Inertia
const page = usePage()
const user = computed(() => page.props.auth.user) // Role pengguna dari backend
// const currentUrl = computed(() => page.url)
// console.log(String(currentUrl.value).replace(/^\/(pe|ad|du)\//, '').startsWith("transactions"));

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
// ========= DARK MODE VARIABLES =========

// ========= SIDEBAR VARIABLES =========
// Sidebar expansion state
const isExpanded = ref(true);

// Toggle sidebar visibility
const toggleSidebar = () => {
    isExpanded.value = !isExpanded.value;
};

// Menu items
const menuItems = ref([
    { name: 'Dasbor', icon: HomeIcon, href: route('dashboard'), roles: ['admin', 'pengurus', 'duta'], prefix: 'dashboard' },
    { name: 'Pendapatan', icon: ReceiptTextIcon, href: user.value.role === 'pengurus' ? route('board_member.incomes.index') : route('ambassador.incomes.index'), roles: ['pengurus', 'duta'], prefix: 'incomes' },
    { name: 'Penerima Manfaat', icon: HeartHandshakeIcon, href: user.value.role === 'pengurus' ? route('board_member.beneficiaries.index') : route('ambassador.beneficiaries.index'), roles: ['pengurus', 'duta'], prefix: 'beneficiaries' },
    { name: 'Akun', icon: UsersIcon, href: route('admin.users.index'), roles: ['admin'], prefix: 'users' },
    { name: 'Profil', icon: SquareUserIcon, href: route('profile.edit'), roles: ['admin', 'pengurus', 'duta'], prefix: 'profile' },
]);

const filteredMenuItems = computed(() => {
    if (!user.value || !user.value.role) return [];

    return menuItems.value.filter(item =>
        item.roles.includes(user.value.role) // user.value.role
    );
});

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
// ========= SIDEBAR VARIABLES =========
</script>

<template>
    <div :class="['flex h-screen bg-neutral-100 dark:bg-neutral-900', { 'dark': isDarkMode }]">
        <!-- Sidebar -->
        <div v-if="!isTablet" :class="[
            'flex flex-col h-full bg-white transition-all duration-300 border-r border-neutral-300 ease-in-out dark:bg-neutral-800 dark:border-neutral-700',
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
                    <li v-for="item in filteredMenuItems" :key="item.name">
                        <Link :href="item.href">
                        <Button variant="ghost" :class="[
                            'w-full justify-start transition-colors duration-200',
                            isExpanded ? 'px-4' : 'px-2',
                            // String(currentUrl.value).replace(/^\/(pe|ad|du)\//, '').startsWith(item.prefix)
                            //     ? 'bg-neutral-200 dark:bg-neutral-700 hover:bg-neutral-300 dark:hover:bg-neutral-800'
                            //     : 'hover:bg-neutral-200 dark:hover:bg-neutral-700'
                        ]">
                            <component :is="item.icon" :class="['h-5 w-5', isExpanded ? 'mr-4' : 'mx-auto']" />
                            <span v-if="isExpanded" class="truncate">{{ item.name }}</span>
                        </Button>
                        </Link>
                    </li>
                </ul>
            </nav>

            <!-- Footer Logout -->
            <div class="p-3">
                <div
                    :class="['flex justify-between items-center', { 'p-2 border border-neutral-200 rounded-lg dark:border-neutral-700': isExpanded }]">
                    <div v-if="isExpanded">
                        <p class="font-semibold tracking-tight -mb-1">{{ user.name }}</p>
                        <p class="text-sm text-neutral-400 first-letter:uppercase">{{ user.role }}</p>
                    </div>
                    <Button variant="outline" :size="[isExpanded ? 'icon' : 'default']" as-child>
                        <Link :href="route('logout')" method="post" as="button">
                        <LogOutIcon size="18" />
                        </Link>
                    </Button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow dark:bg-neutral-800">
                <div class="p-3 flex justify-between items-center">
                    <span :class="['flex items-center', isTablet ? 'gap-3' : 'gap-2']">
                        <!-- Sheet Component -->
                        <Sheet v-if="isTablet">
                            <SheetTrigger as-child>
                                <Button size="icon" variant="outline">
                                    <MenuIcon size="18" />
                                </Button>
                            </SheetTrigger>
                            <SheetContent side="left" class="p-3 flex flex-col h-full">
                                <SheetHeader class="my-5">
                                    <SheetTitle class="font-semibold tracking-tight">Qolbu App</SheetTitle>
                                </SheetHeader>
                                <ul class="space-y-2">
                                    <li v-for="item in filteredMenuItems" :key="item.name">
                                        <Link :href="item.href">
                                        <Button variant="ghost"
                                            class="w-full justify-start hover:bg-neutral-200 transition-colors duration-200 dark:hover:bg-neutral-700">
                                            <component :is="item.icon"
                                                :class="['h-5 w-5', isExpanded ? 'mr-4' : 'mx-auto']" />
                                            <span v-if="isExpanded" class="truncate">{{ item.name }}</span>
                                        </Button>
                                        </Link>
                                    </li>
                                </ul>
                                <SheetFooter class="mt-auto">
                                    <div
                                        :class="['flex justify-between items-center', isExpanded ? 'p-2 border border-neutral-200 rounded-lg dark:border-neutral-700' : '']">
                                        <div v-if="isExpanded">
                                            <p class="font-semibold tracking-tight -mb-1">{{ user.name }}</p>
                                            <p class="text-sm text-neutral-400 first-letter:uppercase">{{ user.role }}
                                            </p>
                                        </div>
                                        <Button variant="outline" :size="[isExpanded ? 'icon' : 'default']" as-child>
                                            <Link :href="route('logout')" method="post" as="button">
                                            <LogOutIcon size="18" />
                                            </Link>
                                        </Button>
                                    </div>
                                </SheetFooter>
                            </SheetContent>
                        </Sheet>

                        <Button v-if="!isTablet" variant="outline" size="icon" @click="toggleSidebar">
                            <SidebarIcon v-if="isExpanded" size="18" />
                            <ChevronRightIcon v-else size="18" />
                        </Button>
                        <slot name="header">
                            <h1 class="text-xl font-semibold tracking-tight">Header</h1>
                        </slot>
                    </span>

                    <Button variant="outline" @click="toggleDarkMode" size="icon">
                        <component :is="isDarkMode ? MoonIcon : SunIcon" class="w-5 h-5" />
                    </Button>
                </div>
            </header>
            <main>
                <div :class="`${containerClass ?? 'max-w-7xl'} mx-auto py-4 px-2 sm:px-3 lg:px-4`">
                    <slot />
                </div>
            </main>
        </div>
    </div>

    <Toaster position="bottom-right" :theme="isDarkMode ? 'dark' : 'light'" richColors />
</template>
