<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Send, MapPin, Phone, Mail } from 'lucide-vue-next'
</script>

<template>
    <footer class="bg-white dark:bg-solar-bg-dark border-t border-solar-primary/10 dark:border-white/5 pt-16 pb-12 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Branding -->
                <div class="md:col-span-1">
                    <Link href="/" class="flex items-center gap-2 group mb-4">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-solar-primary to-solar-primary-accent flex items-center justify-center">
                            <span class="text-white font-extrabold text-sm">SL</span>
                        </div>
                        <span class="font-bold text-xl tracking-tight bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">SolarLink</span>
                    </Link>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                        Connecting solar owners with expert, certified technicians and top-tier vendors for seamless energy maintenance and smart procurement.
                    </p>
                    <div class="flex gap-4 text-slate-400 dark:text-slate-500">
                        <span class="hover:text-solar-primary cursor-pointer transition-colors"><i class="fab fa-twitter text-lg"></i></span>
                        <span class="hover:text-solar-primary cursor-pointer transition-colors"><i class="fab fa-linkedin text-lg"></i></span>
                        <span class="hover:text-solar-primary cursor-pointer transition-colors"><i class="fab fa-facebook text-lg"></i></span>
                        <span class="hover:text-solar-primary cursor-pointer transition-colors"><i class="fab fa-instagram text-lg"></i></span>
                    </div>
                </div>

                <!-- Fast Links -->
                <div>
                    <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4">Solutions</h3>
                    <ul class="flex flex-col gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <li><Link href="/features" class="hover:text-solar-primary transition-colors">Tech Dispatch</Link></li>
                        <li><Link href="/features" class="hover:text-solar-primary transition-colors">Marketplace Procurement</Link></li>
                        <li><Link href="/features" class="hover:text-solar-primary transition-colors">Real-time Telemetry</Link></li>
                        <li><Link href="/pricing" class="hover:text-solar-primary transition-colors">Predictive Maintenance</Link></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4">Company</h3>
                    <ul class="flex flex-col gap-3 text-sm text-slate-500 dark:text-slate-400">
                        <li><Link href="/about" class="hover:text-solar-primary transition-colors">About Us</Link></li>
                        <li><Link href="/about" class="hover:text-solar-primary transition-colors">Careers</Link></li>
                        <li><Link href="/contact" class="hover:text-solar-primary transition-colors">Press Kit</Link></li>
                        <li><Link href="/faq" class="hover:text-solar-primary transition-colors">Help Center / FAQ</Link></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-4">Stay Connected</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                        Subscribe to get updates on solar technology news and seasonal maintenance specials.
                    </p>
                    <div class="relative flex items-center">
                        <input 
                            type="email" 
                            placeholder="Enter your email"
                            class="w-full pl-4 pr-12 h-11 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-sm focus:outline-none focus:border-solar-primary transition-all duration-300"
                        />
                        <button class="absolute right-1.5 h-8 w-8 rounded-lg bg-solar-primary hover:bg-solar-primary-active text-white flex items-center justify-center shadow-solar-glow transition-all duration-300">
                            <Send class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="h-px bg-slate-100 dark:bg-white/5 my-10"></div>

            <!-- Bottom legal -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400 dark:text-slate-500">
                <p>&copy; 2026 SolarLink Inc. All rights reserved.</p>
                <div class="flex gap-6">
                    <span class="hover:text-solar-primary cursor-pointer transition-colors">Privacy Policy</span>
                    <span class="hover:text-solar-primary cursor-pointer transition-colors">Terms of Service</span>
                    <span class="hover:text-solar-primary cursor-pointer transition-colors">Cookie Policy</span>
                </div>
            </div>
        </div>
    </footer>
</template>
