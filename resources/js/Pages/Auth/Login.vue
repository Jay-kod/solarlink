<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowRight,
    Eye,
    EyeOff,
    House,
    LoaderCircle,
    Moon,
    ShieldCheck,
    Store,
    Sun,
    Wrench,
} from 'lucide-vue-next'
import { useDarkMode } from '@/composables/useDarkMode'

type Role = 'customer' | 'technician' | 'vendor' | 'admin'

interface DemoAccount {
    name: string
    email: string
    role: Role
}

const props = defineProps<{
    canResetPassword?: boolean
    status?: string
    role?: Role
    demoAccounts?: DemoAccount[]
}>()

const { isDark, toggleDarkMode } = useDarkMode()

const portals = [
    { key: 'customer', label: 'Homeowner', icon: House },
    { key: 'technician', label: 'Technician', icon: Wrench },
    { key: 'vendor', label: 'Supplier', icon: Store },
    { key: 'admin', label: 'Admin', icon: ShieldCheck },
] as const

const roleRoutes: Record<Role, string> = {
    customer: 'user.login',
    technician: 'technician.login',
    vendor: 'vendor.login',
    admin: 'admin.login',
}

const registerRoutes: Partial<Record<Role, string>> = {
    customer: 'user.register',
    technician: 'technician.register',
    vendor: 'vendor.register',
}

const activeRole = ref<Role>(props.role ?? 'customer')
const activePortal = computed(() => portals.find((portal) => portal.key === activeRole.value) ?? portals[0])
const registrationRoute = computed(() => registerRoutes[activeRole.value])
const demoAccounts = computed(() => props.demoAccounts ?? [])
const showPassword = ref(false)

const form = useForm({
    email: '',
    password: '',
    role: activeRole.value,
    remember: false,
})

watch(() => props.role, (role) => {
    if (role) activeRole.value = role
})

watch(activeRole, (role) => {
    form.role = role
})

const fillDemoAccount = (account: DemoAccount) => {
    activeRole.value = account.role
    form.email = account.email
    form.password = 'password'
}

const submit = () => {
    form.role = activeRole.value
    form.post('/login')
}
</script>

