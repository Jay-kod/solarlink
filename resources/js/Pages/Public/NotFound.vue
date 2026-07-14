<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { ShieldAlert, ArrowLeft, Home } from 'lucide-vue-next'
</script>

<template>
    <Head title="SolarLink — Page Not Found" />

    <PublicLayout>
        <section class="min-h-[70vh] flex flex-col items-center justify-center text-center px-4 max-w-xl mx-auto gap-6">
            <div class="h-20 w-20 rounded-full bg-solar-danger/10 text-solar-danger flex items-center justify-center shadow-solar-glow pulse-glow mb-2 animate-bounce">
                <ShieldAlert class="h-10 w-10" />
            </div>
            
            <h1 class="text-7xl font-extrabold text-slate-900 dark:text-white leading-none tracking-tight">404</h1>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">Grid Signal Lost!</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                The solar routing nodes could not locate the requested system address. The page may have been decommissioned or moved to a different array segment.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 mt-2">
                <button 
                    onclick="history.back()"
                    class="px-5 h-11 rounded-xl bg-slate-200 dark:bg-solar-primary-dark text-slate-700 dark:text-slate-200 font-bold text-sm flex items-center justify-center gap-2 hover:scale-105 transition-all duration-300"
                >
                    <ArrowLeft class="h-4 w-4" />
                    <span>Return Back</span>
                </button>
                <Link 
                    href="/" 
                    class="px-5 h-11 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow-solar-lg hover:shadow-solar-glow hover:scale-105 transition-all duration-300 btn-glow"
                >
                    <Home class="h-4 w-4" />
                    <span>Back to Home</span>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
