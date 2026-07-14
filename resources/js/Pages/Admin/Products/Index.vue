<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { mockProducts, Product } from '@/data/products'
import { ShoppingBag, Eye, CheckCircle2, XCircle, Sparkles, Search, AlertCircle } from 'lucide-vue-next'

interface AdminProduct extends Product {
    vendorName: string;
    submittedDate: string;
    approvalStatus: 'approved' | 'pending' | 'rejected';
}

const productsList = ref<AdminProduct[]>([
    {
        ...mockProducts[0],
        id: 701,
        vendorName: 'EcoGrid Direct',
        submittedDate: 'May 28, 2026',
        approvalStatus: 'approved'
    },
    {
        ...mockProducts[1],
        id: 702,
        vendorName: 'EcoGrid Direct',
        submittedDate: 'May 27, 2026',
        approvalStatus: 'pending'
    },
    {
        ...mockProducts[2],
        id: 703,
        vendorName: 'AeroVolt Power',
        submittedDate: 'May 26, 2026',
        approvalStatus: 'approved'
    },
    {
        ...mockProducts[3],
        id: 704,
        vendorName: 'NovaGrid Systems',
        submittedDate: 'May 25, 2026',
        approvalStatus: 'pending'
    },
    {
        ...mockProducts[4],
        id: 705,
        vendorName: 'NovaGrid Systems',
        submittedDate: 'May 24, 2026',
        approvalStatus: 'approved'
    },
    {
        id: 706,
        name: 'HyperCharge MPPT Controller 40A',
        category: 'accessories',
        price: 99,
        rating: 4.4,
        reviewsCount: 12,
        image: 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&q=80&w=200',
        description: 'Mid-range controller perfect for smaller setups.',
        specs: {},
        stock: 5,
        features: [],
        vendorName: 'EcoGrid Direct',
        submittedDate: 'May 22, 2026',
        approvalStatus: 'rejected'
    }
])

const searchQuery = ref('')
const selectedCategory = ref('all')

const handleApproveProduct = (id: number) => {
    const prod = productsList.value.find(p => p.id === id)
    if (prod) {
        prod.approvalStatus = 'approved'
    }
}

const handleRejectProduct = (id: number) => {
    const prod = productsList.value.find(p => p.id === id)
    if (prod) {
        prod.approvalStatus = 'rejected'
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
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Catalog Audits</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Marketplace Listings Moderation</h2>
                <p class="text-xs text-slate-450 mt-0.5">Approve or flag new wholesale catalog items uploaded by verified third-party manufacturer networks.</p>
            </div>

            <!-- Summary Chips -->
            <div class="flex flex-wrap gap-4 text-xs font-bold">
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Total Listings: <span class="text-solar-primary font-extrabold ml-1">{{ totalListings }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Approved: <span class="text-solar-success font-extrabold ml-1">{{ approvedCount }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Pending Review: <span class="text-amber-500 font-extrabold ml-1">{{ pendingCount }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Rejected: <span class="text-solar-danger font-extrabold ml-1">{{ rejectedCount }}</span>
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
                        class="w-full h-11 pl-10 pr-4 rounded-xl bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-850 dark:text-white shadow-sm"
                    />
                </div>
                
                <select 
                    v-model="selectedCategory"
                    class="h-11 px-4 rounded-xl bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white shadow-sm"
                >
                    <option value="all">All Categories</option>
                    <option value="panels">Solar Panels</option>
                    <option value="batteries">Battery Walls</option>
                    <option value="inverters">Hybrid Inverters</option>
                    <option value="accessories">Accessories</option>
                </select>
            </div>

            <!-- Products Moderation Table -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-850 dark:text-white font-bold">
                            <th class="p-4 w-16">Thumbnail</th>
                            <th class="p-4">Equipment Details</th>
                            <th class="p-4">Manufacturer</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Wholesale Price</th>
                            <th class="p-4">Submission Date</th>
                            <th class="p-4">Approval Status</th>
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
                        <tr v-for="prod in filteredProducts" :key="prod.id" class="hover:bg-slate-50/50 dark:hover:bg-solar-primary-dark/10 transition-all">
                            <td class="p-4">
                                <div class="h-10 w-10 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                                    <img :src="prod.image" :alt="prod.name" class="h-full w-full object-cover" />
                                </div>
                            </td>
                            <td class="p-4 font-bold text-slate-850 dark:text-white truncate max-w-[200px]" :title="prod.name">
                                {{ prod.name }}
                            </td>
                            <td class="p-4 font-semibold">{{ prod.vendorName }}</td>
                            <td class="p-4 uppercase tracking-wider font-semibold text-[10px] text-solar-primary dark:text-solar-primary-accent">
                                {{ prod.category }}
                            </td>
                            <td class="p-4 font-extrabold text-slate-850 dark:text-white">${{ prod.price.toLocaleString() }}</td>
                            <td class="p-4 font-mono">{{ prod.submittedDate }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="{
                                        'bg-solar-success/15 text-solar-success': prod.approvalStatus === 'approved',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300': prod.approvalStatus === 'pending',
                                        'bg-solar-danger/15 text-solar-danger': prod.approvalStatus === 'rejected'
                                    }"
                                >
                                    {{ prod.approvalStatus }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button class="p-2 rounded-lg border border-solar-primary/10 text-solar-primary hover:bg-solar-primary/10" title="Inspect Listing specs">
                                        <Eye class="h-3.5 w-3.5" />
                                    </button>
                                    
                                    <button 
                                        v-if="prod.approvalStatus !== 'approved'"
                                        @click="handleApproveProduct(prod.id)"
                                        class="px-2.5 py-1.5 rounded-lg bg-solar-primary hover:bg-solar-primary-active text-white text-[9px] font-bold uppercase tracking-wider transition-all"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        v-if="prod.approvalStatus !== 'rejected'"
                                        @click="handleRejectProduct(prod.id)"
                                        class="px-2.5 py-1.5 rounded-lg border border-solar-danger/25 text-solar-danger hover:bg-solar-danger/5 text-[9px] font-bold uppercase tracking-wider transition-all"
                                    >
                                        Reject
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
