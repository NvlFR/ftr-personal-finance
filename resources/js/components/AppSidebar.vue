<script setup lang="ts">
// import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItemGroup } from '@/types';
import { Link } from '@inertiajs/vue3';

// Import semua ikon yang dibutuhkan dari lucide-vue-next
import {
    LayoutDashboard,
    ArrowRightLeft,
    Tags,
    Wallet,
    PiggyBank,
    BarChart3,
    Target,
    Scale,
    RefreshCw,
    Menu,
    ClipboardCheck,
    Shapes,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

// Menggunakan struktur data yang dikelompokkan untuk navigasi utama
const navigationGroups: NavItemGroup[] = [
    {
        title: 'Menu Utama',
        icon: Menu,
        items: [
            { title: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
            { title: 'Transaksi', href: '/transactions', icon: ArrowRightLeft },
            { title: 'Kategori', href: '/categories', icon: Tags },
            { title: 'Akun & Dompet', href: '/accounts', icon: Wallet },
        ]
    },
    {
        title: 'Perencanaan',
        icon: ClipboardCheck,
        items: [
            { title: 'Budgeting', href: '/budgets', icon: PiggyBank },
            { title: 'Laporan', href: '/reports', icon: BarChart3 },
            { title: 'Financial Goals', href: '/financial-goals', icon: Target },
        ]
    },
    {
        title: 'Lainnya',
        icon: Shapes,
        items:
        [
            { title: 'Utang & Piutang', href: '/debts', icon: Scale },
            { title: 'Transaksi Berulang', href: '/recurring-transactions', icon: RefreshCw },
        ]
    }
];

// Navigasi untuk footer tetap sama
// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#vue',
//         icon: BookOpen,
//     },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="flex flex-col gap-y-2">
            <!-- Loop melalui setiap grup navigasi -->
            <div v-for="(group, index) in navigationGroups" :key="index">
                <!-- Tampilkan judul grup jika ada, sekarang dengan ikon -->
                <!-- <h4 v-if="group.title" class="px-4 py-2 text-xs font-semibold text-muted-foreground flex items-center gap-x-2">
                    <component :is="group.icon" class="h-4 w-4" />
                    <span>{{ group.title }}</span>
                </h4> -->
                <!-- Render komponen NavMain untuk item di grup tersebut -->
                <NavMain :items="group.items" />
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
