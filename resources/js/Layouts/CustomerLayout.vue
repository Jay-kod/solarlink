<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import DashboardLayout from './DashboardLayout.vue'
import { Cpu, Zap, Calendar, ShoppingBag, MessageSquare, Bell } from 'lucide-vue-next'

defineProps<{
    title: string;
}>()

const mobileTabs = [
    { name: 'Telemetry', href: '/user', icon: Cpu },
    { name: 'Map', href: '/user/map', icon: Zap },
    { name: 'Bookings', href: '/user/bookings', icon: Calendar },
    { name: 'Parts', href: '/user/marketplace', icon: ShoppingBag },
    { name: 'Chat', href: '/user/chat', icon: MessageSquare }
]
</script>

<template>
    <DashboardLayout role="customer" :title="title">
        <!-- Render default slot inside dashboard -->
        <slot />

        <!-- Floating Mobile Bottom Tab Bar (Visible on mobile only, hidden on desktop md) -->
        <div class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/80 dark:bg-solar-bg-dark/85 backdrop-blur-md border-t border-solar-primary/10 dark:border-white/5 py-2 px-3 flex items-center justify-around shadow-solar-lg">
            <Link 
                v-for="tab in mobileTabs" 
                :key="tab.name"
                :href="tab.href"
                class="flex flex-col items-center gap-0.5 justify-center py-1 transition-all duration-300"
                :class="$page.url === tab.href 
                    ? 'text-solar-primary font-bold scale-105' 
                    : 'text-slate-400 dark:text-slate-500 hover:text-solar-primary'"
            >
                <component :is="tab.icon" class="h-5 w-5" />
                <span class="text-[9px] uppercase tracking-wider font-semibold">{{ tab.name }}</span>
            </Link>
        </div>
    </DashboardLayout>
</template>
