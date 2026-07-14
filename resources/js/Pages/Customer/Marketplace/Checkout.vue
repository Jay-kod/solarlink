<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { CheckCircle2, ChevronRight, Truck, CreditCard, Sparkles } from 'lucide-vue-next'

const props = defineProps<{
    cartItems?: Array<{
        id: number;
        name: string;
        price: number;
        quantity: number;
        image: string;
        category: string;
        stock: number;
    }>;
    status?: string;
}>()

const cartItems = computed(() => props.cartItems || [])

const shippingAddress = ref('1225 Grid Access Road, sector 4B, San Francisco, CA')
const cardNumber = ref('4111 •••• •••• 9024')

const formSubmitted = ref(false)
const orderTrackStep = ref(1)

// Check if status is order-placed on mount or update
watch(() => props.status, (newStatus) => {
    if (newStatus === 'order-placed') {
        formSubmitted.value = true
        simulateTracking()
    }
}, { immediate: true })

const simulateTracking = () => {
    orderTrackStep.value = 1
    setTimeout(() => {
        orderTrackStep.value = 2 // Processed
    }, 4000)
    setTimeout(() => {
        orderTrackStep.value = 3 // Dispatched
    }, 8000)
}

const submitCheckout = () => {
    router.post('/user/checkout', {
        shipping_address: shippingAddress.value,
        card_number: cardNumber.value,
    }, {
        preserveScroll: true
    })
}

// Financial calculations
const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const shippingFee = computed(() => {
    if (cartItems.value.length === 0) return 0
    return subtotal.value > 1500 ? 0 : 150 // Free shipping over $1500
})

const taxAmount = computed(() => {
    return subtotal.value * 0.085 // 8.5% regional sales tax
})

const grandTotal = computed(() => {
    return subtotal.value + shippingFee.value + taxAmount.value
})
</script>

