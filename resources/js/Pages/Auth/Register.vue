<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'
import { 
    Sun, Moon, ArrowRight, UserCheck, Wrench, Store, 
    Sparkles, CheckCircle2, ChevronRight, Upload, MapPin, Building
} from 'lucide-vue-next'

const props = defineProps<{
    role?: 'customer' | 'technician' | 'vendor'
}>()

const { isDark, toggleDarkMode } = useDarkMode()
const step = ref<number>(1)
const selectedRole = ref<'customer' | 'technician' | 'vendor'>(props.role || 'customer')

// Form using Inertia useForm
const form = useForm({
    name: '',
    email: '',
    password: '',
    role: (props.role || 'customer') as 'customer' | 'technician' | 'vendor',
    
    // Technician specific details
    cert_name: '',
    cert_file: null as File | null,
    years_experience: '3',
    hourly_rate: 85,

    // Vendor specific details
    store_name: '',
    company_address: '',
    vat_number: '',
})

// Keep form role in sync with selectedRole tab
watch(selectedRole, (newRole) => {
    form.role = newRole
})

const handleNextStep = () => {
    if (step.value === 1) {
        step.value = 2
    } else if (step.value === 2) {
        // If customer, we submit. Otherwise go to role specific onboarding
        if (form.role === 'customer') {
            submitForm()
        } else {
            step.value = 3
        }
    } else if (step.value === 3) {
        submitForm()
    }
}

const submitForm = () => {
    form.post('/register', {
        onError: () => {
            // If validation fails on step 2, keep the user on step 2
            if (form.errors.name || form.errors.email || form.errors.password) {
                step.value = 2
            }
        }
    })
}

const handleFileUpload = (e: Event) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        form.cert_file = target.files[0]
    }
}
</script>