<template>
    <Head :title="`Sign in · SolarLink`" />

    <main class="min-h-dvh bg-[#f4f7fc] text-[#17243a] dark:bg-[#101a2c] dark:text-[#edf3ff] lg:grid lg:grid-cols-[minmax(0,0.92fr)_minmax(520px,1.08fr)]">
        <section class="relative isolate flex min-h-[290px] flex-col justify-between overflow-hidden bg-[#17355d] px-6 py-6 text-white sm:min-h-[340px] sm:px-10 sm:py-8 lg:min-h-dvh lg:px-12 lg:py-10 xl:px-16">
            <img
                src="https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&w=1800&q=85"
                alt="Rows of solar panels collecting sunlight"
                class="absolute inset-0 -z-20 h-full w-full object-cover object-center"
            />
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#0b1c35]/95 via-[#14345d]/45 to-[#14345d]/15"></div>

            <Link :href="route('home')" class="group flex w-fit items-center gap-3 rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-sky-300">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-300 text-[#14345d] shadow-lg shadow-black/10">
                    <Sun class="h-6 w-6" aria-hidden="true" />
                </span>
                <span class="text-xl font-bold tracking-normal">SolarLink</span>
            </Link>

            <div class="max-w-xl pb-1 pt-12 sm:pb-4 lg:pb-8">
                <p class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase text-sky-200">
                    <span class="h-px w-7 bg-sky-300"></span>
                    Clean energy, connected
                </p>
                <h1 class="max-w-lg text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl">
                    Energy work, connected.
                </h1>
                <p class="mt-4 max-w-md text-sm leading-6 text-white/80 sm:text-base sm:leading-7">
                    One network for homeowners, field teams, and the people who keep solar moving.
                </p>
                <div class="mt-8 hidden items-center gap-3 text-xs font-medium text-white/75 sm:flex">
                    <span>Homes</span><span class="h-1 w-1 rounded-full bg-sky-300"></span>
                    <span>Service teams</span><span class="h-1 w-1 rounded-full bg-sky-300"></span>
                    <span>Suppliers</span>
                </div>
            </div>

            <p class="hidden text-xs text-white/60 lg:block">A brighter grid starts with better connections.</p>
        </section>

        <section class="relative flex min-h-[calc(100dvh-290px)] flex-col justify-center px-6 py-10 sm:min-h-[calc(100dvh-340px)] sm:px-10 lg:min-h-dvh lg:px-14 xl:px-20">
            <button
                type="button"
                @click="toggleDarkMode"
                :aria-label="isDark ? 'Switch to light theme' : 'Switch to dark theme'"
                class="absolute right-5 top-5 flex h-10 w-10 items-center justify-center rounded-full text-[#52627a] transition hover:bg-black/5 hover:text-[#17243a] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:text-[#c1cde2] dark:hover:bg-white/10 dark:hover:text-white dark:focus-visible:outline-sky-300 sm:right-8 sm:top-8"
            >
                <Sun v-if="isDark" class="h-5 w-5" aria-hidden="true" />
                <Moon v-else class="h-5 w-5" aria-hidden="true" />
            </button>

            <div class="mx-auto w-full max-w-[430px] animate-fade-in-up">
                <div class="mb-8">
                    <p class="text-xs font-bold uppercase text-blue-800 dark:text-sky-300">Your SolarLink account</p>
                    <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Welcome back.</h2>
                    <p class="mt-2 text-sm leading-6 text-[#66768f] dark:text-[#adbad1]">Sign in to continue to your workspace.</p>
                </div>

                <div class="mb-7">
                    <p class="mb-2.5 text-xs font-semibold text-[#52627a] dark:text-[#c1cde2]">Choose your workspace</p>
                    <nav aria-label="Choose a sign-in portal" class="grid grid-cols-4 gap-2">
                        <Link
                            v-for="portal in portals"
                            :key="portal.key"
                            :href="route(roleRoutes[portal.key])"
                            :aria-current="activeRole === portal.key ? 'page' : undefined"
                            class="flex min-h-[72px] flex-col items-center justify-center gap-2 rounded-lg border px-1.5 py-2 text-center transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:focus-visible:outline-sky-300"
                            :class="activeRole === portal.key ? 'border-blue-700 bg-blue-50 text-blue-950 dark:border-sky-300 dark:bg-blue-950/40 dark:text-white' : 'border-[#dfe7f2] bg-white/70 text-[#66768f] hover:border-[#aebcd3] hover:bg-white dark:border-white/10 dark:bg-white/[0.03] dark:text-[#adbad1] dark:hover:bg-white/[0.07]'"
                        >
                            <component :is="portal.icon" class="h-4 w-4" :class="activeRole === portal.key ? 'text-blue-800 dark:text-sky-300' : ''" aria-hidden="true" />
                            <span class="text-[11px] font-semibold leading-none">{{ portal.label }}</span>
                        </Link>
                    </nav>
                </div>

                <div v-if="status" role="status" class="mb-5 border-l-2 border-blue-700 bg-blue-50 px-4 py-3 text-sm text-blue-900 dark:border-sky-300 dark:bg-blue-950/40 dark:text-blue-100">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold">Email address</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            name="email"
                            autocomplete="username"
                            required
                            autofocus
                            placeholder="you@example.com"
                            :aria-invalid="Boolean(form.errors.email)"
                            :aria-describedby="form.errors.email ? 'email-error' : undefined"
                            class="h-12 w-full rounded-lg border border-[#d6e0ef] bg-white px-4 text-sm text-[#17243a] placeholder:text-[#8999b2] transition focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700/15 dark:border-white/15 dark:bg-white/[0.04] dark:text-white dark:placeholder:text-[#8797b0] dark:focus:border-sky-300 dark:focus:ring-sky-300/15"
                            :class="{ 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.email }"
                        />
                        <p v-if="form.errors.email" id="email-error" class="mt-2 text-sm text-red-700 dark:text-red-300">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-4">
                            <label for="password" class="text-sm font-semibold">Password</label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-semibold text-blue-800 underline-offset-4 hover:underline focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:text-sky-300 dark:focus-visible:outline-sky-300"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                autocomplete="current-password"
                                required
                                placeholder="Enter your password"
                                :aria-invalid="Boolean(form.errors.password)"
                                :aria-describedby="form.errors.password ? 'password-error' : undefined"
                                class="h-12 w-full rounded-lg border border-[#d6e0ef] bg-white px-4 pr-12 text-sm text-[#17243a] placeholder:text-[#8999b2] transition focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700/15 dark:border-white/15 dark:bg-white/[0.04] dark:text-white dark:placeholder:text-[#8797b0] dark:focus:border-sky-300 dark:focus:ring-sky-300/15"
                                :class="{ 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.password }"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-[#66768f] hover:text-[#17243a] focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:text-[#adbad1] dark:hover:text-white dark:focus-visible:outline-sky-300"
                            >
                                <EyeOff v-if="showPassword" class="h-4 w-4" aria-hidden="true" />
                                <Eye v-else class="h-4 w-4" aria-hidden="true" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" id="password-error" class="mt-2 text-sm text-red-700 dark:text-red-300">{{ form.errors.password }}</p>
                    </div>

                    <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-[#52627a] dark:text-[#c1cde2]">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-[#bac8dd] text-blue-800 focus:ring-blue-700 dark:border-white/30 dark:bg-white/5 dark:text-sky-400 dark:focus:ring-sky-300"
                        />
                        Keep me signed in
                    </label>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex h-12 w-full items-center justify-center gap-2 rounded-lg px-5 text-sm font-semibold text-white transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-65 dark:focus-visible:ring-offset-[#101a2c]"
                        :class="'bg-blue-800 hover:bg-blue-900 focus-visible:ring-blue-700 dark:bg-sky-300 dark:text-[#14345d] dark:hover:bg-sky-200 dark:focus-visible:ring-sky-300'"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" aria-hidden="true" />
                        <span>{{ form.processing ? 'Signing in…' : `Continue as ${activePortal.label.toLowerCase()}` }}</span>
                        <ArrowRight v-if="!form.processing" class="h-4 w-4" aria-hidden="true" />
                    </button>
                </form>

                <div v-if="demoAccounts.length" class="mt-6 border-t border-[#dfe7f2] pt-4 dark:border-white/10">
                    <div class="flex items-baseline justify-between gap-3">
                        <h3 class="text-sm font-semibold">Demo logins</h3>
                        <p class="text-xs text-[#66768f] dark:text-[#adbad1]">Password: <span class="font-mono font-semibold">password</span></p>
                    </div>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <button
                            v-for="account in demoAccounts"
                            :key="account.email"
                            type="button"
                            @click="fillDemoAccount(account)"
                            :aria-label="`Use demo login for ${account.name}, ${account.role}`"
                            :aria-pressed="activeRole === account.role && form.email === account.email && form.password === 'password'"
                            class="flex min-w-0 items-center justify-between gap-2 rounded-lg border px-3 py-2.5 text-left transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:focus-visible:outline-sky-300"
                            :class="activeRole === account.role && form.email === account.email ? 'border-blue-700 bg-blue-50 dark:border-sky-300 dark:bg-blue-950/40' : 'border-[#dfe7f2] bg-white/70 hover:border-[#aebcd3] hover:bg-white dark:border-white/10 dark:bg-white/[0.03] dark:hover:bg-white/[0.07]'"
                        >
                            <span class="min-w-0">
                                <span class="flex items-center gap-1.5">
                                    <span class="truncate text-xs font-semibold">{{ account.name }}</span>
                                    <span class="shrink-0 text-[9px] font-semibold uppercase text-blue-800 dark:text-sky-300">{{ account.role }}</span>
                                </span>
                                <span class="mt-0.5 block truncate text-[10px] text-[#66768f] dark:text-[#adbad1]">{{ account.email }}</span>
                            </span>
                            <ArrowRight class="h-4 w-4 shrink-0 text-blue-800 dark:text-sky-300" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <p v-if="registrationRoute" class="mt-7 border-t border-[#dfe7f2] pt-6 text-center text-sm text-[#66768f] dark:border-white/10 dark:text-[#adbad1]">
                    New to SolarLink?
                    <Link :href="route(registrationRoute)" class="ml-1 font-semibold text-blue-800 underline-offset-4 hover:underline dark:text-sky-300">
                        Create an account
                    </Link>
                </p>

                <p class="mt-8 text-center text-xs text-[#8999b2] dark:text-[#8797b0]">Secure access for the SolarLink network</p>
            </div>
        </section>
    </main>
</template>