<template>
    <Head title="SolarLink — Secure Wholesales Checkout" />

    <CustomerLayout title="Parts Checkout Hub">
        <div class="flex flex-col gap-8 text-left relative max-w-4xl mx-auto">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Secure Gateway Processing</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Wholesale Checkout Queue</h2>
                <p class="text-xs text-slate-450 mt-0.5">Finalize your orders and coordinate factory dispatch tracking.</p>
            </div>
            
            <div 
                v-if="formSubmitted"
                class="glass-card p-8 flex flex-col gap-6 items-center text-center animate-fade-in-up"
            >
                <div class="h-16 w-16 rounded-full bg-solar-success/10 text-solar-success flex items-center justify-center pulse-glow mb-2">
                    <CheckCircle2 class="h-10 w-10" />
                </div>
                <div>
                    <h3 class="font-extrabold text-2xl text-slate-800 dark:text-white">Wholesale Order Transmitted!</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm">Your factory parts order has been handshaked and sent to the OEM supplier dispatch queue.</p>
                </div>

                <!-- Live simulated parcel tracking pipeline -->
                <div class="w-full max-w-md my-4 p-6 bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-100 dark:border-white/5 rounded-2xl">
                    <h4 class="font-bold text-xs text-solar-primary dark:text-solar-primary-accent uppercase tracking-wider mb-5 flex items-center gap-1.5 justify-center">
                        <Truck class="h-4 w-4" />
                        <span>Factory Dispatch Tracking</span>
                    </h4>

                    <!-- Progress steps indicator -->
                    <div class="flex items-center justify-between relative px-2">
                        <div class="absolute top-[13px] left-8 right-8 h-1 bg-slate-200 dark:bg-white/5 -z-10"></div>
                        <div 
                            class="absolute top-[13px] left-8 h-1 bg-solar-success -z-10 transition-all duration-1000"
                            :style="{ width: orderTrackStep === 1 ? '0%' : (orderTrackStep === 2 ? '50%' : '100%') }"
                        ></div>

                        <!-- Step 1 -->
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="h-7 w-7 rounded-full flex items-center justify-center font-bold text-xs" :class="orderTrackStep >= 1 ? 'bg-solar-success text-white' : 'bg-slate-200 text-slate-550'">1</span>
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Accepted</span>
                        </div>
                        <!-- Step 2 -->
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="h-7 w-7 rounded-full flex items-center justify-center font-bold text-xs" :class="orderTrackStep >= 2 ? 'bg-solar-success text-white' : 'bg-slate-200 text-slate-550'">2</span>
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Processed</span>
                        </div>
                        <!-- Step 3 -->
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="h-7 w-7 rounded-full flex items-center justify-center font-bold text-xs" :class="orderTrackStep >= 3 ? 'bg-solar-success text-white' : 'bg-slate-200 text-slate-550'">3</span>
                            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Dispatched</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Link 
                        href="/user/marketplace"
                        class="px-5 h-11 rounded-xl bg-solar-primary text-white font-bold text-xs flex items-center justify-center shadow hover:bg-solar-primary-active transition-all"
                    >
                        Return to Catalog
                    </Link>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Form Inputs (Left) -->
                <div class="lg:col-span-8 flex flex-col gap-6">
                    <div class="glass-card p-6 flex flex-col gap-5">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white flex items-center gap-2">
                            <Truck class="h-5 w-5 text-solar-primary" />
                            <span>Shipping Parameters</span>
                        </h3>
                        
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div class="flex flex-col gap-1.5 col-span-2">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Full Delivery Address</label>
                                <input 
                                    type="text" 
                                    required 
                                    v-model="shippingAddress"
                                    class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-250 dark:border-white/5 focus:outline-none focus:border-solar-primary transition-all duration-300"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-6 flex flex-col gap-5">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white flex items-center gap-2">
                            <CreditCard class="h-5 w-5 text-solar-primary" />
                            <span>Payment Token Gate</span>
                        </h3>

                        <div class="grid grid-cols-3 gap-4 text-xs">
                            <div class="flex flex-col gap-1.5 col-span-3">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Credit Card Number</label>
                                <input 
                                    type="text" 
                                    required 
                                    v-model="cardNumber"
                                    class="h-11 px-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-250 dark:border-white/5 focus:outline-none focus:border-solar-primary transition-all duration-300"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order summary block (Right) -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="glass-card p-6 flex flex-col gap-5 border border-solar-primary/25 bg-gradient-to-tr from-solar-primary/5 to-solar-primary-dark/10">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Order Summary</h3>
                        
                        <!-- List of Items in Checkout -->
                        <div class="flex flex-col gap-2 max-h-48 overflow-y-auto border-b border-solar-primary/10 pb-3 mb-1">
                            <div v-for="item in cartItems" :key="item.id" class="flex justify-between items-center text-xs">
                                <span class="text-slate-500 truncate max-w-[150px]">{{ item.name }} <span class="font-semibold text-slate-400">x{{ item.quantity }}</span></span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">${{ (item.price * item.quantity).toLocaleString() }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 text-xs border-b border-solar-primary/10 pb-4">
                            <div class="flex justify-between text-slate-500">
                                <span>Subtotal</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">${{ subtotal.toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Shipping Fees</span>
                                <span class="font-semibold" :class="shippingFee === 0 ? 'text-solar-success' : 'text-slate-700 dark:text-slate-200'">
                                    {{ shippingFee === 0 ? 'FREE' : '$' + shippingFee }}
                                </span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Regional Tax (8.5%)</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">${{ taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-sm font-extrabold text-slate-800 dark:text-white pb-2">
                            <span>Grand Total</span>
                            <span>${{ grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                        </div>

                        <button 
                            @click="submitCheckout"
                            :disabled="cartItems.length === 0"
                            class="w-full h-12 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow hover:shadow-solar-glow transition-all duration-300 btn-glow disabled:opacity-50"
                        >
                            <span>Authorize Purchase</span>
                            <ChevronRight class="h-4.5 w-4.5" />
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </CustomerLayout>
</template>
