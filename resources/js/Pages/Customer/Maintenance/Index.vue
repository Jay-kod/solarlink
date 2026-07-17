<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'

interface RequestItem {
    id: number;
    issueType: string;
    severity: 'low' | 'medium' | 'high' | 'critical';
    description: string;
    location: string;
    status: 'open' | 'assigned' | 'in_progress' | 'resolved' | 'paid';
    paymentStatus: 'unpaid' | 'paid';
    estimatedCost: number;
    appliance: string | null;
    technician: string | null;
    photos: string[];
    createdAt: string;
}

interface OptionItem {
    id: number;
    name: string;
}

interface NotificationItem {
    id: number;
    title: string;
    body: string;
    type: 'info' | 'success' | 'warning' | 'error';
    read_at: string | null;
}

const props = defineProps<{
    requests: RequestItem[];
    appliances: OptionItem[];
    technicians: Array<{ id: number; name: string; hourly_rate: number }>;
    notifications: NotificationItem[];
}>()

const form = useForm({
    solar_appliance_id: '',
    technician_profile_id: '',
    issue_type: '',
    severity: 'medium',
    description: '',
    location_address: '',
    latitude: '',
    longitude: '',
    estimated_cost: 0,
    photos: [] as File[],
})

const submit = () => {
    form.post('/user/maintenance', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset('issue_type', 'description', 'location_address', 'latitude', 'longitude', 'estimated_cost', 'photos')
            form.severity = 'medium'
        },
    })
}

const onPhotosChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    form.photos = target.files ? Array.from(target.files) : []
}

const captureLocation = () => {
    if (!navigator.geolocation) return
    navigator.geolocation.getCurrentPosition((position) => {
        form.latitude = String(position.coords.latitude)
        form.longitude = String(position.coords.longitude)
    })
}
</script>

<template>
    <Head title="Maintenance Requests" />

    <CustomerLayout title="Maintenance Requests">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 text-left">
            <section class="xl:col-span-2 space-y-6">
                <div class="glass-card p-6">
                    <h3 class="font-bold text-lg mb-4">Create Fault Ticket</h3>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-4" @submit.prevent="submit">
                        <div>
                            <label class="text-xs font-semibold">Appliance</label>
                            <select v-model="form.solar_appliance_id" class="w-full mt-1 rounded-lg border-slate-200 text-sm">
                                <option value="">Select appliance</option>
                                <option v-for="appliance in appliances" :key="appliance.id" :value="appliance.id">{{ appliance.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Preferred Technician</label>
                            <select v-model="form.technician_profile_id" class="w-full mt-1 rounded-lg border-slate-200 text-sm">
                                <option value="">Auto assign nearest</option>
                                <option v-for="tech in technicians" :key="tech.id" :value="tech.id">
                                    {{ tech.name }} (${{ tech.hourly_rate }}/hr)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Issue Type</label>
                            <input v-model="form.issue_type" class="w-full mt-1 rounded-lg border-slate-200 text-sm" placeholder="Inverter fault" required />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Severity</label>
                            <select v-model="form.severity" class="w-full mt-1 rounded-lg border-slate-200 text-sm">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold">Description</label>
                            <textarea v-model="form.description" class="w-full mt-1 rounded-lg border-slate-200 text-sm" rows="3" required />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold">Location Address</label>
                            <input v-model="form.location_address" class="w-full mt-1 rounded-lg border-slate-200 text-sm" placeholder="Address" required />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Latitude</label>
                            <input v-model="form.latitude" class="w-full mt-1 rounded-lg border-slate-200 text-sm" placeholder="0.0000" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Longitude</label>
                            <input v-model="form.longitude" class="w-full mt-1 rounded-lg border-slate-200 text-sm" placeholder="0.0000" />
                        </div>
                        <div>
                            <button type="button" class="text-xs px-3 py-2 border rounded-lg" @click="captureLocation">Use Device GPS</button>
                        </div>
                        <div>
                            <label class="text-xs font-semibold">Estimated Cost (optional)</label>
                            <input v-model.number="form.estimated_cost" type="number" min="0" class="w-full mt-1 rounded-lg border-slate-200 text-sm" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold">Photos</label>
                            <input type="file" multiple accept="image/*" class="w-full mt-1 text-sm" @change="onPhotosChange" />
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-solar-primary text-white text-sm font-semibold" :disabled="form.processing">
                                Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>

                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-lg">Request Tracking</h3>
                        <Link href="/user/procurement" class="text-xs text-solar-primary font-semibold">Open Procurement RFQ</Link>
                    </div>
                    <div class="space-y-3">
                        <div v-for="item in requests" :key="item.id" class="border rounded-xl p-4 bg-white/70 dark:bg-transparent">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-sm">#{{ item.id }} {{ item.issueType }}</p>
                                <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-slate-100">{{ item.severity }}</span>
                                <span class="text-[10px] uppercase px-2 py-0.5 rounded bg-solar-primary/10 text-solar-primary">{{ item.status }}</span>
                                <span class="text-[10px] uppercase px-2 py-0.5 rounded" :class="item.paymentStatus === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                    {{ item.paymentStatus }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ item.description }}</p>
                            <p class="text-xs mt-1">Appliance: {{ item.appliance || 'N/A' }} | Technician: {{ item.technician || 'Unassigned' }}</p>
                            <p class="text-xs">Location: {{ item.location }} | Cost: ${{ item.estimatedCost }}</p>
                            <div v-if="item.photos.length" class="flex gap-2 mt-2 overflow-x-auto">
                                <img v-for="photo in item.photos" :key="photo" :src="photo" class="h-14 w-14 rounded object-cover" />
                            </div>
                            <div class="mt-2" v-if="item.status !== 'paid' && item.paymentStatus === 'unpaid'">
                                <Link as="button" method="post" :href="`/user/maintenance/${item.id}/pay`" class="text-xs px-3 py-1.5 rounded bg-amber-500 text-white font-semibold">
                                    Mark as Paid
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="glass-card p-6">
                    <h3 class="font-bold text-base mb-3">Notifications</h3>
                    <div class="space-y-3">
                        <div v-for="notice in notifications" :key="notice.id" class="border rounded-lg p-3">
                            <p class="text-xs font-semibold">{{ notice.title }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ notice.body }}</p>
                            <p class="text-[10px] mt-1" :class="notice.read_at ? 'text-slate-400' : 'text-solar-primary'">
                                {{ notice.read_at ? 'Read' : 'Unread' }}
                            </p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </CustomerLayout>
</template>
