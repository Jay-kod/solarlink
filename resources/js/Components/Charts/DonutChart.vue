<script setup lang="ts">
import { ref, onMounted } from 'vue'
import apexchart from 'vue3-apexcharts'

const props = defineProps<{
    series: number[];
    labels: string[];
    colors?: string[];
}>()

const chartOptions = ref({
    chart: {
        type: 'donut',
        background: 'transparent'
    },
    colors: props.colors || ['#6C3BFF', '#22C55E', '#F59E0B', '#EF4444'],
    labels: props.labels,
    dataLabels: { enabled: false },
    stroke: { show: false },
    legend: {
        position: 'bottom',
        fontSize: '11px',
        fontWeight: 600,
        fontFamily: 'Outfit, sans-serif',
        labels: {
            colors: '#8B8BA7'
        },
        markers: {
            width: 10,
            height: 10,
            radius: 4
        }
    },
    theme: {
        mode: 'light'
    },
    plotOptions: {
        pie: {
            donut: {
                size: '72%',
                labels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '12px',
                        fontFamily: 'Outfit, sans-serif',
                        fontWeight: 600,
                        color: '#8B8BA7'
                    },
                    value: {
                        show: true,
                        fontSize: '20px',
                        fontFamily: 'Outfit, sans-serif',
                        fontWeight: 800,
                        color: '#6C3BFF',
                        formatter: (val: string) => `${val}%`
                    },
                    total: {
                        show: true,
                        label: 'Total Output',
                        fontSize: '11px',
                        fontWeight: 650,
                        color: '#8B8BA7',
                        formatter: () => '100%'
                    }
                }
            }
        }
    },
    tooltip: {
        theme: 'dark'
    }
})

onMounted(() => {
    const isDarkModeActive = document.documentElement.classList.contains('dark')
    chartOptions.value.theme.mode = isDarkModeActive ? 'dark' : 'light'
})
</script>

<template>
    <div class="w-full flex items-center justify-center overflow-hidden">
        <apexchart 
            width="320" 
            height="320"
            :options="chartOptions" 
            :series="series"
        ></apexchart>
    </div>
</template>
