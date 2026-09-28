<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeft,
    ArrowRight,
    Building2,
    Eye,
    EyeOff,
    FileUp,
    House,
    LoaderCircle,
    MapPin,
    Moon,
    ShieldCheck,
    Store,
    Sun,
    Wrench,
} from 'lucide-vue-next'
import { useDarkMode } from '@/composables/useDarkMode'

type Role = 'customer' | 'technician' | 'vendor'

const props = defineProps<{
    role?: Role
}>()

const { isDark, toggleDarkMode } = useDarkMode()
const isRolePreselected = computed(() => Boolean(props.role))
const selectedRole = ref<Role>(props.role ?? 'customer')
const step = ref(props.role ? 2 : 1)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const portals = [
    { key: 'customer', label: 'Homeowner', icon: House, detail: 'Find trusted solar services for your home.' },
    { key: 'technician', label: 'Technician', icon: Wrench, detail: 'Offer field services and manage your work.' },
    { key: 'vendor', label: 'Supplier', icon: Store, detail: 'List products and manage wholesale orders.' },
] as const

const loginRoutes: Record<Role, string> = {
    customer: 'user.login',
    technician: 'technician.login',
    vendor: 'vendor.login',
}

const stepCount = computed(() => selectedRole.value === 'customer' ? 2 : 3)
const displayStep = computed(() => isRolePreselected.value ? step.value - 1 : step.value)
const totalDisplaySteps = computed(() => isRolePreselected.value ? stepCount.value - 1 : stepCount.value)
const progressWidth = computed(() => `${(displayStep.value / totalDisplaySteps.value) * 100}%`)
const stepTitle = computed(() => {
    if (step.value === 1) return 'Choose your workspace'
    if (step.value === 2) return 'Create your account'
    return selectedRole.value === 'technician' ? 'Professional details' : 'Supplier details'
})
const stepDescription = computed(() => {
    if (step.value === 1) return 'Choose how you will use SolarLink.'
    if (step.value === 2) return 'Add your sign-in details to get started.'
    return selectedRole.value === 'technician'
        ? 'Share your qualifications and service rate.'
        : 'Add the details customers need to identify your business.'
})
const inputClass = 'h-12 w-full rounded-lg border border-[#d6e0ef] bg-white px-4 text-sm text-[#17243a] placeholder:text-[#8999b2] transition focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-700/15 dark:border-white/15 dark:bg-white/[0.04] dark:text-white dark:placeholder:text-[#8797b0] dark:focus:border-sky-300 dark:focus:ring-sky-300/15'
const labelClass = 'mb-2 block text-sm font-semibold text-[#243750] dark:text-[#e4ecf8]'
const errorClass = 'mt-2 text-sm text-red-700 dark:text-red-300'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: selectedRole.value,
    cert_name: '',
    cert_file: null as File | null,
    years_experience: '3',
    hourly_rate: 85,
    store_name: '',
    company_address: '',
    vat_number: '',
})

const passwordStrength = computed(() => {
    const password = form.password || ''

    if (!password) return { label: 'Not set', width: '0%', bar: 'bg-[#dfe7f2]', text: 'text-[#8999b2]' }

    let score = 0
    if (password.length >= 8) score += 1
    if (password.length >= 12) score += 1
    if (/[A-Z]/.test(password)) score += 1
    if (/[a-z]/.test(password)) score += 1
    if (/[0-9]/.test(password)) score += 1
    if (/[^A-Za-z0-9]/.test(password)) score += 1

    if (score <= 2) return { label: 'Weak', width: '33%', bar: 'bg-red-500', text: 'text-red-700 dark:text-red-300' }
    if (score <= 4) return { label: 'Moderate', width: '66%', bar: 'bg-amber-500', text: 'text-amber-700 dark:text-amber-300' }
    return { label: 'Strong', width: '100%', bar: 'bg-blue-700', text: 'text-blue-800 dark:text-sky-300' }
})

watch(selectedRole, (role) => {
    form.role = role
})

const advance = () => {
    if (step.value === 1) {
        step.value = 2
        return
    }

    if (step.value === 2 && selectedRole.value !== 'customer') {
        step.value = 3
        return
    }

    form.post('/register', {
        onError: () => {
            if (form.errors.name || form.errors.email || form.errors.password || form.errors.password_confirmation) {
                step.value = 2
            }
        },
    })
}

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files?.length) form.cert_file = target.files[0]
}
</script>

