<script setup lang="ts">
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { Expand, LoaderCircle, MapPin, Search, TriangleAlert } from 'lucide-vue-next'

export interface MapLocation {
    id: string
    userId: number
    name: string | null
    role: string | null
    label: string
    address: string | null
    latitude: number | null
    longitude: number | null
    state: string | null
    localGovernment: string | null
    kind: 'saved' | 'live'
    sharing: boolean
    shareDetail: 'street' | 'admin_area'
    updatedAt: string | null
    distanceKm: number | null
}

const props = defineProps<{
    locations: MapLocation[]
    latitude: number | null
    longitude: number | null
    allowPinning?: boolean
}>()

const emit = defineEmits<{
    (event: 'select-point', point: { latitude: number; longitude: number; address?: string; state?: string; localGovernment?: string }): void
}>()

const mapElement = ref<HTMLDivElement | null>(null)
const addressQuery = ref('')
const mapAvailable = ref(false)
const mapError = ref('')
const isSearching = ref(false)
let map: L.Map | null = null
let selectedMarker: L.Marker | null = null
let locationMarkers: L.Marker[] = []
let reverseTimer: number | null = null
let lastNominatimRequest = 0

const icon = (kind: 'saved' | 'live' | 'selected') => {
    const className = kind === 'live' ? 'osm-marker-live' : kind === 'selected' ? 'osm-marker-selected' : 'osm-marker-saved'
    const size = kind === 'selected' ? 24 : 20
    return L.divIcon({
        className: 'osm-marker-wrapper',
        html: `<span class="osm-location-marker ${className}"></span>`,
        iconSize: [size, size],
        iconAnchor: [size / 2, size / 2],
    })
}

const nominatim = async (path: 'search' | 'reverse', params: Record<string, string>) => {
    const waitMs = Math.max(0, 1100 - (Date.now() - lastNominatimRequest))
    if (waitMs) await new Promise((resolve) => window.setTimeout(resolve, waitMs))
    lastNominatimRequest = Date.now()
    const query = new URLSearchParams({ format: 'jsonv2', ...params })
    const response = await fetch(`https://nominatim.openstreetmap.org/${path}?${query.toString()}`, {
        headers: { Accept: 'application/json' },
    })
    if (!response.ok) throw new Error('Address search is temporarily unavailable.')
    return response.json()
}

const getAdministrativeAreas = (address: Record<string, string> = {}) => ({
    state: address.state || address.region || '',
    localGovernment: address.county || address.municipality || address.city_district || address.state_district || address.city || address.town || address.village || '',
})

const handleSelectedMarkerDrag = (event: L.DragEndEvent) => {
    const point = event.target.getLatLng() as L.LatLng
    emit('select-point', { latitude: point.lat, longitude: point.lng })
    scheduleReverseGeocode(point.lat, point.lng)
}

const setSelectedMarker = (point: L.LatLng) => {
    if (selectedMarker) {
        selectedMarker.setLatLng(point)
    } else if (map && props.allowPinning) {
        selectedMarker = L.marker(point, { draggable: true, icon: icon('selected'), title: 'Selected location' })
            .addTo(map)
            .on('dragend', handleSelectedMarkerDrag)
    }
}

const selectPoint = (latitude: number, longitude: number, address?: string) => {
    const point = L.latLng(latitude, longitude)
    setSelectedMarker(point)
    if (map) map.panTo(point)
    emit('select-point', { latitude, longitude, address })
    if (props.allowPinning && !address) scheduleReverseGeocode(latitude, longitude)
}

const scheduleReverseGeocode = (latitude: number, longitude: number) => {
    if (reverseTimer !== null) window.clearTimeout(reverseTimer)
    reverseTimer = window.setTimeout(async () => {
        try {
            const result = await nominatim('reverse', { lat: String(latitude), lon: String(longitude) })
            if (result.display_name) {
                emit('select-point', {
                    latitude,
                    longitude,
                    address: result.display_name,
                    ...getAdministrativeAreas(result.address),
                })
            }
        } catch (error) {
            mapError.value = error instanceof Error ? error.message : 'Could not look up this address.'
        }
    }, 1200)
}

