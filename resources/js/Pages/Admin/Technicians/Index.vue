<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Wrench, Eye, CheckCircle2, XCircle, Sparkles, Star, AlertCircle, AlertTriangle } from 'lucide-vue-next'

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

const technicians = ref<TechProfile[]>([
    {
        id: 501,
        name: 'Marcus Vance',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150',
        license: 'NABCEP PV-9204921',
        experience: '5 Years',
        specialization: 'Battery Backups & Off-Grid Arrays',
        rating: 4.9,
        completedJobs: 82,
        status: 'verified'
    },
    {
        id: 502,
        name: 'Sarah Jenkins',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150',
        license: 'NABCEP PV-8104821',
        experience: '3 Years',
        specialization: 'Residential Microinverters',
        rating: 4.6,
        completedJobs: 34,
        status: 'pending'
    },
    {
        id: 503,
        name: 'Elena Rostova',
        avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150',
        license: 'NABCEP PV-7028192',
        experience: '6 Years',
        specialization: 'Commercial Grid-Tied Solar',
        rating: 4.85,
        completedJobs: 110,
        status: 'verified'
    },
    {
        id: 504,
        name: 'John Doe',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150',
        license: 'NABCEP PV-4492102',
        experience: '2 Years',
        specialization: 'General Cleaning & Calibration',
        rating: 4.2,
        completedJobs: 18,
        status: 'suspended'
    },
    {
        id: 505,
        name: 'Bruce Wayne',
        avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=150',
        license: 'NABCEP PV-0070007',
        experience: '10 Years',
        specialization: 'Megawatt Industrial Parks',
        rating: 5.0,
        completedJobs: 245,
        status: 'verified'
    }
])

const handleVerifyTech = (id: number) => {
    const tech = technicians.value.find(t => t.id === id)
    if (tech) {
        tech.status = 'verified'
    }
}

const handleSuspendTech = (id: number) => {
    const tech = technicians.value.find(t => t.id === id)
    if (tech) {
        tech.status = tech.status === 'suspended' ? 'pending' : 'suspended'
    }
}

const totalTechs = computed(() => technicians.value.length)
const verifiedCount = computed(() => technicians.value.filter(t => t.status === 'verified').length)
const pendingCount = computed(() => technicians.value.filter(t => t.status === 'pending').length)
const suspendedCount = computed(() => technicians.value.filter(t => t.status === 'suspended').length)
</script>

<template>
    <Head title="SolarLink — Field Audits" />

    <DashboardLayout role="admin" title="Technician Audits">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>License Verification Node</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Certified Field Engineers Queue</h2>
                <p class="text-xs text-slate-450 mt-0.5">Audit field technicians licenses, verify NABCEP compliance, and manage operational eligibility.</p>
            </div>

            <!-- KPI Cards Summary -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl text-left">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Total Registrations</span>
                    <h3 class="text-2xl font-extrabold text-slate-850 dark:text-white mt-1.5">{{ totalTechs }} Engineers</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">Active roster</p>
                </div>
                <div class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl text-left">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Verified & Active</span>
                    <h3 class="text-2xl font-extrabold text-solar-success mt-1.5">{{ verifiedCount }} Verified</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">NABCEP fully compliant</p>
                </div>
                <div class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl text-left">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Pending Review</span>
                    <h3 class="text-2xl font-extrabold text-amber-500 mt-1.5">{{ pendingCount }} Pending</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">Awaiting document audits</p>
                </div>
                <div class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl text-left">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Suspended access</span>
                    <h3 class="text-2xl font-extrabold text-solar-danger mt-1.5">{{ suspendedCount }} Suspended</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">Violations or expired licenses</p>
                </div>
            </div>

            <!-- Technicians Ledger Table -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-850 dark:text-white font-bold">
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
                                    <button class="p-2 rounded-lg border border-solar-primary/10 text-solar-primary hover:bg-solar-primary/10" title="Inspect Documents">
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
    </DashboardLayout>
</template>
