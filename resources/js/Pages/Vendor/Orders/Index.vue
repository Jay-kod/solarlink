<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Calendar, Truck, User, MapPin, Sparkles, CheckCircle2, ChevronDown, ChevronUp, AlertCircle } from 'lucide-vue-next'

interface Order {
    id: number;
    buyer: string;
    productName: string;
    qty: number;
    address: string;
    total: number;
    status: 'pending' | 'processing' | 'shipped' | 'delivered';
    date: string;
}

const activeTab = ref<'all' | 'pending' | 'processing' | 'shipped' | 'delivered'>('all')
const expandedRow = ref<number | null>(null)

const orders = ref<Order[]>([
    {
        id: 3001,
        buyer: 'Alice Johnson',
        productName: 'EcoGuard 400W Monocrystalline Panel',
        qty: 4,
        address: '1225 Grid Access Road, SF, CA 94103',
        total: 1156,
        status: 'pending',
        date: 'Today'
    },
    {
        id: 3002,
        buyer: 'Bob Robertson',
        productName: 'Tesla Powerwall 2 Battery Storage',
        qty: 1,
        address: '44 Bayview Lane, SF, CA 94107',
        total: 3499,
        status: 'shipped',
        date: 'May 28th'
    },
    {
        id: 3003,
        buyer: 'Sarah Jenkins',
        productName: 'NovaGrid Smart 8kW Hybrid Inverter',
        qty: 1,
        address: '228 Battery Point, East Bay, CA 94501',
        total: 1199,
        status: 'processing',
        date: 'May 27th'
    },
    {
        id: 3004,
        buyer: 'David Miller',
        productName: 'EcoWire Heavy-Duty 10 AWG Solar Cable (100ft)',
        qty: 3,
        address: '89 Junction St, Peninsula, CA 94002',
        total: 177,
        status: 'delivered',
        date: 'May 25th'
    },
    {
        id: 3005,
        buyer: 'Elena Rostova',
        productName: 'AeroVolt 450W Monocrystalline Panel',
        qty: 10,
        address: '775 Sunshine Ridge, Marin, CA 94901',
        total: 2890,
        status: 'pending',
        date: 'May 24th'
    },
    {
        id: 3006,
        buyer: 'Gregory House',
        productName: 'SolarLink Max 10kWh Battery Wall',
        qty: 2,
        address: '100 Medical Center Way, Oakland, CA 94602',
        total: 6998,
        status: 'processing',
        date: 'May 20th'
    }
])

const handleStatusChange = (id: number, nextStatus: 'processing' | 'shipped' | 'delivered') => {
    const ord = orders.value.find(o => o.id === id)
    if (ord) {
        ord.status = nextStatus
    }
}

const toggleRow = (id: number) => {
    if (expandedRow.value === id) {
        expandedRow.value = null
    } else {
        expandedRow.value = id
    }
}

const filteredOrders = computed(() => {
    if (activeTab.value === 'all') return orders.value
    return orders.value.filter(o => o.status === activeTab.value)
})

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300'
        case 'processing': return 'bg-solar-primary-light text-solar-primary dark:bg-solar-primary-dark dark:text-solar-primary-accent'
        case 'shipped': return 'bg-blue-100 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300'
        case 'delivered': return 'bg-solar-success/15 text-solar-success'
        default: return 'bg-slate-100 text-slate-500'
    }
}
</script>

