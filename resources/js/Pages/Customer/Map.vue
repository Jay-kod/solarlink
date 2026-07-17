<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import MapPlaceholder, { type MapTechnician } from '@/Components/Map/MapPlaceholder.vue'
import { Wrench, Sparkles } from 'lucide-vue-next'

const props = defineProps<{
    technicians: MapTechnician[];
    search: {
        lat: number;
        lng: number;
        q: string;
    };
}>()

const selectedTech = ref<MapTechnician | null>(null)
const bookingFlowActive = ref(false)
const filters = ref({
    lat: props.search.lat,
    lng: props.search.lng,
    q: props.search.q,
})

const sortedTechnicians = computed(() => props.technicians || [])

const handleTechSelect = (tech: MapTechnician) => {
    selectedTech.value = tech
    form.technician_profile_id = tech.id
    form.cost = tech.pricePerHour
}

const form = useForm({
    technician_profile_id: '',
    service_type: 'Solar Panel Maintenance',
    date: new Date(Date.now() + 86400000).toISOString().split('T')[0], // tomorrow
    time: '02:00 PM',
    cost: 0,
    location: '430 Mission District, San Francisco, CA',
    notes: 'Emergency grid array performance diagnostic request.'
})

watch(selectedTech, (newTech) => {
    if (newTech) {
        form.technician_profile_id = newTech.id
        form.cost = newTech.pricePerHour
    }
})

const handleConfirmBooking = () => {
    bookingFlowActive.value = true
    form.post('/user/bookings', {
        onSuccess: () => {
            bookingFlowActive.value = false
        },
        onError: () => {
            bookingFlowActive.value = false
        }
    })
}

