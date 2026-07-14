<script setup lang="ts">
import PublicNavbar from '@/Components/Navigation/PublicNavbar.vue'
import Footer from '@/Components/Navigation/Footer.vue'
</script>

<template>
    <div class="relative min-h-screen flex flex-col bg-slate-50 dark:bg-solar-bg-dark transition-colors duration-300">
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
