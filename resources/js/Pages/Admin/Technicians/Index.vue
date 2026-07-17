<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Wrench, Eye, CheckCircle2, XCircle, Sparkles, Star, AlertCircle, AlertTriangle, X, MapPin, Clock, DollarSign, Award } from 'lucide-vue-next'

interface TechProfile {
    id: number;
    name: string;
    avatar: string;
    license: string;
    experience: string;
    specialization: string;
    rating: number;
    completedJobs: number;
    status: 'verified' | 'pending' | 'suspended';
}

const props = defineProps<{
    technicians?: TechProfile[]
}>()

const technicians = ref<TechProfile[]>(props.technicians || [])

import { router } from '@inertiajs/vue3'

const handleVerifyTech = (id: number) => {
    router.patch(`/admin/technicians/${id}/status`, { status: 'verified' }, {
        onSuccess: () => {
            const tech = technicians.value.find(t => t.id === id)
            if (tech) tech.status = 'verified'
        }
    })
}

const handleSuspendTech = (id: number) => {
    const tech = technicians.value.find(t => t.id === id)
    if (tech) {
        const newStatus = tech.status === 'suspended' ? 'pending' : 'suspended'
        router.patch(`/admin/technicians/${id}/status`, { status: newStatus }, {
            onSuccess: () => {
                tech.status = newStatus
            }
        })
    }
}

const totalTechs = computed(() => technicians.value.length)
const verifiedCount = computed(() => technicians.value.filter(t => t.status === 'verified').length)
const pendingCount = computed(() => technicians.value.filter(t => t.status === 'pending').length)
const suspendedCount = computed(() => technicians.value.filter(t => t.status === 'suspended').length)

// Inspect modal
const selectedTech = ref<TechProfile | null>(null)
const showInspectModal = ref(false)

const openInspect = (tech: TechProfile) => {
    selectedTech.value = tech
    showInspectModal.value = true
}

const closeInspect = () => {
    showInspectModal.value = false
    selectedTech.value = null
}
</script>

