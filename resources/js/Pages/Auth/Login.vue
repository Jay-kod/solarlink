<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'
import { 
    Sun, Moon, ArrowRight, UserCheck, ShieldAlert, Wrench, Store, 
    Sparkles, CheckCircle2, ChevronRight, Cpu, Lock, Terminal,
    Eye, EyeOff
} from 'lucide-vue-next'

const props = defineProps<{
    canResetPassword?: boolean
    status?: string
    role?: 'customer' | 'technician' | 'vendor' | 'admin'
}>()

const { isDark, toggleDarkMode } = useDarkMode()

// Dynamic configuration of login portals
const roleConfigs = {
    customer: {
        title: 'Homeowner Telemetry Portal',
        subtitle: 'Clean Renewable Telemetry & Dispatches',
        icon: Cpu,
        themeColor: 'text-solar-primary bg-solar-primary-light dark:bg-solar-primary-dark/80 border-solar-primary/20',
        ctaColor: 'bg-solar-primary hover:bg-solar-primary-active shadow-solar hover:shadow-solar-glow focus:ring-solar-primary/50',
        glowColor: 'bg-solar-primary/10 dark:bg-solar-primary/5',
        badge: 'Homeowner Operations',
        email: 'customer@solarlink.io',
        cardBg: 'from-solar-primary/10 via-solar-primary-dark/5 to-solar-primary-accent/10',
        illustrationTitle: 'Active Telemetry Simulation',
        illustrationDesc: 'Track live panel wattage outputs, optimize grid netting profiles, and dispatch local NABCEP certified technicians.',
        features: [
            { label: 'LiFePO4 Storage Buffer', val: '92.4% Charge' },
            { label: 'CO2 Mitigation Index', val: '14.2 Tons' },
            { label: 'Emergency Tech Service', val: '0 Pending' }
        ],
        gatewayLabel: 'Homeowner Link'
    },
    technician: {
        title: 'Certified Engineer Panel',
        subtitle: 'Field Operations & Dispatch Scheduler',
        icon: Wrench,
        themeColor: 'text-emerald-500 bg-emerald-50 dark:bg-emerald-950/50 border-emerald-500/20',
        ctaColor: 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/20 hover:shadow-emerald-500/40 focus:ring-emerald-500/50',
        glowColor: 'bg-emerald-500/10 dark:bg-emerald-500/5',
        badge: 'Field Engineering',
        email: 'technician@solarlink.io',
        cardBg: 'from-emerald-500/10 via-teal-500/5 to-emerald-600/10',
        illustrationTitle: 'Workforce Operations Ledger',
        illustrationDesc: 'Manage emergency array repair tickets, schedule local calendar slots, and review direct customer payouts.',
        features: [
            { label: 'Certified Grid Rating', val: '4.98 Stars' },
            { label: 'Active Grid Dispatches', val: '2 Assigned' },
            { label: 'NABCEP Registration ID', val: 'SL-8842-US' }
        ],
        gatewayLabel: 'Technician Hub'
    },
    vendor: {
        title: 'Wholesale Supplier Hub',
        subtitle: 'OEM Direct Solar Parts Inventory',
        icon: Store,
        themeColor: 'text-indigo-500 bg-indigo-50 dark:bg-indigo-950/50 border-indigo-500/20',
        ctaColor: 'bg-indigo-650 hover:bg-indigo-700 shadow-indigo-500/20 hover:shadow-indigo-500/40 focus:ring-indigo-500/50',
        glowColor: 'bg-indigo-500/10 dark:bg-indigo-500/5',
        badge: 'Supply Chain Operations',
        email: 'vendor@solarlink.io',
        cardBg: 'from-indigo-500/10 via-blue-500/5 to-indigo-600/10',
        illustrationTitle: 'OEM Wholesale Clearing',
        illustrationDesc: 'Deploy inventory straight from clean factories, fulfill tracking requests, and customize direct storefront designs.',
        features: [
            { label: 'Factory Direct Catalog', val: '48 Active SKUs' },
            { label: 'Fulfillment Handshake', val: '8 Dispatched' },
            { label: 'OEM Margin Health', val: '+24.6%' }
        ],
        gatewayLabel: 'Supplier Gate'
    },
    admin: {
        title: 'Global Operations Console',
        subtitle: 'Infrastructure Moderation & System Ledger',
        icon: ShieldAlert,
        themeColor: 'text-purple-500 bg-purple-50 dark:bg-purple-950/50 border-purple-500/20',
        ctaColor: 'bg-purple-900 hover:bg-purple-950 shadow-purple-500/20 hover:shadow-purple-500/40 focus:ring-purple-500/50',
        glowColor: 'bg-purple-500/10 dark:bg-purple-500/5',
        badge: 'Global Administration',
        email: 'admin@solarlink.io',
        cardBg: 'from-purple-500/10 via-fuchsia-500/5 to-purple-600/10',
        illustrationTitle: 'Administration command console',
        illustrationDesc: 'Moderate incoming OEM supplier listings, verify licensed engineers, and edit platform ledger transaction limits.',
        features: [
            { label: 'Active Regional Arrays', val: '1.2k Systems' },
            { label: 'Moderation Applications', val: '3 Pending' },
            { label: 'Ledger Audit Status', val: 'Synchronized' }
        ],
        gatewayLabel: 'Admin Terminal'
    }
}

