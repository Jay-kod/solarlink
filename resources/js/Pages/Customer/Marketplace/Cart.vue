<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { ShoppingBag, ArrowLeft, Trash2, Plus, Minus, Tag, Check, Sparkles } from 'lucide-vue-next'

interface CartItem {
    id: number;
    name: string;
    price: number;
    quantity: number;
    image: string;
    category: string;
    stock: number;
}

const props = defineProps<{
    cartItems?: CartItem[]
}>()

const cartItems = computed(() => props.cartItems || [])

const couponCode = ref('')
const couponApplied = ref(false)
const couponDiscount = ref(0)
const couponError = ref('')

const incrementQty = (id: number) => {
    const item = cartItems.value.find(i => i.id === id)
    if (item && item.quantity < item.stock) {
        router.patch(`/user/cart/${id}`, {
            quantity: item.quantity + 1
        }, {
            preserveScroll: true
        })
    }
}

const decrementQty = (id: number) => {
    const item = cartItems.value.find(i => i.id === id)
    if (item && item.quantity > 1) {
        router.patch(`/user/cart/${id}`, {
            quantity: item.quantity - 1
        }, {
            preserveScroll: true
        })
    }
}

const removeItem = (id: number) => {
    router.delete(`/user/cart/${id}`, {
        preserveScroll: true
    })
}

const applyCoupon = () => {
    couponError.value = ''
    if (couponCode.value.toUpperCase() === 'SOLAR10') {
        couponApplied.value = true
        couponDiscount.value = 0.10 // 10% off
    } else if (couponCode.value.toUpperCase() === 'FREEGRID') {
        couponApplied.value = true
        couponDiscount.value = 0.15 // 15% off
    } else if (couponCode.value.trim() === '') {
        couponError.value = 'Please enter a coupon code'
    } else {
        couponError.value = 'Invalid coupon code'
    }
}

const subtotal = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const discountAmount = computed(() => {
    return subtotal.value * couponDiscount.value
})

const shippingFee = computed(() => {
    if (cartItems.value.length === 0) return 0
    return subtotal.value > 1500 ? 0 : 150 // Free shipping over $1500
})

const taxAmount = computed(() => {
    return (subtotal.value - discountAmount.value) * 0.08 // 8% sales tax
})

const grandTotal = computed(() => {
    return subtotal.value - discountAmount.value + shippingFee.value + taxAmount.value
})
</script>

