<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'

const props = defineProps<{ appliances?: Array<{ id: number; name: string }> }>()

const form = {
    solar_appliance_id: '',
    fault_type: 'inverter',
    priority: 'high',
    description: '',
    location: '',
    latitude: '',
    longitude: '',
}

const captureLocation = () => {
    if (!navigator.geolocation) return
    navigator.geolocation.getCurrentPosition((position) => {
        form.latitude = String(position.coords.latitude)
        form.longitude = String(position.coords.longitude)
    }, () => {
        form.location = 'Current location unavailable'
    })
}

const submit = () => {
    router.post('/user/maintenance', form)
}
</script>

<template>
    <Head title="Report a fault" />
    <CustomerLayout title="Report Maintenance Fault">
        <div class="max-w-3xl mx-auto glass-card p-6">
            <h2 class="text-xl font-bold mb-4">Report a solar appliance fault</h2>

            <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="submit">
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold block">Appliance</label>
                    <select v-model="form.solar_appliance_id" required class="w-full border rounded-lg p-2 mt-1">
                        <option value="">Select appliance</option>
                        <option v-for="appliance in props.appliances || []" :key="appliance.id" :value="appliance.id">
                            {{ appliance.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold block">Fault type</label>
                    <select v-model="form.fault_type" class="w-full border rounded-lg p-2 mt-1">
                        <option value="inverter">Inverter</option>
                        <option value="battery">Battery</option>
                        <option value="solar_panel">Solar panel</option>
                        <option value="charging">Charging</option>
                        <option value="power_output">Power output</option>
                        <option value="wiring">Wiring</option>
                        <option value="electrical">Electrical</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold block">Priority</label>
                    <select v-model="form.priority" class="w-full border rounded-lg p-2 mt-1">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold block">Description</label>
                    <textarea v-model="form.description" required rows="4" class="w-full border rounded-lg p-2 mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold block">Location</label>
                    <input v-model="form.location" class="w-full border rounded-lg p-2 mt-1" placeholder="Lekki Phase 1, Lagos" />
                </div>

                <div>
                    <label class="text-xs font-semibold block">Latitude</label>
                    <input v-model="form.latitude" type="number" step="0.000001" class="w-full border rounded-lg p-2 mt-1" />
                </div>

                <div>
                    <label class="text-xs font-semibold block">Longitude</label>
                    <input v-model="form.longitude" type="number" step="0.000001" class="w-full border rounded-lg p-2 mt-1" />
                </div>

                <div class="md:col-span-2 flex items-center gap-3">
                    <button type="button" @click="captureLocation" class="border px-4 py-2 rounded-lg">Use my location</button>
                    <button type="submit" class="bg-solar-primary text-white px-4 py-2 rounded-lg">Submit request</button>
                </div>
            </form>
        </div>
    </CustomerLayout>
</template>
