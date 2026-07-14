<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Sparkles, ChevronDown, Wrench, ShieldCheck, CreditCard, HeartHandshake } from 'lucide-vue-next'

const props = defineProps<{
    faqs?: { id: number; category: string; question: string; answer: string }[]
}>()

const isLoading = ref(true)

const getIconForCategory = (name: string) => {
    switch (name) {
        case 'Technical & Telemetry':
            return Wrench
        case 'Marketplace & Orders':
            return HeartHandshake
        case 'Billing & Subscriptions':
            return CreditCard
        default:
            return ShieldCheck
    }
}

const staticCategories = [
    {
        name: 'Technical & Telemetry',
        icon: Wrench,
        items: [
            {
                q: 'How does the predictive AI diagnostic tool track outages?',
                a: 'By pulling streaming metrics (V, I, Temp) from connected solar panels, our machine-learning health models compare generation curves against localized weather projections to spot microinverter degradations immediately.',
                open: false
            },
            {
                q: 'What types of inverters are supported by SolarLink?',
                a: 'We support all major hybrid and microinverter models, including Enphase, Tesla, SMA, and Sol-Ark, through our open telemetry API structures.',
                open: false
            }
        ]
    },
    {
        name: 'Marketplace & Orders',
        icon: HeartHandshake,
        items: [
            {
                q: 'How does direct factory parts procurement work?',
                a: 'We bypass regional logistics hubs by listing replacement units direct from certified manufacturing inventories, routing parts straight to your address with zero middleman markup.',
                open: false
            },
            {
                q: 'Are parts sold through the marketplace covered by warranties?',
                a: 'Yes, all products (panels, batteries, smart breaker arrays) carry full linear manufacturer performance warranties, easily managed inside your client dashboard.',
                open: false
            }
        ]
    },
    {
        name: 'Billing & Subscriptions',
        icon: CreditCard,
        items: [
            {
                q: 'Is there a contract required for the Pro subscription plan?',
                a: 'No contracts. All premium monitoring and priority dispatch tiers operate on a monthly recurring schedule, cancellable at any time with a single click in your settings.',
                open: false
            },
            {
                q: 'How do you structure dispatcher payment rates?',
                a: 'Technicians set their own local hourly service rates. Booking fees are pre-calculated and securely collected via the dashboard during technician selection.',
                open: false
            }
        ]
    }
]

const categories = ref<any[]>([])

onMounted(() => {
    if (props.faqs && props.faqs.length > 0) {
        const groups: Record<string, any[]> = {}
        props.faqs.forEach(faq => {
            if (!groups[faq.category]) {
                groups[faq.category] = []
            }
            groups[faq.category].push({
                q: faq.question,
                a: faq.answer,
                open: false
            })
        })
        categories.value = Object.keys(groups).map(name => ({
            name,
            icon: getIconForCategory(name),
            items: groups[name]
        }))
    } else {
        categories.value = staticCategories
    }

    setTimeout(() => {
        isLoading.value = false
    }, 800)
})

const toggleItem = (catIdx: number, itemIdx: number) => {
    categories.value[catIdx].items[itemIdx].open = !categories.value[catIdx].items[itemIdx].open
}
</script>

<template>
    <Head title="SolarLink — Help Center & FAQ" />

    <PublicLayout>
        <!-- Hero section -->
        <section class="pt-16 pb-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col gap-6 items-center" v-reveal>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/60 text-solar-primary dark:text-solar-primary-accent font-bold text-xs tracking-wide uppercase">
                <Sparkles class="h-3.5 w-3.5" />
                <span>Knowledge Base</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white leading-tight">
                Clear Answers for <br/>
                <span class="bg-gradient-to-r from-solar-primary to-solar-primary-accent bg-clip-text text-transparent">Every Solar Owner</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-xl leading-relaxed">
                Browse our complete knowledge base categories. If you have additional inquiries, reach out using our contact portal.
            </p>
        </section>

        <!-- Categories & Accordion -->
        <section class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-left">
            <!-- Skeleton FAQs Accordions -->
            <div v-if="isLoading" class="flex flex-col gap-10 animate-pulse">
                <div v-for="c in 2" :key="c" class="flex flex-col gap-4">
                    <div class="flex items-center gap-3 mb-2 pb-2 border-b border-solar-primary/10 w-1/3">
                        <div class="h-6 w-6 bg-slate-200 dark:bg-slate-800 rounded-lg"></div>
                        <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                    </div>
                    <div v-for="i in 2" :key="i" class="glass-card overflow-hidden h-16 bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5 flex items-center justify-between px-6">
                        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
                        <div class="h-5 w-5 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Real FAQs Accordions -->
            <div v-else class="animate-fade-in">
                <div 
                    v-for="(cat, catIdx) in categories" 
                    :key="cat.name"
                    class="mb-12"
                >
                    <div class="flex items-center gap-3 mb-6 pb-2 border-b border-solar-primary/10" v-reveal>
                        <component :is="cat.icon" class="h-5.5 w-5.5 text-solar-primary" />
                        <h3 class="font-extrabold text-xl text-slate-800 dark:text-white">{{ cat.name }}</h3>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div 
                            v-for="(item, itemIdx) in cat.items" 
                            :key="item.q"
                            v-reveal="{ delay: itemIdx * 50 }"
                            class="glass-card overflow-hidden"
                        >
                            <button 
                                @click="toggleItem(catIdx, itemIdx)"
                                class="w-full px-6 py-5 flex items-center justify-between text-left font-bold text-base text-slate-850 dark:text-slate-100 hover:text-solar-primary transition-colors focus:outline-none"
                            >
                                <span>{{ item.q }}</span>
                                <ChevronDown class="h-5 w-5 transition-transform duration-300 text-slate-450" :class="{ 'transform rotate-180 text-solar-primary': item.open }" />
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
            </div>
        </section>
    </PublicLayout>
</template>
