<script setup lang="ts">
import { computed, ref, watch } from 'vue'

export interface MapTechnician {
    id: string;
    name: string;
    avatar: string | null;
    status: string;
    rating: number;
    reviewCount: number;
    skills: string[];
    pricePerHour: number;
    eta: string;
    experience: string;
    lat: number;
    lng: number;
    distanceKm: number;
    distance: string;
}

const props = defineProps<{
    technicians: MapTechnician[];
}>()

const emit = defineEmits<{
    (e: 'select-tech', tech: MapTechnician): void;
}>()

const selectedTech = ref<MapTechnician | null>(null)

watch(
    () => props.technicians,
    (next) => {
        selectedTech.value = next.length ? next[0] : null
        if (next.length) {
            emit('select-tech', next[0])
        }
    },
    { immediate: true }
)

const bounds = computed(() => {
    if (!props.technicians.length) {
        return { minLat: 0, maxLat: 1, minLng: 0, maxLng: 1 }
    }
    const lats = props.technicians.map((t) => t.lat)
    const lngs = props.technicians.map((t) => t.lng)
    return {
        minLat: Math.min(...lats),
        maxLat: Math.max(...lats),
        minLng: Math.min(...lngs),
        maxLng: Math.max(...lngs),
    }
})

const pinStyle = (tech: MapTechnician) => {
    const latRange = bounds.value.maxLat - bounds.value.minLat || 1
    const lngRange = bounds.value.maxLng - bounds.value.minLng || 1

    const x = ((tech.lng - bounds.value.minLng) / lngRange) * 80 + 10
    const y = ((bounds.value.maxLat - tech.lat) / latRange) * 70 + 10

    return {
        left: `${x}%`,
        top: `${y}%`,
    }
}

const selectMarker = (tech: MapTechnician) => {
    selectedTech.value = tech
    emit('select-tech', tech)
}
</script>

<template>
    <div class="relative w-full h-[540px] rounded-2xl overflow-hidden border border-solar-primary/10 bg-slate-100 dark:bg-solar-primary-dark/20">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_25%_25%,rgba(34,197,94,0.12),transparent_45%),radial-gradient(circle_at_75%_75%,rgba(14,165,233,0.15),transparent_45%)]"></div>
        <div class="absolute inset-0">
            <div class="absolute inset-0 grid grid-cols-6 grid-rows-6 opacity-25">
                <div v-for="n in 36" :key="n" class="border border-white/60"></div>
            </div>
        </div>

        <div
            v-for="tech in technicians"
            :key="tech.id"
            class="absolute -translate-x-1/2 -translate-y-1/2"
            :style="pinStyle(tech)"
        >
            <button @click="selectMarker(tech)" class="relative">
                <span
                    class="block h-10 w-10 rounded-full border-2 overflow-hidden bg-white"
                    :class="selectedTech?.id === tech.id ? 'border-solar-primary ring-4 ring-solar-primary/25' : 'border-white'"
                >
                    <img v-if="tech.avatar" :src="tech.avatar" :alt="tech.name" class="h-full w-full object-cover" />
                    <span v-else class="h-full w-full flex items-center justify-center text-xs font-bold text-slate-600">{{ tech.name.charAt(0) }}</span>
                </span>
                <span class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border border-white" :class="tech.status === 'online' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
            </button>
        </div>

        <div v-if="selectedTech" class="absolute left-4 right-4 bottom-4 rounded-xl bg-white/95 dark:bg-slate-900/90 p-4 border border-solar-primary/20">
            <p class="font-bold text-sm">{{ selectedTech.name }}</p>
            <p class="text-xs text-slate-500 mt-1">
                {{ selectedTech.distance }} away | Rating {{ selectedTech.rating }} | ${{ selectedTech.pricePerHour }}/hr | ETA {{ selectedTech.eta }}
            </p>
            <p class="text-xs text-slate-500 mt-1">{{ selectedTech.skills.join(', ') }}</p>
        </div>
    </div>
</template>
