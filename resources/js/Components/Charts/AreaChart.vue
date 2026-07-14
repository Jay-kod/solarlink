<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import apexchart from 'vue3-apexcharts'

const props = defineProps<{
    series: { name: string; data: number[] }[];
    categories: string[];
    colors?: string[];
}>()

const chartOptions = ref({
    chart: {
        type: 'area',
        toolbar: { show: false },
        zoom: { enabled: false },
        background: 'transparent',
    },
    colors: props.colors || ['#6C3BFF', '#22C55E'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [0, 100]
        }
    },
    grid: {
        borderColor: 'rgba(108, 59, 255, 0.1)',
        strokeDashArray: 4,
        xaxis: { lines: { show: false } },
        yaxis: { lines: { show: true } },
    },
    theme: {
        mode: 'light' // dynamically toggled
    },
    xaxis: {
        categories: props.categories,
        labels: {
            style: {
                colors: '#8B8BA7',
                fontSize: '10px',
                fontWeight: 600,
                fontFamily: 'Outfit, sans-serif'
            }
        },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: {
        labels: {
            style: {
                colors: '#8B8BA7',
                fontSize: '10px',
                fontWeight: 600,
                fontFamily: 'Outfit, sans-serif'
            }
        }
    },
    tooltip: {
        theme: 'dark',
        x: { show: true },
        marker: { show: true }
    }
})

// Synchronize dark theme adjustments
onMounted(() => {
    const isDarkModeActive = document.documentElement.classList.contains('dark')
    chartOptions.value.theme.mode = isDarkModeActive ? 'dark' : 'light'
    chartOptions.value.tooltip.theme = isDarkModeActive ? 'dark' : 'light'
})
</script>

<template>
    <div class="w-full overflow-hidden">
        <apexchart 
            height="320" 
            :options="chartOptions" 
            :series="series"
        ></apexchart>
    </div>
</template>
