<script setup lang="ts">
import PublicNavbar from '@/Components/Navigation/PublicNavbar.vue'
import Footer from '@/Components/Navigation/Footer.vue'
import GlobalAlert from '@/Components/GlobalAlert.vue'
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-solar-bg-dark text-slate-900 dark:text-slate-100 font-sans antialiased transition-colors duration-300 flex flex-col">
        <GlobalAlert />
        
        <!-- Premium Navbar with Glassmorphism -->
        <!-- Floating purple glowing blobs to make the aesthetic look top-tier -->
        <div class="absolute top-20 left-10 w-96 h-96 glow-purple rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-slow"></div>
        <div class="absolute top-[800px] right-20 w-80 h-80 glow-purple rounded-full blur-3xl pointer-events-none -z-10"></div>
        
        <!-- Navbar -->
        <PublicNavbar />

        <!-- Main Slot -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>