const applyFilters = () => {
    router.get('/user/map', filters.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const useMyLocation = () => {
    if (!navigator.geolocation) {
        return
    }
    navigator.geolocation.getCurrentPosition((position) => {
        filters.value.lat = Number(position.coords.latitude.toFixed(6))
        filters.value.lng = Number(position.coords.longitude.toFixed(6))
        applyFilters()
    })
}
</script>

<template>
    <Head title="SolarLink — Technician Dispatch Map" />

    <CustomerLayout title="Field Engineer Dispatch">
        <div class="flex flex-col gap-6 text-left">
            
            <div class="flex flex-col gap-1.5">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark/80 text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Live GPS Telemetry Simulation</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Emergency Field Dispatch Map</h2>
                <p class="text-xs text-slate-400">Technicians are ranked by geodesic distance from your search point. Select a marker to book immediately.</p>
            </div>

            <div class="glass-card p-4 grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Latitude</label>
                    <input v-model.number="filters.lat" type="number" step="0.000001" class="w-full h-9 mt-1 px-2 rounded-lg border border-slate-200 text-sm" />
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Longitude</label>
                    <input v-model.number="filters.lng" type="number" step="0.000001" class="w-full h-9 mt-1 px-2 rounded-lg border border-slate-200 text-sm" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Search by name or skill</label>
                    <input v-model="filters.q" type="text" placeholder="e.g. inverter" class="w-full h-9 mt-1 px-2 rounded-lg border border-slate-200 text-sm" />
                </div>
                <div class="flex gap-2">
                    <button type="button" class="h-9 px-3 rounded-lg border text-xs font-bold" @click="useMyLocation">Use GPS</button>
                    <button type="button" class="h-9 px-3 rounded-lg bg-solar-primary text-white text-xs font-bold" @click="applyFilters">Apply</button>
                </div>
            </div>

            <!-- Grid container -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Map (Left) -->
                <div class="lg:col-span-8">
                    <MapPlaceholder :technicians="sortedTechnicians" @select-tech="handleTechSelect" />
                </div>

                <!-- Control / Booking details panel (Right) -->
                <div class="lg:col-span-4 flex flex-col gap-6 relative">
                    <div class="glass-card p-4">
                        <h4 class="font-bold text-sm mb-2">Nearest Technicians</h4>
                        <div class="space-y-2 max-h-52 overflow-auto">
                            <button
                                v-for="tech in sortedTechnicians"
                                :key="tech.id"
                                type="button"
                                class="w-full text-left p-2 rounded-lg border border-slate-200 hover:border-solar-primary"
                                @click="handleTechSelect(tech)"
                            >
                                <p class="text-xs font-bold">{{ tech.name }}</p>
                                <p class="text-[11px] text-slate-500">{{ tech.distance }} | {{ tech.skills.join(', ') }}</p>
                            </button>
                            <p v-if="!sortedTechnicians.length" class="text-xs text-slate-500">No technician matches current search.</p>
                        </div>
                    </div>

                    <!-- Loading overlay -->
                    <div 
                        v-if="bookingFlowActive"
                        class="absolute inset-0 bg-white/90 dark:bg-solar-bg-dark/90 z-20 flex flex-col items-center justify-center gap-4 text-center p-6 rounded-3xl border border-solar-primary/10"
                    >
                        <div class="h-12 w-12 rounded-full border-4 border-solar-primary border-t-transparent animate-spin"></div>
                        <div>
                            <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">Broadcasting Dispatch...</h3>
                            <p class="text-[10px] text-slate-450 mt-1 uppercase tracking-wider font-bold">Securing engineer GPS slot</p>
                        </div>
                    </div>

                    <!-- Selected Tech panel actions -->
                    <div 
                        v-if="selectedTech"
                        class="glass-card p-6 flex flex-col gap-4 border border-solar-primary/20 bg-gradient-to-tr from-solar-primary/5 to-solar-primary-dark/10"
                    >
                        <div>
                            <h4 class="font-bold text-xs text-solar-primary dark:text-solar-primary-accent uppercase tracking-wider">Confirm Operations Dispatch</h4>
                            <h3 class="text-xl font-extrabold text-slate-800 dark:text-white mt-1">{{ selectedTech.name }}</h3>
                        </div>

                        <!-- Form fields -->
                        <form @submit.prevent="handleConfirmBooking" class="flex flex-col gap-4 text-xs">
                            <div class="flex flex-col gap-1 text-left">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Service Request Type</label>
                                <select 
                                    v-model="form.service_type"
                                    class="h-9 px-2 rounded-lg bg-white dark:bg-solar-primary-dark/40 border border-slate-200 dark:border-white/5 font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    <option value="Solar Panel Maintenance">Solar Panel Maintenance</option>
                                    <option value="Inverter Swap & Parts Claim">Inverter Swap & Parts Claim</option>
                                    <option value="Battery Cycle Diagnostic">Battery Cycle Diagnostic</option>
                                    <option value="Grid-Tie System Overhaul">Grid-Tie System Overhaul</option>
                                    <option value="Emergency System Repair">Emergency System Repair</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="flex flex-col gap-1 text-left">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Scheduled Date</label>
                                    <input 
                                        v-model="form.date"
                                        type="date"
                                        required
                                        class="h-9 px-2 rounded-lg bg-white dark:bg-solar-primary-dark/40 border border-slate-200 dark:border-white/5 font-semibold text-slate-700 dark:text-slate-200"
                                    />
                                </div>
                                <div class="flex flex-col gap-1 text-left">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Arrival Slot</label>
                                    <select 
                                        v-model="form.time"
                                        class="h-9 px-2 rounded-lg bg-white dark:bg-solar-primary-dark/40 border border-slate-200 dark:border-white/5 font-semibold text-slate-700 dark:text-slate-200"
                                    >
                                        <option value="09:00 AM">09:00 AM</option>
                                        <option value="12:00 PM">12:00 PM</option>
                                        <option value="02:00 PM">02:00 PM</option>
                                        <option value="04:00 PM">04:00 PM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1 text-left">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Service Location</label>
                                <input 
                                    v-model="form.location"
                                    type="text"
                                    required
                                    placeholder="Service Address"
                                    class="h-9 px-2 rounded-lg bg-white dark:bg-solar-primary-dark/40 border border-slate-200 dark:border-white/5 font-semibold text-slate-700 dark:text-slate-200"
                                />
                            </div>

                            <div class="flex flex-col gap-1 text-left">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dispatch Notes</label>
                                <textarea 
                                    v-model="form.notes"
                                    rows="2"
                                    placeholder="Enter issues, panel models, etc."
                                    class="p-2 rounded-lg bg-white dark:bg-solar-primary-dark/40 border border-slate-200 dark:border-white/5 font-semibold text-slate-700 dark:text-slate-200 resize-none"
                                ></textarea>
                            </div>

                            <!-- Cost Stats -->
                            <div class="grid grid-cols-2 gap-3 text-xs border-t border-slate-200/50 dark:border-white/5 pt-3">
                                <div class="p-2.5 rounded-lg bg-white dark:bg-solar-primary-dark/30 border border-slate-100 dark:border-white/5">
                                    <span class="text-slate-400 block text-[9px] uppercase tracking-wider">Consultation</span>
                                    <span class="font-bold text-slate-800 dark:text-white">${{ selectedTech.pricePerHour }}/hr</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-white dark:bg-solar-primary-dark/30 border border-slate-100 dark:border-white/5">
                                    <span class="text-slate-400 block text-[9px] uppercase tracking-wider">ETA</span>
                                    <span class="font-bold text-solar-primary dark:text-solar-primary-accent">{{ selectedTech.eta }}</span>
                                </div>
                            </div>

                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="w-full h-11 mt-1 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-xs flex items-center justify-center gap-2 shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow"
                            >
                                <Wrench class="h-4 w-4" />
                                <span>Confirm Service Booking</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </CustomerLayout>
</template>
