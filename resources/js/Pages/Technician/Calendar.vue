<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Calendar, ChevronLeft, ChevronRight, Clock, MapPin, Sparkles, User, ShieldAlert } from 'lucide-vue-next'

interface Event {
    id: number;
    title: string;
    client: string;
    time: string;
    location: string;
    date: number; // Day in June 2026
    type: 'completed' | 'scheduled' | 'pending';
}

const selectedDay = ref<number>(1)
const currentMonth = ref('June 2026')

const miniStats = ref([
    { title: 'Total Bookings', value: '14 Jobs', desc: 'Active in June' },
    { title: 'Available Slots', value: '8 Open', desc: 'Within shifts' },
    { title: 'Utilization Rate', value: '82%', desc: 'Optimal allocation' }
])

const events = ref<Event[]>([
    {
        id: 1,
        title: 'Residential Panels Clean',
        client: 'Alice Johnson',
        time: '10:00 AM - 12:00 PM',
        location: '1225 Grid Access Road, SF',
        date: 1,
        type: 'scheduled'
    },
    {
        id: 2,
        title: 'Microinverter Degradation Swap',
        client: 'Bob Robertson',
        time: '2:00 PM - 4:00 PM',
        location: '44 Bayview Lane, SF',
        date: 1,
        type: 'completed'
    },
    {
        id: 3,
        title: 'Tesla Powerwall 2 Calibration',
        client: 'Sarah Jenkins',
        time: '11:00 AM - 1:00 PM',
        location: '228 Battery Point, East Bay',
        date: 2,
        type: 'pending'
    },
    {
        id: 4,
        title: 'Commercial Inverter Inspection',
        client: 'Bruce Wayne',
        time: '9:00 AM - 12:00 PM',
        location: '1007 Mountain Drive, Gotham',
        date: 5,
        type: 'scheduled'
    },
    {
        id: 5,
        title: 'Grounding Wire Replacement',
        client: 'Arthur Dent',
        time: '3:00 PM - 4:30 PM',
        location: '42 Towel Lane, East Bay',
        date: 8,
        type: 'completed'
    },
    {
        id: 6,
        title: 'Emergency Inverter Short Audit',
        client: 'David Miller',
        time: '1:30 PM - 3:00 PM',
        location: '89 Junction St, Peninsula',
        date: 12,
        type: 'scheduled'
    },
    {
        id: 7,
        title: 'Seasonal Grid Netting Setup',
        client: 'Elena Rostova',
        time: '8:30 AM - 10:30 AM',
        location: '775 Sunshine Ridge, Marin',
        date: 15,
        type: 'pending'
    }
])

// June 2026 starts on a Monday
// We want a 35-day grid. The calendar will show:
// 1st to 30th as current month, and some preceding/succeeding empty spaces
const days = computed(() => {
    const list = []
    
    // Preceding month filler (e.g. May 31)
    // June 1st is Monday, so no preceding days if we start grid on Monday!
    // Let's assume the grid starts on Sunday, so we have 1 preceding day (May 31)
    list.push({ num: 31, currentMonth: false, events: [] })
    
    for (let i = 1; i <= 30; i++) {
        const dayEvents = events.value.filter(e => e.date === i)
        list.push({
            num: i,
            currentMonth: true,
            events: dayEvents
        })
    }
    
    // Succeeding month filler (July 1 to July 4) to make it exactly 35 days
    for (let i = 1; i <= 4; i++) {
        list.push({ num: i, currentMonth: false, events: [] })
    }
    
    return list
})

const activeEvents = computed(() => {
    return events.value.filter(e => e.date === selectedDay.value)
})

const getDayEventsClasses = (type: 'completed' | 'scheduled' | 'pending') => {
    switch (type) {
        case 'completed': return 'bg-solar-success'
        case 'scheduled': return 'bg-solar-primary'
        case 'pending': return 'bg-amber-500'
    }
}
</script>

