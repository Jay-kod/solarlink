<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

const props = defineProps<{ requests?: any[] }>()

const actions = {
  accept: (id: number) => router.post(`/technician/jobs/${id}/accept`),
  reject: (id: number) => router.post(`/technician/jobs/${id}/reject`),
  start: (id: number) => router.post(`/technician/jobs/${id}/start`),
  complete: (id: number) => router.post(`/technician/jobs/${id}/complete`),
}
</script>

<template>
    <Head title="Technician jobs" />
    <DashboardLayout role="technician" title="Assigned jobs">
        <div class="space-y-4">
            <div v-if="!props.requests || props.requests.length === 0" class="glass-card p-6 text-sm">
                No maintenance jobs are currently assigned to you.
            </div>

            <div v-for="request in props.requests || []" :key="request.id" class="glass-card p-5">
                <div class="flex justify-between gap-4 flex-wrap">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">#{{ request.id }}</p>
                        <h3 class="font-bold text-lg">{{ request.faultType || request.issueType || 'Maintenance' }}</h3>
                        <p class="text-sm text-slate-600">Customer: {{ request.customer || 'N/A' }}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs font-semibold bg-solar-primary/10 text-solar-primary">{{ request.status }}</span>
                </div>

                <div class="mt-4 grid md:grid-cols-3 gap-3 text-sm">
                    <div>Appliance: {{ request.appliance || 'N/A' }}</div>
                    <div>Location: {{ request.location || 'N/A' }}</div>
                    <div>Priority: {{ request.priority || request.severity || 'medium' }}</div>
                </div>

                <div class="mt-5 flex gap-2 flex-wrap">
                    <button type="button" @click="actions.accept(request.id)" class="bg-emerald-600 text-white px-3 py-2 rounded">Accept</button>
                    <button type="button" @click="actions.reject(request.id)" class="bg-red-600 text-white px-3 py-2 rounded">Reject</button>
                    <button type="button" @click="actions.start(request.id)" class="bg-sky-600 text-white px-3 py-2 rounded">Start</button>
                    <button type="button" @click="actions.complete(request.id)" class="bg-slate-800 text-white px-3 py-2 rounded">Complete</button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
