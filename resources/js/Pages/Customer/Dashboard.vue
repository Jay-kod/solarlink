<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AnalyticsCard from '@/Components/Cards/AnalyticsCard.vue'
import AreaChart from '@/Components/Charts/AreaChart.vue'
import DonutChart from '@/Components/Charts/DonutChart.vue'
import { technicianDirectory } from '@/data/technicians'
import {
    Zap, Battery, DollarSign, Leaf, ArrowRight,
    Bell, AlertCircle
} from 'lucide-vue-next'

const chartCategories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

const stats = [
    { title: 'Live Array Output', value: '6.84 kW', icon: Zap, trend: '+14.2%', trendType: 'up', desc: 'System health: optimal' },
    { title: 'Home Battery Charge', value: '92.4%', icon: Battery, trend: '+2.1%', trendType: 'up', desc: 'LiFePO4 status: floating' },
    { title: 'Month Net Savings', value: '$482.50', icon: DollarSign, trend: '+8.4%', trendType: 'up', desc: 'Estimated utility rebate: $84' },
    { title: 'Total Carbon Mitigation', value: '14.2 Tons', icon: Leaf, trend: 'Offset 42 trees', trendType: 'neutral', desc: 'Platform lifetime offset' },
]

const energyGeneration = [
    { name: 'Grid Consumption (kWh)', data: [15, 12, 18, 14, 19, 11, 13] },
    { name: 'Solar Generation (kWh)', data: [22, 28, 32, 18, 26, 34, 30] },
]

const batteryAllocation = [60, 30, 10]
const batteryLabels = ['Direct Home Load', 'Battery Buffer Storage', 'Grid Export Netting']

const technicianHighlights = technicianDirectory.slice(0, 2)

const reminders = [
    {
        title: 'Annual System Audit',
        detail: 'Booked for June 2nd with Marcus Vance.',
        tone: 'bg-solar-primary',
    },
    {
        title: 'LFP Battery Cycle Audit',
        detail: 'Auto check complete: 98% efficiency buffer.',
        tone: 'bg-solar-success',
    },
]
</script>

<template>
    <Head title="SolarLink — Telemetry Dashboard" />

    <CustomerLayout title="Telemetry Diagnostics">
        <div class="flex flex-col gap-8">
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

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <AnalyticsCard
                    v-for="card in stats"
                    :key="card.title"
                    :title="card.title"
                    :value="card.value"
                    :icon="card.icon"
                    :trend="card.trend"
                    :trendType="card.trendType"
                    :desc="card.desc"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 glass-card p-6 text-left flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Active Grid Telemetry</h3>
                        <p class="text-xs text-slate-400">Weekly solar generation compared against local grid consumption patterns</p>
                    </div>
                    <AreaChart :series="energyGeneration" :categories="chartCategories" />
                </div>

                <div class="lg:col-span-1 glass-card p-6 text-left flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Battery Allocation</h3>
                        <p class="text-xs text-slate-400">Distribution of today's solar load offsets</p>
                    </div>
                    <DonutChart :series="batteryAllocation" :labels="batteryLabels" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 glass-card p-6 text-left flex flex-col gap-5">
                    <div class="flex items-center justify-between border-b border-solar-primary/10 dark:border-white/5 pb-3">
                        <div>
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Nearby Local Technicians</h3>
                            <p class="text-xs text-slate-400">Certified technicians currently active in your grid sector</p>
                        </div>
                        <Link href="/user/map" class="text-xs font-bold text-solar-primary hover:underline flex items-center gap-1">
                            <span>Live Dispatch Map</span>
                            <ArrowRight class="h-3 w-3" />
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="tech in technicianHighlights"
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

                <div class="lg:col-span-1 glass-card p-6 text-left flex flex-col gap-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-solar-primary/10">
                        <Bell class="h-5 w-5 text-solar-primary" />
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Array Reminders</h3>
                    </div>

                    <div class="flex flex-col gap-3">
                        <div
                            v-for="item in reminders"
                            :key="item.title"
                            class="flex items-start gap-2.5 text-xs"
                            :class="item.title.includes('Battery') ? 'opacity-65' : ''"
                        >
                            <span class="h-2 w-2 rounded-full mt-1.5 shrink-0" :class="item.tone"></span>
                            <div>
                                <h5 class="font-bold text-slate-800 dark:text-slate-100">{{ item.title }}</h5>
                                <p class="text-[10px] text-slate-450 leading-relaxed">{{ item.detail }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
