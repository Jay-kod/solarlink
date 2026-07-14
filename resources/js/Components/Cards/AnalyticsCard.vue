<script setup lang="ts">
import { ArrowUpRight, ArrowDownRight } from 'lucide-vue-next'

defineProps<{
    title: string;
    value: string | number;
    icon: any;
    trend?: string;
    trendType?: 'up' | 'down' | 'neutral';
    desc?: string;
}>()
</script>

<template>
    <div class="glass-card p-6 flex flex-col gap-3 text-left relative overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 hover:shadow-solar hover:border-solar-primary/20 transition-all duration-300">
        
        <!-- Top bar icon & title -->
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ title }}</span>
            <div class="p-2.5 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark/80 text-solar-primary dark:text-solar-primary-accent shadow-solar">
                <component :is="icon" class="h-5 w-5" />
            </div>
        </div>

        <!-- Big Metric value -->
        <div class="flex flex-col gap-1">
            <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white leading-none tracking-tight">{{ value }}</h3>
            <p v-if="desc" class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-1">{{ desc }}</p>
        </div>

        <!-- Bottom trend -->
        <div v-if="trend" class="flex items-center gap-1.5 mt-1 text-xs">
            <div 
                class="flex items-center gap-0.5 font-bold"
                :class="{
                    'text-solar-success': trendType === 'up',
                    'text-solar-danger': trendType === 'down',
                    'text-slate-400': trendType === 'neutral'
                }"
            >
                <ArrowUpRight v-if="trendType === 'up'" class="h-3.5 w-3.5" />
                <ArrowDownRight v-if="trendType === 'down'" class="h-3.5 w-3.5" />
                <span>{{ trend }}</span>
            </div>
            <span class="text-slate-400 dark:text-slate-500 font-medium">vs previous period</span>
        </div>
    </div>
</template>
