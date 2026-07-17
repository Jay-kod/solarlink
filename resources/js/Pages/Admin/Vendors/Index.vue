<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Store, Eye, CheckCircle2, Sparkles, X, Building2, FileText, MapPin } from 'lucide-vue-next'

import { router } from '@inertiajs/vue3'

interface VendorProfile {
    id: number;
    name: string;
    vat: string;
    headquarters: string;
    email: string;
    joined: string;
    status: 'verified' | 'pending' | 'suspended';
}

const props = defineProps<{
    vendors?: VendorProfile[]
}>()

const vendors = ref<VendorProfile[]>(props.vendors || [])

const handleVerifyVendor = (id: number) => {
    router.patch(`/admin/vendors/${id}/status`, { status: 'verified' }, {
        onSuccess: () => {
            const vendor = vendors.value.find(v => v.id === id)
            if (vendor) vendor.status = 'verified'
        }
    })
}

const handleSuspendVendor = (id: number) => {
    const vendor = vendors.value.find(v => v.id === id)
    if (vendor) {
        const newStatus = vendor.status === 'suspended' ? 'pending' : 'suspended'
        router.patch(`/admin/vendors/${id}/status`, { status: newStatus }, {
            onSuccess: () => {
                vendor.status = newStatus
            }
        })
    }
}

// Inspect modal state
const selectedVendor = ref<VendorProfile | null>(null)
const showInspectModal = ref(false)

const openInspect = (vendor: VendorProfile) => {
    selectedVendor.value = vendor
    showInspectModal.value = true
}

const closeInspect = () => {
    showInspectModal.value = false
    selectedVendor.value = null
}
</script>

