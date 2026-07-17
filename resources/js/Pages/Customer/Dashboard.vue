<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AnalyticsCard from '@/Components/Cards/AnalyticsCard.vue'
import AreaChart from '@/Components/Charts/AreaChart.vue'
import DonutChart from '@/Components/Charts/DonutChart.vue'
import { technicianDirectory } from '@/data/technicians'
import { 
    Cpu, Zap, Battery, DollarSign, Leaf, Wrench, Calendar, 
    ArrowRight, Bell, Sparkles, AlertCircle 
} from 'lucide-vue-next'

const isLoading = ref(true)
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
    }, 800)
})

// Mock chart data
const energyGeneration = [
    { name: 'Grid Consumption (kWh)', data: [15, 12, 18, 14, 19, 11, 13] },
    { name: 'Solar Generation (kWh)', data: [22, 28, 32, 18, 26, 34, 30] }
]
const chartCategories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

const batteryAllocation = [60, 30, 10]
const batteryLabels = ['Direct Home Load', 'Battery Buffer Storage', 'Grid Export Netting']
</script>

<template>
    <Head title="SolarLink — Telemetry Dashboard" />

    <CustomerLayout title="Telemetry Diagnostics">
        <div class="flex flex-col gap-8">
            
            <!-- SKELETON LOADING STATE -->
            <div v-if="isLoading" class="flex flex-col gap-8 animate-pulse">
                <!-- Skeleton KPI Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="i in 4" :key="i" class="glass-card p-6 flex flex-col gap-4 bg-white dark:bg-solar-bg-dark/40 h-28 border border-slate-100 dark:border-white/5">
                        <div class="flex justify-between items-center">
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-2/3"></div>
                            <div class="h-8 w-8 bg-slate-250 dark:bg-slate-800 rounded-lg"></div>
                        </div>
                        <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                    </div>
                </div>

                <!-- Skeleton Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 glass-card p-6 flex flex-col gap-4 bg-white dark:bg-solar-bg-dark/40 h-80 border border-slate-100 dark:border-white/5">
                        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                        <div class="h-2.5 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                        <div class="flex-grow bg-slate-100 dark:bg-slate-800/30 rounded-xl mt-4"></div>
                    </div>
                    <div class="lg:col-span-1 glass-card p-6 flex flex-col gap-4 bg-white dark:bg-solar-bg-dark/40 h-80 border border-slate-100 dark:border-white/5">
                        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                        <div class="h-2.5 bg-slate-200 dark:bg-slate-800 rounded w-2/3"></div>
                        <div class="flex-grow bg-slate-100 dark:bg-slate-800/30 rounded-full w-48 h-48 mx-auto mt-4"></div>
                    </div>
                </div>

                <!-- Skeleton Techs & Reminders Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 glass-card p-6 flex flex-col gap-4 bg-white dark:bg-solar-bg-dark/40 h-48 border border-slate-100 dark:border-white/5">
                        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                            <div v-for="i in 2" :key="i" class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800/50 h-20 flex items-center gap-3">
                                <div class="h-10 w-10 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                                <div class="flex-grow space-y-2">
                                    <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
                                    <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1 glass-card p-6 flex flex-col gap-4 bg-white dark:bg-solar-bg-dark/40 h-48 border border-slate-100 dark:border-white/5">
                        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                        <div class="space-y-3 mt-2">
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-5/6"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REAL CONTENT STATE -->
            <div v-else class="flex flex-col gap-8">
                <!-- Quick Alerts Header -->
                <div class="glass-card p-4 border-l-4 border-solar-warning bg-solar-warning/5 flex items-center justify-between text-left">
                    <div class="flex items-center gap-3">
                        <AlertCircle class="h-5 w-5 text-solar-warning" />
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-white">Seasonal Dust Advisory</h4>
                            <p class="text-[10px] text-slate-500">Panel dust deposits have increased by 8%. Consider booking a local panel wash service to restore peak wattage.</p>
                        </div>
                    </div>
                    <Link 
                        href="/user/bookings"
                        class="px-4 py-2 rounded-xl bg-solar-warning/10 text-solar-warning hover:bg-solar-warning hover:text-white font-bold text-[10px] uppercase tracking-wider transition-all"
                    >
                        Book Wash
                    </Link>
                </div>

                <!-- KPI Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <AnalyticsCard 
                        title="Live Array Output"
                        value="6.84 kW"
                        :icon="Zap"
                        trend="+14.2%"
                        trendType="up"
                        desc="System health: optimal"
                    />

                    <AnalyticsCard 
                        title="Home Battery Charge"
                        value="92.4%"
                        :icon="Battery"
                        trend="+2.1%"
                        trendType="up"
                        desc="LiFePO4 status: floating"
                    />

                    <AnalyticsCard 
                        title="Month Net Savings"
                        value="$482.50"
                        :icon="DollarSign"
                        trend="+8.4%"
                        trendType="up"
                        desc="Estimated utility rebate: $84"
                    />

                    <AnalyticsCard 
                        title="Total Carbon Mitigation"
                        value="14.2 Tons"
                        :icon="Leaf"
                        trend="Offset 42 trees"
                        trendType="neutral"
                        desc="Platform lifetime offset"
                    />
                </div>

                <!-- Charts & Telemetry Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Area generation curve -->
                    <div class="lg:col-span-2 glass-card p-6 text-left flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Active Grid Telemetry</h3>
                                <p class="text-xs text-slate-400">Weekly solar generation compared against local grid consumption patterns</p>
                            </div>
                        </div>
                        <AreaChart 
                            :series="energyGeneration" 
                            :categories="chartCategories"
                        />
                    </div>

                    <!-- Donut battery breakdown -->
                    <div class="lg:col-span-1 glass-card p-6 text-left flex flex-col gap-4">
                        <div>
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Battery Allocation</h3>
                            <p class="text-xs text-slate-400">Distribution of today's solar load offsets</p>
                        </div>
                        <DonutChart 
                            :series="batteryAllocation" 
                            :labels="batteryLabels"
                        />
                    </div>
                </div>

                <!-- Nearby Technicians & Reminders -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Nearby Techs list -->
                    <div class="lg:col-span-2 glass-card p-6 text-left flex flex-col gap-5">
                        <div class="flex items-center justify-between border-b border-solar-primary/10 dark:border-white/5 pb-3">
                            <div>
                                <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Nearby Local Technicians</h3>
                                <p class="text-xs text-slate-400">Certified technicians currently active in your grid sector</p>
                            </div>
                            <Link 
                                href="/user/map" 
                                class="text-xs font-bold text-solar-primary hover:underline flex items-center gap-1"
                            >
                                <span>Live Dispatch Map</span>
                                <ArrowRight class="h-3 w-3" />
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div 
                                v-for="tech in technicianDirectory.slice(0, 2)" 
                                :key="tech.id"
                                class="p-4 rounded-2xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-100 dark:border-white/5 flex flex-col gap-3"
                            >
                                <div class="flex items-center gap-3">
                                    <img :src="tech.avatar" :alt="tech.name" class="h-10 w-10 rounded-xl object-cover" />
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-800 dark:text-white">{{ tech.name }}</h4>
                                        <p class="text-[10px] text-slate-400">{{ tech.distance }} away &bull; {{ tech.experience }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    <span 
                                        v-for="skill in tech.skills.slice(0, 2)" 
                                        :key="skill"
                                        class="text-[8px] font-bold text-solar-primary dark:text-solar-primary-accent bg-solar-primary-light dark:bg-solar-primary-dark px-2 py-0.5 rounded-full uppercase tracking-wider"
                                    >
                                        {{ skill }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Reminders -->
                    <div class="lg:col-span-1 glass-card p-6 text-left flex flex-col gap-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-solar-primary/10">
                            <Bell class="h-5 w-5 text-solar-primary" />
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Array Reminders</h3>
                        </div>

                        <div class="flex flex-col gap-3">
                            <div class="flex items-start gap-2.5 text-xs">
                                <span class="h-2 w-2 rounded-full bg-solar-primary mt-1.5 shrink-0"></span>
                                <div>
                                    <h5 class="font-bold text-slate-800 dark:text-slate-100">Annual System Audit</h5>
                                    <p class="text-[10px] text-slate-450 leading-relaxed">Booked for June 2nd with Marcus Vance.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 text-xs opacity-65">
                                <span class="h-2 w-2 rounded-full bg-solar-success mt-1.5 shrink-0"></span>
                                <div>
                                    <h5 class="font-bold text-slate-800 dark:text-slate-100">LFP Battery Cycle Audit</h5>
                                    <p class="text-[10px] text-slate-450 leading-relaxed">Auto check complete: 98% efficiency buffer.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
