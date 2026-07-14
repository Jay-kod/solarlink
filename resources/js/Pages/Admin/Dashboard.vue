<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import AnalyticsCard from '@/Components/Cards/AnalyticsCard.vue'
import AreaChart from '@/Components/Charts/AreaChart.vue'
import DonutChart from '@/Components/Charts/DonutChart.vue'
import { ShieldAlert, Users, DollarSign, TrendingUp, Sparkles } from 'lucide-vue-next'

const platformGrowth = [
    { name: 'Platform Transactions ($)', data: [12000, 18000, 24000, 16000, 29000, 34000, 42000] }
]
const chartCategories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

const platformLoads = [40, 35, 15, 10]
const platformLabels = ['Homeowners', 'Field Technicians', 'Wholesale Vendors', 'Administrators']
</script>

<template>
    <Head title="SolarLink — Admin Control Center" />

    <DashboardLayout role="admin" title="Operations Control">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Global Control Node</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Admin Headquarters</h2>
                <p class="text-xs text-slate-450 mt-0.5">Audit global platform transactions, verify contractor certifications, moderate wholesale listings, and edit blog posts.</p>
            </div>

            <!-- KPI rows -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <AnalyticsCard 
                    title="Active Platform Users"
                    value="12,840"
                    :icon="Users"
                    trend="+12% this month"
                    trendType="up"
                />

                <AnalyticsCard 
                    title="Total Transactions Value"
                    value="$184,200.00"
                    :icon="DollarSign"
                    trend="+22.4%"
                    trendType="up"
                />

                <AnalyticsCard 
                    title="Pending Verifications"
                    value="4 Tickets"
                    :icon="ShieldAlert"
                    trend="Requires review"
                    trendType="neutral"
                />

                <AnalyticsCard 
                    title="Platform Health Index"
                    value="99.98%"
                    :icon="TrendingUp"
                    trend="Optimal status"
                    trendType="neutral"
                />
            </div>

            <!-- Charts grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Area chart -->
                <div class="lg:col-span-2 glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Gross Transaction Value</h3>
                        <p class="text-xs text-slate-450">Track weekly financial ledger growth volume</p>
                    </div>
                    <AreaChart 
                        :series="platformGrowth" 
                        :categories="chartCategories"
                        :colors="['#581C87']"
                    />
                </div>

                <!-- Donut chart -->
                <div class="lg:col-span-1 glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Platform Allocations</h3>
                        <p class="text-xs text-slate-450">Distribution of active system roles</p>
                    </div>
                    <DonutChart 
                        :series="platformLoads" 
                        :labels="platformLabels"
                        :colors="['#581C87', '#6C3BFF', '#F59E0B', '#EF4444']"
                    />
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>
