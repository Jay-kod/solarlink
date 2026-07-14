<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Store, Eye, CheckCircle2, Sparkles } from 'lucide-vue-next'

const vendors = ref([
    { id: 601, name: 'EcoGrid Direct', vat: 'US-VAT-90249219', status: 'verified', headquarters: 'Oakland, CA' },
    { id: 602, name: 'SolarLink OEM Supply', vat: 'US-VAT-81048218', status: 'pending', headquarters: 'San Jose, CA' }
])

const handleVerifyVendor = (id: number) => {
    const vendor = vendors.value.find(v => v.id === id)
    if (vendor) {
        vendor.status = 'verified'
    }
}
</script>

<template>
    <Head title="SolarLink — Supplier Moderation" />

    <DashboardLayout role="admin" title="Vendor Controls">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Supplier Moderation Hub</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Wholesale Supplier Registry</h2>
                <p class="text-xs text-slate-450 mt-0.5">Approve direct-to-marketplace wholesale manufacturers, inspect tax certificates, and audit parts logs.</p>
            </div>

            <!-- Table list -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-800 dark:text-white font-bold">
                            <th class="p-4">Brand / Company</th>
                            <th class="p-4">Business VAT ID</th>
                            <th class="p-4">Headquarters</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-for="v in vendors" :key="v.id">
                            <td class="p-4 font-bold text-slate-800 dark:text-white">{{ v.name }}</td>
                            <td class="p-4 font-mono">{{ v.vat }}</td>
                            <td class="p-4">{{ v.headquarters }}</td>
                            <td class="p-4">
                                <span 
                                    class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="v.status === 'verified' ? 'bg-solar-success/15 text-solar-success' : 'bg-solar-warning/15 text-solar-warning'"
                                >
                                    {{ v.status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <button 
                                    v-if="v.status === 'pending'"
                                    @click="handleVerifyVendor(v.id)"
                                    class="px-3.5 py-1.5 rounded-lg bg-solar-primary hover:bg-solar-primary-active text-white text-[10px] font-bold uppercase tracking-wider transition-all"
                                >
                                    Approve Vendor
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </DashboardLayout>
</template>
