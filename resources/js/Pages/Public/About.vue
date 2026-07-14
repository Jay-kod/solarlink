<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Sparkles, Users, Award, ShieldAlert, Cpu } from 'lucide-vue-next'

const team = [
    {
        name: 'Dr. Evelyn Carter',
        role: 'Founder & Chief Grid Architect',
        avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=150',
        bio: 'Former NREL Senior Scientist, pioneering battery degradation models.'
    },
    {
        name: 'Alexei Vance',
        role: 'Head of Supply Chain Logistics',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150',
        bio: '12 years directing global wholesale hardware routes and solar parts logistics.'
    },
    {
        name: 'Siddharth Patel',
        role: 'Lead Predictive AI Engineer',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150',
        bio: 'Specialist in low-power IoT algorithms and grid telemetry synchrony.'
    }
]
</script>

<template>
    <Head title="SolarLink — Our Team & Story" />

    <PublicLayout>
        <!-- Hero section -->
        <section class="pt-16 pb-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col gap-6 items-center" v-reveal>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/60 text-solar-primary dark:text-solar-primary-accent font-bold text-xs tracking-wide uppercase">
                <Sparkles class="h-3.5 w-3.5" />
                <span>Our Renewable Mission</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white leading-tight">
                Pioneering the Future of <br/>
                <span class="bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">Decentralized Clean Energy</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-xl leading-relaxed">
                We believe that clean power generation should be highly resilient, easily procurable, and supported by a network of local service experts.
            </p>
        </section>

        <!-- Company Values -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-8 flex flex-col gap-4 text-left" v-reveal>
                    <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-xl self-start">
                        <Users class="h-6 w-6" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 dark:text-white">Community Driven</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Supporting local solar installers and specialized technicians, creating jobs in the regional clean-tech maintenance sector.
                    </p>
                </div>
                <div class="glass-card p-8 flex flex-col gap-4 text-left" v-reveal="{ delay: 100 }">
                    <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-xl self-start">
                        <Cpu class="h-6 w-6" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 dark:text-white">Diagnostic Innovation</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Fusing high-frequency IoT inverter feeds with smart failure-prediction maps to prevent catastrophic panel outages.
                    </p>
                </div>
                <div class="glass-card p-8 flex flex-col gap-4 text-left" v-reveal="{ delay: 200 }">
                    <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-xl self-start">
                        <Award class="h-6 w-6" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 dark:text-white">Direct Wholesale Value</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Establishing direct wholesale supplier connections to make clean home battery stack expansion affordable for standard owners.
                    </p>
                </div>
            </div>
        </section>

        <!-- Team profiles -->
        <section class="py-20 bg-solar-primary-light/20 dark:bg-solar-primary-dark/10 border-t border-solar-primary/10 dark:border-white/5 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16 flex flex-col gap-3" v-reveal>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Expert Leadership</h2>
                    <p class="text-sm text-slate-500">Combining decades of academic research, hardware logistics, and AI telemetry modelling.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div 
                        v-for="(member, idx) in team" 
                        :key="member.name"
                        v-reveal="{ delay: idx * 100 }"
                        class="glass-card p-6 flex flex-col items-center text-center gap-4 bg-white dark:bg-solar-bg-dark/40"
                    >
                        <img :src="member.avatar" :alt="member.name" class="h-24 w-24 rounded-full object-cover border-2 border-solar-primary shadow-solar" />
                        <div>
                            <h4 class="font-bold text-base text-slate-800 dark:text-white">{{ member.name }}</h4>
                            <p class="text-xs text-solar-primary dark:text-solar-primary-accent font-semibold">{{ member.role }}</p>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed max-w-xs">{{ member.bio }}</p>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