<template>
    <Head title="Create account · SolarLink" />

    <main class="min-h-dvh bg-[#f4f7fc] text-[#17243a] dark:bg-[#101a2c] dark:text-[#edf3ff] lg:grid lg:grid-cols-[minmax(0,0.92fr)_minmax(520px,1.08fr)]">
        <section class="relative isolate flex min-h-[250px] flex-col justify-between overflow-hidden bg-[#17355d] px-6 py-6 text-white sm:min-h-[300px] sm:px-10 sm:py-8 lg:min-h-dvh lg:px-12 lg:py-10 xl:px-16">
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

            <div class="max-w-xl pb-1 pt-10 sm:pb-4 lg:pb-8">
                <p class="mb-4 flex items-center gap-2 text-xs font-semibold uppercase text-sky-200">
                    <span class="h-px w-7 bg-sky-300"></span>
                    Clean energy, connected
                </p>
                <h1 class="max-w-lg text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl">Energy work, connected.</h1>
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

        <section class="relative flex min-h-[calc(100dvh-250px)] flex-col justify-center px-6 py-10 sm:min-h-[calc(100dvh-300px)] sm:px-10 lg:min-h-dvh lg:px-14 lg:py-14 xl:px-20">
            <button
                type="button"
                @click="toggleDarkMode"
                :aria-label="isDark ? 'Switch to light theme' : 'Switch to dark theme'"
                class="absolute right-5 top-5 flex h-10 w-10 items-center justify-center rounded-full text-[#52627a] transition hover:bg-black/5 hover:text-[#17243a] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:text-[#c1cde2] dark:hover:bg-white/10 dark:hover:text-white dark:focus-visible:outline-sky-300 sm:right-8 sm:top-8"
            >
                <Sun v-if="isDark" class="h-5 w-5" aria-hidden="true" />
                <Moon v-else class="h-5 w-5" aria-hidden="true" />
            </button>

            <div class="mx-auto w-full max-w-[470px] animate-fade-in-up">
                <div class="mb-7">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <p class="text-xs font-bold uppercase text-blue-800 dark:text-sky-300">Create your SolarLink account</p>
                        <span class="shrink-0 text-xs font-semibold text-[#66768f] dark:text-[#adbad1]">{{ displayStep }} of {{ totalDisplaySteps }}</span>
                    </div>
                    <div class="mb-6 h-1 overflow-hidden rounded-full bg-[#dfe7f2] dark:bg-white/10" role="progressbar" :aria-valuenow="displayStep" :aria-valuemin="1" :aria-valuemax="totalDisplaySteps" :aria-label="`Registration step ${displayStep} of ${totalDisplaySteps}`">
                        <div class="h-full rounded-full bg-blue-800 transition-[width] duration-300 dark:bg-sky-300" :style="{ width: progressWidth }"></div>
                    </div>
                    <h2 class="text-3xl font-semibold leading-tight sm:text-4xl">{{ stepTitle }}</h2>
                    <p class="mt-2 text-sm leading-6 text-[#66768f] dark:text-[#adbad1]">{{ stepDescription }}</p>
                </div>

                <form @submit.prevent="advance" class="space-y-5">
                    <div v-if="!isRolePreselected && step === 1" class="space-y-3">
                        <button
                            v-for="portal in portals"
                            :key="portal.key"
                            type="button"
                            :aria-pressed="selectedRole === portal.key"
                            @click="selectedRole = portal.key"
                            class="flex w-full items-center gap-4 rounded-lg border p-4 text-left transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:focus-visible:outline-sky-300"
                            :class="selectedRole === portal.key ? 'border-blue-700 bg-blue-50 dark:border-sky-300 dark:bg-blue-950/40' : 'border-[#dfe7f2] bg-white/70 hover:border-[#aebcd3] hover:bg-white dark:border-white/10 dark:bg-white/[0.03] dark:hover:bg-white/[0.07]'"
                        >
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-sky-300">
                                <component :is="portal.icon" class="h-5 w-5" aria-hidden="true" />
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold">{{ portal.label }}</span>
                                <span class="mt-1 block text-xs leading-5 text-[#66768f] dark:text-[#adbad1]">{{ portal.detail }}</span>
                            </span>
                            <span class="h-4 w-4 shrink-0 rounded-full border" :class="selectedRole === portal.key ? 'border-[5px] border-blue-800 dark:border-sky-300' : 'border-[#bac8dd] dark:border-white/30'" aria-hidden="true"></span>
                        </button>
                    </div>

                    <div v-if="step === 2" class="space-y-4">
                        <div>
                            <label for="name" :class="labelClass">Full name</label>
                            <input id="name" v-model="form.name" type="text" name="name" autocomplete="name" required autofocus placeholder="Your name" :aria-invalid="Boolean(form.errors.name)" :aria-describedby="form.errors.name ? 'name-error' : undefined" :class="[inputClass, { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.name }]" />
                            <p v-if="form.errors.name" id="name-error" :class="errorClass">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="email" :class="labelClass">Email address</label>
                            <input id="email" v-model="form.email" type="email" name="email" autocomplete="email" required placeholder="you@example.com" :aria-invalid="Boolean(form.errors.email)" :aria-describedby="form.errors.email ? 'email-error' : undefined" :class="[inputClass, { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.email }]" />
                            <p v-if="form.errors.email" id="email-error" :class="errorClass">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" :class="labelClass">Password</label>
                            <div class="relative">
                                <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="new-password" required placeholder="At least 8 characters" :aria-invalid="Boolean(form.errors.password)" :aria-describedby="form.errors.password ? 'password-error' : 'password-strength password-strength-label'" :class="[inputClass, 'pr-12', { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.password }]" />
                                <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-[#66768f] hover:text-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:text-[#adbad1] dark:hover:text-sky-300 dark:focus-visible:outline-sky-300">
                                    <EyeOff v-if="showPassword" class="h-4 w-4" aria-hidden="true" />
                                    <Eye v-else class="h-4 w-4" aria-hidden="true" />
                                </button>
                            </div>
                            <div class="mt-3 space-y-2" aria-live="polite">
                                <div id="password-strength" class="h-1.5 overflow-hidden rounded-full bg-[#dfe7f2] dark:bg-white/10">
                                    <div class="h-full rounded-full transition-[width] duration-300" :class="passwordStrength.bar" :style="{ width: passwordStrength.width }"></div>
                                </div>
                                <p id="password-strength-label" class="flex items-center justify-between text-xs">
                                    <span class="text-[#66768f] dark:text-[#adbad1]">Password strength</span>
                                    <span class="font-semibold" :class="passwordStrength.text">{{ passwordStrength.label }}</span>
                                </p>
                            </div>
                            <p v-if="form.errors.password" id="password-error" :class="errorClass">{{ form.errors.password }}</p>
                        </div>

                        <div>
                            <label for="password_confirmation" :class="labelClass">Confirm password</label>
                            <div class="relative">
                                <input id="password_confirmation" v-model="form.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" required placeholder="Enter your password again" :aria-invalid="Boolean(form.errors.password_confirmation)" :aria-describedby="form.errors.password_confirmation ? 'password-confirmation-error' : undefined" :class="[inputClass, 'pr-12', { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.password_confirmation }]" />
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" :aria-label="showConfirmPassword ? 'Hide confirmation password' : 'Show confirmation password'" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-[#66768f] hover:text-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700 dark:text-[#adbad1] dark:hover:text-sky-300 dark:focus-visible:outline-sky-300">
                                    <EyeOff v-if="showConfirmPassword" class="h-4 w-4" aria-hidden="true" />
                                    <Eye v-else class="h-4 w-4" aria-hidden="true" />
                                </button>
                            </div>
                            <p v-if="form.errors.password_confirmation" id="password-confirmation-error" :class="errorClass">{{ form.errors.password_confirmation }}</p>
                        </div>
                    </div>

                    <div v-if="step === 3 && selectedRole === 'technician'" class="space-y-4">
                        <div>
                            <label for="cert_name" :class="labelClass">Certification name</label>
                            <input id="cert_name" v-model="form.cert_name" type="text" name="cert_name" required placeholder="e.g. NABCEP PV Installation Professional" :aria-invalid="Boolean(form.errors.cert_name)" :aria-describedby="form.errors.cert_name ? 'cert-name-error' : undefined" :class="[inputClass, { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.cert_name }]" />
                            <p v-if="form.errors.cert_name" id="cert-name-error" :class="errorClass">{{ form.errors.cert_name }}</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="years_experience" :class="labelClass">Experience</label>
                                <select id="years_experience" v-model="form.years_experience" name="years_experience" :class="inputClass" :aria-invalid="Boolean(form.errors.years_experience)">
                                    <option value="1">1–2 years</option>
                                    <option value="3">3–5 years</option>
                                    <option value="6">6+ years</option>
                                </select>
                                <p v-if="form.errors.years_experience" :class="errorClass">{{ form.errors.years_experience }}</p>
                            </div>
                            <div>
                                <label for="hourly_rate" :class="labelClass">Hourly rate</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-4 flex items-center text-sm text-[#66768f] dark:text-[#adbad1]">$</span>
                                    <input id="hourly_rate" v-model="form.hourly_rate" type="number" name="hourly_rate" min="0" required :aria-invalid="Boolean(form.errors.hourly_rate)" :class="[inputClass, 'pl-8', { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.hourly_rate }]" />
                                </div>
                                <p v-if="form.errors.hourly_rate" :class="errorClass">{{ form.errors.hourly_rate }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="cert_file" :class="labelClass">Certification file <span class="font-normal text-[#8999b2]">(optional)</span></label>
                            <div class="relative flex min-h-20 items-center gap-3 rounded-lg border border-dashed border-[#bac8dd] bg-white/60 px-4 py-3 transition hover:border-blue-700 dark:border-white/20 dark:bg-white/[0.03] dark:hover:border-sky-300">
                                <FileUp class="h-5 w-5 shrink-0 text-blue-800 dark:text-sky-300" aria-hidden="true" />
                                <span class="min-w-0 flex-1 truncate text-sm text-[#52627a] dark:text-[#c1cde2]">{{ form.cert_file?.name ?? 'Choose a file to upload' }}</span>
                                <input id="cert_file" type="file" name="cert_file" @change="handleFileUpload" class="absolute inset-0 cursor-pointer opacity-0" />
                            </div>
                            <p v-if="form.errors.cert_file" :class="errorClass">{{ form.errors.cert_file }}</p>
                        </div>
                    </div>

                    <div v-if="step === 3 && selectedRole === 'vendor'" class="space-y-4">
                        <div>
                            <label for="store_name" :class="labelClass">Store or company name</label>
                            <div class="relative">
                                <Building2 class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8999b2]" aria-hidden="true" />
                                <input id="store_name" v-model="form.store_name" type="text" name="store_name" required placeholder="Your business name" :aria-invalid="Boolean(form.errors.store_name)" :class="[inputClass, 'pl-11', { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.store_name }]" />
                            </div>
                            <p v-if="form.errors.store_name" :class="errorClass">{{ form.errors.store_name }}</p>
                        </div>

                        <div>
                            <label for="company_address" :class="labelClass">Business address</label>
                            <div class="relative">
                                <MapPin class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8999b2]" aria-hidden="true" />
                                <input id="company_address" v-model="form.company_address" type="text" name="company_address" autocomplete="street-address" required placeholder="Street, city, region" :aria-invalid="Boolean(form.errors.company_address)" :class="[inputClass, 'pl-11', { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.company_address }]" />
                            </div>
                            <p v-if="form.errors.company_address" :class="errorClass">{{ form.errors.company_address }}</p>
                        </div>

                        <div>
                            <label for="vat_number" :class="labelClass">Business registration or VAT number</label>
                            <input id="vat_number" v-model="form.vat_number" type="text" name="vat_number" required placeholder="Registration number" :aria-invalid="Boolean(form.errors.vat_number)" :class="[inputClass, { 'border-red-600 focus:border-red-600 focus:ring-red-600/15 dark:border-red-400': form.errors.vat_number }]" />
                            <p v-if="form.errors.vat_number" :class="errorClass">{{ form.errors.vat_number }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button
                            v-if="step > 1"
                            type="button"
                            @click="step--"
                            class="flex h-12 items-center justify-center gap-2 rounded-lg border border-[#d6e0ef] px-4 text-sm font-semibold text-[#52627a] transition hover:bg-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 dark:border-white/15 dark:text-[#c1cde2] dark:hover:bg-white/[0.05] dark:focus-visible:outline-sky-300"
                        >
                            <ArrowLeft class="h-4 w-4" aria-hidden="true" />
                            Back
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex h-12 flex-1 items-center justify-center gap-2 rounded-lg bg-blue-800 px-5 text-sm font-semibold text-white transition hover:bg-blue-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700 disabled:cursor-not-allowed disabled:opacity-65 dark:bg-sky-300 dark:text-[#14345d] dark:hover:bg-sky-200 dark:focus-visible:outline-sky-300"
                        >
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" aria-hidden="true" />
                            <span>{{ form.processing ? 'Creating account…' : step === stepCount ? 'Create account' : 'Continue' }}</span>
                            <ArrowRight v-if="!form.processing" class="h-4 w-4" aria-hidden="true" />
                        </button>
                    </div>
                </form>

                <p class="mt-7 border-t border-[#dfe7f2] pt-6 text-center text-sm text-[#66768f] dark:border-white/10 dark:text-[#adbad1]">
                    Already have an account?
                    <Link :href="route(loginRoutes[selectedRole])" class="ml-1 font-semibold text-blue-800 underline-offset-4 hover:underline dark:text-sky-300">
                        Sign in
                    </Link>
                </p>
            </div>
        </section>
    </main>
</template>
