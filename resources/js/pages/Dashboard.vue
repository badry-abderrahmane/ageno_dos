<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import StatCard from '@/components/StatCard.vue';
import LineChart from '@/components/LineChart.vue';
import PieChart from '@/components/PieChart.vue';
import { Briefcase, CircleDollarSign, File } from 'lucide-vue-next';
import { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

// Define the full structure of the Inertia props
interface Props {
    stats: {
        totalInvoices: number;
        totalRevenue: string; // Formatted string from Laravel
        totalRevenueMonth: string;
        totalRevenueYear: string;
        totalClients: number;
        clientsThisMonth: number;
    };
    monthlyRevenueData: {
        categories: string[];
        series: { name: string; data: number[] }[];
    };
    clientsBySectorData: {
        series: number[];
        labels: string[];
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tableau de bord',
        href: dashboard().url,
    },
];
</script>

<template>
    <AppLayout title="Dashboard" :breadcrumbs="breadcrumbs">

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <StatCard title="Revenue du mois" :value="`${props.stats.totalRevenueMonth}`"
                    :icon="CircleDollarSign" />
                <StatCard title="Revenue Année" :value="`${props.stats.totalRevenueYear}`" :icon="CircleDollarSign" />
                <StatCard title="Revenue Historique" :value="`${props.stats.totalRevenue}`" :icon="CircleDollarSign" />
                <StatCard title="Total Invoices" :value="props.stats.totalInvoices" :icon="File" />
                <StatCard title="Total Clients" :value="props.stats.totalClients" :icon="Briefcase" />
                <StatCard title="Clients This Month" :value="props.stats.clientsThisMonth" :icon="Briefcase" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class=" p-6 shadow-xl rounded-xl border ">
                    <h3 class="text-xl font-semibold mb-4 ">Revenue par mois</h3>
                    <LineChart :data="props.monthlyRevenueData" />
                </div>

                <div class=" p-6 shadow-xl rounded-xl border ">
                    <h3 class="text-xl font-semibold mb-4 ">Client par Secteur</h3>
                    <PieChart :data="props.clientsBySectorData" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>