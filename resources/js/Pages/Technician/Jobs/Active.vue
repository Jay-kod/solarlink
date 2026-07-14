<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Calendar, Wrench, MapPin, User, CheckCircle2, XCircle, AlertCircle, Sparkles } from 'lucide-vue-next'

interface Job {
    id: number;
    client: string;
    avatar: string;
    service: string;
    address: string;
    date: string;
    time: string;
    pay: number;
    status: 'pending' | 'active' | 'completed';
    progress?: number;
}

const activeTab = ref<'all' | 'pending' | 'active' | 'completed'>('all')

const jobs = ref<Job[]>([
    {
        id: 101,
        client: 'Alice Johnson',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
        service: 'Panel Deep Wash & Dusting',
        address: '1225 Grid Access Road, SF',
        date: 'Today',
        time: '4:00 PM',
        pay: 140,
        status: 'pending'
    },
    {
        id: 102,
        client: 'Bob Robertson',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150',
        service: 'Microinverter Degradation Swap',
        address: '44 Bayview Lane, SF',
        date: 'Tomorrow',
        time: '9:00 AM',
        pay: 280,
        status: 'active',
        progress: 40
    },
    {
        id: 103,
        client: 'Sarah Jenkins',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150',
        service: 'Tesla Powerwall 2 Calibration',
        address: '228 Battery Point, East Bay',
        date: 'June 2nd',
        time: '11:00 AM',
        pay: 195,
        status: 'pending'
    },
    {
        id: 104,
        client: 'David Miller',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150',
        service: 'Emergency Charge Controller Fix',
        address: '89 Junction St, Peninsula',
        date: 'Today',
        time: '1:30 PM',
        pay: 320,
        status: 'active',
        progress: 85
    },
    {
        id: 105,
        client: 'Elena Rostova',
        avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150',
        service: 'Seasonal Grid Netting Setup',
        address: '775 Sunshine Ridge, Marin',
        date: 'June 5th',
        time: '8:30 AM',
        pay: 210,
        status: 'pending'
    },
    {
        id: 106,
        client: 'Gregory House',
        avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=150',
        service: '15kW Commercial Arrays Audit',
        address: '100 Medical Center Way, Oakland',
        date: 'Tomorrow',
        time: '2:30 PM',
        pay: 450,
        status: 'active',
        progress: 10
    },
    {
        id: 901,
        client: 'Clara Oswald',
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150',
        service: 'Residential Array Health Audit',
        address: '900 TARDIS Road, SF',
        date: 'May 20, 2026',
        time: '10:00 AM',
        pay: 180,
        status: 'completed'
    },
    {
        id: 902,
        client: 'Arthur Dent',
        avatar: 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?auto=format&fit=crop&q=80&w=150',
        service: 'Grounding Wire Replacement',
        address: '42 Towel Lane, East Bay',
        date: 'May 18, 2026',
        time: '1:00 PM',
        pay: 110,
        status: 'completed'
    }
])

const pendingCount = computed(() => jobs.value.filter(j => j.status === 'pending').length)
const activeCount = computed(() => jobs.value.filter(j => j.status === 'active').length)
const completedCount = computed(() => jobs.value.filter(j => j.status === 'completed').length)

const filteredJobs = computed(() => {
    if (activeTab.value === 'all') return jobs.value
    return jobs.value.filter(j => j.status === activeTab.value)
})

const handleAcceptJob = (id: number) => {
    const job = jobs.value.find(j => j.id === id)
    if (job) {
        job.status = 'active'
        job.progress = 0
    }
}

const handleRejectJob = (id: number) => {
    jobs.value = jobs.value.filter(j => j.id !== id)
}

const handleCompleteJob = (id: number) => {
    const job = jobs.value.find(j => j.id === id)
    if (job) {
        job.status = 'completed'
        job.progress = undefined
    }
}
</script>

