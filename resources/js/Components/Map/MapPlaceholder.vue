<script setup lang="ts">
import { ref } from 'vue'
import { mockTechnicians, Technician } from '@/data/technicians'
import { MapPin, Navigation, Star, ShieldCheck, Zap, X } from 'lucide-vue-next'

const emit = defineEmits<{
    (e: 'select-tech', tech: Technician): void;
}>()

const selectedTech = ref<Technician | null>(mockTechnicians[0])

const selectMarker = (tech: Technician) => {
    selectedTech.value = tech
    emit('select-tech', tech)
}
</script>

<template>
    <div class="relative w-full h-[540px] rounded-3xl overflow-hidden border border-solar-primary/10 dark:border-white/5 shadow-solar bg-slate-100 dark:bg-solar-primary-dark/20">
        <!-- SVG Mock Map Background (Very high fidelity custom drawing) -->
        <svg class="absolute inset-0 w-full h-full object-cover select-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 600">
            <!-- Ocean background -->
            <rect width="100% " height="100%" fill="#E3ECF5" class="dark:fill-purple-950/20" />
            <!-- Land mass -->
            <path d="M100,50 Q200,80 350,40 T600,100 T800,20 T950,90 L1000,600 L0,600 Z" fill="#F4F7F6" class="dark:fill-[#150F2B]" />
            <!-- Grid lines / roads -->
            <g stroke="#ffffff" stroke-width="3" class="dark:stroke-purple-900/30 opacity-70">
                <line x1="0" y1="100" x2="1000" y2="100" />
                <line x1="0" y1="250" x2="1000" y2="250" />
                <line x1="0" y1="400" x2="1000" y2="400" />
                <line x1="300" y1="0" x2="300" y2="600" stroke-width="5" />
                <line x1="650" y1="0" x2="650" y2="600" stroke-width="4" />
                <line x1="150" y1="0" x2="150" y2="600" />
                <line x1="850" y1="0" x2="850" y2="600" />
            </g>
            <!-- Bay Area / Water features -->
            <path d="M0,0 Q120,40 180,110 T300,300 L0,400 Z" fill="#D3E2F2" class="dark:fill-purple-950/40" />
            
            <!-- Grid Park Greenery -->
            <rect x="350" y="150" width="120" height="80" rx="8" fill="#E2F0D9" class="dark:fill-emerald-950/20" />
            <rect x="700" y="320" width="100" height="60" rx="8" fill="#E2F0D9" class="dark:fill-emerald-950/20" />
        </svg>

        <!-- Simulated live radar scanner sweep overlay -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-gradient-radial from-solar-primary/5 to-transparent rounded-full pointer-events-none pulse-glow animate-pulse-slow"></div>

        <!-- Custom Interactive Pins -->
        <div 
            v-for="tech in mockTechnicians" 
            :key="tech.id"
            class="absolute transition-all duration-300"
            :style="{ 
                left: `${150 + tech.id * 130}px`, 
                top: `${120 + (tech.id % 2 === 0 ? 180 : 80)}px` 
            }"
        >
            <button 
                @click="selectMarker(tech)"
                class="flex flex-col items-center group relative focus:outline-none"
            >
                <!-- Avatar bubble marker -->
                <div 
                    class="h-10 w-10 rounded-full border-2 p-0.5 bg-white shadow-solar transition-transform duration-300 hover:scale-115"
                    :class="selectedTech?.id === tech.id 
                        ? 'border-solar-primary ring-4 ring-solar-primary/20 scale-110' 
                        : (tech.status === 'online' ? 'border-solar-success' : 'border-slate-350')"
                >
                    <img :src="tech.avatar" :alt="tech.name" class="h-full w-full object-cover rounded-full" />
                </div>
                
                <!-- Small status badge -->
                <span 
                    class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full border-2 border-white flex items-center justify-center text-[7px]"
                    :class="tech.status === 'online' 
                        ? 'bg-solar-success' 
                        : (tech.status === 'busy' ? 'bg-solar-warning' : 'bg-slate-400')"
                ></span>
            </button>
        </div>

        <!-- Floating UI: Details Overlay Card -->
        <div 
            v-if="selectedTech"
            class="absolute bottom-6 left-6 right-6 md:right-auto md:w-96 rounded-2xl glass-card border border-solar-primary/20 p-5 shadow-solar-lg text-left animate-fade-in-up"
        >
            <button 
                @click="selectedTech = null"
                class="absolute top-4 right-4 p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
            >
                <X class="h-4.5 w-4.5" />
            </button>

            <div class="flex items-start gap-4 mb-4">
                <img :src="selectedTech.avatar" :alt="selectedTech.name" class="h-14 w-14 rounded-xl object-cover border border-solar-primary/10" />
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h4 class="font-extrabold text-base text-slate-800 dark:text-white truncate">{{ selectedTech.name }}</h4>
                        <span class="px-2 py-0.5 rounded bg-solar-success/10 text-solar-success font-bold text-[8px] uppercase tracking-wider">
                            {{ selectedTech.status }}
                        </span>
                    </div>
                    
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-normal mt-0.5">{{ selectedTech.experience }} &bull; {{ selectedTech.distance }} away</p>
                    
                    <div class="flex items-center gap-1 text-amber-500 text-xs mt-1.5">
                        <Star class="h-3.5 w-3.5 fill-amber-500" />
                        <span class="font-bold">{{ selectedTech.rating }}</span>
                        <span class="text-slate-400 dark:text-slate-500">({{ selectedTech.reviewCount }} dispatches)</span>
                    </div>
                </div>
            </div>

            <!-- Skills pills -->
            <div class="flex flex-wrap gap-1.5 mb-4">
                <span 
                    v-for="skill in selectedTech.skills.slice(0, 2)" 
                    :key="skill"
                    class="text-[9px] font-bold text-solar-primary dark:text-solar-primary-accent bg-solar-primary-light dark:bg-solar-primary-dark/80 px-2.5 py-0.5 rounded-full uppercase tracking-wider"
                >
                    {{ skill }}
                </span>
            </div>

            <!-- Booking link overlay -->
            <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400">Consultation rate</span>
                    <p class="text-lg font-extrabold text-slate-900 dark:text-white">${{ selectedTech.pricePerHour }} <span class="text-xs font-medium text-slate-400">/hr</span></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-solar-primary dark:text-solar-primary-accent bg-solar-primary-light dark:bg-solar-primary-dark/50 px-2.5 py-1.5 rounded-lg flex items-center gap-1.5">
                        <Zap class="h-3.5 w-3.5" />
                        <span>ETA: {{ selectedTech.eta }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
