<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import {
    Check,
    Crosshair,
    LoaderCircle,
    MapPin,
    Navigation,
    Pencil,
    Radio,
    Trash2,
    X,
} from 'lucide-vue-next'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import OpenStreetMap, { type MapLocation } from '@/Components/Map/OpenStreetMap.vue'

interface SavedLocation {
    id: number
    label: string
    address: string
    state: string | null
    local_government: string | null
    latitude: number
    longitude: number
    is_primary: boolean
    share_with_users: boolean
    share_detail: 'street' | 'admin_area'
}

type ShareSettings = Partial<Pick<SavedLocation, 'share_with_users' | 'share_detail'>>

const props = defineProps<{
    locations: MapLocation[]
    savedLocations: SavedLocation[]
    sharingEnabled: boolean
    isAdmin: boolean
}>()

const page = usePage()
const authUser = computed(() => (page.props.auth as any)?.user)
const role = computed(() => authUser.value?.role ?? 'customer')
const defaultLocationLabel = computed(() => role.value === 'vendor' ? 'Business' : role.value === 'technician' ? 'Service base' : 'Home')
const locations = ref<MapLocation[]>(props.locations ?? [])
const savedLocations = computed(() => props.savedLocations ?? [])
const isSharing = ref(props.sharingEnabled)
const sharingError = ref('')
const pageError = ref('')
const isGettingPosition = ref(false)
const isResolvingAddress = ref(false)
const editingId = ref<number | null>(null)
const selectedLatitude = ref<number | null>(savedLocations.value.find((location) => location.is_primary)?.latitude ?? null)
const selectedLongitude = ref<number | null>(savedLocations.value.find((location) => location.is_primary)?.longitude ?? null)
const currentPosition = ref<{ latitude: number; longitude: number } | null>(null)
let watchId: number | null = null
let refreshTimer: number | null = null
let lastSentPosition: { latitude: number; longitude: number; sentAt: number } | null = null
let isPublishingPosition = false

const DEVICE_LOCATION_OPTIONS: PositionOptions = {
    enableHighAccuracy: false,
    timeout: 8000,
    maximumAge: 120000,
}

const LIVE_LOCATION_OPTIONS: PositionOptions = {
    enableHighAccuracy: false,
    timeout: 20000,
    maximumAge: 60000,
}

const MIN_LIVE_MOVEMENT_KM = 0.1
const MIN_LIVE_UPDATE_MS = 60000

const form = useForm({
    label: defaultLocationLabel.value,
    address: '',
    state: '',
    local_government: '',
    latitude: null as number | null,
    longitude: null as number | null,
    is_primary: savedLocations.value.length === 0,
    share_with_users: false,
    share_detail: 'street' as 'street' | 'admin_area',
})

const roleTitle = computed(() => ({
    customer: 'Customer locations',
    technician: 'Technician locations',
    vendor: 'Vendor locations',
    admin: 'Organization location map',
}[role.value as 'customer' | 'technician' | 'vendor' | 'admin'] ?? 'Locations'))

const startNewLocation = () => {
    editingId.value = null
    form.reset()
    form.clearErrors()
    form.is_primary = savedLocations.value.length === 0
    form.latitude = selectedLatitude.value
    form.longitude = selectedLongitude.value
    form.address = ''
    form.state = ''
    form.local_government = ''
    form.label = defaultLocationLabel.value
    form.share_detail = 'street'
    pageError.value = ''
}

const editLocation = (location: SavedLocation) => {
    editingId.value = location.id
    form.clearErrors()
    form.label = location.label
    form.address = location.address
    form.state = location.state ?? ''
    form.local_government = location.local_government ?? ''
    form.latitude = location.latitude
    form.longitude = location.longitude
    form.is_primary = location.is_primary
    form.share_with_users = location.share_with_users
    form.share_detail = location.share_detail ?? 'street'
    selectedLatitude.value = location.latitude
    selectedLongitude.value = location.longitude
    pageError.value = ''
}

const selectPoint = (point: { latitude: number; longitude: number; address?: string; state?: string; localGovernment?: string }) => {
    selectedLatitude.value = Number(point.latitude.toFixed(7))
    selectedLongitude.value = Number(point.longitude.toFixed(7))
    form.latitude = selectedLatitude.value
    form.longitude = selectedLongitude.value
    if (point.address) form.address = point.address
    if (point.state !== undefined) form.state = point.state
    if (point.localGovernment !== undefined) form.local_government = point.localGovernment
}

