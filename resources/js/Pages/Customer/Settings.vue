<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { useDarkMode } from '@/composables/useDarkMode'
import { Settings, Sun, Moon, Bell, ShieldCheck, User, Save, CheckCircle2, Sparkles } from 'lucide-vue-next'

const { isDark, toggleDarkMode } = useDarkMode()
const formSaved = ref(false)

const preferences = ref({
    notifications: true,
    emailAlerts: true,
    smsAlerts: false,
    autoBookReminders: true,
    publicProfile: false
})

const handleSave = () => {
    formSaved.value = true
    setTimeout(() => { formSaved.value = false }, 2500)
}
</script>

<template>
    <Head title="SolarLink — Account Settings" />

    <CustomerLayout title="Account Preferences">
        <div class="flex flex-col gap-8 text-left max-w-2xl mx-auto relative">
            
            <!-- Save success overlay -->
            <div 
                v-if="formSaved"
                class="fixed inset-0 z-50 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm flex items-center justify-center animate-fade-in-up"
            >
                <div class="glass-card p-8 flex flex-col items-center gap-4 text-center max-w-sm">
                    <div class="h-14 w-14 rounded-full bg-solar-success/10 text-solar-success flex items-center justify-center pulse-glow">
                        <CheckCircle2 class="h-8 w-8" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">Preferences Synchronized</h3>
                    <p class="text-xs text-slate-500">Your notification pipeline and account switches have been saved.</p>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Profile Configuration</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">System Preferences</h2>
                <p class="text-xs text-slate-450 mt-0.5">Manage notification delivery channels, toggle display theme, and configure account privacy.</p>
            </div>

            <!-- Profile Card -->
            <div class="glass-card p-6 flex items-center gap-5 bg-white dark:bg-solar-bg-dark/40">
                <img 
                    src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150" 
                    alt="Profile"
                    class="h-16 w-16 rounded-2xl object-cover border-2 border-solar-primary shadow-solar"
                />
                <div>
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Alice Johnson</h3>
                    <p class="text-xs text-solar-primary dark:text-solar-primary-accent font-semibold">Solar Asset Owner — Residential Grid</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">alice.johnson@gmail.com</p>
                </div>
            </div>

            <!-- Theme Toggle Section -->
            <div class="glass-card p-6 flex items-center justify-between bg-white dark:bg-solar-bg-dark/40">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent">
                        <component :is="isDark ? Moon : Sun" class="h-5 w-5" />
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-800 dark:text-white">Display Theme</h4>
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
                    {{ isDark ? 'Switch to Light' : 'Switch to Dark' }}
                </button>
            </div>

            <!-- Notification Preferences -->
            <div class="glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40">
                <div class="flex items-center gap-2 pb-3 border-b border-solar-primary/10 dark:border-white/5">
                    <Bell class="h-5 w-5 text-solar-primary" />
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Notification Channels</h3>
                </div>

                <div class="flex flex-col gap-4">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">Push Notifications</h4>
                            <p class="text-[10px] text-slate-400">Battery drops, generation alerts, booking confirmations</p>
                        </div>
                        <input v-model="preferences.notifications" type="checkbox" class="h-5 w-5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary" />
                    </label>

                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">Email Digest Alerts</h4>
                            <p class="text-[10px] text-slate-400">Weekly performance summaries and maintenance reminders</p>
                        </div>
                        <input v-model="preferences.emailAlerts" type="checkbox" class="h-5 w-5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary" />
                    </label>

                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">SMS Emergency Alerts</h4>
                            <p class="text-[10px] text-slate-400">Critical system failures and urgent technician dispatches</p>
                        </div>
                        <input v-model="preferences.smsAlerts" type="checkbox" class="h-5 w-5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary" />
                    </label>

                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">Auto-Booking Maintenance Reminders</h4>
                            <p class="text-[10px] text-slate-400">Proactively suggest seasonal audits and panel cleaning</p>
                        </div>
                        <input v-model="preferences.autoBookReminders" type="checkbox" class="h-5 w-5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary" />
                    </label>
                </div>
            </div>

            <!-- Save -->
            <button 
                @click="handleSave"
                class="h-12 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow"
            >
                <Save class="h-4 w-4" />
                <span>Save Preferences</span>
            </button>

        </div>
    </CustomerLayout>
</template>
