<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Sparkles, Store, CheckCircle2, Save, Upload, Star, Eye, TrendingUp, Layers, ShoppingBag } from 'lucide-vue-next'

const formSubmitted = ref(false)
const selectedTheme = ref('blue')

const storeName = ref('EcoGrid Direct')
const tagline = ref('Pioneering High-Efficiency Tier 1 Photovoltaic Modules')
const description = ref('EcoGrid Direct is a premier wholesale supplier of monocrystalline panels, grid-tied hybrid inverters, and LFP battery cabinets. We service commercial developers and solar installer networks globally.')
const email = ref('logistics@ecogrid-direct.com')
const phone = ref('+1 (800) 555-8822')
const moq = ref(5)
const shippingSla = ref('3-5 Business Days')
const returnPolicy = ref('30-Day Return (15% restocking fee)')

const themes = [
    { id: 'blue', name: 'Ocean Blue', primary: 'bg-blue-600', text: 'text-blue-600' },
    { id: 'green', name: 'Forest Green', primary: 'bg-emerald-600', text: 'text-emerald-600' },
    { id: 'orange', name: 'Solar Amber', primary: 'bg-amber-500', text: 'text-amber-500' },
    { id: 'purple', name: 'Hybrid Violet', primary: 'bg-purple-600', text: 'text-purple-600' }
]

const stats = [
    { title: 'Total Store Views', value: '12.4k', icon: Eye, desc: 'Last 30 days' },
    { title: 'Conversion Rate', value: '3.8%', icon: TrendingUp, desc: 'Above wholesale average' },
    { title: 'Store Rating', value: '4.7', icon: Star, desc: 'Based on 45 reviews' },
    { title: 'Products Listed', value: '18 Items', icon: ShoppingBag, desc: 'Active in catalog' }
]

const handleSaveStore = () => {
    formSubmitted.value = true
    setTimeout(() => {
        formSubmitted.value = false
    }, 2500)
}
</script>

