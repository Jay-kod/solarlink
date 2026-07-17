<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { 
    ClipboardList, Wrench, CheckCircle2, AlertTriangle, AlertCircle, 
    Calendar, MapPin, DollarSign, Plug, Zap, Clock, User
} from 'lucide-vue-next'

interface Ticket {
    id: number;
    customer: string;
    issueType: string;
    severity: 'low' | 'medium' | 'high' | 'critical';
    status: 'open' | 'assigned' | 'in_progress' | 'resolved' | 'paid';
    paymentStatus: 'unpaid' | 'paid';
    location: string;
    appliance: string | null;
    estimatedCost: number;
    createdAt: string;
}

const props = defineProps<{ requests: Ticket[] }>()

// Filter state
const activeTab = ref('all')
const tabs = [
    { id: 'all', name: 'All Requests' },
    { id: 'open', name: 'Open' },
    { id: 'assigned', name: 'Assigned to me' },
    { id: 'in_progress', name: 'In Progress' },
    { id: 'resolved', name: 'Resolved' },
]

const filteredRequests = computed(() => {
    if (activeTab.value === 'all') return props.requests
    return props.requests.filter(r => r.status === activeTab.value)
})

// Stats
const openCount = computed(() => props.requests.filter(r => r.status === 'open').length)
const progressCount = computed(() => props.requests.filter(r => r.status === 'in_progress').length)
const resolvedCount = computed(() => props.requests.filter(r => r.status === 'resolved' || r.status === 'paid').length)

const getSeverityStyles = (severity: string) => {
    switch(severity) {
        case 'critical': return 'bg-red-500/10 text-red-500 border-red-500/20'
        case 'high': return 'bg-orange-500/10 text-orange-500 border-orange-500/20'
        case 'medium': return 'bg-amber-500/10 text-amber-500 border-amber-500/20'
        default: return 'bg-blue-500/10 text-blue-500 border-blue-500/20'
    }
}

const getSeverityIcon = (severity: string) => {
    switch(severity) {
        case 'critical': return AlertCircle
        case 'high': return AlertTriangle
        default: return Zap
    }
}

const getStatusBadge = (status: string) => {
    switch(status) {
        case 'open': return 'bg-slate-500/10 text-slate-500'
        case 'assigned': return 'bg-blue-500/10 text-blue-500'
        case 'in_progress': return 'bg-amber-500/10 text-amber-500'
        case 'resolved': 
        case 'paid': return 'bg-emerald-500/10 text-emerald-500'
        default: return 'bg-slate-500/10 text-slate-500'
    }
}
</script>