<template>
    <Head title="SolarLink — Field Audits" />

    <DashboardLayout role="admin" title="Technician Audits">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-extrabold text-[9px] uppercase tracking-widest border border-emerald-500/20 shadow-sm animate-pulse-slow">
                    <Sparkles class="h-3 w-3" />
                    <span>License Verification Node</span>
                </div>
                <h2 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-400 tracking-tight mt-1">Certified Field Engineers Queue</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">Audit field technicians licenses, verify NABCEP compliance, and manage operational eligibility.</p>
            </div>

            <!-- KPI Cards Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card p-5 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-xl border border-emerald-500/10 dark:border-white/5 rounded-2xl text-left shadow-lg transition-transform hover:-translate-y-1">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Total Registrations</span>
                    <h3 class="text-2xl font-black text-slate-850 dark:text-white mt-1.5">{{ totalTechs }} Engineers</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-extrabold">Active roster</p>
                </div>
                <div class="glass-card p-5 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-xl border border-emerald-500/10 dark:border-white/5 rounded-2xl text-left shadow-lg transition-transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <CheckCircle2 class="h-24 w-24 text-emerald-500" />
                    </div>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider relative z-10">Verified & Active</span>
                    <h3 class="text-2xl font-black text-emerald-500 mt-1.5 relative z-10">{{ verifiedCount }} Verified</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-extrabold relative z-10">NABCEP fully compliant</p>
                </div>
                <div class="glass-card p-5 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-xl border border-emerald-500/10 dark:border-white/5 rounded-2xl text-left shadow-lg transition-transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <AlertTriangle class="h-24 w-24 text-amber-500" />
                    </div>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider relative z-10">Pending Review</span>
                    <h3 class="text-2xl font-black text-amber-500 mt-1.5 relative z-10">{{ pendingCount }} Pending</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-extrabold relative z-10">Awaiting document audits</p>
                </div>
                <div class="glass-card p-5 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-xl border border-emerald-500/10 dark:border-white/5 rounded-2xl text-left shadow-lg transition-transform hover:-translate-y-1 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 opacity-10">
                        <XCircle class="h-24 w-24 text-red-500" />
                    </div>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider relative z-10">Suspended access</span>
                    <h3 class="text-2xl font-black text-red-500 mt-1.5 relative z-10">{{ suspendedCount }} Suspended</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-extrabold relative z-10">Violations or expired licenses</p>
                </div>
            </div>

            <!-- Technicians Ledger Table -->
            <div class="glass-card overflow-hidden bg-white/60 dark:bg-[#0B0F19]/70 backdrop-blur-2xl border border-emerald-500/20 dark:border-white/5 rounded-3xl shadow-[0_0_40px_-15px_rgba(16,185,129,0.2)]">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-emerald-500/5 border-b border-emerald-500/10 text-left text-slate-850 dark:text-white font-black">
                            <th class="p-4">Technician</th>
                            <th class="p-4">NABCEP License</th>
                            <th class="p-4">Exp</th>
                            <th class="p-4">Specialization</th>
                            <th class="p-4">Rating</th>
                            <th class="p-4">Jobs</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Verification Controls</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-for="tech in technicians" :key="tech.id" class="hover:bg-slate-50/50 dark:hover:bg-solar-primary-dark/10 transition-all">
                            <td class="p-4 flex items-center gap-3">
                                <img :src="tech.avatar" :alt="tech.name" class="h-9 w-9 rounded-full object-cover border border-solar-primary/10" />
                                <span class="font-bold text-slate-850 dark:text-white">{{ tech.name }}</span>
                            </td>
                            <td class="p-4 font-mono font-bold text-slate-800 dark:text-white">{{ tech.license }}</td>
                            <td class="p-4 font-semibold text-slate-450">{{ tech.experience }}</td>
                            <td class="p-4 truncate max-w-[150px]" :title="tech.specialization">{{ tech.specialization }}</td>
                            <td class="p-4">
                                <div class="flex items-center text-amber-500 font-bold gap-0.5">
                                    <Star class="h-3.5 w-3.5 fill-current" />
                                    <span>{{ tech.rating }}</span>
                                </div>
                            </td>
                            <td class="p-4 font-semibold">{{ tech.completedJobs }} jobs</td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="{
                                        'bg-solar-success/15 text-solar-success': tech.status === 'verified',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300': tech.status === 'pending',
                                        'bg-solar-danger/15 text-solar-danger': tech.status === 'suspended'
                                    }"
                                >
                                    {{ tech.status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button @click="openInspect(tech)" class="p-2 rounded-lg border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/10 transition-all" title="Inspect Documents">
                                        <Eye class="h-3.5 w-3.5" />
                                    </button>
                                    
                                    <button 
                                        v-if="tech.status === 'pending'"
                                        @click="handleVerifyTech(tech.id)"
                                        class="px-3 py-1.5 rounded-lg bg-solar-primary hover:bg-solar-primary-active text-white text-[10px] font-bold uppercase tracking-wider transition-all shadow"
                                    >
                                        Verify License
                                    </button>
                                    
                                    <button 
                                        v-else
                                        @click="handleSuspendTech(tech.id)"
                                        class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all"
                                        :class="tech.status === 'verified' ? 'border border-solar-danger/25 text-solar-danger hover:bg-solar-danger/5' : 'bg-solar-success text-white hover:bg-emerald-650'"
                                    >
                                        {{ tech.status === 'verified' ? 'Suspend' : 'Re-verify' }}
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
                <div v-if="showInspectModal && selectedTech" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeInspect"></div>
                    
                    <!-- Modal Card -->
                    <div class="relative w-full max-w-lg bg-white/95 dark:bg-[#0B0F19]/95 backdrop-blur-2xl border border-emerald-500/30 rounded-3xl shadow-[0_0_60px_-15px_rgba(16,185,129,0.4)] overflow-hidden animate-scale-in">
                        
                        <!-- Header gradient bar -->
                        <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>
                        
                        <!-- Close button -->
                        <button @click="closeInspect" class="absolute top-5 right-5 p-2 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-500 dark:text-slate-400 transition-all z-10">
                            <X class="h-4 w-4" />
                        </button>

                        <div class="p-8">
                            <!-- Profile Header -->
                            <div class="flex items-center gap-5 mb-8">
                                <img :src="selectedTech.avatar" :alt="selectedTech.name" class="h-20 w-20 rounded-2xl object-cover border-2 border-emerald-500/30 shadow-lg bg-[#0B0F19]" />
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ selectedTech.name }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-1">Field Engineer · ID #{{ selectedTech.id }}</p>
                                    <span class="inline-block mt-2 px-3 py-0.5 rounded-full font-black text-[8px] uppercase tracking-widest"
                                        :class="{
                                            'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400': selectedTech.status === 'verified',
                                            'bg-amber-500/15 text-amber-600 dark:text-amber-400': selectedTech.status === 'pending',
                                            'bg-red-500/15 text-red-600 dark:text-red-400': selectedTech.status === 'suspended'
                                        }"
                                    >{{ selectedTech.status }}</span>
                                </div>
                            </div>

                            <!-- Detail Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div class="bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Award class="h-4 w-4 text-emerald-500" />
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">License</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white font-mono">{{ selectedTech.license }}</p>
                                </div>
                                <div class="bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Clock class="h-4 w-4 text-blue-500" />
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Experience</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">{{ selectedTech.experience }}</p>
                                </div>
                                <div class="bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Star class="h-4 w-4 text-amber-500 fill-current" />
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Rating</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">{{ selectedTech.rating }} / 5.0</p>
                                </div>
                                <div class="bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <Wrench class="h-4 w-4 text-indigo-500" />
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Completed Jobs</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-white">{{ selectedTech.completedJobs }} jobs</p>
                                </div>
                            </div>

                            <!-- Specialization -->
                            <div class="bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 rounded-2xl p-4 mb-8">
                                <span class="text-[9px] font-black uppercase tracking-wider text-slate-400">Specialization</span>
                                <p class="text-xs font-bold text-slate-800 dark:text-white mt-2">{{ selectedTech.specialization }}</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-3">
                                <button 
                                    v-if="selectedTech.status === 'pending'"
                                    @click="handleVerifyTech(selectedTech.id); closeInspect()"
                                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-black uppercase tracking-wider transition-all shadow-lg"
                                >
                                    ✓ Verify License
                                </button>
                                <button 
                                    v-if="selectedTech.status !== 'suspended'"
                                    @click="handleSuspendTech(selectedTech.id); closeInspect()"
                                    class="flex-1 py-3 rounded-2xl border-2 border-red-500/30 text-red-500 hover:bg-red-500/10 text-xs font-black uppercase tracking-wider transition-all"
                                >
                                    ✕ Suspend
                                </button>
                                <button 
                                    v-if="selectedTech.status === 'suspended'"
                                    @click="handleSuspendTech(selectedTech.id); closeInspect()"
                                    class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-black uppercase tracking-wider transition-all shadow-lg"
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
