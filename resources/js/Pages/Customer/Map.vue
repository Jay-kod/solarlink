<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import MapPlaceholder from '@/Components/Map/MapPlaceholder.vue'
import { Technician } from '@/data/technicians'
import { Wrench, ShieldCheck, MapPin, Sparkles, Calendar, Clock, FileText, CheckCircle } from 'lucide-vue-next'

const selectedTech = ref<Technician | null>(null)
const bookingFlowActive = ref(false)

const handleTechSelect = (tech: Technician) => {
    selectedTech.value = tech
    form.technician_profile_id = tech.id
    form.cost = tech.pricePerHour
}

// Prefill form for booking
const form = useForm({
    technician_profile_id: '',
    service_type: 'Solar Panel Maintenance',
    date: new Date(Date.now() + 86400000).toISOString().split('T')[0], // tomorrow
    time: '02:00 PM',
    cost: 0,
    location: '430 Mission District, San Francisco, CA',
    notes: 'Emergency grid array performance diagnostic request.'
})

// Update form details if technician changes
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
                <p class="text-xs text-slate-400">Click any technician pin on the radar grid below to view dispatch options, check credentials, and request immediate deployment.</p>
            </div>

            <!-- Grid container -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Map (Left) -->
                <div class="lg:col-span-8">
                    <MapPlaceholder @select-tech="handleTechSelect" />
                </div>

                <!-- Control / Booking details panel (Right) -->
                <div class="lg:col-span-4 flex flex-col gap-6 relative">
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

                    <!-- Dispatch instructions -->
                    <div class="glass-card p-6 flex flex-col gap-4 bg-white dark:bg-solar-bg-dark/40">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Dispatch Operations</h3>
                        <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-1"></div>
                        <ul class="flex flex-col gap-3.5 text-xs text-slate-500 dark:text-slate-400 leading-normal">
                            <li class="flex items-start gap-2">
                                <span class="h-4.5 w-4.5 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary flex items-center justify-center font-bold text-[10px] shrink-0">1</span>
                                <span>Hover/click tech coordinates on map grid.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="h-4.5 w-4.5 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary flex items-center justify-center font-bold text-[10px] shrink-0">2</span>
                                <span>Configure service categories, schedules, and dispatches.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="h-4.5 w-4.5 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary flex items-center justify-center font-bold text-[10px] shrink-0">3</span>
                                <span>Confirm ticket and track engineer arrival.</span>
                            </li>
                        </ul>
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