<template>
    <Head title="SolarLink — Custom Storefront" />

    <DashboardLayout role="vendor" title="Store Settings">
        <div class="flex flex-col gap-8 text-left max-w-5xl mx-auto relative">
            
            <!-- Success indicator modal overlay -->
            <div 
                v-if="formSubmitted"
                class="fixed inset-0 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center animate-fade-in-up"
            >
                <div class="glass-card p-8 flex flex-col items-center gap-4 text-center max-w-sm bg-white dark:bg-solar-bg-dark border border-solar-primary/15 rounded-2xl shadow-solar">
                    <div class="h-16 w-16 rounded-full bg-solar-success/10 text-solar-success flex items-center justify-center pulse-glow">
                        <CheckCircle2 class="h-10 w-10" />
                    </div>
                    <div>
                        <h3 class="font-extrabold text-xl text-slate-800 dark:text-white">Storefront Profile Synchronized!</h3>
                        <p class="text-xs text-slate-500 mt-1">Changes are live in the wholesale marketplace storefront catalog.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Brand Hub</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Custom Manufacturer Storefront</h2>
                <p class="text-xs text-slate-450 mt-0.5">Customize your brand description, minimum order quantity, logistics contracts, and showcase themes.</p>
            </div>

            <!-- Analytics widgets -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div 
                    v-for="st in stats" 
                    :key="st.title"
                    class="glass-card p-5 flex items-center justify-between bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl hover:shadow-solar transition-all"
                >
                    <div class="text-left">
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ st.title }}</span>
                        <h3 class="text-2xl font-extrabold text-slate-850 dark:text-white mt-1">{{ st.value }}</h3>
                        <p class="text-[9px] text-slate-500 mt-0.5 font-semibold uppercase tracking-wider">{{ st.desc }}</p>
                    </div>
                    <div class="p-2.5 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark/80 text-solar-primary dark:text-solar-primary-accent">
                        <component :is="st.icon" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Store Editor Form (Left 8 Columns) -->
                <div class="lg:col-span-7 glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl">
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Storefront Metadata</h3>
                    <div class="h-px bg-solar-primary/10 dark:bg-white/5"></div>

                    <form @submit.prevent="handleSaveStore" class="flex flex-col gap-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Manufacturer Name</label>
                                <input v-model="storeName" type="text" required class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Brand Tagline</label>
                                <input v-model="tagline" type="text" required class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Corporate Bio / Description</label>
                            <textarea v-model="description" rows="3" class="p-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Logistics Contact Email</label>
                                <input v-model="email" type="email" required class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Distribution Hotline</label>
                                <input v-model="phone" type="text" required class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white" />
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Min. Order Qty (MOQ)</label>
                                <input v-model.number="moq" type="number" required class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Shipping SLA</label>
                                <input v-model="shippingSla" type="text" required class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Return Policy</label>
                                <select v-model="returnPolicy" class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white">
                                    <option value="30-Day Return (15% restocking fee)">30-Day Return (15% fee)</option>
                                    <option value="14-Day Free Replacement">14-Day Free Replacement</option>
                                    <option value="No Returns - Final Wholesale Sale">No Returns - Final Sale</option>
                                </select>
                            </div>
                        </div>

                        <!-- Brand color presets -->
                        <div class="flex flex-col gap-2 mt-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Brand Showcase Color</label>
                            <div class="flex gap-4">
                                <button 
                                    v-for="th in themes" 
                                    :key="th.id"
                                    type="button"
                                    @click="selectedTheme = th.id"
                                    class="px-3.5 py-2 rounded-xl text-[10px] font-bold uppercase transition-all flex items-center gap-1.5 border"
                                    :class="selectedTheme === th.id 
                                        ? 'border-solar-primary text-slate-800 dark:text-white bg-solar-primary/5' 
                                        : 'border-slate-200 dark:border-white/5 text-slate-450 hover:bg-slate-50'"
                                >
                                    <span class="h-3 w-3 rounded-full shrink-0" :class="th.primary"></span>
                                    <span>{{ th.name }}</span>
                                </button>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            class="h-12 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow mt-3"
                        >
                            <Save class="h-4 w-4" />
                            <span>Save Store Profile</span>
                        </button>
                    </form>
                </div>

                <!-- Store Preview Component (Right 5 Columns) -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    <div class="glass-card bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl overflow-hidden shadow-solar text-left">
                        <!-- Banner -->
                        <div class="h-28 bg-gradient-to-r relative" :class="[
                            selectedTheme === 'blue' ? 'from-blue-650 to-indigo-650' : '',
                            selectedTheme === 'green' ? 'from-emerald-600 to-teal-700' : '',
                            selectedTheme === 'orange' ? 'from-amber-500 to-orange-600' : '',
                            selectedTheme === 'purple' ? 'from-purple-600 to-indigo-700' : ''
                        ]">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <span class="absolute top-3 right-3 text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-white/20 backdrop-blur text-white">
                                PREVIEW MODEL
                            </span>
                        </div>

                        <!-- Brand Header -->
                        <div class="p-6 relative pt-0">
                            <!-- Logo Avatar Offset -->
                            <div class="h-16 w-16 bg-white dark:bg-solar-bg-dark rounded-2xl shadow border border-slate-100 dark:border-white/5 p-2.5 flex items-center justify-center -mt-8 relative z-10 overflow-hidden">
                                <Store class="h-9 w-9 text-solar-primary" :class="[
                                    selectedTheme === 'blue' ? 'text-blue-600' : '',
                                    selectedTheme === 'green' ? 'text-emerald-600' : '',
                                    selectedTheme === 'orange' ? 'text-amber-500' : '',
                                    selectedTheme === 'purple' ? 'text-purple-600' : ''
                                ]" />
                            </div>

                            <div class="mt-4 flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-extrabold text-base text-slate-850 dark:text-white truncate">{{ storeName || 'Brand Name' }}</h3>
                                    <div class="flex items-center text-amber-500 gap-0.5 text-xs font-bold shrink-0">
                                        <Star class="h-3.5 w-3.5 fill-current" />
                                        <span>4.7</span>
                                    </div>
                                </div>
                                <p class="text-[10px] text-solar-primary font-bold uppercase tracking-wider" :class="[
                                    selectedTheme === 'blue' ? 'text-blue-600 dark:text-blue-400' : '',
                                    selectedTheme === 'green' ? 'text-emerald-600 dark:text-emerald-400' : '',
                                    selectedTheme === 'orange' ? 'text-amber-500 dark:text-amber-400' : '',
                                    selectedTheme === 'purple' ? 'text-purple-600 dark:text-purple-400' : ''
                                ]">{{ tagline || 'Brand Tagline' }}</p>
                                <p class="text-xs text-slate-450 mt-2.5 leading-relaxed">{{ description || 'Company description.' }}</p>
                            </div>

                            <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-4"></div>

                            <!-- Logistics Details -->
                            <div class="grid grid-cols-2 gap-4 text-xs">
                                <div>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Moq threshold</span>
                                    <p class="font-bold text-slate-800 dark:text-white mt-0.5">{{ moq }} Wholesale units</p>
                                </div>
                                <div>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Avg Shipping SLA</span>
                                    <p class="font-bold text-slate-800 dark:text-white mt-0.5">{{ shippingSla }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>
