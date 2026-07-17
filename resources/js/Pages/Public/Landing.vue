<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { productCatalog } from '@/data/products'
import { 
    Zap, Cpu, ShieldCheck, HeartHandshake, CheckCircle2, ChevronDown, 
    ArrowRight, Star, ShoppingBag, ShieldAlert, Sparkles 
} from 'lucide-vue-next'

const isLoading = ref(true)
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
    }, 800)
})

const stats = [
    { value: '45,200+', label: 'Active Solar Systems' },
    { value: '99.94%', label: 'Average System Uptime' },
    { value: '1.2M+', label: 'Tons of CO2 Offset' },
    { value: '$14.2M+', label: 'Customer Savings' }
]

const features = [
    {
        icon: Cpu,
        title: 'Real-time System Telemetry',
        desc: 'Advanced IoT hardware integrations stream generation metrics directly to our machine-learning health models.'
    },
    {
        icon: Zap,
        title: 'Predictive Outage Protection',
        desc: 'Detect microinverter degrades and wiring hot-spots days before they cause performance decay.'
    },
    {
        icon: ShieldCheck,
        title: 'Certified Technicians On-Demand',
        desc: 'Request localized emergency maintenance and certified system engineers with a single tap.'
    },
    {
        icon: HeartHandshake,
        title: 'Verified Vendor Procurement',
        desc: 'Procure replacement panels, stackable modular batteries, and smart breakers straight from direct factories.'
    }
]

const testimonials = [
    {
        quote: "SolarLink saved our farm from an inverter failure that would have cost us thousands in lost dairy cooling time. The local tech showed up in under two hours with the correct replacement board.",
        author: "Harlan Cooper",
        role: "EcoFarm Holdings Owner",
        rating: 5,
        avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=100"
    },
    {
        quote: "Direct parts buying through the SolarLink Marketplace cut our expansion budget by 35%. Getting certified OEM gear without premium retail markup is a game-changer.",
        author: "Miriam Vance",
        role: "Urban Grid Solutions Director",
        rating: 5,
        avatar: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=100"
    }
]

const faqItems = ref([
    {
        q: "How does technician mapping and service routing work?",
        a: "When you book a service, SolarLink connects you with verified local engineers. You can view their service updates, direct messaging details, and real-time dispatch arrival status in your customer dashboard.",
        open: false
    },
    {
        q: "What roles are supported on the SolarLink platform?",
        a: "SolarLink supports four distinct database roles: Customers (asset owners), Technicians (field engineers), Vendors (OEM parts suppliers), and Administrators. Each role accesses a custom dashboard designed specifically for their operational workflow.",
        open: false
    },
    {
        q: "Is there a functional parts marketplace?",
        a: "Yes. Customers can browse and purchase solar hardware, battery units, and microinverters directly from certified vendors, while vendors can manage product listings and process orders directly in their dashboard.",
        open: false
    }
])

const toggleFaq = (index: number) => {
    faqItems.value[index].open = !faqItems.value[index].open
}

const featuredProducts = productCatalog.slice(0, 3)

const page = usePage()
const authUser = computed(() => {
    const auth = page.props.auth as any
    return auth ? auth.user : null
})
</script>

