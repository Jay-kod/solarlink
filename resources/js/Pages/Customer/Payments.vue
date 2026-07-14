<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { useDarkMode } from '@/composables/useDarkMode'
import { 
    DollarSign, FileText, CheckCircle, Clock, AlertCircle, 
    Printer, Download, ShieldCheck, Sparkles, ChevronRight, X,
    Calendar, ArrowUpRight
} from 'lucide-vue-next'

interface PaymentItem {
    id: number;
    reference: string;
    type: 'Procurement' | 'Maintenance';
    item: string;
    quantity: number;
    amount: number;
    status: 'paid' | 'unpaid' | 'processing' | 'refunded' | 'cancelled';
    date: string;
}

const props = defineProps<{
    payments: PaymentItem[]
}>()

const activeInvoice = ref<PaymentItem | null>(null)
const isInvoiceModalOpen = ref(false)

// Calculate KPIs
const totalPaid = computed(() => {
    return props.payments
        .filter(p => p.status === 'paid')
        .reduce((sum, p) => sum + p.amount, 0)
        .toFixed(2)
})

const outstandingBalance = computed(() => {
    return props.payments
        .filter(p => p.status === 'unpaid')
        .reduce((sum, p) => sum + p.amount, 0)
        .toFixed(2)
})

const paidCount = computed(() => props.payments.filter(p => p.status === 'paid').length)
const pendingCount = computed(() => props.payments.filter(p => p.status === 'unpaid' || p.status === 'processing').length)

// Inertia Form for payment submission
const form = useForm({})

const payInvoice = (payment: PaymentItem) => {
    // If it is a maintenance service booking invoice
    if (payment.type === 'Maintenance') {
        form.post(`/user/bookings/${payment.id}/pay`, {
            onSuccess: () => {
                if (activeInvoice.value && activeInvoice.value.id === payment.id) {
                    activeInvoice.value.status = 'paid'
                }
            }
        })
    }
}

const viewInvoiceDetails = (payment: PaymentItem) => {
    activeInvoice.value = payment
    isInvoiceModalOpen.value = true
}

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'paid': return 'bg-emerald-500/10 text-emerald-500'
        case 'unpaid': return 'bg-amber-500/10 text-amber-500'
        case 'processing': return 'bg-blue-500/10 text-blue-500'
        case 'refunded':
        case 'cancelled':
            return 'bg-red-500/10 text-red-500'
        default: return 'bg-slate-500/10 text-slate-500'
    }
}

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'paid': return CheckCircle
        case 'unpaid': return AlertCircle
        case 'processing': return Clock
        default: return AlertCircle
    }
}

const handlePrint = () => {
    window.print()
}
</script>

