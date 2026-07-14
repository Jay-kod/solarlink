<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'
import { Sun, Moon, Menu, X, ArrowRight, UserCheck, ShieldAlert, Wrench, Store } from 'lucide-vue-next'

const { isDark, toggleDarkMode, initDarkMode } = useDarkMode()
const isMobileMenuOpen = ref(false)

onMounted(() => {
    initDarkMode()
})

const page = usePage()
const authUser = computed(() => {
    const auth = page.props.auth as any
    return auth ? auth.user : null
})

const getDashboardUrl = (role: string) => {
    return role === 'customer' ? '/user' : '/' + role
}

const navLinks = [
    { name: 'Features', href: '/features' },
    { name: 'Pricing', href: '/pricing' },
    { name: 'About Us', href: '/about' },
    { name: 'FAQ', href: '/faq' },
    { name: 'Contact', href: '/contact' }
]

const roles = [
    { name: 'Customer App', href: '/user', icon: UserCheck, desc: 'Client portal & bookings' },
    { name: 'Technician Panel', href: '/technician', icon: Wrench, desc: 'Jobs & scheduling' },
    { name: 'Vendor Portal', href: '/vendor', icon: Store, desc: 'Store & inventory' },
    { name: 'Admin Dashboard', href: '/admin', icon: ShieldAlert, desc: 'Global settings & moderation' }
]
</script>

