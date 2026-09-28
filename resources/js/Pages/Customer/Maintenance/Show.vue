<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'

const props = defineProps<{ request: any }>()
</script>

<template>
    <Head title="Maintenance request details" />
    <CustomerLayout title="Maintenance request details">
        <div class="max-w-3xl mx-auto glass-card p-6">
            <h2 class="text-xl font-bold mb-4">Request #{{ props.request.id }}</h2>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div><strong>Fault:</strong> {{ props.request.faultType }}</div>
                <div><strong>Status:</strong> {{ props.request.status }}</div>
                <div><strong>Priority:</strong> {{ props.request.priority }}</div>
                <div><strong>Appliance:</strong> {{ props.request.appliance || 'N/A' }}</div>
                <div><strong>Technician:</strong> {{ props.request.technician || 'Unassigned' }}</div>
                <div><strong>Location:</strong> {{ props.request.location || 'N/A' }}</div>
                <div><strong>Scheduled:</strong> {{ props.request.scheduledDate || 'Not scheduled' }} {{ props.request.scheduledTime || '' }}</div>
                <div><strong>Cost:</strong> {{ props.request.cost || 0 }}</div>
                <div class="md:col-span-2"><strong>Description:</strong> {{ props.request.description }}</div>
                <div class="md:col-span-2"><strong>Technician notes:</strong> {{ props.request.technicianNotes || 'No notes yet.' }}</div>
                <div class="md:col-span-2"><strong>Completion date:</strong> {{ props.request.completedAt || 'Not completed yet.' }}</div>
            </div>
        </div>
    </CustomerLayout>
</template>