<template>
    <Head title="SolarLink — Field Job Manager" />

    <DashboardLayout role="technician" title="Field Dispatch Queue">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Dispatch Operations</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Active Field Jobs</h2>
                <p class="text-xs text-slate-450 mt-0.5">Manage pending service dispatches, update work progress, and log client details.</p>
            </div>

            <!-- Tab Filters -->
            <div class="flex border-b border-solar-primary/10 dark:border-white/5 pb-px gap-4">
                <button 
                    @click="activeTab = 'all'"
                    class="pb-3 text-xs font-bold transition-all relative"
                    :class="activeTab === 'all' ? 'text-solar-primary border-b-2 border-b-solar-primary' : 'text-slate-400 hover:text-slate-650'"
                >
                    All Jobs ({{ jobs.length }})
                </button>
                <button 
                    @click="activeTab = 'pending'"
                    class="pb-3 text-xs font-bold transition-all relative"
                    :class="activeTab === 'pending' ? 'text-solar-warning border-b-2 border-b-solar-warning' : 'text-slate-400 hover:text-slate-650'"
                >
                    Pending dispatches ({{ pendingCount }})
                </button>
                <button 
                    @click="activeTab = 'active'"
                    class="pb-3 text-xs font-bold transition-all relative"
                    :class="activeTab === 'active' ? 'text-solar-primary border-b-2 border-b-solar-primary' : 'text-slate-400 hover:text-slate-650'"
                >
                    Active Work ({{ activeCount }})
                </button>
                <button 
                    @click="activeTab = 'completed'"
                    class="pb-3 text-xs font-bold transition-all relative"
                    :class="activeTab === 'completed' ? 'text-solar-success border-b-2 border-b-solar-success' : 'text-slate-400 hover:text-slate-650'"
                >
                    Archived history ({{ completedCount }})
                </button>
            </div>

            <!-- List Layout -->
            <div class="flex flex-col gap-4">
                <div v-if="filteredJobs.length === 0" class="glass-card p-10 flex flex-col items-center justify-center text-center gap-4 bg-white dark:bg-solar-bg-dark/40">
                    <AlertCircle class="h-8 w-8 text-slate-400" />
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white">No jobs in this category</h4>
                        <p class="text-xs text-slate-450 mt-1">Try toggling to a different status filter or refresh the queue.</p>
                    </div>
                </div>

                <div 
                    v-for="job in filteredJobs" 
                    :key="job.id"
                    class="glass-card p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border-l-4 transition-all duration-300 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl hover:shadow-solar"
                    :class="{
                        'border-l-amber-500': job.status === 'pending',
                        'border-l-solar-primary': job.status === 'active',
                        'border-l-slate-300 dark:border-l-white/20 opacity-70': job.status === 'completed'
                    }"
                >
                    <!-- Left Section: Details -->
                    <div class="flex items-start gap-4 flex-grow min-w-0">
                        <img :src="job.avatar" :alt="job.client" class="h-10 w-10 rounded-full object-cover border-2 border-solar-primary-light" />
                        <div class="min-w-0 flex-grow">
                            <span class="text-[8px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider"
                                :class="{
                                    'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300': job.status === 'pending',
                                    'bg-solar-primary-light text-solar-primary dark:bg-solar-primary-dark dark:text-solar-primary-accent': job.status === 'active',
                                    'bg-slate-100 text-slate-650 dark:bg-white/5 dark:text-slate-400': job.status === 'completed'
                                }"
                            >
                                {{ job.status }}
                            </span>
                            <h4 class="font-extrabold text-sm text-slate-800 dark:text-white mt-1.5 truncate">{{ job.service }}</h4>
                            
                            <div class="flex items-center gap-3.5 text-xs text-slate-500 dark:text-slate-400 mt-1 flex-wrap font-medium">
                                <span class="flex items-center gap-1"><User class="h-3.5 w-3.5 text-slate-400" /> {{ job.client }}</span>
                                <span class="flex items-center gap-1"><MapPin class="h-3.5 w-3.5 text-slate-400" /> {{ job.address }}</span>
                                <span class="flex items-center gap-1"><Calendar class="h-3.5 w-3.5 text-slate-400" /> {{ job.date }} @ {{ job.time }}</span>
                                <span class="font-extrabold text-solar-primary dark:text-solar-primary-accent">${{ job.pay }} payout</span>
                            </div>

                            <!-- In-Progress Bar -->
                            <div v-if="job.status === 'active' && job.progress !== undefined" class="w-full max-w-md mt-4 flex items-center gap-3">
                                <div class="flex-grow bg-slate-100 dark:bg-white/5 h-2 rounded-full overflow-hidden">
                                    <div class="bg-solar-primary h-full rounded-full transition-all duration-500" :style="{ width: job.progress + '%' }"></div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ job.progress }}% done</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Actions -->
                    <div class="flex items-center gap-2.5 w-full md:w-auto justify-end shrink-0">
                        <template v-if="job.status === 'pending'">
                            <button 
                                @click="handleAcceptJob(job.id)"
                                class="px-4 py-2 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold transition-all shadow btn-glow"
                            >
                                Accept Dispatch
                            </button>
                            <button 
                                @click="handleRejectJob(job.id)"
                                class="px-4 py-2 rounded-xl border border-solar-danger/25 text-solar-danger hover:bg-solar-danger/5 text-xs font-bold transition-all"
                            >
                                Decline
                            </button>
                        </template>
                        <template v-else-if="job.status === 'active'">
                            <button 
                                @click="handleCompleteJob(job.id)"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow"
                            >
                                Mark Completed
                            </button>
                        </template>
                        <template v-else>
                            <span class="text-xs text-slate-400 font-semibold italic flex items-center gap-1">
                                <CheckCircle2 class="h-4 w-4 text-solar-success" />
                                <span>Archived</span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>
