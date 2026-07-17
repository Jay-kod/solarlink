<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'

interface Quote {
    id: number;
    supplier: string;
    amount: number;
    lead_time_days: number;
    notes: string | null;
    status: 'submitted' | 'selected' | 'rejected';
}

interface RFQ {
    id: number;
    title: string;
    description: string;
    status: 'open' | 'quoted' | 'approved' | 'ordered' | 'delivered' | 'cancelled';
    items: Array<{ id: number; part_name: string; quantity: number; notes?: string }>;
    quotes: Quote[];
}

const props = defineProps<{
    suppliers: Array<{ id: number; name: string; address: string | null; contact: string | null }>;
    rfqs: RFQ[];
}>()

const form = useForm({
    title: '',
    description: '',
    part_name: '',
    quantity: 1,
    notes: '',
})

const submitRFQ = () => {
    form.post('/user/procurement', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Procurement RFQ" />

    <CustomerLayout title="Procurement RFQ">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 text-left">
            <section class="xl:col-span-2 space-y-6">
                <div class="glass-card p-6">
                    <h3 class="font-bold text-lg mb-4">Create RFQ</h3>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="submitRFQ">
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold">Title</label>
                            <input v-model="form.title" class="w-full mt-1 rounded-lg border-slate-200 text-sm" placeholder="Replacement parts for inverter" required />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold">Description</label>
                            <textarea v-model="form.description" rows="3" class="w-full mt-1 rounded-lg border-slate-200 text-sm" required />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Part Name</label>
                            <input v-model="form.part_name" class="w-full mt-1 rounded-lg border-slate-200 text-sm" required />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Quantity</label>
                            <input v-model.number="form.quantity" type="number" min="1" class="w-full mt-1 rounded-lg border-slate-200 text-sm" required />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold">Item Notes</label>
                            <input v-model="form.notes" class="w-full mt-1 rounded-lg border-slate-200 text-sm" />
                        </div>
                        <div class="md:col-span-2">
                            <button class="px-4 py-2 rounded-lg bg-solar-primary text-white text-sm font-semibold" :disabled="form.processing">Submit RFQ</button>
                        </div>
                    </form>
                </div>

                <div class="glass-card p-6">
                    <h3 class="font-bold text-lg mb-4">Quote Comparison</h3>
                    <div class="space-y-4">
                        <div v-for="rfq in rfqs" :key="rfq.id" class="border rounded-xl p-4 bg-white/70 dark:bg-transparent">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-semibold text-sm">RFQ #{{ rfq.id }} {{ rfq.title }}</p>
                                <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-solar-primary/10 text-solar-primary">{{ rfq.status }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ rfq.description }}</p>
                            <ul class="text-xs mt-2 list-disc pl-4">
                                <li v-for="item in rfq.items" :key="item.id">{{ item.part_name }} x {{ item.quantity }}</li>
                            </ul>

                            <div v-if="rfq.quotes.length" class="mt-3 overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="text-left border-b">
                                            <th class="py-1">Supplier</th>
                                            <th class="py-1">Amount</th>
                                            <th class="py-1">Lead Time</th>
                                            <th class="py-1">Status</th>
                                            <th class="py-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="quote in rfq.quotes" :key="quote.id" class="border-b last:border-b-0">
                                            <td class="py-1.5">{{ quote.supplier }}</td>
                                            <td class="py-1.5">${{ quote.amount }}</td>
                                            <td class="py-1.5">{{ quote.lead_time_days }} days</td>
                                            <td class="py-1.5">{{ quote.status }}</td>
                                            <td class="py-1.5">
                                                <Link
                                                    v-if="quote.status !== 'selected'"
                                                    as="button"
                                                    method="post"
                                                    :href="`/user/procurement/quotes/${quote.id}/approve`"
                                                    class="px-2 py-1 rounded bg-emerald-600 text-white text-[10px]"
                                                >
                                                    Approve
                                                </Link>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="glass-card p-6">
                    <h3 class="font-bold text-base mb-3">Verified Suppliers</h3>
                    <div class="space-y-3">
                        <div v-for="supplier in suppliers" :key="supplier.id" class="border rounded-lg p-3">
                            <p class="text-xs font-semibold">{{ supplier.name }}</p>
                            <p class="text-xs text-slate-500">{{ supplier.address || 'Address not set' }}</p>
                            <p class="text-xs text-slate-500">{{ supplier.contact || 'No contact email' }}</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </CustomerLayout>
</template>
