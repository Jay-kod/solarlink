<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { productCatalog, Product } from '@/data/products'
import { ShoppingBag, Eye, CheckCircle2, XCircle, Sparkles, Search, AlertCircle } from 'lucide-vue-next'

import { router } from '@inertiajs/vue3'

interface AdminProduct extends Product {
    vendorName: string;
    submittedDate: string;
    approvalStatus: 'approved' | 'pending' | 'rejected';
}

const props = defineProps<{
    products?: AdminProduct[]
}>()

const productsList = ref<AdminProduct[]>(props.products || [])

const searchQuery = ref('')
const selectedCategory = ref('all')

const handleApproveProduct = (id: number) => {
    router.patch(`/admin/products/${id}/status`, { status: 'approved' }, {
        onSuccess: () => {
            const prod = productsList.value.find(p => p.id === id)
            if (prod) prod.approvalStatus = 'approved'
        }
    })
}

const handleRejectProduct = (id: number) => {
    const prod = productsList.value.find(p => p.id === id)
    if (prod) {
        const newStatus = prod.approvalStatus === 'rejected' ? 'pending' : 'rejected'
        router.patch(`/admin/products/${id}/status`, { status: newStatus }, {
            onSuccess: () => {
                prod.approvalStatus = newStatus
            }
        })
    }
}

const totalListings = computed(() => productsList.value.length)
const approvedCount = computed(() => productsList.value.filter(p => p.approvalStatus === 'approved').length)
const pendingCount = computed(() => productsList.value.filter(p => p.approvalStatus === 'pending').length)
const rejectedCount = computed(() => productsList.value.filter(p => p.approvalStatus === 'rejected').length)

const filteredProducts = computed(() => {
    return productsList.value.filter(prod => {
        const matchesSearch = prod.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                             prod.vendorName.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesCategory = selectedCategory.value === 'all' || prod.category === selectedCategory.value
        return matchesSearch && matchesCategory
    })
})
</script>

<template>
    <Head title="SolarLink — Marketplace Audits" />

    <DashboardLayout role="admin" title="Parts Controls">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2.5 py-1 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-extrabold text-[9px] uppercase tracking-widest border border-indigo-500/20 shadow-sm animate-pulse-slow">
                    <Sparkles class="h-3 w-3" />
                    <span>Catalog Audits</span>
                </div>
                <h2 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-500 dark:from-indigo-400 dark:to-purple-400 tracking-tight mt-1">Marketplace Listings Moderation</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">Approve or flag new wholesale catalog items uploaded by verified third-party manufacturer networks.</p>
            </div>

            <!-- Summary Chips -->
            <div class="flex flex-wrap gap-4 text-xs font-bold">
                <div class="px-4 py-2 bg-slate-50/80 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-xl shadow-sm">
                    Total Listings: <span class="text-indigo-500 font-extrabold ml-1">{{ totalListings }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50/80 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-xl shadow-sm">
                    Approved: <span class="text-emerald-500 font-extrabold ml-1">{{ approvedCount }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50/80 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-xl shadow-sm">
                    Pending Review: <span class="text-amber-500 font-extrabold ml-1">{{ pendingCount }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50/80 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200 dark:border-white/5 rounded-xl shadow-sm">
                    Rejected: <span class="text-red-500 font-extrabold ml-1">{{ rejectedCount }}</span>
                </div>
            </div>

            <!-- Search + Category Filter -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="relative flex items-center w-full sm:max-w-sm">
                    <Search class="absolute left-3.5 h-4 w-4 text-slate-400 pointer-events-none" />
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Search products or vendors..."
                        class="w-full h-11 pl-10 pr-4 rounded-xl bg-white/80 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white shadow-sm"
                    />
                </div>
                
                <select 
                    v-model="selectedCategory"
                    class="h-11 px-4 rounded-xl bg-white/80 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white shadow-sm"
                >
                    <option value="all">All Categories</option>
                    <option value="panels">Solar Panels</option>
                    <option value="batteries">Battery Walls</option>
                    <option value="inverters">Hybrid Inverters</option>
                    <option value="accessories">Accessories</option>
                </select>
            </div>

            <!-- Products Moderation Table -->
            <div class="glass-card overflow-hidden bg-white/60 dark:bg-[#0B0F19]/70 backdrop-blur-2xl border border-indigo-500/20 dark:border-white/5 rounded-3xl shadow-[0_0_40px_-15px_rgba(99,102,241,0.2)]">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-indigo-500/5 border-b border-indigo-500/10 text-left text-slate-850 dark:text-white font-black">
                            <th class="p-4 w-16">Thumbnail</th>
                            <th class="p-4">Equipment Details</th>
                            <th class="p-4">Manufacturer</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Wholesale Price</th>
                            <th class="p-4">Submission Date</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Moderator Controls</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-if="filteredProducts.length === 0">
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <AlertCircle class="h-6 w-6 text-slate-300" />
                                    <span>No wholesale listings match this query.</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="prod in filteredProducts" :key="prod.id" class="hover:bg-slate-50/50 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="p-4">
                                <div class="h-10 w-10 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200 dark:border-white/10">
                                    <img :src="prod.image" :alt="prod.name" class="h-full w-full object-cover" />
                                </div>
                            </td>
                            <td class="p-4 font-bold text-slate-850 dark:text-white truncate max-w-[200px]" :title="prod.name">
                                {{ prod.name }}
                            </td>
                            <td class="p-4 font-semibold">{{ prod.vendorName }}</td>
                            <td class="p-4 uppercase tracking-wider font-semibold text-[10px] text-indigo-500 dark:text-indigo-400">
                                {{ prod.category }}
                            </td>
                            <td class="p-4 font-extrabold text-slate-850 dark:text-white">${{ prod.price.toLocaleString() }}</td>
                            <td class="p-4 font-mono">{{ prod.submittedDate }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400': prod.approvalStatus === 'approved',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300': prod.approvalStatus === 'pending',
                                        'bg-red-500/15 text-red-600 dark:text-red-400': prod.approvalStatus === 'rejected'
                                    }"
                                >
                                    {{ prod.approvalStatus }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button class="p-2 rounded-lg border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/10 transition-all" title="Inspect Listing specs">
                                        <Eye class="h-3.5 w-3.5" />
                                    </button>
                                    
                                    <button 
                                        v-if="prod.approvalStatus === 'pending'"
                                        @click="handleApproveProduct(prod.id)"
                                        class="px-3 py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-wider transition-all shadow"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        v-else
                                        @click="handleRejectProduct(prod.id)"
                                        class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all"
                                        :class="prod.approvalStatus === 'approved' ? 'border border-red-500/25 text-red-500 hover:bg-red-500/5' : 'bg-emerald-500 text-white hover:bg-emerald-600 shadow'"
                                    >
                                        {{ prod.approvalStatus === 'approved' ? 'Reject' : 'Re-approve' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </DashboardLayout>
</template>