<template>
    <Head title="Maintenance Queue | Technician" />

    <DashboardLayout role="technician" title="Maintenance Queue">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Stats Header -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-card p-5 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 h-24 w-24 bg-slate-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-slate-500/10 flex items-center justify-center shrink-0">
                            <ClipboardList class="h-6 w-6 text-slate-500" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Open Requests</p>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ openCount }}</h3>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-5 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 h-24 w-24 bg-amber-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0">
                            <Wrench class="h-6 w-6 text-amber-500" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">In Progress</p>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ progressCount }}</h3>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-5 relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 h-24 w-24 bg-emerald-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-xl bg-emerald-500/10 flex items-center justify-center shrink-0">
                            <CheckCircle2 class="h-6 w-6 text-emerald-500" />
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Resolved</p>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ resolvedCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex overflow-x-auto hide-scrollbar gap-2 pb-2">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="px-4 py-2 rounded-full text-xs font-semibold transition-all duration-200 whitespace-nowrap"
                    :class="activeTab === tab.id 
                        ? 'bg-solar-primary text-white shadow-md shadow-solar-primary/20' 
                        : 'bg-white dark:bg-[#151B2E] text-slate-500 hover:text-slate-800 dark:hover:text-white border border-slate-200 dark:border-white/5'"
                >
                    {{ tab.name }}
                </button>
            </div>

            <!-- Ticket Grid -->
            <div class="relative min-h-[400px]">
                <TransitionGroup 
                    name="list" 
                    tag="div" 
                    class="grid grid-cols-1 lg:grid-cols-2 gap-4 relative"
                >
                    <div 
                        v-for="ticket in filteredRequests" 
                        :key="ticket.id"
                        class="glass-card p-0 overflow-hidden flex flex-col transition-all duration-300 hover:shadow-lg hover:border-solar-primary/30 group"
                    >
                        <!-- Card Header -->
                        <div class="p-5 border-b border-slate-100 dark:border-white/5 relative bg-slate-50/50 dark:bg-white/[.01]">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex gap-3">
                                    <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0 border" :class="getSeverityStyles(ticket.severity)">
                                        <component :is="getSeverityIcon(ticket.severity)" class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">#{{ String(ticket.id).padStart(4, '0') }}</span>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider" :class="getStatusBadge(ticket.status)">
                                                {{ ticket.status.replace('_', ' ') }}
                                            </span>
                                        </div>
                                        <h3 class="font-bold text-slate-800 dark:text-white">{{ ticket.issueType }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-grow grid grid-cols-2 gap-y-4 gap-x-2">
                            <div class="flex items-start gap-2.5">
                                <User class="h-4 w-4 text-slate-400 mt-0.5 shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase">Customer</p>
                                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate" :title="ticket.customer">{{ ticket.customer }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-2.5">
                                <MapPin class="h-4 w-4 text-slate-400 mt-0.5 shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase">Location</p>
                                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate" :title="ticket.location">{{ ticket.location }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <Plug class="h-4 w-4 text-slate-400 mt-0.5 shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase">Appliance</p>
                                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate" :title="ticket.appliance || 'Not Specified'">{{ ticket.appliance || 'Not Specified' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <Clock class="h-4 w-4 text-slate-400 mt-0.5 shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase">Logged At</p>
                                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300 truncate" :title="ticket.createdAt">{{ ticket.createdAt }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer / Actions -->
                        <div class="p-4 bg-slate-50/50 dark:bg-white/[.01] border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold text-xs border border-emerald-500/20">
                                <DollarSign class="h-3.5 w-3.5" />
                                <span>{{ ticket.estimatedCost.toFixed(2) }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <Link 
                                    v-if="ticket.status === 'open'"
                                    as="button" 
                                    method="post" 
                                    :href="`/technician/requests/${ticket.id}/status`" 
                                    :data="{ status: 'assigned' }" 
                                    preserve-scroll
                                    class="px-4 py-2 rounded-lg text-xs font-bold bg-solar-primary text-white hover:bg-solar-primary-accent transition-colors shadow-sm"
                                >
                                    Assign to me
                                </Link>

                                <Link 
                                    v-if="ticket.status === 'assigned'"
                                    as="button" 
                                    method="post" 
                                    :href="`/technician/requests/${ticket.id}/status`" 
                                    :data="{ status: 'in_progress' }" 
                                    preserve-scroll
                                    class="px-4 py-2 rounded-lg text-xs font-bold bg-amber-500 text-white hover:bg-amber-600 transition-colors shadow-sm"
                                >
                                    Start Working
                                </Link>

                                <Link 
                                    v-if="ticket.status === 'in_progress'"
                                    as="button" 
                                    method="post" 
                                    :href="`/technician/requests/${ticket.id}/status`" 
                                    :data="{ status: 'resolved' }" 
                                    preserve-scroll
                                    class="px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500 text-white hover:bg-emerald-600 transition-colors shadow-sm"
                                >
                                    Mark Resolved
                                </Link>
                                
                                <span v-if="ticket.status === 'resolved' || ticket.status === 'paid'" class="px-3 py-1.5 flex items-center gap-1.5 text-xs font-bold text-slate-400">
                                    <CheckCircle2 class="h-4 w-4" /> Finished
                                </span>
                            </div>
                        </div>
                    </div>
                </TransitionGroup>

                <!-- Empty State -->
                <div 
                    v-if="filteredRequests.length === 0" 
                    class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center animate-fade-in"
                >
                    <div class="h-20 w-20 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-4">
                        <ClipboardList class="h-10 w-10 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">No requests found</h3>
                    <p class="text-sm text-slate-500 max-w-sm">
                        There are no maintenance requests matching your current filter. Check back later or clear your filters.
                    </p>
                    <button 
                        v-if="activeTab !== 'all'"
                        @click="activeTab = 'all'"
                        class="mt-4 px-4 py-2 rounded-lg text-xs font-bold bg-solar-primary/10 text-solar-primary hover:bg-solar-primary/20 transition-colors"
                    >
                        View All Requests
                    </button>
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>

<style scoped>
.list-move, /* apply transition to moving elements */
.list-enter-active,
.list-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.98);
}

/* ensure leaving items are taken out of layout flow so that moving
   animations can be calculated correctly. */
.list-leave-active {
  position: absolute;
}
</style>