<template>
    <Head title="SolarLink — Supplier Moderation" />

    <DashboardLayout role="admin" title="Vendor Controls">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 font-extrabold text-[9px] uppercase tracking-widest border border-blue-500/20 shadow-sm animate-pulse-slow">
                    <Sparkles class="h-3 w-3" />
                    <span>Supplier Moderation Hub</span>
                </div>
                <h2 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500 dark:from-blue-400 dark:to-indigo-400 tracking-tight mt-1">Wholesale Supplier Registry</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">Approve direct-to-marketplace wholesale manufacturers, inspect tax certificates, and audit parts logs.</p>
            </div>

            <!-- Table list -->
            <div class="glass-card overflow-hidden bg-white/60 dark:bg-[#0B0F19]/70 backdrop-blur-2xl border border-blue-500/20 dark:border-white/5 rounded-3xl shadow-[0_0_40px_-15px_rgba(59,130,246,0.2)]">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-blue-500/5 border-b border-blue-500/10 text-left text-slate-850 dark:text-white font-black">
                            <th class="p-4">Brand / Company</th>
                            <th class="p-4">Business VAT ID</th>
                            <th class="p-4">Headquarters</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-500/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-for="v in vendors" :key="v.id" class="hover:bg-slate-50/50 dark:hover:bg-blue-900/10 transition-all">
                            <td class="p-4 font-bold text-slate-800 dark:text-white">{{ v.name }}</td>
                            <td class="p-4 font-mono font-bold text-slate-800 dark:text-white">{{ v.vat }}</td>
                            <td class="p-4 font-semibold text-slate-450">{{ v.headquarters }}</td>
                            <td class="p-4">
                                <span 
                                    class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400': v.status === 'verified',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300': v.status === 'pending',
                                        'bg-red-500/15 text-red-600 dark:text-red-400': v.status === 'suspended'
                                    }"
                                >
                                    {{ v.status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button @click="openInspect(v)" class="px-3 py-1.5 flex items-center gap-1.5 rounded-lg border border-blue-500/20 text-blue-600 dark:text-blue-400 hover:bg-blue-500/10 font-bold text-[10px] uppercase tracking-wider transition-all" title="Inspect Vendor Details">
                                        <Eye class="h-3.5 w-3.5" />
                                        <span>Details</span>
                                    </button>
                                    
                                    <button 
                                        v-if="v.status === 'pending'"
                                        @click="handleVerifyVendor(v.id)"
                                        class="px-3 py-1.5 rounded-lg bg-blue-500 hover:bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider transition-all shadow"
                                    >
                                        Verify Vendor
                                    </button>
                                    
                                    <button 
                                        v-else
                                        @click="handleSuspendVendor(v.id)"
                                        class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all"
                                        :class="v.status === 'verified' ? 'border border-red-500/25 text-red-500 hover:bg-red-500/5' : 'bg-emerald-500 text-white hover:bg-emerald-600 shadow'"
                                    >
                                        {{ v.status === 'verified' ? 'Suspend' : 'Re-verify' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Inspect Modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showInspectModal && selectedVendor" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeInspect"></div>
                    
                    <!-- Modal Card -->
                    <div class="relative w-full max-w-lg bg-white/95 dark:bg-[#0B0F19]/95 backdrop-blur-2xl border border-blue-500/30 rounded-3xl shadow-[0_0_60px_-15px_rgba(59,130,246,0.4)] overflow-hidden animate-scale-in">
                        
                        <!-- Header gradient bar -->
                        <div class="h-1.5 bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500"></div>
                        
                        <!-- Close button -->
                        <button @click="closeInspect" class="absolute top-5 right-5 p-2 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-500 dark:text-slate-400 transition-all z-10">
                            <X class="h-4 w-4" />
                        </button>

                        <div class="p-8">
                            <!-- Profile Header -->
                            <div class="flex items-center gap-5 mb-8">
                                <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-blue-500/20 to-indigo-500/20 border-2 border-blue-500/30 shadow-lg flex items-center justify-center text-blue-500">
                                    <Store class="h-10 w-10" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ selectedVendor.name }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">Vendor ID #{{ selectedVendor.id }}</p>
                                    <span class="inline-block mt-2 px-3 py-0.5 rounded-full font-black text-[8px] uppercase tracking-widest"
                                        :class="{
                                            'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400': selectedVendor.status === 'verified',
                                            'bg-amber-500/15 text-amber-600 dark:text-amber-400': selectedVendor.status === 'pending',
                                            'bg-red-500/15 text-red-600 dark:text-red-400': selectedVendor.status === 'suspended'
                                        }"
                                    >{{ selectedVendor.status }}</span>
                                </div>
                            </div>

                            <!-- Detail Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div class="col-span-2 bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Building2 class="h-4 w-4 text-indigo-500" />
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Headquarters / Address</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">{{ selectedVendor.headquarters }}</p>
                                </div>
                                <div class="bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <FileText class="h-4 w-4 text-blue-500" />
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">VAT / Tax ID</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white font-mono">{{ selectedVendor.vat }}</p>
                                </div>
                                <div class="bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Store class="h-4 w-4 text-purple-500" />
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Contact Email</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">{{ selectedVendor.email }}</p>
                                </div>
                                <div class="col-span-2 bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4 flex items-center justify-between">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Member Since</span>
                                    <span class="text-xs font-bold text-slate-800 dark:text-white">{{ selectedVendor.joined }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3">
                                <button 
                                    v-if="selectedVendor.status === 'pending'"
                                    @click="handleVerifyVendor(selectedVendor.id); closeInspect()"
                                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-700 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-wider transition-all shadow-lg"
                                >
                                    ✓ Verify Vendor
                                </button>
                                <button 
                                    v-if="selectedVendor.status !== 'suspended'"
                                    @click="handleSuspendVendor(selectedVendor.id); closeInspect()"
                                    class="flex-1 py-3 rounded-2xl border-2 border-red-500/30 text-red-500 hover:bg-red-500/10 text-xs font-black uppercase tracking-wider transition-all"
                                >
                                    ✕ Suspend
                                </button>
                                <button 
                                    v-if="selectedVendor.status === 'suspended'"
                                    @click="handleSuspendVendor(selectedVendor.id); closeInspect()"
                                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-blue-700 hover:to-indigo-600 text-white text-xs font-black uppercase tracking-wider transition-all shadow-lg"
                                >
                                    ↻ Re-verify
                                </button>
                                <button 
                                    @click="closeInspect"
                                    class="px-6 py-3 rounded-2xl bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 text-xs font-black uppercase tracking-wider transition-all"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </DashboardLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@keyframes scale-in {
    from {
        transform: scale(0.92);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
</style>
