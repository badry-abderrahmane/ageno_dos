<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import StatCard from '@/components/StatCard.vue';
import LineChart from '@/components/LineChart.vue';
import PieChart from '@/components/PieChart.vue';
import { Briefcase, CircleDollarSign, File } from 'lucide-vue-next';
import { BreadcrumbItem, DashboardData } from '@/types';
import { dashboard } from '@/routes';

const props = defineProps<DashboardData>();

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
                <StatCard class="delay-1000" title="Client(s) ce mois" :stat="stats.clientsThisMonth"
                    :icon="Briefcase" />
                <StatCard class="delay-1300" title="Facture(s) ce mois" :stat="stats.invoicesThisMonth" :icon="File" />
                <StatCard class="delay-1600" title="Revenue du mois" :stat="stats.totalRevenueMonth"
                    :icon="CircleDollarSign" />
                <StatCard class="delay-1900" title="Revenue Année" :stat="stats.totalRevenueYear"
                    :icon="CircleDollarSign" />
                <StatCard class="delay-2200" title="Revenue Historique" :stat="stats.totalRevenue"
                    :icon="CircleDollarSign" />
                <StatCard class="delay-2500" title="Factures" :stat="stats.totalInvoices" :icon="File" />
                <StatCard class="delay-2700" title="Clients" :stat="stats.totalClients" :icon="Briefcase" />
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