<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { 
    Calendar, Wrench, ShieldCheck, MapPin, User, ChevronRight, 
    X, AlertCircle, CreditCard, DollarSign, Sparkles
} from 'lucide-vue-next'

interface Booking {
    id: number;
    customerName: string;
    serviceType: string;
    technicianName: string;
    technicianAvatar: string;
    date: string;
    time: string;
    status: 'pending' | 'active' | 'completed' | 'cancelled';
    cost: number;
    payment_status: 'paid' | 'unpaid';
    location: string;
    notes: string | null;
}

const props = defineProps<{
    bookings: Booking[]
}>()

const bookingsList = computed(() => props.bookings || [])
const isModalOpen = ref(false)
const cancelSelectedBooking = ref<Booking | null>(null)

const confirmCancelBooking = (booking: Booking) => {
    cancelSelectedBooking.value = booking
    isModalOpen.value = true
}

const executeCancellation = () => {
    if (cancelSelectedBooking.value) {
        router.post(`/user/bookings/${cancelSelectedBooking.value.id}/cancel`, {}, {
            onSuccess: () => {
                isModalOpen.value = false
                cancelSelectedBooking.value = null
            }
        })
    }
}
</script>

<template>
    <Head title="SolarLink — Bookings History" />

    <CustomerLayout title="Service Ticketing Log">
        <div class="flex flex-col gap-8 text-left relative">
            
            <!-- Modal cancel overlay -->
            <div 
                v-if="isModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm animate-fade-in-up"
            >
                <div class="w-full max-w-md rounded-2xl bg-white dark:bg-solar-bg-dark border border-solar-primary/20 p-6 flex flex-col gap-5 text-left shadow-solar-lg">
                    <div class="flex items-center gap-3 text-solar-danger">
                        <AlertCircle class="h-6 w-6" />
                        <h3 class="font-extrabold text-lg">Decommission Booking?</h3>
                    </div>
                    
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Are you sure you want to cancel the scheduled <span class="font-bold text-slate-800 dark:text-white">{{ cancelSelectedBooking?.serviceType }}</span> service with technician {{ cancelSelectedBooking?.technicianName }}?
                    </p>

                    <div class="flex items-center gap-3.5 mt-2 justify-end">
                        <button 
                            @click="isModalOpen = false"
                            class="px-5 h-11 rounded-xl bg-slate-100 dark:bg-solar-primary-dark/40 text-slate-650 dark:text-slate-200 font-bold text-xs"
                        >
                            Keep Booking
                        </button>
                        <button 
                            @click="executeCancellation"
                            class="px-5 h-11 rounded-xl bg-solar-danger text-white font-bold text-xs hover:bg-red-650 transition-colors"
                        >
                            Cancel Ticket
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page actions header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider mb-1">
                        <Sparkles class="h-3 w-3" />
                        <span>Maintenance Scheduler</span>
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Active Service Tickets</h2>
                    <p class="text-xs text-slate-450 mt-0.5 font-medium">Track live repair progress, maintenance status updates, and dispatch history.</p>
                </div>
                <Link 
                    href="/user/map"
                    class="px-5 h-11 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold flex items-center justify-center gap-2 shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow"
                >
                    <Wrench class="h-4.5 w-4.5" />
                    <span>Request Field Dispatch</span>
                </Link>
            </div>

            <!-- Bookings List -->
            <div v-if="bookingsList.length > 0" class="flex flex-col gap-4">
                <div 
                    v-for="b in bookingsList" 
                    :key="b.id"
                    class="glass-card p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:border-solar-primary/20 transition-all duration-300"
                >
                    <!-- Left profile details -->
                    <div class="flex items-start gap-4">
                        <img :src="b.technicianAvatar" :alt="b.technicianName" class="h-12 w-12 rounded-xl object-cover border border-solar-primary/10 shrink-0" />
                        <div class="flex flex-col gap-1 text-left">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-extrabold text-base text-slate-800 dark:text-white">{{ b.serviceType }}</h4>
                                <span 
                                    class="px-2 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="{
                                        'bg-solar-success/15 text-solar-success': b.status === 'completed',
                                        'bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent': b.status === 'active',
                                        'bg-solar-warning/15 text-solar-warning': b.status === 'pending',
                                        'bg-solar-danger/15 text-solar-danger': b.status === 'cancelled'
                                    }"
                                >
                                    {{ b.status }}
                                </span>

                                <span 
                                    v-if="b.status !== 'cancelled'"
                                    class="px-2 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="b.payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500'"
                                >
                                    {{ b.payment_status === 'paid' ? 'Paid' : 'Unpaid' }}
                                </span>
                            </div>
                            
                            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5 flex-wrap">
                                <User class="h-3.5 w-3.5 text-slate-400" />
                                <span>Technician: {{ b.technicianName }}</span>
                                &bull;
                                <span class="font-semibold text-solar-primary dark:text-solar-primary-accent">${{ b.cost }}</span>
                                &bull;
                                <span class="text-slate-450 dark:text-slate-500">{{ b.location }}</span>
                            </p>
                            <p v-if="b.notes" class="text-[10px] text-slate-400 dark:text-slate-500 italic mt-1">
                                Notes: "{{ b.notes }}"
                            </p>
                        </div>
                    </div>

                    <!-- Right scheduling info -->
                    <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-start w-full sm:w-auto gap-4 pt-4 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-white/5">
                        <div class="text-left sm:text-right">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Scheduled Arrival</span>
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 mt-0.5">
                                <Calendar class="h-3.5 w-3.5 text-solar-primary" />
                                <span>{{ b.date }} at {{ b.time }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <!-- Cancel button -->
                            <button 
                                v-if="b.status === 'active' || b.status === 'pending'"
                                @click="confirmCancelBooking(b)"
                                class="px-3.5 py-1.5 rounded-lg border border-solar-danger/25 text-solar-danger hover:bg-solar-danger/10 text-[10px] font-bold transition-all uppercase tracking-wider"
                            >
                                Cancel Ticket
                            </button>

                            <!-- Pay Invoice button -->
                            <Link 
                                v-if="b.payment_status === 'unpaid' && b.status !== 'cancelled'"
                                method="post"
                                as="button"
                                :href="`/user/bookings/${b.id}/pay`"
                                class="px-3.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold transition-all uppercase tracking-wider"
                            >
                                Pay Invoice
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="glass-card py-16 px-6 text-center bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 max-w-md mx-auto w-full">
                <Calendar class="h-12 w-12 text-slate-350 dark:text-slate-650 mx-auto mb-4 animate-pulse" />
                <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">No Scheduled Service Tickets</h3>
                <p class="text-xs text-slate-500 leading-relaxed mt-1">You have no active maintenance service tickets. Click "Request Field Dispatch" to locate technicians on the GPS grid.</p>
            </div>
        </div>
    </CustomerLayout>
</template>
