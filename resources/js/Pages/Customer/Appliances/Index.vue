<script setup lang="ts">
useDarkMode();import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { useDarkMode } from '@/composables/useDarkMode'
import { 
    Sun, Battery, Cpu, Activity, Sparkles, Plus, Trash2, 
    Calendar, CheckCircle, Info, RefreshCw, X, ShieldAlert,
    AlertCircle
} from 'lucide-vue-next'

interface SolarAppliance {
    id: number;
    name: string;
    type: 'panel' | 'inverter' | 'battery' | 'other';
    brand: string;
    model: string;
    capacity: string | null;
    install_date: string | null;
    serial_number: string | null;
    status: 'active' | 'standby' | 'offline';
    notes: string | null;
}

const props = defineProps<{
    appliances: SolarAppliance[]
}>()

const activeTab = ref<'all' | 'panel' | 'inverter' | 'battery'>('all')
const isRegisterModalOpen = ref(false)
const isConfirmDeleteOpen = ref(false)
const applianceToDelete = ref<SolarAppliance | null>(null)

// Filter appliances
const filteredAppliances = computed(() => {
    if (activeTab.value === 'all') return props.appliances
    return props.appliances.filter(app => app.type === activeTab.value)
})

// Inertia Form
const form = useForm({
    name: '',
    type: 'panel' as 'panel' | 'inverter' | 'battery' | 'other',
    brand: '',
    model: '',
    capacity: '',
    install_date: '',
    serial_number: '',
    status: 'active' as 'active' | 'standby' | 'offline',
    notes: ''
})

const handleRegisterSubmit = () => {
    form.post('/user/appliances', {
        onSuccess: () => {
            isRegisterModalOpen.value = false
            form.reset()
        }
    })
}

const openDeleteConfirm = (app: SolarAppliance) => {
    applianceToDelete.value = app
    isConfirmDeleteOpen.value = true
}

const handleDelete = () => {
    if (applianceToDelete.value) {
        form.delete(`/user/appliances/${applianceToDelete.value.id}`, {
            onSuccess: () => {
                isConfirmDeleteOpen.value = false
                applianceToDelete.value = null
            }
        })
    }
}

// Icon mapper
const getApplianceIcon = (type: string) => {
    switch (type) {
        case 'panel': return Sun
        case 'inverter': return Activity
        case 'battery': return Battery
        default: return Cpu
    }
}

const getApplianceColor = (type: string) => {
    switch (type) {
        case 'panel': return 'text-amber-500 bg-amber-500/10'
        case 'inverter': return 'text-emerald-500 bg-emerald-500/10'
        case 'battery': return 'text-blue-500 bg-blue-500/10'
        default: return 'text-slate-500 bg-slate-500/10'
    }
}

// Simulated maintenance history events linked to types
const applianceEvents = [
    { type: 'panel', event: 'Dust index advisory issued (8% accumulation)', date: '2026-05-28', status: 'info' },
    { type: 'battery', event: 'LiFePO4 battery cell health self-test: 98% efficiency', date: '2026-05-20', status: 'success' },
    { type: 'inverter', event: 'Microinverter firmware auto-upgrade to v4.8.1', date: '2026-05-15', status: 'success' },
    { type: 'panel', event: 'Technician Marcus Vance scheduled for microinverter swap service', date: '2026-05-30', status: 'pending' },
    { type: 'panel', event: 'Completed panel cleaning dispatch by EcoWash Inc.', date: '2026-04-10', status: 'success' }
]

const filteredEvents = computed(() => {
    if (activeTab.value === 'all') return applianceEvents
    return applianceEvents.filter(ev => ev.type === activeTab.value)
})
</script>

