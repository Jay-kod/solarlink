<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import AnalyticsCard from '@/Components/Cards/AnalyticsCard.vue'
import AreaChart from '@/Components/Charts/AreaChart.vue'
import { DollarSign, ClipboardList, TrendingUp, Sparkles, CheckCircle2, CreditCard, Clock, Check } from 'lucide-vue-next'

interface Transaction {
    id: number;
    service: string;
    client: string;
    amount: number;
    date: string;
    status: 'completed' | 'pending' | 'processing';
}

const showPayoutModal = ref(false)
const payoutRequested = ref(false)
const isMonthlyChart = ref(false)

const chartSeries = computed(() => {
    if (isMonthlyChart.value) {
        return [
            { name: 'Monthly Revenues ($)', data: [1200, 1850, 2400, 1900, 2800, 3150] }
        ]
    } else {
        return [
            { name: 'Daily Billings ($)', data: [320, 480, 710, 420, 810, 940, 680] }
        ]
    }
})

const chartCategories = computed(() => {
    if (isMonthlyChart.value) {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
    } else {
        return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    }
})

const transactions = ref<Transaction[]>([
    { id: 4001, service: 'Microinverter Degradation Swap', client: 'Bob Robertson', amount: 280, date: 'May 28, 2026', status: 'completed' },
    { id: 4002, service: 'Panel Deep Wash & Dusting', client: 'Alice Johnson', amount: 140, date: 'May 27, 2026', status: 'completed' },
    { id: 4003, service: 'Annual System Audit', client: 'Timothy Vance', amount: 180, date: 'May 25, 2026', status: 'completed' },
    { id: 4004, service: 'Tesla Powerwall 2 Calibration', client: 'Sarah Jenkins', amount: 195, date: 'May 24, 2026', status: 'pending' },
    { id: 4005, service: 'Emergency Charge Controller Fix', client: 'David Miller', amount: 320, date: 'May 22, 2026', status: 'completed' },
    { id: 4006, service: 'Seasonal Grid Netting Setup', client: 'Elena Rostova', amount: 210, date: 'May 20, 2026', status: 'processing' },
    { id: 4007, service: 'Commercial Array Wiring Audit', client: 'Gregory House', amount: 450, date: 'May 18, 2026', status: 'completed' },
    { id: 4008, service: 'Grounding Wire Replacement', client: 'Arthur Dent', amount: 110, date: 'May 15, 2026', status: 'completed' }
])

const handleRequestPayout = () => {
    payoutRequested.value = true
    setTimeout(() => {
        payoutRequested.value = false
        showPayoutModal.value = false
    }, 2000)
}
</script>

