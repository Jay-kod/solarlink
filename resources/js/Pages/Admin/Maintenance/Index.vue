<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps<{ requests?: any[] }>()
</script>

<template>
    <Head title="Maintenance overview" />
    <DashboardLayout role="admin" title="Maintenance overview">
        <div class="space-y-4">
            <div v-if="!props.requests || props.requests.length === 0" class="glass-card p-6 text-sm">No maintenance requests found.</div>

            <div v-for="request in props.requests || []" :key="request.id" class="glass-card p-4">
                <div class="flex justify-between items-center gap-4 flex-wrap">
                    <div>
                        <p class="text-xs text-slate-500">#{{ request.id }}</p>
                        <h3 class="font-bold">{{ request.faultType || 'Maintenance request' }}</h3>
                    </div>
                    <span class="px-2 py-1 rounded bg-solar-primary/10 text-solar-primary text-xs font-semibold">{{ request.status }}</span>
                </div>
                <div class="mt-3 text-sm text-slate-600">Customer: {{ request.customer || 'N/A' }} | Appliance: {{ request.appliance || 'N/A' }} | Technician: {{ request.technician || 'Unassigned' }}</div>
                <div class="mt-3 text-sm text-slate-600">Location: {{ request.location || 'N/A' }}</div>
            </div>
        </div>
    </DashboardLayout>
</template>
