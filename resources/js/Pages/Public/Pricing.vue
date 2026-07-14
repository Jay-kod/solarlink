<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { CheckCircle2, ShieldCheck, ArrowRight, Sparkles } from 'lucide-vue-next'

const props = defineProps<{
    pricingPlans?: any[]
}>()

const page = usePage()
const authUser = computed(() => {
    const auth = page.props.auth as any
    return auth ? auth.user : null
})

const staticTiers = [
    {
        name: 'Starter Plan',
        price: '0',
        desc: 'Best for standard residential arrays requiring direct replacement components and simple bookings.',
        features: [
            'Access to OEM Parts Marketplace',
            'Emergency Tech Dispatch Booking',
            'Standard Email Support',
            'Single Solar System Telemetry'
        ],
        cta: 'Get Started Now',
        href: '/user',
        popular: false
    },
    {
        name: 'Pro Premium',
        price: '29',
        desc: 'Advanced predictive monitoring designed to completely eliminate array breakdown periods.',
        features: [
            'All OEM Marketplace access',
            'Priority 2-Hour Technician Booking',
            'Predictive microinverter diagnostics',
            'Custom alerts (SMS / push notes)',
            'Stackable modular battery dashboards',
            'Covers up to 3 individual arrays'
        ],
        cta: 'Get Started Now',
        href: '/user',
        popular: true
    },
    {
        name: 'Enterprise Grid',
        price: '149',
        desc: 'Designed for commercial farms, solar installations, and regional asset managers.',
        features: [
            'API Telemetry feeds & webhooks',
            'Guaranteed SLA arrival commitments',
            'Uncapped system profiles',
            'Automated vendor wholesale matching',
            'Technician team dispatch logs',
            'Dedicated regional manager dashboard'
        ],
        cta: 'Contact Sales',
        href: '/admin',
        popular: false
    }
]

const tiers = computed(() => {
    if (props.pricingPlans && props.pricingPlans.length > 0) {
        return props.pricingPlans.map(plan => ({
            name: plan.name,
            price: String(Math.round(plan.price)),
            desc: plan.desc,
            features: plan.features,
            cta: plan.cta,
            href: plan.href,
            popular: plan.popular
        }))
    }
    return staticTiers
})
</script>

<template>
    <Head title="SolarLink — Solutions Pricing" />

    <PublicLayout>
        <!-- Hero section -->
        <section class="pt-16 pb-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col gap-6 items-center" v-reveal>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/60 text-solar-primary dark:text-solar-primary-accent font-bold text-xs tracking-wide uppercase">
                <Sparkles class="h-3.5 w-3.5" />
                <span>Subscription Plans</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white leading-tight">
                Predictable Pricing for <br/>
                <span class="bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">Continuous Renewables Generation</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-xl leading-relaxed">
                Choose a plan that fits your solar infrastructure. Create an account to access our intelligent operations suite.
            </p>
        </section>

        <!-- Pricing Grid -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div 
                    v-for="(tier, idx) in tiers" 
                    :key="tier.name"
                    v-reveal="{ delay: idx * 100 }"
                    class="glass-card p-8 flex flex-col gap-6 text-left relative overflow-hidden"
                    :class="{ 'border-2 border-solar-primary shadow-solar-glow': tier.popular }"
                >
                    <!-- Highlight banner -->
                    <div 
                        v-if="tier.popular"
                        class="absolute top-0 right-0 bg-solar-primary text-white text-[10px] font-extrabold px-3 py-1 rounded-bl-xl uppercase tracking-wider shadow"
                    >
                        Most Popular
                    </div>

                    <div class="flex flex-col gap-2">
                        <h3 class="font-extrabold text-xl text-slate-800 dark:text-white">{{ tier.name }}</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-400 leading-relaxed">{{ tier.desc }}</p>
                    </div>

                    <!-- Price -->
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-extrabold text-slate-900 dark:text-white">${{ tier.price }}</span>
                        <span class="text-sm font-semibold text-slate-400">/ month</span>
                    </div>

                    <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-2"></div>

                    <!-- Features -->
                    <ul class="flex flex-col gap-3.5 flex-grow">
                        <li 
                            v-for="feat in tier.features" 
                            :key="feat"
                            class="flex items-start gap-2.5 text-sm text-slate-600 dark:text-slate-300 leading-normal"
                        >
                            <CheckCircle2 class="h-4.5 w-4.5 text-solar-success shrink-0 mt-0.5" />
                            <span>{{ feat }}</span>
                        </li>
                    </ul>

                    <Link 
                        :href="authUser ? (authUser.role === 'customer' ? '/user' : '/' + authUser.role) : (tier.name === 'Enterprise Grid' ? '/contact' : '/user/register')"
                        class="w-full h-12 rounded-xl flex items-center justify-center gap-2 font-bold text-sm transition-all duration-300"
                        :class="tier.popular 
                            ? 'bg-solar-primary hover:bg-solar-primary-active text-white shadow-solar hover:shadow-solar-glow' 
                            : 'bg-solar-primary-light dark:bg-solar-primary-dark/40 text-solar-primary dark:text-solar-primary-accent hover:bg-solar-primary hover:text-white'"
                    >
                        <span>{{ authUser ? 'Go to Dashboard' : (tier.name === 'Enterprise Grid' ? 'Contact Sales' : 'Select ' + tier.name) }}</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Guarantee -->
        <section class="py-16 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col gap-4 items-center">
            <div class="p-3 bg-solar-success/10 text-solar-success rounded-full">
                <ShieldCheck class="h-7 w-7" />
            </div>
            <h3 class="font-extrabold text-xl text-slate-800 dark:text-white">100% Satisfaction SLA Guarantee</h3>
            <p class="text-sm text-slate-500 max-w-md">
                If a certified local service technician fails to arrive within your scheduled two-hour SLA dispatch window, your next month of predictive monitoring is completely free.
            </p>
        </section>
    </PublicLayout>
</template>
