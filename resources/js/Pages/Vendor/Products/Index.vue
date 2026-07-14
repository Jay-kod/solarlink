<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { mockProducts, Product } from '@/data/products'
import { Plus, Sparkles, Trash2, Edit, X, Upload, Search, AlertCircle } from 'lucide-vue-next'

// Initial list of 6 products
const productsList = ref<Product[]>([
    {
        ...mockProducts[0],
        id: 1,
        stock: 65 // green
    },
    {
        ...mockProducts[1],
        id: 2,
        stock: 12 // yellow
    },
    {
        ...mockProducts[2],
        id: 3,
        stock: 25 // yellow
    },
    {
        ...mockProducts[3],
        id: 4,
        stock: 5 // red
    },
    {
        ...mockProducts[4],
        id: 5,
        stock: 120 // green
    },
    {
        id: 6,
        name: 'HyperCharge MPPT 40A Controller',
        category: 'accessories',
        price: 99,
        rating: 4.4,
        reviewsCount: 15,
        image: 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&q=80&w=300',
        description: 'Mid-range MPPT charge controller perfect for smaller cabin or van setups.',
        specs: { Current: '40A', Tech: 'MPPT' },
        stock: 0, // out of stock (red)
        features: ['LCD display', 'Multiple protections']
    }
])

const isAddModalOpen = ref(false)
const searchQuery = ref('')

// Form states
const newName = ref('')
const newPrice = ref(0)
const newCategory = ref<'panels' | 'batteries' | 'inverters' | 'accessories'>('panels')
const newStock = ref(45)
const newDescription = ref('')

const handleAddProduct = () => {
    const freshItem: Product = {
        id: productsList.value.length + 1,
        name: newName.value || 'Custom Equipment X1',
        description: newDescription.value || 'OEM quality certified renewable energy components.',
        price: newPrice.value || 199,
        category: newCategory.value,
        image: 'https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&q=80&w=200',
        rating: 5.0,
        reviewsCount: 0,
        specs: {},
        stock: newStock.value || 0,
        features: []
    }
    
    productsList.value.push(freshItem)
    isAddModalOpen.value = false
    
    // reset form
    newName.value = ''
    newPrice.value = 0
    newCategory.value = 'panels'
    newStock.value = 45
    newDescription.value = ''
}

const handleDeleteProduct = (id: number) => {
    productsList.value = productsList.value.filter(p => p.id !== id)
}

const getStockClass = (qty: number) => {
    if (qty === 0) return 'text-solar-danger font-bold'
    if (qty < 10) return 'text-solar-danger font-bold'
    if (qty >= 10 && qty <= 50) return 'text-solar-warning font-bold'
    return 'text-solar-success font-bold'
}

const getStatusBadge = (qty: number) => {
    if (qty === 0) return { label: 'Out of Stock', class: 'bg-solar-danger/15 text-solar-danger' }
    if (qty < 15) return { label: 'Draft', class: 'bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400' }
    return { label: 'Active', class: 'bg-solar-success/15 text-solar-success' }
}

const filteredProducts = computed(() => {
    if (!searchQuery.value.trim()) return productsList.value
    return productsList.value.filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
})
</script>

<template>
    <Head title="SolarLink — Product Center" />

    <DashboardLayout role="vendor" title="Products Inventory">
        <div class="flex flex-col gap-8 text-left relative">
            
            <!-- Add Product Modal Overlay -->
            <div 
                v-if="isAddModalOpen"
                class="fixed inset-0 z-50 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in-up"
            >
                <div class="glass-card p-6 flex flex-col gap-5 w-full max-w-lg bg-white dark:bg-solar-bg-dark border border-solar-primary/15 rounded-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white flex items-center gap-2">
                            <Plus class="h-5 w-5 text-solar-primary" />
                            <span>Add New Wholesale Listing</span>
                        </h3>
                        <button @click="isAddModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5">
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="h-px bg-solar-primary/10 dark:bg-white/5"></div>

                    <form @submit.prevent="handleAddProduct" class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Product Name</label>
                            <input 
                                v-model="newName"
                                type="text"
                                required
                                placeholder="e.g. AeroVolt Hybrid Cell"
                                class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Category</label>
                                <select 
                                    v-model="newCategory"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                                >
                                    <option value="panels">Solar Panels</option>
                                    <option value="batteries">Battery Walls</option>
                                    <option value="inverters">Hybrid Inverters</option>
                                    <option value="accessories">Accessories</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Wholesale Price ($)</label>
                                <input 
                                    v-model.number="newPrice"
                                    type="number"
                                    required
                                    min="0"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Initial Stock Qty</label>
                                <input 
                                    v-model.number="newStock"
                                    type="number"
                                    required
                                    min="0"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                                />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Product Spec Sheet</label>
                                <div class="flex items-center justify-center border border-dashed border-slate-200 dark:border-white/5 h-10 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 cursor-pointer hover:bg-slate-100 dark:hover:bg-solar-primary-dark/30 transition-all text-slate-400">
                                    <Upload class="h-4 w-4 mr-2" />
                                    <span class="text-[10px] font-bold">Upload PDF</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Specifications / Description</label>
                            <textarea 
                                v-model="newDescription"
                                rows="3"
                                placeholder="Detail dimensions, certifications, degradation warranties..."
                                class="p-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                            ></textarea>
                        </div>

                        <button 
                            type="submit"
                            class="h-11 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow mt-2"
                        >
                            Publish Wholesale Listing
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Inventory Ledger</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Wholesale Catalog</h2>
                        <p class="text-xs text-slate-450 mt-0.5">Edit active items listed on the platform, manage stock levels, and review pricing schedules.</p>
                    </div>
                    <button 
                        @click="isAddModalOpen = true"
                        class="px-5 py-2.5 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow uppercase tracking-wider flex items-center gap-1.5"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Add Product</span>
                    </button>
                </div>
            </div>

            <!-- Search bar -->
            <div class="relative flex items-center max-w-sm">
                <Search class="absolute left-3.5 h-4 w-4 text-slate-400 pointer-events-none" />
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Search catalog by name..."
                    class="w-full h-11 pl-10 pr-4 rounded-xl bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-850 dark:text-white shadow-sm"
                />
            </div>

            <!-- Products Table -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-850 dark:text-white font-bold">
                            <th class="p-4 w-16">Image</th>
                            <th class="p-4">Product details</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Stock</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-if="filteredProducts.length === 0">
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <AlertCircle class="h-6 w-6 text-slate-300" />
                                    <span>No matching wholesale items found.</span>
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
                            <td class="p-4 uppercase tracking-wider font-semibold text-[10px] text-solar-primary dark:text-solar-primary-accent">
                                {{ prod.category }}
                            </td>
                            <td class="p-4 font-extrabold text-slate-850 dark:text-white">
                                ${{ prod.price.toLocaleString() }}
                            </td>
                            <td class="p-4">
                                <span :class="getStockClass(prod.stock)">
                                    {{ prod.stock }} units
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="getStatusBadge(prod.stock).class"
                                >
                                    {{ getStatusBadge(prod.stock).label }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button class="p-2 rounded-lg border border-solar-primary/10 text-solar-primary hover:bg-solar-primary/10" title="Edit Listing">
                                        <Edit class="h-3.5 w-3.5" />
                                    </button>
                                    <button 
                                        @click="handleDeleteProduct(prod.id)"
                                        class="p-2.5 rounded-lg border border-solar-danger/10 text-solar-danger hover:bg-solar-danger/10"
                                        title="Remove Listing"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
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
