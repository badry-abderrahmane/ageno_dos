<script setup lang="ts">
import { defineProps, computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import type { ApexOptions } from 'apexcharts';

// Define the expected structure of the data prop
interface ChartData {
  series: number[]; // The counts for each sector
  labels: string[]; // The names of the sectors
}

interface Props {
  data: ChartData;
}

const props = defineProps<Props>();

// Computed property for ApexCharts options, using ApexOptions type
const chartOptions = computed<ApexOptions>(() => ({
  chart: {
    type: 'donut',
  },
  labels: props.data.labels,
  responsive: [{
    breakpoint: 480,
    options: {
      chart: { width: 300 },
      legend: { position: 'bottom' }
    }
  }],
  legend: {
    position: 'right', // Place legend on the side
    offsetY: 0,
    height: 130,
  },
  // Tailwind colors for a nice look
  colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#8B5CF6'],
}));
</script>

<template>
  <VueApexCharts type="donut" :options="chartOptions" :series="data.series" height="350" />
</template>