<template>
    <Head title="SolarLink — Shopping Cart" />

    <CustomerLayout title="Shopping Cart">
        <div class="flex flex-col gap-8 text-left max-w-5xl mx-auto">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Wholesale Checkout Queue</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Your Shopping Cart</h2>
                <p class="text-xs text-slate-450 mt-0.5">Manage wholesale equipment and schedule logistics for your project installation.</p>
            </div>

            <!-- Empty Cart State -->
            <div v-if="cartItems.length === 0" class="glass-card p-10 flex flex-col items-center justify-center text-center gap-5 bg-white dark:bg-solar-bg-dark/40">
                <div class="h-16 w-16 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark/80 text-solar-primary dark:text-solar-primary-accent flex items-center justify-center shadow-solar">
                    <ShoppingBag class="h-8 w-8" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">Your Cart is Empty</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
                        You don't have any items in your cart. Browse the wholesale marketplace to find solar panels, batteries, inverters, and accessories.
                    </p>
                </div>
                <Link 
                    href="/user/marketplace"
                    class="px-5 h-11 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold flex items-center gap-2 shadow-solar btn-glow transition-all"
                >
                    <ArrowLeft class="h-4 w-4" />
                    <span>Browse Marketplace</span>
                </Link>
            </div>

            <!-- Cart Content -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items List (Left 2 Columns) -->
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <div 
                        v-for="item in cartItems" 
                        :key="item.id"
                        class="glass-card p-4 flex items-center gap-4 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl hover:shadow-solar transition-all"
                    >
                        <div class="h-20 w-20 rounded-lg overflow-hidden flex-shrink-0 bg-slate-100">
                            <img :src="item.image" :alt="item.name" class="h-full w-full object-cover" />
                        </div>

                        <div class="flex-grow min-w-0">
                            <span class="text-[9px] uppercase tracking-wider font-bold text-solar-primary dark:text-solar-primary-accent bg-solar-primary-light dark:bg-solar-primary-dark/60 px-1.5 py-0.5 rounded">
                                {{ item.category }}
                            </span>
                            <h3 class="font-extrabold text-sm text-slate-800 dark:text-white truncate mt-1">{{ item.name }}</h3>
                            <p class="text-xs font-extrabold text-slate-900 dark:text-white mt-0.5">${{ item.price.toLocaleString() }}</p>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <!-- Qty Control -->
                            <div class="flex items-center border border-slate-200 dark:border-white/10 rounded-lg bg-slate-50 dark:bg-solar-primary-dark/20 p-1">
                                <button 
                                    @click="decrementQty(item.id)"
                                    class="p-1 text-slate-500 dark:text-slate-400 hover:text-solar-primary disabled:opacity-30"
                                    :disabled="item.quantity <= 1"
                                >
                                    <Minus class="h-3.5 w-3.5" />
                                </button>
                                <span class="px-2.5 text-xs font-extrabold text-slate-850 dark:text-white">{{ item.quantity }}</span>
                                <button 
                                    @click="incrementQty(item.id)"
                                    class="p-1 text-slate-500 dark:text-slate-400 hover:text-solar-primary disabled:opacity-30"
                                    :disabled="item.quantity >= item.stock"
                                >
                                    <Plus class="h-3.5 w-3.5" />
                                </button>
                            </div>

                            <!-- Remove Button -->
                            <button 
                                @click="removeItem(item.id)"
                                class="p-2.5 rounded-lg border border-solar-danger/10 text-solar-danger hover:bg-solar-danger/10 transition-all"
                                title="Remove Item"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-2">
                        <Link 
                            href="/user/marketplace"
                            class="inline-flex items-center gap-2 text-xs font-bold text-solar-primary hover:underline"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            <span>Continue Shopping</span>
                        </Link>
                    </div>
                </div>

                <!-- Summary Sidebar (Right Column) -->
                <div class="flex flex-col gap-6">
                    <div class="glass-card p-6 flex flex-col gap-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Order Summary</h3>
                        
                        <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-1"></div>

                        <!-- Calculations -->
                        <div class="flex flex-col gap-3.5 text-xs">
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Subtotal</span>
                                <span class="font-bold text-slate-800 dark:text-white">${{ subtotal.toLocaleString() }}</span>
                            </div>
                            <div v-if="couponApplied" class="flex justify-between text-solar-success font-semibold">
                                <span class="flex items-center gap-1">Discount ({{ couponDiscount * 100 }}%)</span>
                                <span>-${{ discountAmount.toLocaleString() }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Est. Logistics & Shipping</span>
                                <span v-if="shippingFee === 0" class="font-bold text-solar-success uppercase text-[10px]">Free</span>
                                <span v-else class="font-bold text-slate-800 dark:text-white">${{ shippingFee }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                <span>Estimated Sales Tax (8%)</span>
                                <span class="font-bold text-slate-800 dark:text-white">${{ taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                            </div>
                            
                            <div class="h-px bg-solar-primary/10 dark:bg-white/5 my-2"></div>
                            
                            <div class="flex justify-between text-sm font-extrabold text-slate-800 dark:text-white">
                                <span>Grand Total</span>
                                <span class="text-base text-solar-primary dark:text-solar-primary-accent">${{ grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                            </div>
                        </div>

                        <!-- Coupon Code -->
                        <div class="flex flex-col gap-2 mt-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Promo Coupon Code</label>
                            <div class="flex gap-2">
                                <div class="relative flex-grow">
                                    <input 
                                        v-model="couponCode"
                                        type="text" 
                                        placeholder="e.g. SOLAR10"
                                        class="h-10 w-full pl-8 pr-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                                        :disabled="couponApplied"
                                    />
                                    <Tag class="absolute left-2.5 top-3 h-4 w-4 text-slate-400" />
                                </div>
                                <button 
                                    @click="applyCoupon"
                                    class="px-4 h-10 rounded-xl bg-slate-200 dark:bg-solar-primary-dark/50 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all hover:bg-slate-300"
                                    :class="couponApplied ? 'bg-solar-success/10 text-solar-success border border-solar-success/20 hover:bg-solar-success/15' : ''"
                                    :disabled="couponApplied"
                                >
                                    <Check v-if="couponApplied" class="h-4 w-4" />
                                    <span v-else>Apply</span>
                                </button>
                            </div>
                            <p v-if="couponApplied" class="text-[10px] text-solar-success font-semibold mt-0.5">Discount code applied successfully!</p>
                            <p v-if="couponError" class="text-[10px] text-solar-danger font-semibold mt-0.5">{{ couponError }}</p>
                        </div>

                        <Link 
                            href="/user/checkout"
                            class="h-12 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow mt-2"
                        >
                            <span>Proceed to Checkout</span>
                        </Link>
                    </div>

                    <div class="glass-card p-4 text-[10px] text-slate-400 dark:text-slate-500 border border-dashed border-slate-200 dark:border-white/5 leading-relaxed rounded-xl text-center">
                        Secure SSL checkout portal. Orders are dispatched within 24 hours of clearance. Local distributor SLAs apply.
                    </div>
                </div>
            </div>

        </div>
    </CustomerLayout>
</template>
