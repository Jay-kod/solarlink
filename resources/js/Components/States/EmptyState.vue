<script setup lang="ts">
import { ShieldAlert } from 'lucide-vue-next'

defineProps<{
    title: string;
    description: string;
    icon?: any;
    actionLabel?: string;
}>()

const emit = defineEmits<{
    (e: 'action'): void;
}>()
</script>

<template>
    <div class="glass-card p-10 flex flex-col items-center justify-center text-center gap-5 max-w-md mx-auto my-8 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 shadow-solar">
        <div class="h-16 w-16 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/80 text-solar-primary dark:text-solar-primary-accent flex items-center justify-center shadow-solar pulse-glow">
            <component :is="icon || ShieldAlert" class="h-8 w-8" />
        </div>
        <div class="flex flex-col gap-1.5">
            <h3 class="font-extrabold text-lg text-slate-800 dark:text-white leading-snug">{{ title }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ description }}</p>
        </div>
        <button 
            v-if="actionLabel"
            @click="emit('action')"
            class="px-5 h-11 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow"
        >
            {{ actionLabel }}
        </button>
    </div>
</template>