const searchAddress = async () => {
    const query = addressQuery.value.trim()
    if (!query || !map) return
    if (reverseTimer !== null) {
        window.clearTimeout(reverseTimer)
        reverseTimer = null
    }
    isSearching.value = true
    mapError.value = ''
    try {
        const results = await nominatim('search', { q: query, limit: '1', addressdetails: '1' })
        const match = results[0]
        if (!match) {
            mapError.value = 'No matching address found.'
            return
        }
        setSelectedMarker(L.latLng(Number(match.lat), Number(match.lon)))
        emit('select-point', {
            latitude: Number(match.lat),
            longitude: Number(match.lon),
            address: match.display_name,
            ...getAdministrativeAreas(match.address),
        })
        map.setView([Number(match.lat), Number(match.lon)], 16)
    } catch (error) {
        mapError.value = error instanceof Error ? error.message : 'Address search failed.'
    } finally {
        isSearching.value = false
    }
}

const renderLocations = () => {
    if (!map) return
    locationMarkers.forEach((marker) => marker.remove())
    locationMarkers = props.locations.filter((location) => location.latitude !== null && location.longitude !== null).map((location) => L.marker(
        [location.latitude!, location.longitude!],
        {
            icon: icon(location.kind),
            title: `${location.name || 'User'} · ${location.role || 'account'} · ${location.label}`,
        },
    ).addTo(map!))
}

const fitAllLocations = () => {
    if (!map) return
    const points: L.LatLngExpression[] = props.locations
        .filter((location) => location.latitude !== null && location.longitude !== null)
        .map((location) => [location.latitude!, location.longitude!])
    if (props.latitude !== null && props.longitude !== null) points.push([props.latitude, props.longitude])

    if (!points.length) return
    if (points.length === 1) {
        map.setView(points[0], 12)
        return
    }
    map.fitBounds(L.latLngBounds(points).pad(0.14), { maxZoom: 13 })
}

const initializeMap = async () => {
    await nextTick()
    if (!mapElement.value) return
    const firstLocation = props.locations.find((location) => location.latitude !== null && location.longitude !== null)
    const center: L.LatLngExpression = props.latitude !== null && props.longitude !== null
        ? [props.latitude, props.longitude]
        : firstLocation
            ? [firstLocation.latitude, firstLocation.longitude]
            : [37.7749, -122.4194]

    map = L.map(mapElement.value, { scrollWheelZoom: true }).setView(center, 11)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
    }).addTo(map)

    if (props.allowPinning) {
        map.on('click', (event: L.LeafletMouseEvent) => selectPoint(event.latlng.lat, event.latlng.lng))
        if (props.latitude !== null && props.longitude !== null) {
            setSelectedMarker(L.latLng(props.latitude, props.longitude))
        }
    }

    renderLocations()
    mapAvailable.value = true
    if (!props.allowPinning) fitAllLocations()
    window.setTimeout(() => map?.invalidateSize(), 0)
}

watch(() => props.locations, renderLocations, { deep: true })
watch([() => props.latitude, () => props.longitude], ([latitude, longitude]) => {
    if (latitude === null || longitude === null || !map) return
    const point = L.latLng(latitude, longitude)
    setSelectedMarker(point)
    map.panTo(point)
})

onMounted(() => {
    initializeMap().catch(() => {
        mapError.value = 'Map tiles could not be loaded. Check your internet connection.'
    })
})