<template>
    <Head title="SolarLink — Wholesale Orders" />

    <DashboardLayout role="vendor" title="Dispatched Orders">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Logistics Node</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Wholesale Purchases Control</h2>
                <p class="text-xs text-slate-450 mt-0.5">Manage wholesale component invoices, tracking numbers, and shipping queues.</p>
            </div>

            <!-- Tab Filters -->
            <div class="flex border-b border-solar-primary/10 dark:border-white/5 pb-px gap-4 text-xs font-bold">
                <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'text-solar-primary border-b-2 border-b-solar-primary pb-3' : 'text-slate-450 pb-3 hover:text-slate-600'">
                    All Purchases ({{ orders.length }})
                </button>
                <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'text-amber-600 border-b-2 border-b-amber-500 pb-3' : 'text-slate-450 pb-3 hover:text-slate-600'">
                    Pending
                </button>
                <button @click="activeTab = 'processing'" :class="activeTab === 'processing' ? 'text-solar-primary border-b-2 border-b-solar-primary pb-3' : 'text-slate-450 pb-3 hover:text-slate-600'">
                    Processing
                </button>
                <button @click="activeTab = 'shipped'" :class="activeTab === 'shipped' ? 'text-blue-600 border-b-2 border-b-blue-500 pb-3' : 'text-slate-450 pb-3 hover:text-slate-600'">
                    Shipped
                </button>
                <button @click="activeTab = 'delivered'" :class="activeTab === 'delivered' ? 'text-solar-success border-b-2 border-b-solar-success pb-3' : 'text-slate-450 pb-3 hover:text-slate-600'">
                    Delivered
                </button>
            </div>

            <!-- Orders Board -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-850 dark:text-white font-bold">
                            <th class="p-4 w-10"></th>
                            <th class="p-4">Order ID</th>
                            <th class="p-4">Wholesale Item</th>
                            <th class="p-4">Purchaser</th>
                            <th class="p-4">Quantity</th>
                            <th class="p-4">Total</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Logistics Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-if="filteredOrders.length === 0">
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <AlertCircle class="h-6 w-6 text-slate-300" />
                                    <span>No wholesale purchases found under this criteria.</span>
                                </div>
                            </td>
                        </tr>
                        <template v-for="ord in filteredOrders" :key="ord.id">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-solar-primary-dark/10 transition-all cursor-pointer" @click="toggleRow(ord.id)">
                                <td class="p-4">
                                    <component :is="expandedRow === ord.id ? ChevronUp : ChevronDown" class="h-4 w-4 text-slate-400" />
                                </td>
                                <td class="p-4 font-mono font-bold text-slate-800 dark:text-white">
                                    #SL-{{ ord.id }}
                                </td>
                                <td class="p-4 font-bold text-slate-800 dark:text-white truncate max-w-[200px]" :title="ord.productName">
                                    {{ ord.productName }}
                                </td>
                                <td class="p-4 flex items-center gap-1.5 mt-2">
                                    <User class="h-3.5 w-3.5 text-slate-400" />
                                    <span>{{ ord.buyer }}</span>
                                </td>
                                <td class="p-4 font-semibold">{{ ord.qty }} pcs</td>
                                <td class="p-4 font-extrabold text-slate-850 dark:text-white">${{ ord.total.toLocaleString() }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                        :class="getStatusBadgeClass(ord.status)"
                                    >
                                        {{ ord.status }}
                                    </span>
                                </td>
                                <td class="p-4 font-semibold text-slate-450">{{ ord.date }}</td>
                                <td class="p-4 text-right" @click.stop>
                                    <div class="flex justify-end items-center">
                                        <button 
                                            v-if="ord.status === 'pending'"
                                            @click="handleStatusChange(ord.id, 'processing')"
                                            class="px-3.5 py-1.5 rounded-lg bg-solar-primary hover:bg-solar-primary-active text-white text-[10px] font-bold uppercase tracking-wider transition-all shadow"
                                        >
                                            Process Invoice
                                        </button>
                                        <button 
                                            v-else-if="ord.status === 'processing'"
                                            @click="handleStatusChange(ord.id, 'shipped')"
                                            class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-750 text-white text-[10px] font-bold uppercase tracking-wider transition-all shadow"
                                        >
                                            Ship Package
                                        </button>
                                        <button 
                                            v-else-if="ord.status === 'shipped'"
                                            @click="handleStatusChange(ord.id, 'delivered')"
                                            class="px-3.5 py-1.5 rounded-lg bg-emerald-650 hover:bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-wider transition-all shadow"
                                        >
                                            Deliver Confirm
                                        </button>
                                        <span v-else class="text-xs text-slate-400 italic font-semibold flex items-center gap-1">
                                            <CheckCircle2 class="h-4 w-4 text-solar-success" />
                                            <span>Delivered</span>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            <!-- Expanded Detail Row -->
                            <tr v-if="expandedRow === ord.id" class="bg-slate-50/50 dark:bg-solar-primary-dark/5">
                                <td colspan="9" class="p-5 text-left border-t border-solar-primary/5">
                                    <div class="flex items-start gap-3 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/5 p-4 rounded-xl max-w-xl">
                                        <MapPin class="h-5 w-5 text-solar-primary mt-0.5 shrink-0" />
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">Shipping Address & Invoicing</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ ord.address }}</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-2">Logistics SLA: 3-5 Business Days Delivery window.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </DashboardLayout>
</template>
