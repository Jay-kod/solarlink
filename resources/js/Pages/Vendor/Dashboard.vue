<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import AnalyticsCard from '@/Components/Cards/AnalyticsCard.vue'
import AreaChart from '@/Components/Charts/AreaChart.vue'
import DonutChart from '@/Components/Charts/DonutChart.vue'
import { Store, DollarSign, ShoppingBag, FolderKanban, Sparkles } from 'lucide-vue-next'

const salesHistory = [
    { name: 'Direct Wholesale Volume ($)', data: [1800, 2400, 3100, 1500, 2900, 3800, 3200] }
]
const chartCategories = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']

const inventoryAllocation = [45, 35, 20]
const inventoryLabels = ['Solar Panels', 'Hybrid Battery Packs', 'Inverter Boards']
</script>

<template>
    <Head title="SolarLink — Supplier Dashboard" />

    <DashboardLayout role="vendor" title="Wholesale Operations">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Direct Factory Supply Node</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Vendor Distribution Central</h2>
                <p class="text-xs text-slate-450 mt-0.5">Control wholesale inventory channels, list direct accessories, ship buyer orders, and audit billing metrics.</p>
            </div>

            <!-- KPI indicators -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <AnalyticsCard 
                    title="Active Products Listed"
                    value="18 Items"
                    :icon="ShoppingBag"
                    trend="+2 today"
                    trendType="up"
                />
                
                <AnalyticsCard 
                    title="Weekly wholesale Sales"
                    value="$18,720.00"
                    :icon="DollarSign"
                    trend="+14.2%"
                    trendType="up"
                />

                <AnalyticsCard 
                    title="Orders to Dispatch"
                    value="4 Pending"
                    :icon="FolderKanban"
                    trend="3 SLA commitments"
                    trendType="neutral"
                />

                <AnalyticsCard 
                    title="Supplier Rank"
                    value="OEM Tier-1"
                    :icon="Store"
                    trend="Direct contract"
                    trendType="neutral"
                />
            </div>

            <!-- Charts grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sales volume area -->
                <div class="lg:col-span-2 glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Wholesale Volume</h3>
                        <p class="text-xs text-slate-450">Track gross supplier revenues across grid hubs</p>
                    </div>
                    <AreaChart 
                        :series="salesHistory" 
                        :categories="chartCategories"
                        :colors="['#4F46E5']"
                    />
                </div>

                <!-- Product allocations donut -->
                <div class="lg:col-span-1 glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Category Allocation</h3>
                        <p class="text-xs text-slate-450">Inventory distribution listed in marketplace</p>
                    </div>
                    <DonutChart 
                        :series="inventoryAllocation" 
                        :labels="inventoryLabels"
                        :colors="['#4F46E5', '#6C3BFF', '#F59E0B']"
                    />
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>