<template>
    <Head title="SolarLink — Field Schedules" />

    <DashboardLayout role="technician" title="Field Calendar">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Schedules Node</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Field Operations Calendar</h2>
                <p class="text-xs text-slate-450 mt-0.5">View scheduled dispatches, check availability buffers, and manage appointments.</p>
            </div>

            <!-- Mini stats row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div 
                    v-for="stat in miniStats" 
                    :key="stat.title"
                    class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl"
                >
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ stat.title }}</span>
                    <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white mt-1.5">{{ stat.value }}</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">{{ stat.desc }}</p>
                </div>
            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Calendar Board (Left 8 Columns) -->
                <div class="lg:col-span-8 glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl">
                    <!-- Calendar Header -->
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white flex items-center gap-2">
                            <Calendar class="h-5 w-5 text-solar-primary" />
                            <span>{{ currentMonth }}</span>
                        </h3>
                        <div class="flex items-center gap-1.5">
                            <button class="p-2 rounded-lg border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 text-slate-500 dark:text-slate-400">
                                <ChevronLeft class="h-4 w-4" />
                            </button>
                            <button class="p-2 rounded-lg border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 text-slate-500 dark:text-slate-400">
                                <ChevronRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Weekdays -->
                    <div class="grid grid-cols-7 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-solar-primary/5 pb-2">
                        <span>Sun</span>
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                    </div>

                    <!-- Grid -->
                    <div class="grid grid-cols-7 gap-2">
                        <div 
                            v-for="(day, index) in days" 
                            :key="index"
                            @click="day.currentMonth && (selectedDay = day.num)"
                            class="h-16 border rounded-xl p-2 flex flex-col justify-between transition-all"
                            :class="[
                                day.currentMonth 
                                    ? 'bg-slate-50/50 dark:bg-solar-primary-dark/10 border-solar-primary/10 dark:border-white/5 cursor-pointer hover:shadow-solar hover:border-solar-primary/30' 
                                    : 'bg-slate-100/30 dark:bg-transparent border-transparent opacity-30 select-none pointer-events-none',
                                selectedDay === day.num && day.currentMonth
                                    ? 'border-solar-primary dark:border-solar-primary-accent ring-2 ring-solar-primary/20 bg-solar-primary/5 dark:bg-solar-primary-dark/30 shadow-solar' 
                                    : ''
                            ]"
                        >
                            <span class="text-xs font-bold" :class="day.currentMonth ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400'">
                                {{ day.num }}
                            </span>
                            
                            <!-- Dots container -->
                            <div v-if="day.events.length > 0" class="flex gap-1 flex-wrap mt-auto">
                                <span 
                                    v-for="evt in day.events" 
                                    :key="evt.id"
                                    class="h-1.5 w-1.5 rounded-full"
                                    :class="getDayEventsClasses(evt.type)"
                                    :title="evt.title"
                                ></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Details (Right 4 Columns) -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl">
                        <div>
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Day Schedule</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">June {{ selectedDay }}, 2026</p>
                        </div>
                        
                        <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-1"></div>

                        <div class="flex flex-col gap-4">
                            <div v-if="activeEvents.length === 0" class="flex flex-col items-center justify-center text-center py-10 gap-3">
                                <Clock class="h-8 w-8 text-slate-350" />
                                <div>
                                    <h4 class="font-bold text-xs text-slate-800 dark:text-white">No schedules logged</h4>
                                    <p class="text-[10px] text-slate-450 mt-0.5">No client dispatches have been registered on this date.</p>
                                </div>
                            </div>

                            <div 
                                v-for="evt in activeEvents" 
                                :key="evt.id"
                                class="p-4 rounded-xl border border-solar-primary/10 dark:border-white/5 flex flex-col gap-2 relative overflow-hidden"
                                :class="{
                                    'bg-amber-500/5 border-l-4 border-l-amber-500': evt.type === 'pending',
                                    'bg-solar-primary/5 border-l-4 border-l-solar-primary': evt.type === 'scheduled',
                                    'bg-emerald-500/5 border-l-4 border-l-emerald-500 opacity-80': evt.type === 'completed'
                                }"
                            >
                                <div class="flex justify-between items-start">
                                    <h4 class="font-extrabold text-xs text-slate-800 dark:text-white max-w-[80%]">{{ evt.title }}</h4>
                                    <span class="text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded"
                                        :class="{
                                            'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300': evt.type === 'pending',
                                            'bg-solar-primary-light text-solar-primary dark:bg-solar-primary-dark dark:text-solar-primary-accent': evt.type === 'scheduled',
                                            'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300': evt.type === 'completed'
                                        }"
                                    >
                                        {{ evt.type }}
                                    </span>
                                </div>

                                <div class="flex flex-col gap-1 text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                                    <span class="flex items-center gap-1"><User class="h-3.5 w-3.5" /> {{ evt.client }}</span>
                                    <span class="flex items-center gap-1"><Clock class="h-3.5 w-3.5" /> {{ evt.time }}</span>
                                    <span class="flex items-center gap-1"><MapPin class="h-3.5 w-3.5" /> {{ evt.location }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </DashboardLayout>
</template>