// Active portal resolved based on prop passed from controller
const activeRole = computed<'customer' | 'technician' | 'vendor' | 'admin'>(() => {
    return props.role || 'customer'
})

const activeConfig = computed(() => roleConfigs[activeRole.value])

// Inertia useForm
const form = useForm({
    email: activeConfig.value.email,
    password: 'password',
    remember: true,
})

const showPassword = ref(false)
const isPrefilled = ref(false)
const triggerAutofill = () => {
    form.email = activeConfig.value.email
    form.password = 'password'
    isPrefilled.value = true
    setTimeout(() => {
        isPrefilled.value = false
    }, 1000)
}

// Watch activeRole to dynamically pre-fill email/password when switching login portals
watch(activeRole, (newRole) => {
    form.email = roleConfigs[newRole].email
    form.password = 'password'
})

const handleLoginSubmit = () => {
    form.post('/login')
}
</script>

<template>
    <Head :title="`SolarLink — ${activeConfig.title}`" />

    <div class="h-screen relative flex items-center justify-center p-4 sm:p-6 bg-slate-50 dark:bg-solar-bg-dark transition-colors duration-300 overflow-hidden">
        <!-- Background decorative glows tailored to active role -->
        <div 
            class="absolute top-10 left-10 w-96 h-96 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-slow transition-all duration-700"
            :class="activeRole === 'customer' ? 'bg-solar-primary/10' : activeRole === 'technician' ? 'bg-emerald-500/10' : activeRole === 'vendor' ? 'bg-indigo-500/10' : 'bg-purple-500/10'"
        ></div>
        <div 
            class="absolute bottom-10 right-10 w-[450px] h-[450px] rounded-full blur-3xl pointer-events-none -z-10 transition-all duration-700"
            :class="activeRole === 'customer' ? 'bg-solar-primary-accent/10' : activeRole === 'technician' ? 'bg-teal-500/10' : activeRole === 'vendor' ? 'bg-blue-500/10' : 'bg-fuchsia-500/10'"
        ></div>

        <!-- Floating theme switch -->
        <button 
            @click="toggleDarkMode" 
            class="absolute top-6 right-6 p-3 rounded-xl bg-white/70 dark:bg-solar-primary-dark/40 border border-slate-200 dark:border-white/5 text-solar-primary dark:text-solar-primary-accent shadow-solar hover:scale-105 transition-all"
        >
            <Sun v-if="isDark" class="h-5 w-5" />
            <Moon v-else class="h-5 w-5" />
        </button>

        <div class="w-full max-w-[1020px] grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch relative z-10">
            
            <!-- Left Info Panel (Branded visual panel tailored to role) -->
            <div 
                class="md:col-span-5 flex flex-col justify-between gap-8 p-6 sm:p-8 rounded-3xl glass-card text-left bg-gradient-to-br transition-all duration-700"
                :class="activeConfig.cardBg"
            >
                <div class="flex flex-col gap-6">
                    <Link href="/" class="flex items-center gap-2 group self-start">
                        <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-solar-primary to-solar-primary-accent flex items-center justify-center shadow-solar-glow group-hover:scale-105 transition-transform duration-300">
                            <span class="text-white font-extrabold text-sm">SL</span>
                        </div>
                        <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">SolarLink</span>
                    </Link>

                    <div class="flex flex-col gap-3">
                        <div class="inline-flex items-center gap-1.5 self-start px-3 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider transition-colors duration-500" :class="activeConfig.themeColor">
                            <Sparkles class="h-3 w-3" />
                            <span>{{ activeConfig.badge }}</span>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">
                            {{ activeConfig.illustrationTitle }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            {{ activeConfig.illustrationDesc }}
                        </p>
                    </div>

                    <!-- Metrics / Simulation elements for high-fidelity look -->
                    <div class="flex flex-col gap-3 mt-2 bg-white/40 dark:bg-solar-primary-dark/15 backdrop-blur-sm p-4 rounded-2xl border border-slate-100 dark:border-white/5">
                        <div 
                            v-for="feat in activeConfig.features" 
                            :key="feat.label"
                            class="flex justify-between items-center text-xs"
                        >
                            <span class="text-slate-400 dark:text-slate-500 font-semibold">{{ feat.label }}</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[10px]">{{ feat.val }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-relaxed">
                        Database Integration Active. <br>
                        Authorized telemetry handshake enabled.
                    </p>
                </div>
            </div>

            <!-- Right Login Form Panel -->
            <div class="md:col-span-7 glass-card p-6 sm:p-8 flex flex-col justify-center text-left relative overflow-hidden bg-white/70 dark:bg-solar-primary-dark/5">
                <!-- Login success simulated modal overlay -->
                <div 
                    v-if="form.processing"
                    class="absolute inset-0 bg-white/95 dark:bg-solar-bg-dark/95 z-20 flex flex-col items-center justify-center gap-4 text-center p-6"
                >
                    <div class="h-14 w-14 rounded-full border-4 border-solar-primary border-t-transparent animate-spin"></div>
                    <div>
                        <h3 class="font-black text-xl text-slate-800 dark:text-white">Accessing Secure Gateway...</h3>
                        <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-bold">Autoclearing credential tokens</p>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mb-6">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">{{ activeConfig.title }}</h3>
                    <p class="text-xs text-slate-450 dark:text-slate-400 font-medium">{{ activeConfig.subtitle }}</p>
                </div>

                <!-- BEAUTIFUL DEMO AUTOFILL WIDGET -->
                <div 
                    @click="triggerAutofill"
                    class="mb-6 p-4 rounded-2xl cursor-pointer border text-left bg-gradient-to-r transition-all duration-300 hover:scale-[1.01] relative overflow-hidden"
                    :class="[
                        isPrefilled 
                            ? 'border-solar-success bg-solar-success/5 animate-pulse' 
                            : 'bg-slate-50/50 hover:bg-slate-50 dark:bg-solar-primary-dark/20 dark:hover:bg-solar-primary-dark/30 border-slate-100 dark:border-white/5 shadow-sm'
                    ]"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl transition-colors duration-500 border" :class="activeConfig.themeColor">
                                <component :is="activeConfig.icon" class="h-5 w-5" />
                            </div>
                            <div>
                                <h4 class="font-extrabold text-xs text-slate-800 dark:text-white">Click to Pre-fill Demo Account</h4>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold mt-0.5">{{ activeConfig.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-[9px] font-extrabold text-solar-primary uppercase tracking-wider mr-1">One-Click</span>
                            <ChevronRight class="h-4 w-4 text-slate-400" />
                        </div>
                    </div>
                </div>

                <form @submit.prevent="handleLoginSubmit" class="flex flex-col gap-5">
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sign-in Email</label>
                        <input 
                            v-model="form.email"
                            type="email" 
                            name="email"
                            id="email"
                            autocomplete="username"
                            required 
                            placeholder="your@email.com"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300 text-slate-700 dark:text-slate-200 font-semibold"
                            :class="{'border-red-500 focus:border-red-500': form.errors.email}"
                        />
                        <span v-if="form.errors.email" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.email }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Access Token / Password</label>
                            <span class="text-[10px] font-bold text-solar-primary cursor-pointer hover:underline uppercase tracking-wider">Forgot?</span>
                        </div>
                        <div class="relative">
                            <input 
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'" 
                                name="password"
                                id="password"
                                autocomplete="current-password"
                                required 
                                placeholder="password"
                                class="w-full h-11 px-4 pr-12 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300 text-slate-700 dark:text-slate-200 font-semibold"
                                :class="{'border-red-500 focus:border-red-500': form.errors.password}"
                            />
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-lg text-slate-400 hover:text-solar-primary dark:hover:text-solar-primary-accent transition-colors duration-200 cursor-pointer"
                                tabindex="-1"
                            >
                                <EyeOff v-if="showPassword" class="h-4.5 w-4.5" />
                                <Eye v-else class="h-4.5 w-4.5" />
                            </button>
                        </div>
                        <span v-if="form.errors.password" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.password }}</span>
                    </div>

                    <!-- Remember me / login trigger -->
                    <div class="flex items-center justify-between mt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input 
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4.5 w-4.5 rounded border-slate-300 text-solar-primary focus:ring-solar-primary dark:bg-solar-primary-dark/30 dark:border-white/5"
                            />
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Remember Me</span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        class="h-12 w-full rounded-xl text-white font-extrabold text-sm flex items-center justify-center gap-2 transition-all duration-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-solar-bg-dark btn-glow cursor-pointer"
                        :class="activeConfig.ctaColor"
                    >
                        <span>Authorize Secure Session</span>
                        <ArrowRight class="h-4 w-4" />
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        Don't have an account yet? 
                        <Link :href="activeRole === 'customer' ? '/user/register' : activeRole === 'technician' ? '/technician/register' : activeRole === 'vendor' ? '/vendor/register' : '/user/register'" class="font-bold text-solar-primary hover:underline">Register Account</Link>
                    </p>
                </div>

                <!-- SYSTEM GATEWAYS SELECTION (Direct custom links to every portal as requested) -->
                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-white/5">
                    <h4 class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center mb-3">
                        Alternate Portals & Gateways
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <Link 
                            v-for="(conf, roleKey) in roleConfigs"
                            :key="roleKey"
                            :href="roleKey === 'customer' ? '/user/login' : `/${roleKey}/login`"
                            class="p-2.5 rounded-xl border text-center transition-all duration-300 flex flex-col items-center justify-center gap-1 group shadow-sm bg-white/40 hover:bg-white dark:bg-solar-primary-dark/10 dark:hover:bg-solar-primary-dark/30"
                            :class="[
                                activeRole === roleKey 
                                    ? 'border-solar-primary/30 dark:border-white/20 ring-1 ring-solar-primary/20' 
                                    : 'border-slate-100 dark:border-white/5 hover:scale-[1.03]'
                            ]"
                        >
                            <component :is="conf.icon" class="h-4 w-4 text-slate-400 group-hover:text-solar-primary transition-colors" />
                            <span class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                {{ conf.gatewayLabel }}
                            </span>
                        </Link>
                    </div>
                </div>

            </div>

        </div>
    </div>
</template>
