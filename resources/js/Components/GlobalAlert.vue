<script setup lang="ts">
import { useAlert } from '@/composables/useAlert'
import { AlertTriangle, Info, CheckCircle, AlertCircle, X } from 'lucide-vue-next'

const { isOpen, alertState, closeAlert, handleConfirm } = useAlert()

const getIcon = () => {
    switch (alertState.value.type) {
        case 'warning':
        case 'confirm':
            return AlertTriangle
        case 'danger':
            return AlertCircle
        case 'success':
            return CheckCircle
        default:
            return Info
    }
}

const getIconClass = () => {
    switch (alertState.value.type) {
        case 'warning':
        case 'confirm':
            return 'text-amber-500 bg-amber-50 dark:bg-amber-500/10'
        case 'danger':
            return 'text-red-500 bg-red-50 dark:bg-red-500/10'
        case 'success':
            return 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10'
        default:
            return 'text-blue-500 bg-blue-50 dark:bg-blue-500/10'
    }
}

const getButtonClass = () => {
    switch (alertState.value.type) {
        case 'danger':
            return 'bg-red-500 hover:bg-red-600 focus:ring-red-500'
        case 'warning':
        case 'confirm':
            return 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500'
        case 'success':
            return 'bg-emerald-500 hover:bg-emerald-600 focus:ring-emerald-500'
        default:
            return 'bg-solar-primary hover:bg-solar-primary-active focus:ring-solar-primary'
    }
}
</script>

<template>
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 animate-fade-in">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="closeAlert"></div>
        
        <!-- Modal -->
        <div class="relative w-full max-w-md bg-white dark:bg-solar-bg-dark rounded-2xl shadow-solar-lg border border-slate-100 dark:border-white/10 overflow-hidden animate-fade-in-up">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 rounded-full shrink-0 flex items-center justify-center" :class="getIconClass()">
                        <component :is="getIcon()" class="h-6 w-6" />
                    </div>
                    <div class="flex-grow pt-1">
                        <h3 class="font-extrabold text-lg text-slate-900 dark:text-white">{{ alertState.title }}</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ alertState.message }}</p>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 dark:bg-white/5 border-t border-slate-100 dark:border-white/5 flex items-center justify-end gap-3">
                <button 
                    v-if="alertState.type === 'confirm' || alertState.type === 'danger' || alertState.type === 'warning'"
                    @click="closeAlert"
                    class="px-4 py-2.5 rounded-xl text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-slate-100 dark:hover:bg-white/10 transition-colors"
                >
                    {{ alertState.cancelText }}
                </button>
                <button 
                    @click="handleConfirm"
                    class="px-5 py-2.5 rounded-xl text-white font-bold text-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-solar-bg-dark shadow"
                    :class="getButtonClass()"
                >
                    {{ alertState.confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>