<template>
    <Head title="SolarLink — Revenue & Earnings" />

    <DashboardLayout role="technician" title="Revenue & Payouts">
        <div class="flex flex-col gap-8 text-left relative">
            
            <!-- Payout Request Success Modal -->
            <div 
                v-if="showPayoutModal" 
                class="fixed inset-0 z-50 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm flex items-center justify-center animate-fade-in-up"
            >
                <div class="glass-card p-8 flex flex-col items-center gap-4 text-center max-w-sm bg-white dark:bg-solar-bg-dark border border-solar-primary/15">
                    <template v-if="payoutRequested">
                        <div class="h-14 w-14 rounded-full bg-solar-success/10 text-solar-success flex items-center justify-center pulse-glow">
                            <Check class="h-8 w-8" />
                        </div>
                        <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">Payout Dispatched</h3>
                        <p class="text-xs text-slate-500">Funds are being transferred to your checking account ending in ****4821.</p>
                    </template>
                    <template v-else>
                        <div class="h-14 w-14 rounded-full bg-solar-primary-light text-solar-primary flex items-center justify-center shadow-solar">
                            <CreditCard class="h-8 w-8" />
                        </div>
                        <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">Confirm Wallet Payout</h3>
                        <p class="text-xs text-slate-500">Transfer your current balance of <strong>$1,850.00</strong> immediately?</p>
                        
                        <div class="flex gap-3 w-full mt-2">
                            <button 
                                @click="handleRequestPayout"
                                class="flex-grow h-10 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold transition-all shadow"
                            >
                                Confirm Payout
                            </button>
                            <button 
                                @click="showPayoutModal = false"
                                class="flex-grow h-10 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-650 dark:text-slate-300 text-xs font-bold transition-all"
                            >
                                Cancel
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Financial Ledger</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Revenue & Earnings Control</h2>
                        <p class="text-xs text-slate-450 mt-0.5">Track wallet balance, request fast direct deposits, and review historic job payouts.</p>
                    </div>
                    <button 
                        @click="showPayoutModal = true"
                        class="px-5 py-2.5 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow uppercase tracking-wider"
                    >
                        Request Payout
                    </button>
                </div>
            </div>

            <!-- KPI Row (4 cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <AnalyticsCard 
                    title="Wallet Balance"
                    value="$1,850.00"
                    :icon="DollarSign"
                    trend="Available to request"
                    trendType="neutral"
                    desc="Checking account ending in 4821"
                />
                <AnalyticsCard 
                    title="Monthly Revenue"
                    value="$3,150.00"
                    :icon="TrendingUp"
                    trend="+12.4% vs last month"
                    trendType="up"
                    desc="Gross earnings in June"
                />
                <AnalyticsCard 
                    title="Avg Payout per Job"
                    value="$235.00"
                    :icon="ClipboardList"
                    trend="Steady margin"
                    trendType="neutral"
                    desc="Weighted average"
                />
                <AnalyticsCard 
                    title="Completed Jobs"
                    value="14 Jobs"
                    :icon="CheckCircle2"
                    trend="+3 vs last period"
                    trendType="up"
                    desc="100% SLA fulfillment"
                />
            </div>

            <!-- Chart & Payout Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Graph Board (Left 8 Columns) -->
                <div class="lg:col-span-8 glass-card p-6 flex flex-col gap-4 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Billing Revenue Curves</h3>
                            <p class="text-xs text-slate-450">Track billings across active dispatch periods</p>
                        </div>
                        <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-solar-primary-dark/25 p-1 rounded-xl">
                            <button 
                                @click="isMonthlyChart = false"
                                class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all"
                                :class="!isMonthlyChart ? 'bg-white dark:bg-solar-primary text-slate-800 dark:text-white shadow' : 'text-slate-400'"
                            >
                                Weekly
                            </button>
                            <button 
                                @click="isMonthlyChart = true"
                                class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase transition-all"
                                :class="isMonthlyChart ? 'bg-white dark:bg-solar-primary text-slate-800 dark:text-white shadow' : 'text-slate-400'"
                            >
                                Monthly
                            </button>
                        </div>
                    </div>
                    <AreaChart 
                        :series="chartSeries" 
                        :categories="chartCategories"
                        :colors="['#10B981']"
                    />
                </div>

                <!-- Payout Target Info (Right 4 Columns) -->
                <div class="lg:col-span-4 glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Payout Destination</h3>
                    
                    <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-1"></div>

                    <div class="flex items-center gap-3 bg-slate-50 dark:bg-solar-primary-dark/20 p-4 rounded-xl border border-solar-primary/5">
                        <div class="p-2 bg-solar-primary/10 text-solar-primary rounded-lg">
                            <CreditCard class="h-5 w-5" />
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">First National Bank</h4>
                            <p class="text-[9px] text-slate-450 mt-0.5">Checking Account — ****4821</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 text-xs leading-relaxed text-slate-550 dark:text-slate-400 font-medium">
                        <div class="flex items-center gap-1.5 text-solar-success">
                            <CheckCircle2 class="h-4 w-4 shrink-0" />
                            <span>Instant payout activated</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-500">
                            <Clock class="h-4 w-4 shrink-0 text-slate-400" />
                            <span>Next auto-sweep: June 1st</span>
                        </div>
                        <p class="text-[9px] text-slate-450 mt-2 border-t border-solar-primary/5 pt-3 leading-relaxed">
                            Logistics partners may take 1-3 business days to clear transfers outside instant windows. All payouts are audited.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Transactions Ledger Table -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar mt-4">
                <div class="p-6 border-b border-solar-primary/10 dark:border-white/5">
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Transaction Logs</h3>
                    <p class="text-xs text-slate-450">Review fully detailed payout items and verification states.</p>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-850 dark:text-white font-bold">
                            <th class="p-4">Service Performed</th>
                            <th class="p-4">Client</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-slate-50/50 dark:hover:bg-solar-primary-dark/10 transition-all">
                            <td class="p-4 font-bold text-slate-800 dark:text-white">{{ tx.service }}</td>
                            <td class="p-4">{{ tx.client }}</td>
                            <td class="p-4 font-extrabold text-slate-850 dark:text-white">${{ tx.amount }}</td>
                            <td class="p-4 font-mono">{{ tx.date }}</td>
                            <td class="p-4 text-right">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="{
                                        'bg-solar-success/15 text-solar-success': tx.status === 'completed',
                                        'bg-amber-150 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300': tx.status === 'pending',
                                        'bg-solar-primary-light text-solar-primary dark:bg-solar-primary-dark dark:text-solar-primary-accent': tx.status === 'processing'
                                    }"
                                >
                                    {{ tx.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </DashboardLayout>
</template>