<template>
    <Head title="SolarLink — Solar Appliances" />

    <CustomerLayout title="Solar Appliance Management">
        <div class="flex flex-col gap-8 text-left relative">
            
            <!-- REGISTER APPLIANCE MODAL -->
            <div 
                v-if="isRegisterModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in-up"
            >
                <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-solar-bg-dark border border-solar-primary/20 p-6 sm:p-8 flex flex-col gap-6 text-left shadow-solar-lg relative overflow-y-auto max-h-[90vh]">
                    <div class="flex justify-between items-center border-b border-slate-100 dark:border-white/5 pb-4">
                        <div class="flex items-center gap-2 text-solar-primary">
                            <Plus class="h-6 w-6" />
                            <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">Register Solar Appliance</h3>
                        </div>
                        <button @click="isRegisterModalOpen = false" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 transition-all">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <form @submit.prevent="handleRegisterSubmit" class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Appliance Nickname</label>
                                <input 
                                    v-model="form.name"
                                    type="text" 
                                    required 
                                    placeholder="e.g. Main Inverter Array"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Device Type</label>
                                <select 
                                    v-model="form.type"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                >
                                    <option value="panel">Solar Panel Array</option>
                                    <option value="inverter">Inverter Controller</option>
                                    <option value="battery">Battery Storage Buffer</option>
                                    <option value="other">Other Component</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Brand / Manufacturer</label>
                                <input 
                                    v-model="form.brand"
                                    type="text" 
                                    required 
                                    placeholder="e.g. Enphase, Tesla, SMA"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Model Name / Number</label>
                                <input 
                                    v-model="form.model"
                                    type="text" 
                                    required 
                                    placeholder="e.g. IQ8+, Powerwall 2"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Capacity rating (e.g. 7.04 kW)</label>
                                <input 
                                    v-model="form.capacity"
                                    type="text" 
                                    placeholder="e.g. 400W, 13.5 kWh"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Installation Date</label>
                                <input 
                                    v-model="form.install_date"
                                    type="date" 
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Serial Number</label>
                                <input 
                                    v-model="form.serial_number"
                                    type="text" 
                                    placeholder="e.g. SN-88420-US"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Operating Status</label>
                                <select 
                                    v-model="form.status"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none"
                                >
                                    <option value="active">Active Operation</option>
                                    <option value="standby">Standby Mode</option>
                                    <option value="offline">Offline / Fault</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Additional Notes</label>
                            <textarea 
                                v-model="form.notes"
                                rows="3"
                                placeholder="Details about array strings, breaker locations, etc."
                                class="p-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs text-slate-700 dark:text-slate-200 font-semibold focus:border-solar-primary focus:outline-none resize-none"
                            ></textarea>
                        </div>

                        <div class="flex justify-end gap-3 mt-4 border-t border-slate-100 dark:border-white/5 pt-4">
                            <button 
                                type="button" 
                                @click="isRegisterModalOpen = false" 
                                class="px-5 h-10 rounded-xl bg-slate-100 dark:bg-solar-primary-dark/40 text-slate-650 dark:text-slate-200 font-bold text-xs"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-5 h-10 rounded-xl bg-solar-primary text-white font-bold text-xs hover:bg-solar-primary-active transition-all"
                            >
                                Register Asset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CONFIRM DELETE APPLIANCE MODAL -->
            <div 
                v-if="isConfirmDeleteOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in-up"
            >
                <div class="w-full max-w-md rounded-2xl bg-white dark:bg-solar-bg-dark border border-solar-primary/20 p-6 flex flex-col gap-5 text-left shadow-solar-lg">
                    <div class="flex items-center gap-3 text-solar-danger">
                        <AlertCircle class="h-6 w-6" />
                        <h3 class="font-extrabold text-lg">Unregister Appliance Asset?</h3>
                    </div>
                    
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Are you sure you want to unregister the solar asset <span class="font-bold text-slate-800 dark:text-white">{{ applianceToDelete?.name }}</span>? This will permanently delete its specifications and active telemetry diagnostics log.
                    </p>

                    <div class="flex items-center gap-3.5 mt-2 justify-end">
                        <button 
                            @click="isConfirmDeleteOpen = false"
                            class="px-5 h-11 rounded-xl bg-slate-100 dark:bg-solar-primary-dark/40 text-slate-650 dark:text-slate-200 font-bold text-xs"
                        >
                            Keep Asset
                        </button>
                        <button 
                            @click="handleDelete"
                            class="px-5 h-11 rounded-xl bg-solar-danger text-white font-bold text-xs hover:bg-red-650 transition-colors"
                        >
                            Delete Asset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page actions header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider mb-1">
                        <Sparkles class="h-3 w-3" />
                        <span>Asset Registry</span>
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Registered Solar Appliances</h2>
                    <p class="text-xs text-slate-450 mt-0.5 font-medium">Add, register, and monitor panels, inverters, and battery buffers powering your grid netting profiles.</p>
                </div>
                <button 
                    @click="isRegisterModalOpen = true"
                    class="px-5 h-11 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold flex items-center justify-center gap-2 shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow"
                >
                    <Plus class="h-4.5 w-4.5" />
                    <span>Register New Asset</span>
                </button>
            </div>

            <!-- Tab Filters -->
            <div class="flex border-b border-solar-primary/10 dark:border-white/5 gap-4 overflow-x-auto pb-px">
                <button 
                    v-for="t in ['all', 'panel', 'inverter', 'battery']" 
                    :key="t"
                    @click="activeTab = t as any"
                    class="h-10 px-4 text-xs font-bold capitalize transition-all border-b-2 shrink-0"
                    :class="[
                        activeTab === t 
                            ? 'border-solar-primary text-solar-primary font-black' 
                            : 'border-transparent text-slate-400 hover:text-slate-650 dark:hover:text-slate-200'
                    ]"
                >
                    {{ t === 'all' ? 'All Assets' : t + 's' }}
                </button>
            </div>

            <!-- Grid listing -->
            <div v-if="filteredAppliances.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="app in filteredAppliances" 
                    :key="app.id"
                    class="glass-card p-6 flex flex-col justify-between gap-5 bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 hover:border-solar-primary/20 transition-all duration-300"
                >
                    <div class="flex flex-col gap-4">
                        <!-- Card Header -->
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex items-center gap-3">
                                <div class="p-3 rounded-2xl border border-slate-100 dark:border-white/5" :class="getApplianceColor(app.type)">
                                    <component :is="getApplianceIcon(app.type)" class="h-5 w-5" />
                                </div>
                                <div class="text-left">
                                    <h4 class="font-extrabold text-sm text-slate-800 dark:text-white">{{ app.name }}</h4>
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">{{ app.brand }} &bull; {{ app.model }}</span>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <span 
                                class="px-2 py-0.5 rounded-full text-[8px] font-extrabold uppercase tracking-wider inline-flex items-center gap-1"
                                :class="[
                                    app.status === 'active' ? 'bg-emerald-500/10 text-emerald-500' :
                                    app.status === 'standby' ? 'bg-amber-500/10 text-amber-500' :
                                    'bg-red-500/10 text-red-500'
                                ]"
                            >
                                <span class="h-1.5 w-1.5 rounded-full shrink-0" :class="[
                                    app.status === 'active' ? 'bg-emerald-500 animate-ping' :
                                    app.status === 'standby' ? 'bg-amber-500' : 'bg-red-500'
                                ]"></span>
                                {{ app.status }}
                            </span>
                        </div>

                        <!-- Technical Specs -->
                        <div class="grid grid-cols-2 gap-3 mt-1 text-[11px] text-left">
                            <div class="bg-slate-50/50 dark:bg-solar-primary-dark/15 border border-slate-100 dark:border-white/5 p-2.5 rounded-xl">
                                <span class="text-slate-400 block text-[8px] font-bold uppercase tracking-wider">Capacity Rating</span>
                                <span class="font-extrabold text-slate-700 dark:text-slate-200 mt-0.5 block">{{ app.capacity || 'N/A' }}</span>
                            </div>
                            <div class="bg-slate-50/50 dark:bg-solar-primary-dark/15 border border-slate-100 dark:border-white/5 p-2.5 rounded-xl">
                                <span class="text-slate-400 block text-[8px] font-bold uppercase tracking-wider">Install Date</span>
                                <span class="font-extrabold text-slate-700 dark:text-slate-200 mt-0.5 block">{{ app.install_date || 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Serial & Notes -->
                        <div class="text-xs text-left bg-slate-50/30 dark:bg-solar-primary-dark/5 p-3 rounded-xl border border-dashed border-slate-100 dark:border-white/5 flex flex-col gap-1.5">
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="text-slate-400 font-bold uppercase tracking-wider">Serial Number</span>
                                <code class="font-mono text-slate-500 font-bold">{{ app.serial_number || 'N/A' }}</code>
                            </div>
                            <p v-if="app.notes" class="text-[10px] text-slate-400 leading-relaxed font-semibold italic mt-1 border-t border-slate-100 dark:border-white/5 pt-1.5">
                                "{{ app.notes }}"
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end border-t border-slate-100 dark:border-white/5 pt-3.5">
                        <button 
                            @click="openDeleteConfirm(app)"
                            class="p-2 rounded-xl border border-red-500/10 hover:border-red-500/30 text-red-500 hover:bg-red-500/5 font-bold text-xs flex items-center justify-center gap-1.5 transition-all"
                        >
                            <Trash2 class="h-4 w-4" />
                            <span class="text-[10px] uppercase tracking-wider">Decommission Asset</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div v-else class="glass-card py-16 px-6 text-center bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 max-w-md mx-auto w-full">
                <Cpu class="h-12 w-12 text-slate-350 dark:text-slate-650 mx-auto mb-4 animate-pulse" />
                <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">No Registered Assets Found</h3>
                <p class="text-xs text-slate-500 leading-relaxed mt-1">There are no assets registered under this portal matching this category. Please click the button in the top right to register solar panels, battery banks, or inverter gateways.</p>
            </div>

            <!-- Maintenance Logs History Timeline -->
            <div class="mt-8 border-t border-solar-primary/10 dark:border-white/5 pt-8">
                <h3 class="font-extrabold text-lg text-slate-850 dark:text-white mb-6">Asset Maintenance & Telemetry History</h3>
                
                <div class="flex flex-col gap-4 max-w-3xl">
                    <div 
                        v-for="(ev, idx) in filteredEvents"
                        :key="idx"
                        class="flex items-start gap-4 p-4 rounded-2xl bg-white dark:bg-solar-bg-dark/25 border border-slate-100 dark:border-white/5 hover:border-solar-primary/10 transition-colors"
                    >
                        <div class="p-2 rounded-xl" :class="getApplianceColor(ev.type)">
                            <component :is="getApplianceIcon(ev.type)" class="h-4.5 w-4.5" />
                        </div>
                        <div class="flex-grow text-left">
                            <div class="flex justify-between items-center gap-4 flex-wrap">
                                <h4 class="font-bold text-xs text-slate-850 dark:text-slate-200 capitalize">
                                    {{ ev.type }} Diagnostics
                                </h4>
                                <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                    <Calendar class="h-3 w-3" />
                                    {{ ev.date }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-450 leading-relaxed font-medium mt-1">
                                {{ ev.event }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </CustomerLayout>
</template>
