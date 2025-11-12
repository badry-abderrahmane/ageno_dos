<script setup lang="ts">
import { defineProps, computed } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import type { ApexOptions } from 'apexcharts'; // Import the type for better options typing

// Define the expected structure of the data prop
interface ChartData {
  categories: string[];
  series: { name: string; data: number[] }[];
}

interface Props {
  data: ChartData;
}

const props = defineProps<Props>();

// Computed property for ApexCharts options, using ApexOptions type
const chartOptions = computed<ApexOptions>(() => ({
  chart: {
    type: 'line',
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  xaxis: {
    categories: props.data.categories,
    title: { text: 'Month' }
  },
  yaxis: {
    title: { text: 'Revenue ($)' },
    labels: {
      formatter: (value: number) => `$${value.toFixed(0)}`,
    }
  },
  stroke: {
    curve: 'smooth',
    width: 3,
  },
  colors: ['#4F46E5'], // Indigo color
  dataLabels: {
    enabled: false
  }
}));
</script>

<template>
  <VueApexCharts type="line" :options="chartOptions" :series="data.series" height="350" />
</template>