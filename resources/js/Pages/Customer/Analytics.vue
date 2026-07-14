<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import AnalyticsCard from '@/Components/Cards/AnalyticsCard.vue'
import AreaChart from '@/Components/Charts/AreaChart.vue'
import DonutChart from '@/Components/Charts/DonutChart.vue'
import { Zap, Battery, DollarSign, Leaf, TrendingUp, Sparkles, Calendar } from 'lucide-vue-next'

const monthlyGeneration = [
    { name: 'Solar Generation (kWh)', data: [220, 280, 320, 180, 260, 340, 300, 290, 310, 350, 380, 410] },
    { name: 'Grid Import (kWh)', data: [150, 120, 180, 140, 190, 110, 130, 160, 140, 100, 90, 80] }
]
const monthCategories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

const savingsBreakdown = [
    { name: 'Monthly Savings ($)', data: [380, 420, 510, 340, 480, 520, 490, 540, 580, 620, 660, 710] }
]

const energySplits = [55, 25, 20]
const energyLabels = ['Self-Consumption', 'Grid Export Credits', 'Battery Storage']

const carbonSeries = [62, 24, 14]
const carbonLabels = ['Direct CO₂ Avoided', 'Equivalent Tree Offsets', 'Grid Decarbonization']
</script>

<template>
    <Head title="SolarLink — Deep Analytics" />

    <CustomerLayout title="Performance Analytics">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Deep Telemetry Analytics</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Annual System Performance</h2>
                <p class="text-xs text-slate-450 mt-0.5">Review 12-month generation curves, savings trajectories, carbon impact, and energy allocation distributions.</p>
            </div>

            <!-- KPI Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <AnalyticsCard 
                    title="Annual Generation"
                    value="3,640 kWh"
                    :icon="Zap"
                    trend="+18.2% YoY"
                    trendType="up"
                    desc="Peak: July at 410 kWh"
                />
                <AnalyticsCard 
                    title="Annual Net Savings"
                    value="$6,250.00"
                    :icon="DollarSign"
                    trend="+22.4% YoY"
                    trendType="up"
                    desc="After grid credits applied"
                />
                <AnalyticsCard 
                    title="System Efficiency"
                    value="94.2%"
                    :icon="TrendingUp"
                    trend="+1.8% vs rated"
                    trendType="up"
                    desc="Above manufacturer baseline"
                />
                <AnalyticsCard 
                    title="Carbon Offset"
                    value="14.2 Tons"
                    :icon="Leaf"
                    trend="Equiv. 42 mature trees"
                    trendType="neutral"
                    desc="Lifetime clean generation"
                />
            </div>

            <!-- Generation & Savings Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">12-Month Generation vs Grid Import</h3>
                        <p class="text-xs text-slate-400">Solar generation output compared to residual grid dependency</p>
                    </div>
                    <AreaChart 
                        :series="monthlyGeneration" 
                        :categories="monthCategories"
                    />
                </div>

                <div class="glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Monthly Savings Trajectory</h3>
                        <p class="text-xs text-slate-400">Net dollar savings after grid credits and battery netting</p>
                    </div>
                    <AreaChart 
                        :series="savingsBreakdown" 
                        :categories="monthCategories"
                        :colors="['#22C55E']"
                    />
                </div>
            </div>

            <!-- Donut Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Energy Allocation Split</h3>
                        <p class="text-xs text-slate-400">How generated solar energy is distributed across destinations</p>
                    </div>
                    <DonutChart 
                        :series="energySplits" 
                        :labels="energyLabels"
                        :colors="['#6C3BFF', '#22C55E', '#F59E0B']"
                    />
                </div>

                <div class="glass-card p-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Carbon Impact Breakdown</h3>
                        <p class="text-xs text-slate-400">Categories of environmental impact from your array operation</p>
                    </div>
                    <DonutChart 
                        :series="carbonSeries" 
                        :labels="carbonLabels"
                        :colors="['#10B981', '#6C3BFF', '#8B5CF6']"
                    />
                </div>
            </div>

        </div>
    </CustomerLayout>
</template>