<template>
    <Head title="SolarLink — Smart Solar Maintenance & Procurement" />

    <PublicLayout>
        <!-- Hero Section -->
        <section class="relative pt-12 pb-24 md:pt-20 md:pb-32 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <!-- Left Hero Intro -->
                    <div class="lg:col-span-7 flex flex-col gap-6 text-left" v-reveal>
                        <div class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/60 text-solar-primary dark:text-solar-primary-accent font-semibold text-xs border border-solar-primary/10 tracking-wide uppercase">
                            <Sparkles class="h-3.5 w-3.5 animate-pulse" />
                            <span>Revolutionizing Renewable Infrastructure</span>
                        </div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white leading-[1.1] tracking-tight">
                            The Smart Way to <br/>
                            <span class="bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">Maintain & Procure</span> <br/>
                            Your Solar Assets.
                        </h1>
                        <p class="text-lg text-slate-500 dark:text-slate-400 max-w-xl leading-relaxed">
                            SolarLink connects homeowners and commercial operators with certified technicians, predictive diagnostic models, and wholesale supply chains for ultra-reliable carbon generation.
                        </p>
                        <div class="flex flex-wrap items-center gap-4 mt-2">
                            <Link 
                                :href="authUser ? '/' + authUser.role : '/register'"
                                class="px-7 h-14 rounded-2xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-base flex items-center justify-center gap-2 shadow-solar-lg hover:shadow-solar-glow transition-all duration-300 btn-glow"
                            >
                                <span>{{ authUser ? 'Go to Dashboard' : 'Get Started Now' }}</span>
                                <ArrowRight class="h-5 w-5" />
                            </Link>
                            <Link 
                                href="/pricing"
                                class="px-6 h-14 rounded-2xl bg-white dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 hover:border-solar-primary/30 text-slate-700 dark:text-slate-200 font-bold text-base flex items-center justify-center transition-all duration-300"
                            >
                                View Pricing Plans
                            </Link>
                        </div>
                    </div>

                    <!-- Right Hero Graphics -->
                    <div class="lg:col-span-5 relative flex justify-center" v-reveal:zoom="{ delay: 200 }">
                        <div class="absolute inset-0 bg-gradient-to-tr from-solar-primary/10 to-solar-primary-accent/10 dark:from-solar-primary/20 dark:to-transparent rounded-full filter blur-3xl -z-10"></div>
                        
                        <!-- High Fidelity App Mockup Card -->
                        <div class="w-full max-w-[420px] rounded-3xl glass-card border border-solar-primary/20 p-5 shadow-solar-lg relative overflow-hidden animate-float">
                            <!-- Header status -->
                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-2">
                                    <div class="h-2.5 w-2.5 rounded-full bg-solar-success animate-ping"></div>
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">System: Grid-Parallel</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-solar-success/10 text-solar-success font-semibold text-[10px]">Optimal Health</span>
                            </div>

                            <!-- Big Telemetry Number -->
                            <div class="bg-solar-primary-light/50 dark:bg-solar-primary-dark/30 rounded-2xl p-4 border border-solar-primary/10 mb-5 text-center">
                                <p class="text-xs text-slate-400 dark:text-slate-400 uppercase tracking-wider font-semibold">Live Array Generation</p>
                                <p class="text-4xl font-extrabold text-solar-primary dark:text-solar-primary-accent my-1">6.84 kW</p>
                                <div class="flex items-center justify-center gap-1.5 text-xs text-solar-success font-bold">
                                    <Zap class="h-3.5 w-3.5 fill-solar-success" />
                                    <span>+14.2% Above Expected</span>
                                </div>
                            </div>

                            <!-- Small active stats -->
                            <div class="grid grid-cols-2 gap-3 mb-5">
                                <div class="p-3 bg-white/40 dark:bg-solar-primary-dark/10 border border-slate-100 dark:border-white/5 rounded-xl text-left">
                                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Battery Charge</p>
                                    <p class="text-lg font-bold text-slate-800 dark:text-slate-100">92.4%</p>
                                </div>
                                <div class="p-3 bg-white/40 dark:bg-solar-primary-dark/10 border border-slate-100 dark:border-white/5 rounded-xl text-left">
                                    <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Today's Savings</p>
                                    <p class="text-lg font-bold text-solar-success">$28.40</p>
                                </div>
                            </div>

                            <!-- Active maintenance ticket tracker -->
                            <div class="p-3 bg-solar-primary/5 dark:bg-solar-primary/10 border border-solar-primary/10 rounded-xl flex items-center justify-between text-left">
                                <div class="flex items-center gap-2.5">
                                    <div class="p-2 rounded-lg bg-solar-primary/10 text-solar-primary dark:text-solar-primary-accent">
                                        <ShieldCheck class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-100">Annual Audit</h4>
                                        <p class="text-[10px] text-slate-400">June 2nd with Marcus V.</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-solar-primary/10 text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">Assigned</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-12 bg-white/50 dark:bg-solar-primary-dark/20 border-y border-solar-primary/10 dark:border-white/5 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div v-for="(stat, idx) in stats" :key="stat.label" class="flex flex-col gap-1.5" v-reveal="{ delay: idx * 100 }">
                        <p class="text-3xl sm:text-4xl font-extrabold text-solar-primary dark:text-solar-primary-accent">{{ stat.value }}</p>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-semibold">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Key Features Section -->
        <section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 flex flex-col gap-4" v-reveal>
                <span class="text-xs font-bold text-solar-primary dark:text-solar-primary-accent uppercase tracking-widest">Autonomous Performance Architecture</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Engineered to Drive Continuous Solar Output
                </h2>
                <p class="text-base text-slate-500 dark:text-slate-400">
                    Traditional arrays degrade in silence. SolarLink transforms your energy infrastructure into a smart, self-healing grid network.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div 
                    v-for="(feat, idx) in features" 
                    :key="feat.title" 
                    v-reveal="{ delay: idx * 100 }"
                    class="glass-card-hover p-6 flex flex-col gap-4 text-left"
                >
                    <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-xl self-start">
                        <component :is="feat.icon" class="h-6 w-6" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 dark:text-white leading-snug">{{ feat.title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ feat.desc }}</p>
                </div>
            </div>
        </section>

        <!-- Marketplace Preview -->
        <section class="py-20 bg-solar-primary-light/30 dark:bg-solar-primary-dark/10 border-t border-solar-primary/10 dark:border-white/5 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-end justify-between mb-12 gap-4" v-reveal>
                    <div class="text-left max-w-2xl flex flex-col gap-3">
                        <span class="text-xs font-bold text-solar-primary dark:text-solar-primary-accent uppercase tracking-widest">SolarLink Marketplace</span>
                        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Direct Factory Procurement</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            Say goodbye to retail markup. Buy replacement panels, smart home storage units, and solar arrays direct from verified manufacturers with automated shipping tracking.
                        </p>
                    </div>
                    <Link 
                        :href="authUser ? (authUser.role === 'customer' ? '/user/marketplace' : '/' + authUser.role) : '/user/register'" 
                        class="flex items-center gap-2 text-sm font-bold text-solar-primary hover:text-solar-primary-accent transition-colors shrink-0 group"
                    >
                        <span>Explore Full Catalog</span>
                        <ShoppingBag class="h-4 w-4 group-hover:translate-x-0.5 transition-transform" />
                    </Link>
                </div>                <!-- Product Grid -->
                <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-pulse">
                    <div 
                        v-for="i in 3" 
                        :key="i"
                        class="glass-card overflow-hidden rounded-2xl text-left flex flex-col h-[400px] bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5"
                    >
                        <div class="h-48 bg-slate-200 dark:bg-slate-800/80 animate-pulse"></div>
                        <div class="p-5 flex flex-col flex-grow gap-4">
                            <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-2/3 animate-pulse"></div>
                            <div class="space-y-2">
                                <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
                                <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-5/6"></div>
                            </div>
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
                            <div class="flex justify-between items-center mt-auto">
                                <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                                <div class="h-8 w-8 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Grid (Real) -->
                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8 animate-fade-in">
                    <div 
                        v-for="(prod, idx) in featuredProducts" 
                        :key="prod.id"
                        v-reveal="{ delay: idx * 100 }"
                        class="glass-card-hover overflow-hidden rounded-2xl text-left flex flex-col h-full bg-white dark:bg-solar-bg-dark/40"
                    >
                        <div class="h-48 overflow-hidden relative">
                            <img :src="prod.image" :alt="prod.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full bg-solar-primary text-white font-bold text-[9px] uppercase tracking-wider shadow">
                                {{ prod.category }}
                            </span>
                        </div>
                        <div class="p-5 flex flex-col flex-grow gap-3">
                            <h3 class="font-bold text-base text-slate-800 dark:text-slate-100 line-clamp-1">{{ prod.name }}</h3>
                            <p class="text-xs text-slate-450 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ prod.description }}</p>
                            
                            <!-- Rating -->
                            <div class="flex items-center gap-1 text-amber-500 text-xs">
                                <Star class="h-3.5 w-3.5 fill-amber-500" />
                                <span class="font-bold">{{ prod.rating }}</span>
                                <span class="text-slate-400 dark:text-slate-500">({{ prod.reviewsCount }} reviews)</span>
                            </div>

                            <!-- Pricing / Buy Button -->
                            <div class="mt-auto pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                                <div>
                                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">${{ prod.price }}</span>
                                    <span v-if="prod.originalPrice" class="text-xs text-slate-400 line-through ml-1.5">${{ prod.originalPrice }}</span>
                                </div>
                                <Link 
                                    :href="authUser ? (authUser.role === 'customer' ? '/user/marketplace' : '/' + authUser.role) : '/user/register'"
                                    class="p-2 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark/80 text-solar-primary dark:text-solar-primary-accent hover:bg-solar-primary hover:text-white transition-colors"
                                    title="Add to Cart"
                                >
                                    <ShoppingBag class="h-4.5 w-4.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 flex flex-col gap-4" v-reveal>
                <span class="text-xs font-bold text-solar-primary dark:text-solar-primary-accent uppercase tracking-widest">Customer Love</span>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Rave Reviews Across the Grid</h2>
            </div>            <!-- Testimonials Grid (Skeleton) -->
            <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-pulse">
                <div v-for="i in 2" :key="i" class="glass-card p-8 flex flex-col gap-6 text-left h-48 bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5">
                    <div class="flex gap-1">
                        <div v-for="j in 5" :key="j" class="h-4 w-4 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
                        <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-5/6"></div>
                    </div>
                    <div class="flex items-center gap-3.5 mt-auto">
                        <div class="h-11 w-11 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                        <div class="flex-grow space-y-1">
                            <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                            <div class="h-2 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Testimonials Grid (Real) -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-fade-in">
                <div 
                    v-for="(test, idx) in testimonials" 
                    :key="test.author"
                    v-reveal="{ delay: idx * 100 }"
                    class="glass-card p-8 flex flex-col gap-6 text-left"
                >
                    <div class="flex gap-1 text-amber-500">
                        <Star v-for="i in test.rating" :key="i" class="h-4 w-4 fill-amber-500" />
                    </div>
                    <p class="text-base text-slate-600 dark:text-slate-300 italic leading-relaxed">"{{ test.quote }}"</p>
                    <div class="flex items-center gap-3.5 mt-auto">
                        <img :src="test.avatar" :alt="test.author" class="h-11 w-11 rounded-full object-cover border border-solar-primary/20" />
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 dark:text-slate-100">{{ test.author }}</h4>
                            <p class="text-xs text-slate-450 dark:text-slate-400 font-semibold">{{ test.role }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ accordion -->
        <section class="py-20 bg-white/50 dark:bg-solar-primary-dark/20 border-t border-solar-primary/10 dark:border-white/5 transition-colors">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-left">
                <div class="text-center max-w-3xl mx-auto mb-12 flex flex-col gap-3" v-reveal>
                    <span class="text-xs font-bold text-solar-primary dark:text-solar-primary-accent uppercase tracking-widest">Clear Answers</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Frequently Asked Questions</h2>
                </div>

                <!-- FAQs Accordion (Skeleton) -->
                <div v-if="isLoading" class="flex flex-col gap-4 animate-pulse">
                    <div v-for="i in 3" :key="i" class="glass-card overflow-hidden h-16 bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 flex items-center justify-between px-6">
                        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-2/3 animate-pulse"></div>
                        <div class="h-5 w-5 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                    </div>
                </div>

                <!-- FAQs Accordion (Real) -->
                <div v-else class="flex flex-col gap-4 animate-fade-in">
                    <div 
                        v-for="(item, idx) in faqItems" 
                        :key="item.q"
                        v-reveal="{ delay: idx * 50 }"
                        class="glass-card overflow-hidden"
                    >
                        <button 
                            @click="toggleFaq(idx)"
                            class="w-full px-6 py-5 flex items-center justify-between text-left font-bold text-base text-slate-800 dark:text-slate-100 hover:text-solar-primary transition-colors focus:outline-none"
                        >
                            <span>{{ item.q }}</span>
                            <ChevronDown class="h-5 w-5 transition-transform duration-300 text-slate-400 dark:text-slate-500" :class="{ 'transform rotate-180 text-solar-primary': item.open }" />
                        </button>
                        <div 
                            v-if="item.open" 
                            class="px-6 pb-5 text-sm text-slate-500 dark:text-slate-400 leading-relaxed border-t border-solar-primary/5 pt-4 animate-fade-in-up"
                        >
                            {{ item.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sandbox CTA Banner -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl bg-gradient-to-tr from-solar-primary to-solar-primary-dark p-8 md:p-14 text-center overflow-hidden shadow-solar-glow-lg border border-solar-primary/30" v-reveal:zoom>
                <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-15" style="background-image: url('https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&q=80&w=600');"></div>
                <div class="absolute -top-16 -left-16 w-60 h-60 glow-purple rounded-full blur-3xl opacity-50"></div>

                <div class="relative z-10 max-w-3xl mx-auto flex flex-col gap-6 items-center">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                        {{ authUser ? 'Manage Your Solar Infrastructure Today' : 'Ready to Optimize Your Solar Assets?' }}
                    </h2>
                    <p class="text-base text-solar-primary-light/80 max-w-xl">
                        {{ authUser ? 'Access telemetry diagnostics, schedule emergency field service, or order certified parts directly.' : 'Join thousands of homeowners and commercial operators using SolarLink to maximize uptime and reduce maintenance overhead.' }}
                    </p>
                    <Link 
                        :href="authUser ? '/' + authUser.role : '/user/register'" 
                        class="px-8 h-14 rounded-2xl bg-white hover:bg-slate-50 text-solar-primary font-bold text-base flex items-center justify-center gap-2 shadow-solar-lg hover:scale-105 transition-all duration-300"
                    >
                        <span>{{ authUser ? 'Open Dashboard' : 'Get Started Now' }}</span>
                        <ArrowRight class="h-4.5 w-4.5" />
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
