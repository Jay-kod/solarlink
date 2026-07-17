<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useDarkMode } from '@/composables/useDarkMode'
import { Settings, Sun, Moon, ShieldAlert, Save, CheckCircle2, Sparkles, Globe, Database, Lock } from 'lucide-vue-next'

const { isDark, toggleDarkMode } = useDarkMode()
const formSaved = ref(false)

const handleSave = () => {
    formSaved.value = true
    setTimeout(() => { formSaved.value = false }, 2500)
}
</script>

<template>
    <Head title="SolarLink — Admin Settings" />

    <DashboardLayout role="admin" title="System Config">
        <div class="flex flex-col gap-8 text-left max-w-3xl mx-auto relative">
            
            <!-- Save success overlay -->
            <div 
                v-if="formSaved"
                class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center animate-scale-in"
            >
                <div class="glass-card p-8 flex flex-col items-center gap-4 text-center max-w-sm bg-white/95 dark:bg-[#0B0F19]/95 backdrop-blur-2xl border border-indigo-500/30 rounded-3xl shadow-[0_0_60px_-15px_rgba(99,102,241,0.4)]">
                    <div class="h-16 w-16 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center pulse-glow shadow-[0_0_15px_-3px_rgba(16,185,129,0.4)]">
                        <CheckCircle2 class="h-8 w-8" />
                    </div>
                    <h3 class="font-black text-2xl text-slate-800 dark:text-white mt-2">System Config Synced</h3>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Platform parameters have been updated.</p>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2.5 py-1 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 font-extrabold text-[9px] uppercase tracking-widest border border-purple-500/20 shadow-sm animate-pulse-slow">
                    <Sparkles class="h-3 w-3" />
                    <span>Global Configuration</span>
                </div>
                <h2 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-500 dark:from-indigo-400 dark:to-purple-400 tracking-tight mt-1">Platform System Settings</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">Configure global platform parameters, security policies, and infrastructure options.</p>
            </div>

            <!-- Theme Toggle -->
            <div class="glass-card p-6 flex items-center justify-between bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200/50 dark:border-white/5 rounded-2xl shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-2xl bg-gradient-to-br from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 shadow-inner">
                        <component :is="isDark ? Moon : Sun" class="h-5 w-5" />
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-800 dark:text-white uppercase tracking-wider">Admin Theme</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ isDark ? 'Dark mode active' : 'Light mode active' }}</p>
                    </div>
                </div>
                <button 
                    @click="toggleDarkMode"
                    class="px-5 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm border"
                    :class="isDark 
                        ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20 hover:bg-indigo-500/20' 
                        : 'bg-white text-indigo-600 border-slate-200 hover:border-indigo-500/30 hover:bg-indigo-50'"
                >
                    Toggle Theme
                </button>
            </div>

            <!-- Platform Parameters -->
            <div class="glass-card p-8 flex flex-col gap-6 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200/50 dark:border-white/5 rounded-3xl shadow-sm">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-200/50 dark:border-white/5">
                    <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-500">
                        <Globe class="h-5 w-5" />
                    </div>
                    <h3 class="font-black text-lg text-slate-800 dark:text-white uppercase tracking-wider">Platform Parameters</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Platform Name</label>
                        <input 
                            type="text" 
                            value="SolarLink Grid Platform"
                            class="h-12 px-4 rounded-xl bg-white/80 dark:bg-[#0B0F19]/40 border border-slate-200/80 dark:border-white/5 text-xs font-bold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white shadow-sm"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Support Email</label>
                        <input 
                            type="email" 
                            value="support@solarlink.io"
                            class="h-12 px-4 rounded-xl bg-white/80 dark:bg-[#0B0F19]/40 border border-slate-200/80 dark:border-white/5 text-xs font-bold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white shadow-sm"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Max Technician SLA (hrs)</label>
                        <input 
                            type="number" 
                            value="2"
                            class="h-12 px-4 rounded-xl bg-white/80 dark:bg-[#0B0F19]/40 border border-slate-200/80 dark:border-white/5 text-xs font-bold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white shadow-sm"
                        />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Platform Commission (%)</label>
                        <input 
                            type="number" 
                            value="8.5"
                            class="h-12 px-4 rounded-xl bg-white/80 dark:bg-[#0B0F19]/40 border border-slate-200/80 dark:border-white/5 text-xs font-bold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white shadow-sm"
                        />
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="glass-card p-8 flex flex-col gap-6 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200/50 dark:border-white/5 rounded-3xl shadow-sm">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-200/50 dark:border-white/5">
                    <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-500">
                        <Lock class="h-5 w-5" />
                    </div>
                    <h3 class="font-black text-lg text-slate-800 dark:text-white uppercase tracking-wider">Security Policies</h3>
                </div>

                <div class="flex flex-col gap-4">
                    <label class="flex items-center justify-between cursor-pointer p-4 rounded-2xl border border-slate-200/50 dark:border-white/5 bg-white/40 dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/10 transition-all">
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white">Enforce 2FA for Vendors</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Require two-factor authentication for all wholesale supplier accounts</p>
                        </div>
                        <input type="checkbox" checked class="h-5 w-5 rounded border-slate-300 text-indigo-500 focus:ring-indigo-500 bg-white/50 dark:bg-[#0B0F19]/50" />
                    </label>

                    <label class="flex items-center justify-between cursor-pointer p-4 rounded-2xl border border-slate-200/50 dark:border-white/5 bg-white/40 dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/10 transition-all">
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white">Auto-suspend Unverified Technicians</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Automatically disable dispatch for techs with expired certifications</p>
                        </div>
                        <input type="checkbox" checked class="h-5 w-5 rounded border-slate-300 text-indigo-500 focus:ring-indigo-500 bg-white/50 dark:bg-[#0B0F19]/50" />
                    </label>

                    <label class="flex items-center justify-between cursor-pointer p-4 rounded-2xl border border-slate-200/50 dark:border-white/5 bg-white/40 dark:bg-white/5 hover:bg-slate-50 dark:hover:bg-white/10 transition-all">
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-white">Audit Logging</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Log all admin actions for compliance review</p>
                        </div>
                        <input type="checkbox" checked class="h-5 w-5 rounded border-slate-300 text-indigo-500 focus:ring-indigo-500 bg-white/50 dark:bg-[#0B0F19]/50" />
                    </label>
                </div>
            </div>

            <!-- Save -->
            <button 
                @click="handleSave"
                class="h-14 w-full rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-500 hover:from-indigo-700 hover:to-purple-600 text-white font-black text-sm uppercase tracking-widest flex items-center justify-center gap-3 shadow-[0_0_20px_-5px_rgba(99,102,241,0.5)] transition-all duration-300"
            >
                <Save class="h-5 w-5" />
                <span>Save System Configuration</span>
            </button>

        </div>
    </DashboardLayout>
</template>

<style scoped>
@keyframes scale-in {
    from {
        transform: scale(0.92);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
</style>