<template>
    <nav class="sticky top-0 z-50 bg-white/75 dark:bg-solar-bg-dark/75 backdrop-blur-md border-b border-solar-primary/10 dark:border-white/5 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <Link href="/" class="flex items-center gap-2 group">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-solar-primary to-solar-primary-accent flex items-center justify-center shadow-solar-glow group-hover:scale-105 transition-transform duration-300">
                            <span class="text-white font-extrabold text-xl tracking-tight">SL</span>
                        </div>
                        <span class="font-bold text-2xl tracking-tight bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent group-hover:opacity-90 transition-opacity">SolarLink</span>
                    </Link>
                </div>

                <!-- Desktop Links -->
                <div class="hidden md:flex items-center gap-8">
                    <Link 
                        v-for="link in navLinks" 
                        :key="link.name" 
                        :href="link.href"
                        class="text-slate-600 dark:text-slate-300 hover:text-solar-primary dark:hover:text-solar-primary-accent font-medium text-sm transition-colors"
                    >
                        {{ link.name }}
                    </Link>
                </div>

                <!-- Action CTAs -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Theme Toggle -->
                    <button 
                        @click="toggleDarkMode" 
                        class="p-2.5 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark/50 text-solar-primary dark:text-solar-primary-accent hover:scale-105 transition-transform duration-300"
                        title="Toggle theme"
                    >
                        <Sun v-if="isDark" class="h-5 w-5" />
                        <Moon v-else class="h-5 w-5" />
                    </button>

                    <!-- Authenticated User Dashboard Link -->
                    <template v-if="authUser">
                        <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-100/80 dark:bg-solar-primary-dark/30 border border-solar-primary/5 dark:border-white/5 mr-1 select-none">
                            <div class="h-8 w-8 rounded-lg bg-solar-primary/10 flex items-center justify-center font-bold text-xs text-solar-primary uppercase ring-2 ring-solar-primary/10 shrink-0">
                                {{ authUser.name.charAt(0) }}
                            </div>
                            <div class="flex flex-col text-left min-w-0">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate max-w-[110px]">{{ authUser.name }}</span>
                                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ authUser.role }}</span>
                            </div>
                        </div>
                        <Link 
                            :href="getDashboardUrl(authUser.role)"
                            class="flex items-center gap-2 px-4 h-11 rounded-xl bg-solar-primary text-white font-semibold text-sm hover:bg-solar-primary-active shadow-solar hover:shadow-solar-glow transition-all duration-300"
                        >
                            <span>dashboard</span>
                        </Link>
                        <Link 
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex items-center gap-2 px-4 h-11 rounded-xl bg-slate-100 dark:bg-solar-primary-dark/50 text-slate-700 dark:text-slate-200 font-semibold text-sm hover:bg-red-500/10 hover:text-red-500 transition-all duration-300"
                        >
                            <span>Sign Out</span>
                        </Link>
                    </template>
                    <!-- Guest Sign In & Get Started -->
                    <template v-else>
                        <Link 
                            href="/user/login"
                            class="flex items-center gap-2 px-4 h-11 rounded-xl bg-slate-100 dark:bg-solar-primary-dark/50 text-slate-700 dark:text-slate-200 font-semibold text-sm hover:bg-slate-205 dark:hover:bg-solar-primary-dark/80 transition-all duration-300"
                        >
                            <span>Sign In</span>
                        </Link>
                        <Link 
                            href="/user/register"
                            class="flex items-center gap-2 px-4 h-11 rounded-xl bg-solar-primary text-white font-semibold text-sm hover:bg-solar-primary-active shadow-solar hover:shadow-solar-glow transition-all duration-300"
                        >
                            <span>Get Started</span>
                        </Link>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <button 
                        @click="toggleDarkMode" 
                        class="p-2 rounded-lg bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent"
                    >
                        <Sun v-if="isDark" class="h-4 w-4" />
                        <Moon v-else class="h-4 w-4" />
                    </button>
                    <button 
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="p-2 rounded-lg text-slate-600 dark:text-slate-300"
                    >
                        <component :is="isMobileMenuOpen ? X : Menu" class="h-6 w-6" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div v-if="isMobileMenuOpen" class="md:hidden glass-card mx-4 my-2 p-4 border border-solar-primary/10 dark:border-white/5 animate-fade-in-up">
            <div class="flex flex-col gap-4">
                <Link 
                    v-for="link in navLinks" 
                    :key="link.name" 
                    :href="link.href"
                    class="text-slate-700 dark:text-slate-200 hover:text-solar-primary font-medium transition-colors text-lg"
                    @click="isMobileMenuOpen = false"
                >
                    {{ link.name }}
                </Link>
                <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-2"></div>
                <div class="flex flex-col gap-2">
                    <template v-if="authUser">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-100/50 dark:bg-solar-primary-dark/20 border border-solar-primary/5 dark:border-white/5 mb-2 select-none">
                            <div class="h-9 w-9 rounded-lg bg-solar-primary/10 flex items-center justify-center font-bold text-sm text-solar-primary uppercase ring-2 ring-solar-primary/10 shrink-0">
                                {{ authUser.name.charAt(0) }}
                            </div>
                            <div class="flex flex-col text-left min-w-0">
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ authUser.name }}</span>
                                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ authUser.role }}</span>
                            </div>
                        </div>
                        <Link 
                            :href="getDashboardUrl(authUser.role)"
                            class="flex items-center justify-center w-full h-11 rounded-xl bg-solar-primary text-white font-bold text-sm shadow-solar"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>dashboard</span>
                        </Link>
                        <Link 
                            href="/logout"
                            method="post"
                            as="button"
                            class="flex items-center justify-center w-full h-11 rounded-xl bg-slate-200 dark:bg-solar-primary-dark text-slate-800 dark:text-white font-bold text-sm hover:bg-red-500/10 hover:text-red-500 transition-all duration-300"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>Sign Out</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link 
                            href="/user/login"
                            class="flex items-center justify-center w-full h-11 rounded-xl bg-slate-200 dark:bg-solar-primary-dark text-slate-800 dark:text-white font-bold text-sm"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>Sign In</span>
                        </Link>
                        <Link 
                            href="/user/register"
                            class="flex items-center justify-center w-full h-11 rounded-xl bg-solar-primary text-white font-bold text-sm shadow-solar"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>Get Started</span>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>
