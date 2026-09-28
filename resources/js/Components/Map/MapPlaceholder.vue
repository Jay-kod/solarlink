<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

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
    center: { lat: number; lng: number };
}>()

const emit = defineEmits<{
    (e: 'select-tech', tech: MapTechnician): void;
}>()

const selectedTech = ref<MapTechnician | null>(null)
const mapElement = ref<HTMLDivElement | null>(null)
let map: L.Map | null = null
let technicianMarkers: L.Marker[] = []

const selectMarker = (tech: MapTechnician) => {
    selectedTech.value = tech
    map?.panTo([tech.lat, tech.lng])
    emit('select-tech', tech)
}

const renderTechnicians = () => {
    if (!map) return
    technicianMarkers.forEach((marker) => marker.remove())
    technicianMarkers = props.technicians.map((tech) => L.marker([tech.lat, tech.lng], {
        title: `${tech.name} · ${tech.distance} away`,
        icon: L.divIcon({
            className: 'dispatch-marker-wrapper',
            html: `<span class="dispatch-marker ${tech.status === 'online' ? 'dispatch-marker-online' : 'dispatch-marker-offline'}"></span>`,
            iconSize: [22, 22],
            iconAnchor: [11, 11],
        }),
    }).addTo(map!).on('click', () => selectMarker(tech)))

    if (props.technicians.length) {
        const points: L.LatLngExpression[] = [
            [props.center.lat, props.center.lng],
            ...props.technicians.map((tech) => [tech.lat, tech.lng] as L.LatLngExpression),
        ]
        map.fitBounds(L.latLngBounds(points).pad(0.18), { maxZoom: 13 })
        const selected = props.technicians.find((tech) => tech.id === selectedTech.value?.id) ?? props.technicians[0]
        if (selected && selectedTech.value?.id !== selected.id) selectMarker(selected)
    } else {
        selectedTech.value = null
        map.setView([props.center.lat, props.center.lng], 12)
    }
}

watch(() => props.technicians, renderTechnicians, { deep: true })
watch(() => props.center, (center) => {
    if (!map || props.technicians.length) return
    map.setView([center.lat, center.lng], 12)
}, { deep: true })

onMounted(() => {
    if (!mapElement.value) return
    map = L.map(mapElement.value, { scrollWheelZoom: true }).setView([props.center.lat, props.center.lng], 12)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
    }).addTo(map)
    renderTechnicians()
    window.setTimeout(() => map?.invalidateSize(), 0)
})

onBeforeUnmount(() => {
    technicianMarkers.forEach((marker) => marker.remove())
    map?.remove()
    map = null
})
</script>

<template>
    <div class="relative w-full overflow-hidden rounded-2xl border border-solar-primary/10">
        <div ref="mapElement" class="h-[540px] w-full" aria-label="Technician dispatch map"></div>
        <div v-if="selectedTech" class="absolute left-4 right-4 bottom-4 rounded-xl bg-white/95 dark:bg-slate-900/90 p-4 border border-solar-primary/20">
            <p class="font-bold text-sm">{{ selectedTech.name }}</p>
            <p class="text-xs text-slate-500 mt-1">
                {{ selectedTech.distance }} away | Rating {{ selectedTech.rating }} | ${{ selectedTech.pricePerHour }}/hr | ETA {{ selectedTech.eta }}
            </p>
            <p class="text-xs text-slate-500 mt-1">{{ selectedTech.skills.join(', ') }}</p>
        </div>
    </div>
</template>

<style>
.dispatch-marker-wrapper {
    background: transparent;
    border: 0;
}

.dispatch-marker {
    display: block;
    width: 20px;
    height: 20px;
    border: 3px solid #fff;
    border-radius: 9999px;
    box-shadow: 0 1px 5px rgb(15 23 42 / 45%);
}

.dispatch-marker-online {
    background: #0284c7;
}

.dispatch-marker-offline {
    background: #d97706;
}
</style>
