<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

interface RFQ {
    id: number;
    customer: string;
    title: string;
    description: string;
    status: 'open' | 'quoted' | 'approved' | 'ordered' | 'delivered' | 'cancelled';
    items: Array<{ id: number; part_name: string; quantity: number; notes?: string }>;
    myQuote: null | { amount: number; lead_time_days: number; notes: string | null; status: string };
}

const props = defineProps<{
    rfqs: RFQ[];
    supplier: { id: number; name: string; is_verified: boolean };
}>()

const quoteForm = useForm({
    amount: 0,
    lead_time_days: 3,
    notes: '',
})

const submitQuote = (rfqId: number) => {
    quoteForm.post(`/vendor/rfqs/${rfqId}/quote`, {
        preserveScroll: true,
        onSuccess: () => quoteForm.reset('amount', 'lead_time_days', 'notes'),
    })
}
</script>

<template>
    <Head title="Vendor RFQs" />

    <DashboardLayout role="vendor" title="RFQ Desk">
        <div class="space-y-4 text-left">
            <div class="glass-card p-6">
                <h3 class="font-bold text-base">Supplier: {{ supplier.name }}</h3>
                <p class="text-xs" :class="supplier.is_verified ? 'text-emerald-600' : 'text-amber-600'">
                    {{ supplier.is_verified ? 'Verified supplier account' : 'Pending supplier verification' }}
                </p>
            </div>

            <div class="glass-card p-6" v-for="rfq in rfqs" :key="rfq.id">
                <div class="flex items-center flex-wrap gap-2">
                    <h3 class="font-bold text-sm">RFQ #{{ rfq.id }} - {{ rfq.title }}</h3>
                    <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-solar-primary/10 text-solar-primary">{{ rfq.status }}</span>
                </div>
                <p class="text-xs mt-1">Customer: {{ rfq.customer }}</p>
                <p class="text-xs text-slate-500">{{ rfq.description }}</p>
                <ul class="text-xs mt-2 list-disc pl-4">
                    <li v-for="item in rfq.items" :key="item.id">{{ item.part_name }} x {{ item.quantity }}</li>
                </ul>

                <div v-if="rfq.myQuote" class="mt-3 border rounded-lg p-3 bg-slate-50 dark:bg-transparent">
                    <p class="text-xs font-semibold">Your Quote: ${{ rfq.myQuote.amount }} ({{ rfq.myQuote.lead_time_days }} days)</p>
                    <p class="text-xs text-slate-500">Status: {{ rfq.myQuote.status }}</p>
                </div>

                <form class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3" @submit.prevent="submitQuote(rfq.id)">
                    <input v-model.number="quoteForm.amount" type="number" min="0" class="rounded-lg border-slate-200 text-sm" placeholder="Quote amount" required />
                    <input v-model.number="quoteForm.lead_time_days" type="number" min="1" class="rounded-lg border-slate-200 text-sm" placeholder="Lead time (days)" required />
                    <input v-model="quoteForm.notes" class="rounded-lg border-slate-200 text-sm" placeholder="Notes" />
                    <button class="md:col-span-3 px-4 py-2 rounded-lg bg-solar-primary text-white text-sm font-semibold" :disabled="quoteForm.processing">Submit Quote</button>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