<template>
    <Head title="SolarLink — Payments & Billing" />

    <CustomerLayout title="Billing Operations">
        <div class="flex flex-col gap-8 text-left relative">
            
            <!-- INVOICE/RECEIPT MODAL -->
            <div 
                v-if="isInvoiceModalOpen && activeInvoice"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in-up print:bg-white print:p-0"
            >
                <div class="w-full max-w-xl rounded-2xl bg-white dark:bg-solar-bg-dark border border-solar-primary/20 p-6 sm:p-8 flex flex-col gap-6 text-left shadow-solar-lg relative print:border-none print:shadow-none print:bg-white print:text-black">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center border-b border-slate-100 dark:border-white/5 pb-4 print:hidden">
                        <div class="flex items-center gap-2 text-solar-primary">
                            <FileText class="h-6 w-6" />
                            <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">Tax Invoice & Receipt</h3>
                        </div>
                        <button @click="isInvoiceModalOpen = false" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Print Title (Only visible when printing) -->
                    <div class="hidden print:flex items-center justify-between pb-6 border-b border-slate-200">
                        <div>
                            <h1 class="text-2xl font-black text-slate-900">SolarLink Inc.</h1>
                            <p class="text-xs text-slate-500">Clean Grid Operations Platform</p>
                        </div>
                        <div class="text-right">
                            <h2 class="text-lg font-extrabold text-slate-800">TAX RECEIPT</h2>
                            <p class="text-xs text-slate-500">Date: {{ activeInvoice.date }}</p>
                        </div>
                    </div>

                    <!-- Invoice Specifications -->
                    <div class="grid grid-cols-2 gap-6 text-xs mt-2">
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 font-bold block uppercase tracking-wider">Billed To:</span>
                            <span class="font-extrabold text-slate-700 dark:text-slate-200 mt-1 block">{{ $page.props.auth.user?.name || 'Clara Oswald' }}</span>
                            <span class="text-slate-500 mt-0.5 block">{{ $page.props.auth.user?.email || 'customer@solarlink.io' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 dark:text-slate-500 font-bold block uppercase tracking-wider">Invoice Reference:</span>
                            <span class="font-mono font-extrabold text-slate-700 dark:text-slate-200 mt-1 block">{{ activeInvoice.reference }}</span>
                            <span class="text-slate-500 mt-0.5 block">Issued: {{ activeInvoice.date }}</span>
                        </div>
                    </div>

                    <!-- Line Items Table -->
                    <div class="mt-4 border border-slate-150 dark:border-white/5 rounded-xl overflow-hidden bg-slate-50/50 dark:bg-solar-primary-dark/10">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-100 dark:bg-solar-primary-dark/30 text-slate-500 font-bold border-b border-slate-200 dark:border-white/5">
                                    <th class="p-3 text-left">Description</th>
                                    <th class="p-3 text-center">Qty</th>
                                    <th class="p-3 text-right">Price</th>
                                    <th class="p-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-slate-700 dark:text-slate-255 border-b border-slate-100 dark:border-white/5">
                                    <td class="p-3 font-semibold text-left">{{ activeInvoice.item }}</td>
                                    <td class="p-3 text-center">{{ activeInvoice.quantity }}</td>
                                    <td class="p-3 text-right">${{ (activeInvoice.amount / activeInvoice.quantity).toFixed(2) }}</td>
                                    <td class="p-3 text-right font-bold">${{ activeInvoice.amount.toFixed(2) }}</td>
                                </tr>
                                <!-- Subtotals -->
                                <tr class="text-[11px] text-slate-500">
                                    <td colspan="2" class="p-2"></td>
                                    <td class="p-2 text-right">Subtotal:</td>
                                    <td class="p-2 text-right font-semibold">${{ (activeInvoice.amount / 1.0825).toFixed(2) }}</td>
                                </tr>
                                <tr class="text-[11px] text-slate-500 border-b border-slate-100 dark:border-white/5">
                                    <td colspan="2" class="p-2"></td>
                                    <td class="p-2 text-right">Tax (8.25%):</td>
                                    <td class="p-2 text-right font-semibold">${{ (activeInvoice.amount - (activeInvoice.amount / 1.0825)).toFixed(2) }}</td>
                                </tr>
                                <tr class="text-sm font-black text-slate-800 dark:text-white">
                                    <td colspan="2" class="p-3"></td>
                                    <td class="p-3 text-right uppercase tracking-wider">Grand Total:</td>
                                    <td class="p-3 text-right text-solar-primary font-black">${{ activeInvoice.amount.toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Status Alert -->
                    <div 
                        class="p-4 rounded-xl flex items-center justify-between gap-4 text-xs font-semibold"
                        :class="[
                            activeInvoice.status === 'paid' 
                                ? 'bg-emerald-500/5 text-emerald-500 border border-emerald-500/10' 
                                : 'bg-amber-500/5 text-amber-500 border border-amber-500/10'
                        ]"
                    >
                        <div class="flex items-center gap-2">
                            <component :is="getStatusIcon(activeInvoice.status)" class="h-4.5 w-4.5 shrink-0" />
                            <span>Payment Status: <span class="uppercase font-extrabold">{{ activeInvoice.status }}</span></span>
                        </div>
                        
                        <!-- Pay Now button if unpaid inside the modal -->
                        <button 
                            v-if="activeInvoice.status === 'unpaid'"
                            @click="payInvoice(activeInvoice)"
                            class="px-4 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] uppercase tracking-wider transition-all"
                        >
                            Pay Bill Now
                        </button>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end gap-3 border-t border-slate-100 dark:border-white/5 pt-5 print:hidden">
                        <button 
                            @click="handlePrint" 
                            class="px-4 h-10 rounded-xl border border-slate-200 dark:border-white/5 text-slate-650 dark:text-slate-200 font-bold text-xs flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-white/5 transition-all"
                        >
                            <Printer class="h-4 w-4" />
                            <span>Print Receipt</span>
                        </button>
                        <button 
                            @click="isInvoiceModalOpen = false" 
                            class="px-5 h-10 rounded-xl bg-solar-primary text-white font-bold text-xs hover:bg-solar-primary-active transition-all"
                        >
                            Close View
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page actions header -->
            <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider mb-1">
                    <Sparkles class="h-3 w-3" />
                    <span>Transaction Registry</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Payments & Billing Ledger</h2>
                <p class="text-xs text-slate-450 mt-0.5 font-medium">Verify invoices, view tax receipts, download transaction logs, and clear outstanding balances for maintenance tickets.</p>
            </div>

            <!-- KPI Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card p-6 flex items-center justify-between bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5">
                    <div class="text-left flex flex-col gap-1">
                        <span class="text-slate-400 dark:text-slate-500 font-bold text-[10px] uppercase tracking-wider">Settled Payments</span>
                        <span class="text-xl font-black text-slate-850 dark:text-white">${{ totalPaid }}</span>
                        <span class="text-[9px] text-emerald-500 font-bold flex items-center gap-0.5 mt-0.5">
                            <CheckCircle class="h-3 w-3" />
                            {{ paidCount }} Settled
                        </span>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0">
                        <DollarSign class="h-5 w-5" />
                    </div>
                </div>

                <div class="glass-card p-6 flex items-center justify-between bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5">
                    <div class="text-left flex flex-col gap-1">
                        <span class="text-slate-400 dark:text-slate-500 font-bold text-[10px] uppercase tracking-wider">Outstanding Balance</span>
                        <span class="text-xl font-black text-slate-850 dark:text-white">${{ outstandingBalance }}</span>
                        <span class="text-[9px] text-amber-500 font-bold flex items-center gap-0.5 mt-0.5" :class="{'text-slate-450': outstandingBalance === '0.00'}">
                            <Clock class="h-3 w-3" />
                            {{ pendingCount }} Pending
                        </span>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                        <DollarSign class="h-5 w-5" />
                    </div>
                </div>

                <div class="glass-card p-6 flex items-center justify-between bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5">
                    <div class="text-left flex flex-col gap-1">
                        <span class="text-slate-400 dark:text-slate-500 font-bold text-[10px] uppercase tracking-wider">CO2 Offset Rebate</span>
                        <span class="text-xl font-black text-slate-850 dark:text-white">$84.00</span>
                        <span class="text-[9px] text-solar-primary font-bold mt-0.5">Accrued green credits</span>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary flex items-center justify-center shrink-0">
                        <ShieldCheck class="h-5 w-5" />
                    </div>
                </div>

                <div class="glass-card p-6 flex items-center justify-between bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5">
                    <div class="text-left flex flex-col gap-1">
                        <span class="text-slate-400 dark:text-slate-500 font-bold text-[10px] uppercase tracking-wider">Payment Protocol</span>
                        <span class="text-xs font-black text-slate-800 dark:text-white mt-1 uppercase tracking-wider">SSL Encrypted</span>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-1">Authorized Clearing Direct</span>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                        <ShieldCheck class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 flex flex-col gap-4">
                <div class="p-6 pb-2 border-b border-slate-100 dark:border-white/5 flex justify-between items-center">
                    <div>
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Transaction Logs</h3>
                        <p class="text-xs text-slate-400">Chronological history of invoices for product checkouts and field engineer dispatches</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-solar-primary-dark/20 text-slate-450 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100 dark:border-white/5">
                                <th class="p-4">Date</th>
                                <th class="p-4">Invoice ID</th>
                                <th class="p-4">Transaction Type</th>
                                <th class="p-4">Billed Description</th>
                                <th class="p-4 text-right">Amount</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr 
                                v-for="p in payments" 
                                :key="p.reference"
                                class="hover:bg-slate-50/50 dark:hover:bg-solar-primary-dark/5 text-slate-650 dark:text-slate-200 font-medium"
                            >
                                <td class="p-4 font-bold whitespace-nowrap">{{ p.date }}</td>
                                <td class="p-4 font-mono font-bold whitespace-nowrap">{{ p.reference }}</td>
                                <td class="p-4">
                                    <span 
                                        class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                        :class="p.type === 'Procurement' ? 'bg-blue-500/10 text-blue-500' : 'bg-purple-500/10 text-purple-500'"
                                    >
                                        {{ p.type }}
                                    </span>
                                </td>
                                <td class="p-4 max-w-xs truncate">{{ p.item }}</td>
                                <td class="p-4 text-right font-bold text-slate-850 dark:text-white">${{ p.amount.toFixed(2) }}</td>
                                <td class="p-4 text-center">
                                    <span 
                                        class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wider inline-flex items-center gap-1.5"
                                        :class="getStatusBadgeClass(p.status)"
                                    >
                                        <component :is="getStatusIcon(p.status)" class="h-3 w-3 shrink-0" />
                                        <span>{{ p.status }}</span>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- View details -->
                                        <button 
                                            @click="viewInvoiceDetails(p)"
                                            class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-white/5 hover:border-solar-primary dark:hover:border-white/20 text-slate-600 dark:text-slate-350 hover:text-solar-primary transition-all font-bold text-[10px] uppercase tracking-wider flex items-center gap-1"
                                        >
                                            <span>Invoice</span>
                                            <ArrowUpRight class="h-3 w-3" />
                                        </button>
                                        
                                        <!-- Direct Pay now if unpaid -->
                                        <button 
                                            v-if="p.status === 'unpaid'"
                                            @click="payInvoice(p)"
                                            class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition-all font-bold text-[10px] uppercase tracking-wider"
                                        >
                                            Pay Now
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="payments.length === 0">
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    There are no payment invoices recorded in your billing ledger.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </CustomerLayout>
</template>