const visibleAddress = (location: MapLocation) => location.address
    || [location.localGovernment, location.state].filter(Boolean).join(', ')
    || 'Administrative area unavailable'

const updateShareSettings = (location: SavedLocation, updates: ShareSettings) => {
    router.patch(`/locations/${location.id}`, {
        label: location.label,
        address: location.address ?? '',
        state: location.state ?? '',
        local_government: location.local_government ?? '',
        latitude: location.latitude,
        longitude: location.longitude,
        is_primary: location.is_primary,
        share_with_users: updates.share_with_users ?? location.share_with_users,
        share_detail: updates.share_detail ?? location.share_detail,
    }, {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['locations', 'savedLocations'], preserveScroll: true }),
    })
}

const resolveDeviceAddress = async (latitude: number, longitude: number) => {
    const params = new URLSearchParams({
        format: 'jsonv2',
        lat: String(latitude),
        lon: String(longitude),
        addressdetails: '1',
        addressdetails: '1',
    })
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?${params.toString()}`, {
        headers: { Accept: 'application/json' },
    })
    if (!response.ok) throw new Error('Address lookup is temporarily unavailable.')
    const result = await response.json()
    if (!result.display_name) throw new Error('No address was found for this device location.')

    if (selectedLatitude.value === latitude && selectedLongitude.value === longitude) {
        form.address = result.display_name
        form.state = result.address?.state || result.address?.region || ''
        form.local_government = result.address?.county
            || result.address?.municipality
            || result.address?.city_district
            || result.address?.state_district
            || result.address?.city
            || result.address?.town
            || result.address?.village
            || ''
        form.state = result.address?.state || result.address?.region || ''
        form.local_government = result.address?.county
            || result.address?.municipality
            || result.address?.city_district
            || result.address?.state_district
            || result.address?.city
            || result.address?.town
            || result.address?.village
            || ''
    }
}

const locateDevice = () => {
    pageError.value = ''
    if (!navigator.geolocation) {
        pageError.value = 'This browser does not provide device location.'
        return
    }
    isGettingPosition.value = true
    navigator.geolocation.getCurrentPosition((position) => {
        isGettingPosition.value = false
        const latitude = Number(position.coords.latitude.toFixed(7))
        const longitude = Number(position.coords.longitude.toFixed(7))
        selectPoint({ latitude, longitude })
        if (!editingId.value && !form.label.trim()) form.label = defaultLocationLabel.value
        form.address = `${latitude.toFixed(5)}, ${longitude.toFixed(5)}`
        form.state = ''
        form.local_government = ''
        isResolvingAddress.value = true
        void resolveDeviceAddress(latitude, longitude)
            .catch((error: Error) => {
                pageError.value = `${error.message} Coordinates are still ready to save.`
            })
            .finally(() => {
                isResolvingAddress.value = false
            })
    }, (error) => {
        isGettingPosition.value = false
        pageError.value = error.message || 'Device location is unavailable.'
    }, DEVICE_LOCATION_OPTIONS)
}

const saveLocation = () => {
    pageError.value = ''
    if (form.latitude === null || form.longitude === null) {
        pageError.value = 'Choose a point on the map, use device location, or enter coordinates.'
        return
    }
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null
            form.reset()
            router.reload({ only: ['locations', 'savedLocations', 'sharingEnabled'], preserveScroll: true })
        },
    }
    if (editingId.value) {
        form.patch(`/locations/${editingId.value}`, options)
    } else {
        form.post('/locations', options)
    }
}

const removeLocation = (location: SavedLocation) => {
    if (!window.confirm(`Remove ${location.label}?`)) return
    router.delete(`/locations/${location.id}`, {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['locations', 'savedLocations'], preserveScroll: true }),
    })
}

const setSharing = async (sharing: boolean, latitude?: number, longitude?: number) => {
    const response = await axios.put('/locations/live', {
        sharing,
        latitude,
        longitude,
    })
    isSharing.value = response.data.sharing
}

const distanceBetween = (first: { latitude: number; longitude: number }, second: { latitude: number; longitude: number }) => {
    const radians = (value: number) => value * Math.PI / 180
    const dLat = radians(second.latitude - first.latitude)
    const dLng = radians(second.longitude - first.longitude)
    const a = Math.sin(dLat / 2) ** 2
        + Math.cos(radians(first.latitude)) * Math.cos(radians(second.latitude)) * Math.sin(dLng / 2) ** 2
    return 6371 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
}

const sendLivePosition = async (position: GeolocationPosition) => {
    const point = {
        latitude: Number(position.coords.latitude.toFixed(7)),
        longitude: Number(position.coords.longitude.toFixed(7)),
    }
    currentPosition.value = point
    const now = Date.now()
    if (isPublishingPosition) return
    if (lastSentPosition && distanceBetween(lastSentPosition, point) < MIN_LIVE_MOVEMENT_KM && now - lastSentPosition.sentAt < MIN_LIVE_UPDATE_MS) return
    isPublishingPosition = true
    try {
        await setSharing(true, point.latitude, point.longitude)
        lastSentPosition = { ...point, sentAt: now }
    } catch {
        sharingError.value = 'Could not publish your location. Check your connection and try again.'
    } finally {
        isPublishingPosition = false
    }
}

const startLiveSharing = () => {
    sharingError.value = ''
    if (!navigator.geolocation) {
        sharingError.value = 'This browser does not provide device location.'
        return
    }
    isGettingPosition.value = true
    watchId = navigator.geolocation.watchPosition((position) => {
        isGettingPosition.value = false
        void sendLivePosition(position)
    }, (error) => {
        isGettingPosition.value = false
        sharingError.value = error.message || 'Allow location access to start sharing.'
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId)
            watchId = null
        }
    }, LIVE_LOCATION_OPTIONS)
}

const stopLiveSharing = async () => {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId)
        watchId = null
    }
    try {
        await setSharing(false)
        currentPosition.value = null
        lastSentPosition = null
        sharingError.value = ''
    } catch {
        sharingError.value = 'Could not stop location sharing. Try again.'
    }
}

const toggleLiveSharing = () => {
    if (isSharing.value) {
        void stopLiveSharing()
    } else {
        startLiveSharing()
    }
}

const refreshLocations = async () => {
    try {
        const response = await axios.get('/locations/data')
        locations.value = response.data.locations
    } catch {
        // Keep the last successful location snapshot visible while the network is unavailable.
    }
}

onMounted(() => {
    refreshTimer = window.setInterval(refreshLocations, 30000)
    if (isSharing.value) startLiveSharing()
})

onBeforeUnmount(() => {
    if (refreshTimer !== null) window.clearInterval(refreshTimer)
    if (watchId !== null) navigator.geolocation.clearWatch(watchId)
    if (isSharing.value) void axios.put('/locations/live', { sharing: false }).catch(() => undefined)
})
</script>

<template>
    <Head :title="`${roleTitle} · SolarLink`" />

    <DashboardLayout :role="role" :title="isAdmin ? 'Location Map' : 'Locations'">
        <div class="mx-auto max-w-7xl space-y-6">
            <header class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-xs font-semibold uppercase text-blue-800 dark:text-sky-300">Location management</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ roleTitle }}</h2>
                    <p class="mt-1 max-w-2xl text-sm text-slate-600 dark:text-slate-300">Saved pins stay fixed. Live positions are shared only while location sharing is on.</p>
                </div>
                <button v-if="!isAdmin" type="button" @click="toggleLiveSharing" :disabled="isGettingPosition" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg px-4 text-sm font-semibold text-white transition disabled:cursor-wait disabled:opacity-60" :class="isSharing ? 'bg-rose-700 hover:bg-rose-800' : 'bg-blue-800 hover:bg-blue-900'">
                    <LoaderCircle v-if="isGettingPosition" class="h-4 w-4 animate-spin" aria-hidden="true" />
                    <Radio v-else class="h-4 w-4" aria-hidden="true" />
                    {{ isGettingPosition ? 'Getting location…' : isSharing ? 'Stop live sharing' : 'Share live location' }}
                </button>
            </header>

            <div v-if="sharingError || pageError" role="alert" class="rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-100">
                {{ sharingError || pageError }}
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(340px,0.8fr)]">
                <section class="min-w-0 space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white">{{ locations.length }} visible locations</h3>
                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-300"><span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span>Live <span class="ml-2 h-2.5 w-2.5 rounded-full bg-blue-800"></span>Saved</span>
                    </div>
                    <OpenStreetMap
                        :locations="locations"
                        :latitude="selectedLatitude"
                        :longitude="selectedLongitude"
                        @select-point="selectPoint"
                    />
                    <div class="grid gap-2 sm:grid-cols-2">
                        <div v-for="location in locations" :key="location.id" class="flex min-w-0 items-start justify-between gap-3 border-b border-slate-200 py-3 dark:border-white/10">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">{{ location.name || 'Account' }} <span class="font-normal text-slate-500">· {{ location.role }}</span></p>
                                <p class="mt-0.5 truncate text-xs text-slate-600 dark:text-slate-300">{{ location.label }} · {{ visibleAddress(location) }}</p>
                            </div>
                            <span v-if="location.distanceKm !== null" class="shrink-0 text-xs tabular-nums text-slate-500 dark:text-slate-300">{{ location.distanceKm }} km</span>
                        </div>
                        <p v-if="!locations.length" class="py-5 text-sm text-slate-500 dark:text-slate-300">No shared locations yet.</p>
                    </div>
                </section>

                <aside v-if="!isAdmin" class="space-y-6">
                    <section class="border-b border-slate-200 pb-5 dark:border-white/10">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Saved locations</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Choose which pins other accounts can see.</p>
                            </div>
                            <button type="button" @click="startNewLocation" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-blue-800 hover:bg-blue-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:border-white/15 dark:text-sky-300 dark:hover:bg-white/5" aria-label="Add a saved location">
                                <MapPin class="h-4 w-4" aria-hidden="true" />
                            </button>
                        </div>
                        <div v-if="savedLocations.length" class="space-y-3">
                            <article v-for="location in savedLocations" :key="location.id" class="border-l-2 border-blue-800 pl-3 dark:border-sky-300">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-800 dark:text-white">{{ location.label }} <span v-if="location.is_primary" class="ml-1 text-[10px] font-semibold uppercase text-blue-800 dark:text-sky-300">Primary</span></p>
                                        <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-300">{{ location.address }}</p>
                                        <p v-if="location.local_government || location.state" class="mt-1 truncate text-xs text-slate-500 dark:text-slate-300">{{ [location.local_government, location.state].filter(Boolean).join(', ') }}</p>
                                        <p class="mt-1 text-[11px] tabular-nums text-slate-400">{{ location.latitude.toFixed(5) }}, {{ location.longitude.toFixed(5) }}</p>
                                    </div>
                                    <div class="flex shrink-0 gap-1">
                                        <button type="button" @click="editLocation(location)" class="flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-blue-800 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-sky-300" :aria-label="`Edit ${location.label}`"><Pencil class="h-4 w-4" aria-hidden="true" /></button>
                                        <button type="button" @click="removeLocation(location)" class="flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-rose-50 hover:text-rose-700 dark:text-slate-300 dark:hover:bg-rose-950/30 dark:hover:text-rose-300" :aria-label="`Remove ${location.label}`"><Trash2 class="h-4 w-4" aria-hidden="true" /></button>
                                    </div>
                                </div>
                                <label class="mt-2 flex w-fit items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    <input :checked="location.share_with_users" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-800 focus:ring-blue-700" @change="updateShareSettings(location, { share_with_users: ($event.target as HTMLInputElement).checked })" />
                                    Share this saved pin
                                </label>
                                <label v-if="location.share_with_users" class="mt-2 block text-xs text-slate-600 dark:text-slate-300">
                                    Details visible to others
                                    <select :value="location.share_detail" class="mt-1 h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-xs dark:border-white/15 dark:bg-white/[0.04]" @change="updateShareSettings(location, { share_detail: ($event.target as HTMLSelectElement).value as 'street' | 'admin_area' })">
                                        <option value="street">Street address</option>
                                        <option value="admin_area" :disabled="!location.state || !location.local_government">State + local government only</option>
                                    </select>
                                    <span v-if="!location.state || !location.local_government" class="mt-1 block text-[11px] text-amber-700 dark:text-amber-300">Add state and local government details to enable area-only sharing.</span>
                                </label>
                            </article>
                        </div>
                        <p v-else class="py-4 text-sm text-slate-500 dark:text-slate-300">No saved locations yet.</p>
                    </section>

                    <section class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ editingId ? 'Edit location' : 'Add a location' }}</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-300">Use an address, choose a map pin, or enter coordinates.</p>
                        </div>
                        <form class="space-y-3" @submit.prevent="saveLocation">
                            <div>
                                <label for="location-label" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Label</label>
                                <input id="location-label" v-model="form.label" maxlength="80" required class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-white/15 dark:bg-white/[0.04]" placeholder="Home, office, service base" />
                            </div>
                            <div>
                                <label for="location-address" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Address</label>
                                <input id="location-address" v-model="form.address" maxlength="255" :required="form.share_detail === 'street'" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-white/15 dark:bg-white/[0.04]" placeholder="Street address" />
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="location-state" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">State</label>
                                    <input id="location-state" v-model="form.state" maxlength="120" :required="form.share_detail === 'admin_area'" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-white/15 dark:bg-white/[0.04]" placeholder="State / region" />
                                </div>
                                <div>
                                    <label for="location-local-government" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Local government</label>
                                    <input id="location-local-government" v-model="form.local_government" maxlength="120" :required="form.share_detail === 'admin_area'" class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-white/15 dark:bg-white/[0.04]" placeholder="LGA / municipality" />
                                </div>
                            </div>
                            <OpenStreetMap
                                :locations="[]"
                                :latitude="selectedLatitude"
                                :longitude="selectedLongitude"
                                allow-pinning
                                @select-point="selectPoint"
                            />
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="location-lat" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Latitude</label>
                                    <input id="location-lat" v-model.number="form.latitude" type="number" min="-90" max="90" step="0.0000001" required class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm tabular-nums dark:border-white/15 dark:bg-white/[0.04]" />
                                </div>
                                <div>
                                    <label for="location-lng" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-200">Longitude</label>
                                    <input id="location-lng" v-model.number="form.longitude" type="number" min="-180" max="180" step="0.0000001" required class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm tabular-nums dark:border-white/15 dark:bg-white/[0.04]" />
                                </div>
                            </div>
                            <button type="button" @click="locateDevice" :disabled="isGettingPosition" class="flex h-9 w-full items-center justify-center gap-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-60 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/5">
                                <Crosshair class="h-4 w-4" aria-hidden="true" />Use device location
                            </button>
                            <p v-if="isResolvingAddress" role="status" class="text-xs text-slate-500 dark:text-slate-300">Finding the address for your coordinates…</p>
                            <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-200">
                                <input v-model="form.is_primary" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-blue-800 focus:ring-blue-700" />Use as my primary location
                            </label>
                            <label class="flex items-start gap-2 text-xs leading-5 text-slate-700 dark:text-slate-200">
                                <input v-model="form.share_with_users" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-800 focus:ring-blue-700" />Share this saved pin with other accounts
                            </label>
                            <label v-if="form.share_with_users" class="block text-xs text-slate-700 dark:text-slate-200">
                                Details visible to other users
                                <select v-model="form.share_detail" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-white/15 dark:bg-white/[0.04]">
                                    <option value="street">Street address</option>
                                    <option value="admin_area" :disabled="!form.state || !form.local_government">State + local government only</option>
                                </select>
                                <span v-if="!form.state || !form.local_government" class="mt-1 block text-[11px] text-amber-700 dark:text-amber-300">Choose a map point or enter the state and local government to enable area-only sharing.</span>
                            </label>
                            <div class="flex gap-2">
                                <button v-if="editingId" type="button" @click="startNewLocation" class="flex h-10 items-center justify-center gap-1.5 rounded-lg border border-slate-300 px-3 text-sm font-semibold text-slate-700 dark:border-white/15 dark:text-slate-200"><X class="h-4 w-4" aria-hidden="true" />Cancel</button>
                                <button type="submit" :disabled="form.processing" class="flex h-10 flex-1 items-center justify-center gap-2 rounded-lg bg-blue-800 px-4 text-sm font-semibold text-white hover:bg-blue-900 disabled:opacity-60 dark:bg-sky-300 dark:text-[#14345d] dark:hover:bg-sky-200">
                                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" aria-hidden="true" />
                                    <Check v-else class="h-4 w-4" aria-hidden="true" />{{ editingId ? 'Save changes' : 'Save location' }}
                                </button>
                            </div>
                        </form>
                    </section>
                </aside>
            </div>

            <p v-if="isSharing" class="flex items-center gap-2 text-xs text-sky-800 dark:text-sky-200"><Navigation class="h-4 w-4" aria-hidden="true" />Live sharing is on while this page is open. Other signed-in accounts can see your current position.</p>
        </div>
    </DashboardLayout>
</template>
