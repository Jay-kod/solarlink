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
                class="fixed inset-0 z-50 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm flex items-center justify-center animate-fade-in-up"
            >
                <div class="glass-card p-8 flex flex-col items-center gap-4 text-center max-w-sm">
                    <div class="h-14 w-14 rounded-full bg-solar-success/10 text-solar-success flex items-center justify-center pulse-glow">
                        <CheckCircle2 class="h-8 w-8" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">System Config Synced</h3>
                    <p class="text-xs text-slate-500">Platform parameters have been updated.</p>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Global Configuration</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Platform System Settings</h2>
                <p class="text-xs text-slate-450 mt-0.5">Configure global platform parameters, security policies, and infrastructure options.</p>
            </div>

            <!-- Theme Toggle -->
            <div class="glass-card p-6 flex items-center justify-between bg-white dark:bg-solar-bg-dark/40">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent">
                        <component :is="isDark ? Moon : Sun" class="h-5 w-5" />
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white">Admin Theme</h4>
                        <p class="text-[10px] text-slate-400">{{ isDark ? 'Dark mode active' : 'Light mode active' }}</p>
                    </div>
                </div>
                <button 
                    @click="toggleDarkMode"
                    class="px-4 h-9 rounded-xl text-xs font-bold transition-all"
                    :class="isDark 
                        ? 'bg-solar-primary-light text-solar-primary' 
                        : 'bg-solar-primary-dark text-solar-primary-accent'"
                >
                    Toggle Theme
                </button>
            </div>

            <!-- Platform Parameters -->
            <div class="glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40">
                <div class="flex items-center gap-2 pb-3 border-b border-solar-primary/10 dark:border-white/5">
                    <Globe class="h-5 w-5 text-solar-primary" />
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Platform Parameters</h3>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Platform Name</label>
                        <input 
                            type="text" 
                            value="SolarLink Grid Platform"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                        />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Support Email</label>
                        <input 
                            type="email" 
                            value="support@solarlink.io"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                        />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Max Technician SLA (hrs)</label>
                        <input 
                            type="number" 
                            value="2"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                        />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Platform Commission (%)</label>
                        <input 
                            type="number" 
                            value="8.5"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                        />
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40">
                <div class="flex items-center gap-2 pb-3 border-b border-solar-primary/10 dark:border-white/5">
                    <Lock class="h-5 w-5 text-solar-primary" />
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Security Policies</h3>
                </div>

                <div class="flex flex-col gap-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">Enforce 2FA for Vendors</h4>
                            <p class="text-[10px] text-slate-400">Require two-factor authentication for all wholesale supplier accounts</p>
                        </div>
                        <input type="checkbox" checked class="h-5 w-5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary" />
                    </label>

                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">Auto-suspend Unverified Technicians</h4>
                            <p class="text-[10px] text-slate-400">Automatically disable dispatch for techs with expired certifications</p>
                        </div>
                        <input type="checkbox" checked class="h-5 w-5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary" />
                    </label>

                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">Audit Logging</h4>
                            <p class="text-[10px] text-slate-400">Log all admin actions for compliance review</p>
                        </div>
                        <input type="checkbox" checked class="h-5 w-5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary" />
                    </label>
                </div>
            </div>

            <!-- Save -->
            <button 
                @click="handleSave"
                class="h-12 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow"
            >
                <Save class="h-4 w-4" />
                <span>Save System Configuration</span>
            </button>

        </div>
    </DashboardLayout>
</template>
