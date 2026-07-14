<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import AnalyticsCard from '@/Components/Cards/AnalyticsCard.vue'
import AreaChart from '@/Components/Charts/AreaChart.vue'
import DonutChart from '@/Components/Charts/DonutChart.vue'
import { Wrench, DollarSign, Star, Calendar, ShieldCheck, Zap } from 'lucide-vue-next'

const payoutHistory = [
    { name: 'Weekly Earnings ($)', data: [450, 680, 850, 520, 950, 1100, 800] }
]
const chartCategories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

const jobsBreakdown = [70, 20, 10]
const jobsLabels = ['Completed Audits', 'Emergency Panel Cleans', 'Inverter Repairs']
</script>

<template>
    <Head title="SolarLink — Technician Hub" />

    <DashboardLayout role="technician" title="Field Dispatch Overview">
        <div class="flex flex-col gap-8 text-left">
            
            <!-- Quick GPS advisory -->
            <div class="glass-card p-4 border-l-4 border-solar-primary bg-solar-primary/5 flex items-center justify-between text-left">
                <div class="flex items-center gap-3">
                    <Zap class="h-5 w-5 text-solar-primary" />
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-white">Active Grid Positioning: ONLINE</h4>
                        <p class="text-[10px] text-slate-500">Your simulated GPS coordinates are currently streaming. Nearby residential array owners can track your route and dispatch you.</p>
                    </div>
                </div>
                <Link 
                    href="/technician/jobs"
                    class="px-4 py-2 rounded-xl bg-solar-primary text-white font-bold text-[10px] uppercase tracking-wider transition-all"
                >
                    View Active Queue
                </Link>
            </div>

            <!-- KPI Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <AnalyticsCard 
                    title="Active Work Orders"
                    value="3 Scheduled"
                    :icon="Calendar"
                    trend="+1 today"
                    trendType="up"
                    desc="Next arrival: 2:00 PM"
                />

                <AnalyticsCard 
                    title="Earnings This Week"
                    value="$1,240.00"
                    :icon="DollarSign"
                    trend="+18.4%"
                    trendType="up"
                    desc="Clears on Thursday"
                />

                <AnalyticsCard 
                    title="Dispatcher Rating"
                    value="4.96 Stars"
                    :icon="Star"
                    trend="52 ratings"
                    trendType="neutral"
                    desc="NABCEP Certified level"
                />

                <AnalyticsCard 
                    title="Grid Tasks Completed"
                    value="142 Jobs"
                    :icon="Wrench"
                    trend="+8 this month"
                    trendType="up"
                    desc="Lifetime service logs"
                />
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Area chart -->
                <div class="lg:col-span-2 glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-850 dark:text-white">Earnings History</h3>
                        <p class="text-xs text-slate-450">Track weekly hourly dispatch payout credits</p>
                    </div>
                    <AreaChart 
                        :series="payoutHistory" 
                        :categories="chartCategories"
                        :colors="['#10B981']"
                    />
                </div>

                <!-- Donut chart -->
                <div class="lg:col-span-1 glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-850 dark:text-white">Service Allocation</h3>
                        <p class="text-xs text-slate-450">Distribution of completed grid tickets</p>
                    </div>
                    <DonutChart 
                        :series="jobsBreakdown" 
                        :labels="jobsLabels"
                        :colors="['#10B981', '#6C3BFF', '#F59E0B']"
                    />
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>
