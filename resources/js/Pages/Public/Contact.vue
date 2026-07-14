<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Sparkles, Mail, Phone, MapPin, Send, CheckCircle2 } from 'lucide-vue-next'

const isLoading = ref(true)
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
    }, 800)
})

const formSubmitted = ref(false)
const form = ref({
    name: '',
    email: '',
    subject: 'support',
    message: ''
})

const submitForm = () => {
    formSubmitted.value = true
    setTimeout(() => {
        formSubmitted.value = false
        form.value = { name: '', email: '', subject: 'support', message: '' }
    }, 4000)
}
</script>

<template>
    <Head title="SolarLink — Contact Us" />

    <PublicLayout>
        <!-- Hero section -->
        <section class="pt-16 pb-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col gap-6 items-center" v-reveal>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/60 text-solar-primary dark:text-solar-primary-accent font-bold text-xs tracking-wide uppercase">
                <Sparkles class="h-3.5 w-3.5" />
                <span>Get in Touch</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white leading-tight">
                Connect with the <br/>
                <span class="bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">SolarLink Support Network</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-xl leading-relaxed">
                Whether you are an asset owner, installer, direct manufacturer, or emergency repair technician, we would love to connect.
            </p>
        </section>

        <!-- Main Content -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- SKELETON MAIN CONTENT -->
            <div v-if="isLoading" class="grid grid-cols-1 lg:grid-cols-12 gap-12 animate-pulse">
                <!-- Skeleton Contact Cards (Left) -->
                <div class="lg:col-span-5 flex flex-col gap-6 text-left">
                    <div v-for="i in 3" :key="i" class="glass-card p-6 flex items-start gap-4 bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 h-24">
                        <div class="h-10 w-10 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                        <div class="flex-grow space-y-2">
                            <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                        </div>
                    </div>
                </div>

                <!-- Skeleton Contact Form (Right) -->
                <div class="lg:col-span-7">
                    <div class="glass-card p-8 flex flex-col gap-6 bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 h-[480px]">
                        <div class="grid grid-cols-2 gap-4">
                            <div v-for="i in 2" :key="i" class="space-y-2">
                                <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                                <div class="h-11 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
                            <div class="h-11 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
                            <div class="h-32 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                        </div>
                        <div class="h-12 bg-slate-200 dark:bg-slate-800 rounded-xl mt-2"></div>
                    </div>
                </div>
            </div>

            <!-- REAL MAIN CONTENT -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-12 animate-fade-in">
                
                <!-- Contact info cards (Left) -->
                <div class="lg:col-span-5 flex flex-col gap-6 text-left" v-reveal>
                    <div class="glass-card p-6 flex items-start gap-4">
                        <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-xl">
                            <Mail class="h-6 w-6" />
                        </div>
                        <div>
                            <h4 class="font-extrabold text-base text-slate-800 dark:text-white">Email Us</h4>
                            <p class="text-xs text-slate-400 dark:text-slate-455 mb-1">General support & enterprise bids</p>
                            <p class="text-sm font-bold text-solar-primary">support@solarlink.io</p>
                        </div>
                    </div>

                    <div class="glass-card p-6 flex items-start gap-4">
                        <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-xl">
                            <Phone class="h-6 w-6" />
                        </div>
                        <div>
                            <h4 class="font-extrabold text-base text-slate-800 dark:text-white">Call Direct</h4>
                            <p class="text-xs text-slate-400 dark:text-slate-455 mb-1">Emergency technician dispatches</p>
                            <p class="text-sm font-bold text-solar-primary">1-800-SOLAR-NOW</p>
                        </div>
                    </div>

                    <div class="glass-card p-6 flex items-start gap-4">
                        <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-xl">
                            <MapPin class="h-6 w-6" />
                        </div>
                        <div>
                            <h4 class="font-extrabold text-base text-slate-800 dark:text-white">Office Headquarters</h4>
                            <p class="text-xs text-slate-400 dark:text-slate-455 mb-1">Carbon innovation labs</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">555 Mission District, San Francisco, CA</p>
                        </div>
                    </div>
                </div>

                <!-- Contact form card (Right) -->
                <div class="lg:col-span-7" v-reveal="{ delay: 200 }">
                    <div class="glass-card p-8 text-left relative overflow-hidden">
                        <div 
                            v-if="formSubmitted" 
                            class="absolute inset-0 bg-white/95 dark:bg-solar-bg-dark/95 z-20 flex flex-col items-center justify-center gap-4 text-center p-6 animate-fade-in-up"
                        >
                            <div class="h-16 w-16 rounded-full bg-solar-success/10 text-solar-success flex items-center justify-center pulse-glow">
                                <CheckCircle2 class="h-10 w-10" />
                            </div>
                            <div>
                                <h3 class="font-extrabold text-xl text-slate-800 dark:text-white">Message Transmitted!</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-450 mt-1 max-w-xs">
                                    Our diagnostics support desk has received your ticket request. We will follow up shortly.
                                </p>
                            </div>
                        </div>

                        <form @submit.prevent="submitForm" class="flex flex-col gap-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label class="text-xs font-bold text-slate-650 uppercase tracking-wider">Your Name</label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        required 
                                        placeholder="e.g. Timothy Vance"
                                        class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-250 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                                    />
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="text-xs font-bold text-slate-650 uppercase tracking-wider">Email Address</label>
                                    <input 
                                        v-model="form.email" 
                                        type="email" 
                                        required 
                                        placeholder="e.g. tim@gmail.com"
                                        class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-250 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                                    />
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-650 uppercase tracking-wider">Subject Matter</label>
                                <select 
                                    v-model="form.subject"
                                    class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-250 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                                >
                                    <option value="support">Technical Array Support</option>
                                    <option value="marketplace">Marketplace & Orders</option>
                                    <option value="technician">Technician Program</option>
                                    <option value="partnership">Enterprise Bids</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-xs font-bold text-slate-650 uppercase tracking-wider">Detailed Message</label>
                                <textarea 
                                    v-model="form.message" 
                                    rows="5" 
                                    required 
                                    placeholder="Briefly describe your solar system layout or parts procurement questions..."
                                    class="p-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-250 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300 resize-none"
                                ></textarea>
                            </div>

                            <button 
                                type="submit" 
                                class="h-12 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow-solar-lg hover:shadow-solar-glow transition-all duration-300 btn-glow"
                            >
                                <span>Transmit Message</span>
                                <Send class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