<template>
    <Head title="SolarLink — Register Account" />

    <div class="h-screen relative flex items-center justify-center p-4 sm:p-6 bg-slate-50 dark:bg-solar-bg-dark transition-colors duration-300 overflow-hidden">
        <!-- Background decorative glows -->
        <div class="absolute top-10 left-10 w-80 h-80 glow-purple rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-slow"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 glow-purple rounded-full blur-3xl pointer-events-none -z-10"></div>

        <!-- Theme switch -->
        <button 
            @click="toggleDarkMode" 
            class="absolute top-6 right-6 p-3 rounded-xl bg-white/70 dark:bg-solar-primary-dark/40 border border-slate-200 dark:border-white/5 text-solar-primary dark:text-solar-primary-accent shadow-solar hover:scale-105 transition-all"
        >
            <Sun v-slot="isDark" v-if="isDark" class="h-5 w-5" />
            <Moon v-else class="h-5 w-5" />
        </button>

        <div class="w-full max-w-[620px] glass-card p-6 sm:p-8 text-left relative overflow-hidden">
            <!-- Simulated Loading Overlay -->
            <div 
                v-if="form.processing"
                class="absolute inset-0 bg-white/95 dark:bg-solar-bg-dark/95 z-20 flex flex-col items-center justify-center gap-4 text-center p-6"
            >
                <div class="h-16 w-16 rounded-full border-4 border-solar-primary border-t-transparent animate-spin flex items-center justify-center">
                </div>
                <div>
                    <h3 class="font-extrabold text-xl text-slate-800 dark:text-white">Synchronizing Profile...</h3>
                    <p class="text-sm text-slate-500 mt-1">Creating secure database operator credentials...</p>
                </div>
            </div>

            <!-- Header & Steps tracker -->
            <div class="flex items-center justify-between border-b border-solar-primary/10 dark:border-white/5 pb-4 mb-6">
                <Link href="/" class="flex items-center gap-2 group">
                    <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-solar-primary to-solar-primary-accent flex items-center justify-center">
                        <span class="text-white font-extrabold text-sm">SL</span>
                    </div>
                    <span class="font-bold text-lg bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">SolarLink</span>
                </Link>
                <span class="text-xs font-bold text-solar-primary dark:text-solar-primary-accent bg-solar-primary-light dark:bg-solar-primary-dark/80 px-3 py-1 rounded-full uppercase tracking-wider">
                    Step {{ step }} / {{ selectedRole === 'customer' ? '2' : '3' }}
                </span>
            </div>

            <form @submit.prevent="handleNextStep" class="flex flex-col gap-6">
                
                <!-- STEP 1: Select Role -->
                <div v-if="step === 1" class="flex flex-col gap-5 animate-fade-in-up">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-1.5">Select Your Role</h2>
                        <p class="text-xs text-slate-500">Choose the type of account you want to register.</p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button 
                            type="button"
                            @click="selectedRole = 'customer'"
                            class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left border"
                            :class="selectedRole === 'customer' 
                                ? 'bg-solar-primary/10 border-solar-primary dark:bg-solar-primary-dark/50' 
                                : 'bg-slate-50 dark:bg-solar-primary-dark/10 border-transparent hover:bg-slate-100 dark:hover:bg-solar-primary-dark/25'"
                        >
                            <div class="p-3 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary">
                                <UserCheck class="h-6 w-6" />
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-slate-800 dark:text-white">Customer (Asset Owner)</h4>
                                <p class="text-xs text-slate-500">Standard telemetry, parts booking, dispatch calls.</p>
                            </div>
                        </button>

                        <button 
                            type="button"
                            @click="selectedRole = 'technician'"
                            class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left border"
                            :class="selectedRole === 'technician' 
                                ? 'bg-solar-primary/10 border-solar-primary dark:bg-solar-primary-dark/50' 
                                : 'bg-slate-50 dark:bg-solar-primary-dark/10 border-transparent hover:bg-slate-100 dark:hover:bg-solar-primary-dark/25'"
                        >
                            <div class="p-3 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary">
                                <Wrench class="h-6 w-6" />
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-slate-800 dark:text-white">Certified Field Technician</h4>
                                <p class="text-xs text-slate-500">Service ticketing log, calendar availability, and ratings.</p>
                            </div>
                        </button>

                        <button 
                            type="button"
                            @click="selectedRole = 'vendor'"
                            class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left border"
                            :class="selectedRole === 'vendor' 
                                ? 'bg-solar-primary/10 border-solar-primary dark:bg-solar-primary-dark/50' 
                                : 'bg-slate-50 dark:bg-solar-primary-dark/10 border-transparent hover:bg-slate-100 dark:hover:bg-solar-primary-dark/25'"
                        >
                            <div class="p-3 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary">
                                <Store class="h-6 w-6" />
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-slate-800 dark:text-white">Wholesale Hardware Vendor</h4>
                                <p class="text-xs text-slate-500">Wholesale listings, delivery trackers, direct store branding.</p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: General Profile Creation -->
                <div v-if="step === 2" class="flex flex-col gap-4 animate-fade-in-up">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-1">Create Account Profile</h2>
                        <p class="text-xs text-slate-500">Provide your basic account details to register.</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Full Name</label>
                        <input 
                            v-model="form.name"
                            type="text" 
                            required 
                            placeholder="e.g. Clara Oswald"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                            :class="{'border-red-500 focus:border-red-500': form.errors.name}"
                        />
                        <span v-if="form.errors.name" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.name }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                        <input 
                            v-model="form.email"
                            type="email" 
                            required 
                            placeholder="e.g. clara@gmail.com"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                            :class="{'border-red-500 focus:border-red-500': form.errors.email}"
                        />
                        <span v-if="form.errors.email" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.email }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Secure Access Password</label>
                        <input 
                            v-model="form.password"
                            type="password" 
                            required 
                            placeholder="At least 8 characters"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                            :class="{'border-red-500 focus:border-red-500': form.errors.password}"
                        />
                        <span v-if="form.errors.password" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.password }}</span>
                    </div>
                </div>

                <!-- STEP 3: Role Specific Onboarding Specs -->
                <!-- Technician Onboarding Specifics -->
                <div v-if="step === 3 && selectedRole === 'technician'" class="flex flex-col gap-4 animate-fade-in-up">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-1">Upload Certifications</h2>
                        <p class="text-xs text-slate-500">Provide certified contractor credentials to verify dispatch profile.</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Accrediting Authority / Certification Name</label>
                        <input 
                            v-model="form.cert_name"
                            type="text" 
                            required 
                            placeholder="e.g. NABCEP Certified PV Installation Professional"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                            :class="{'border-red-500 focus:border-red-500': form.errors.cert_name}"
                        />
                        <span v-if="form.errors.cert_name" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.cert_name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Active Experience</label>
                            <select 
                                v-model="form.years_experience"
                                class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                                :class="{'border-red-500 focus:border-red-500': form.errors.years_experience}"
                            >
                                <option value="1">1-2 Years</option>
                                <option value="3">3-5 Years</option>
                                <option value="6">6+ Years</option>
                            </select>
                            <span v-if="form.errors.years_experience" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.years_experience }}</span>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Hourly Dispatch Rate</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-sm font-bold text-slate-400">$</span>
                                <input 
                                    v-model="form.hourly_rate"
                                    type="number" 
                                    required 
                                    class="w-full h-11 pl-7 pr-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                                    :class="{'border-red-500 focus:border-red-500': form.errors.hourly_rate}"
                                />
                            </div>
                            <span v-if="form.errors.hourly_rate" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.hourly_rate }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Cert PDF / Licences Copy</label>
                        <div class="h-28 rounded-2xl border-2 border-dashed border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-solar-primary-dark/10 flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-slate-100 dark:hover:bg-solar-primary-dark/20 transition-all relative">
                            <input 
                                type="file" 
                                @change="handleFileUpload"
                                class="absolute inset-0 opacity-0 cursor-pointer"
                            />
                            <Upload class="h-6 w-6 text-solar-primary dark:text-solar-primary-accent" />
                            <span class="text-xs font-semibold text-slate-500">
                                {{ form.cert_file ? form.cert_file.name : 'Click to select license file' }}
                            </span>
                        </div>
                        <span v-if="form.errors.cert_file" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.cert_file }}</span>
                    </div>
                </div>

                <!-- Vendor Onboarding Specifics -->
                <div v-if="step === 3 && selectedRole === 'vendor'" class="flex flex-col gap-4 animate-fade-in-up">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-1">Verify Supplier Brand</h2>
                        <p class="text-xs text-slate-500">Supply company details to begin wholesale parts listings.</p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Store / Brand Name</label>
                        <div class="relative flex items-center">
                            <Building class="absolute left-3.5 h-4 w-4 text-slate-400" />
                            <input 
                                v-model="form.store_name"
                                type="text" 
                                required 
                                placeholder="e.g. EcoGrid wholesale direct"
                                class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                                :class="{'border-red-500 focus:border-red-500': form.errors.store_name}"
                            />
                        </div>
                        <span v-if="form.errors.store_name" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.store_name }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Headquarters Address</label>
                        <div class="relative flex items-center">
                            <MapPin class="absolute left-3.5 h-4 w-4 text-slate-400" />
                            <input 
                                v-model="form.company_address"
                                type="text" 
                                required 
                                placeholder="e.g. Suite 42, Port Industrial, Oakland, CA"
                                class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                                :class="{'border-red-500 focus:border-red-500': form.errors.company_address}"
                            />
                        </div>
                        <span v-if="form.errors.company_address" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.company_address }}</span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Business Verification / VAT License</label>
                        <input 
                            v-model="form.vat_number"
                            type="text" 
                            required 
                            placeholder="e.g. US-VAT-90249219"
                            class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                            :class="{'border-red-500 focus:border-red-500': form.errors.vat_number}"
                        />
                        <span v-if="form.errors.vat_number" class="text-xs text-red-500 font-medium mt-0.5">{{ form.errors.vat_number }}</span>
                    </div>
                </div>

                <!-- Footer buttons -->
                <div class="flex items-center gap-4 mt-2">
                    <button 
                        v-if="step > 1"
                        type="button"
                        @click="step--"
                        class="px-6 h-12 rounded-xl bg-slate-200 dark:bg-solar-primary-dark/50 text-slate-700 dark:text-slate-200 font-bold text-sm"
                    >
                        Previous
                    </button>
                    
                    <button 
                        type="submit" 
                        class="flex-grow h-12 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow-solar-lg hover:shadow-solar-glow transition-all duration-300 btn-glow"
                    >
                        <span>
                            {{ (step === 2 && selectedRole === 'customer') || step === 3 ? 'Create Account' : 'Continue' }}
                        </span>
                        <ChevronRight class="h-4.5 w-4.5" />
                    </button>
                </div>
            </form>

            <div class="text-center mt-6">
                <p class="text-xs text-slate-400 dark:text-slate-500">
                    Already onboarded? 
                    <Link :href="selectedRole === 'customer' ? '/user/login' : `/${selectedRole}/login`" class="font-bold text-solar-primary hover:underline">Secure Login</Link>
                </p>
            </div>
        </div>
    </div>
</template>
