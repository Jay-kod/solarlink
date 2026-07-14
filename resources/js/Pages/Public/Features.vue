<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { 
    Cpu, Zap, ShieldCheck, HeartHandshake, Compass, 
    ArrowRight, BellRing, Sparkles, Database 
} from 'lucide-vue-next'

const page = usePage()
const authUser = computed(() => {
    const auth = page.props.auth as any
    return auth ? auth.user : null
})

const features = [
    {
        icon: Cpu,
        title: 'Predictive Hardware AI Diagnostics',
        desc: 'Our engine monitors voltage drops and grid sync delays, warning you before microinverters fail.'
    },
    {
        icon: Compass,
        title: 'Real-Time GPS Technician Mapping',
        desc: 'Customers can see exact real-time coordinates of nearby local service engineers and dynamic ETAs.'
    },
    {
        icon: ShieldCheck,
        title: 'Smart Service Bookings',
        desc: 'Schedule panels cleanings, battery stacking, or system health audits with flexible scheduling tools.'
    },
    {
        icon: HeartHandshake,
        title: 'Factory-Direct Procurement',
        desc: 'Order solar modules, high-capacity hybrid battery cells, or microinverters without reseller markups.'
    },
    {
        icon: BellRing,
        title: 'Instant Telemetry Alerts',
        desc: 'Receive immediate warning notifications if generation capacity drops below expected season curves.'
    },
    {
        icon: Database,
        title: 'Detailed Carbon & Savings Analytics',
        desc: 'Track daily dollar offsets, carbon tons mitigated, grid feeds, and battery storage levels in a single unified hub.'
    }
]
</script>

<template>
    <Head title="SolarLink — Advanced Solar Features" />

    <PublicLayout>
        <!-- Hero section -->
        <section class="pt-16 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col gap-6 items-center" v-reveal>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/60 text-solar-primary dark:text-solar-primary-accent font-bold text-xs tracking-wide uppercase">
                <Sparkles class="h-3.5 w-3.5" />
                <span>Modern Grid Architecture</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white leading-tight">
                Enterprise Solar Infrastructure, <br/>
                <span class="bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">Optimized for Performance</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed">
                Explore the complete features of SolarLink. Tap into live dispatch maps, instant custom parts bidding, system diagnostics summaries, and role moderation systems.
            </p>
        </section>

        <!-- Detailed Features Grid -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div 
                    v-for="(feat, idx) in features" 
                    :key="feat.title" 
                    v-reveal="{ delay: idx * 100 }"
                    class="glass-card p-8 flex flex-col gap-5 text-left border hover:border-solar-primary/20 transition-all duration-300 hover:shadow-solar-lg"
                >
                    <div class="p-3 bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent rounded-2xl self-start">
                        <component :is="feat.icon" class="h-6 w-6" />
                    </div>
                    <h3 class="font-extrabold text-lg text-slate-900 dark:text-white leading-snug">{{ feat.title }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ feat.desc }}</p>
                </div>
            </div>
        </section>

        <!-- Comparison matrix -->
        <section class="py-20 bg-solar-primary-light/20 dark:bg-solar-primary-dark/10 border-t border-solar-primary/10 dark:border-white/5 transition-colors">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-left">
                <div class="text-center mb-12 flex flex-col gap-3" v-reveal>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Why Operators Prefer SolarLink</h2>
                    <p class="text-sm text-slate-500">Standard panels decay by 3% a year without active predictive telemetry. SolarLink cuts down degradation curves.</p>
                </div>

                <div class="glass-card overflow-hidden" v-reveal>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-solar-primary/5 border-b border-solar-primary/10">
                                <th class="p-4 font-bold text-slate-800 dark:text-slate-100">Core Performance</th>
                                <th class="p-4 font-bold text-solar-primary text-center">SolarLink Platform</th>
                                <th class="p-4 font-semibold text-slate-500 text-center">Traditional Solar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-solar-primary/5 dark:divide-white/5">
                            <tr>
                                <td class="p-4 font-bold text-slate-800 dark:text-slate-200">Emergency Dispatch</td>
                                <td class="p-4 text-center text-solar-success font-semibold">Under 2 Hours (Guaranteed ETA)</td>
                                <td class="p-4 text-center text-slate-500">2-5 Business Days</td>
                            </tr>
                            <tr>
                                <td class="p-4 font-bold text-slate-800 dark:text-slate-200">Hardware Prices</td>
                                <td class="p-4 text-center text-solar-success font-semibold">Direct Factory Wholesale</td>
                                <td class="p-4 text-center text-slate-500">30-40% Retail Markup</td>
                            </tr>
                            <tr>
                                <td class="p-4 font-bold text-slate-800 dark:text-slate-200">Fault Detection</td>
                                <td class="p-4 text-center text-solar-success font-semibold">Predictive Telemetry Warns</td>
                                <td class="p-4 text-center text-slate-500">Manual inspection after collapse</td>
                            </tr>
                            <tr>
                                <td class="p-4 font-bold text-slate-800 dark:text-slate-200">Management Scope</td>
                                <td class="p-4 text-center text-solar-success font-semibold">4 Role Integrated Dashboard</td>
                                <td class="p-4 text-center text-slate-500">Fragmented Emails & Calls</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Dynamic CTA -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col gap-6 items-center" v-reveal>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Start Optimizing Your Solar Operations Today</h2>
            <p class="text-sm text-slate-500 max-w-md">Create your account to access our intelligent operations and telemetry suite.</p>
            <Link 
                :href="authUser ? '/' + authUser.role : '/user/register'" 
                class="px-8 h-14 rounded-2xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-base flex items-center justify-center gap-2 shadow-solar-lg hover:shadow-solar-glow transition-all duration-300 btn-glow"
            >
                <span>{{ authUser ? 'Enter Dashboard' : 'Get Started Now' }}</span>
                <ArrowRight class="h-4.5 w-4.5" />
            </Link>
        </section>
    </PublicLayout>
</template>