onBeforeUnmount(() => {
    if (reverseTimer !== null) window.clearTimeout(reverseTimer)
    locationMarkers.forEach((marker) => marker.remove())
    map?.remove()
    map = null
})
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-[#d6e0ef] bg-white dark:border-white/10 dark:bg-[#131f33]">
        <form v-if="allowPinning" class="flex gap-2 border-b border-[#dfe7f2] p-3 dark:border-white/10" @submit.prevent="searchAddress">
            <label class="sr-only" for="map-address-search">Find an address</label>
            <input
                id="map-address-search"
                v-model="addressQuery"
                type="search"
                placeholder="Find an address"
                class="h-10 min-w-0 flex-1 rounded-lg border border-[#d6e0ef] bg-white px-3 text-sm text-[#17243a] placeholder:text-[#8999b2] focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700/15 dark:border-white/15 dark:bg-white/[0.04] dark:text-white"
            />
            <button type="submit" :disabled="isSearching || !addressQuery.trim()" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-800 text-white hover:bg-blue-900 disabled:opacity-50 dark:bg-sky-300 dark:text-[#14345d] dark:hover:bg-sky-200" aria-label="Search address">
                <LoaderCircle v-if="isSearching" class="h-4 w-4 animate-spin" aria-hidden="true" />
                <Search v-else class="h-4 w-4" aria-hidden="true" />
            </button>
        </form>
        <div class="relative">
            <div ref="mapElement" class="h-[360px] w-full sm:h-[440px]" aria-label="OpenStreetMap location map"></div>
            <button
                v-if="mapAvailable && !allowPinning && locations.length"
                type="button"
                @click="fitAllLocations"
                title="Fit all visible locations"
                class="absolute right-3 top-3 z-[1000] flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white/95 px-3 text-xs font-semibold text-slate-700 shadow-sm hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-slate-700 dark:bg-slate-900/95 dark:text-slate-100 dark:hover:bg-slate-900"
            >
                <Expand class="h-4 w-4" aria-hidden="true" />
                Fit all locations
            </button>
            <div v-if="!mapAvailable" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-[#eef3fa] px-5 text-center dark:bg-[#101a2c]">
                <MapPin class="h-8 w-8 text-blue-800 dark:text-sky-300" aria-hidden="true" />
                <p v-if="mapError" class="flex max-w-md items-start gap-2 text-sm text-amber-800 dark:text-amber-200">
                    <TriangleAlert class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true" />{{ mapError }}
                </p>
                <p v-else class="text-sm text-[#52627a] dark:text-[#c1cde2]">Loading map…</p>
            </div>
        </div>
        <p v-if="allowPinning && mapAvailable" class="border-t border-[#dfe7f2] px-3 py-2 text-xs text-[#66768f] dark:border-white/10 dark:text-[#adbad1]">Choose an address above or click the map to place the pin. Map data © OpenStreetMap contributors.</p>
        <p v-else-if="mapAvailable" class="border-t border-[#dfe7f2] px-3 py-2 text-xs text-[#66768f] dark:border-white/10 dark:text-[#adbad1]">Map data © OpenStreetMap contributors.</p>
        <p v-if="mapError && mapAvailable" role="status" class="px-3 pb-2 text-xs text-amber-800 dark:text-amber-200">{{ mapError }}</p>
    </div>
</template>

<style>
.osm-marker-wrapper {
    background: transparent;
    border: 0;
}

.osm-location-marker {
    display: block;
    width: 18px;
    height: 18px;
    border: 3px solid #ffffff;
    border-radius: 9999px;
    box-shadow: 0 1px 5px rgb(15 23 42 / 45%);
}

.osm-marker-saved {
    background: #1e40af;
}

.osm-marker-live {
    background: #0ea5e9;
    box-shadow: 0 0 0 4px rgb(14 165 233 / 25%), 0 1px 5px rgb(15 23 42 / 45%);
}

.osm-marker-selected {
    width: 22px;
    height: 22px;
    background: #2563eb;
    box-shadow: 0 0 0 5px rgb(37 99 235 / 24%), 0 1px 5px rgb(15 23 42 / 45%);
}
</style>
