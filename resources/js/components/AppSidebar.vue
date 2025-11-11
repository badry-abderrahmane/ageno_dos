<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import invoice from '@/routes/invoice/index'
import client from '@/routes/client/index'
import product from '@/routes/product/index'
import supplier from '@/routes/supplier/index'
import productCategory from '@/routes/productCategory/index'
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Package, Briefcase, LayoutGrid, FileText, Factory, Boxes } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Tableau de bord',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clients',
        href: client.index(),
        icon: Briefcase,
    },
    {
        title: 'Factures',
        href: invoice.index(),
        icon: FileText,
    },
    {
        title: 'Fournisseurs',
        href: supplier.index(),
        icon: Factory
    },
    {
        title: 'Produits',
        href: product.index(),
        icon: Package
    },
    {
        title: 'Catégories',
        href: productCategory.index(),
        icon: Boxes
    }
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
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

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
