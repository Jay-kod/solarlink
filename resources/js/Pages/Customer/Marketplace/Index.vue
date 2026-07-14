<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { mockProducts, Product, ProductCategories } from '@/data/products'
import { ShoppingBag, Star, Search, Filter, Sparkles, CheckCircle2, ChevronRight, X, Trash2, Heart } from 'lucide-vue-next'

const props = defineProps<{
    products?: Product[]
    wishlistIds?: number[]
    cartItems?: Array<{
        id: number;
        name: string;
        price: number;
        quantity: number;
        image: string;
        category: string;
        stock: number;
    }>
}>()

const products = ref<Product[]>(props.products && props.products.length > 0 ? props.products : mockProducts)
const searchQuery = ref('')
const selectedCategory = ref<ProductCategories | 'all'>('all')

const isLoading = ref(true)
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
    }, 800)
})

// Shopping Cart and Wishlist from Backend props
const cartItems = computed(() => {
    return (props.cartItems || []).map(item => ({
        quantity: item.quantity,
        product: {
            id: item.id,
            name: item.name,
            price: item.price,
            image: item.image,
            category: item.category,
            stock: item.stock
        }
    }))
})

const wishlistIds = computed(() => props.wishlistIds || [])
const isCartOpen = ref(false)

const filteredProducts = computed(() => {
    return products.value.filter(p => {
        const matchesSearch = p.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || p.description.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesCategory = selectedCategory.value === 'all' || p.category === selectedCategory.value
        return matchesSearch && matchesCategory
    })
})

const addToCart = (product: Product) => {
    router.post('/user/cart', {
        product_id: product.id,
        quantity: 1
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isCartOpen.value = true
        }
    })
}

const toggleWishlist = (product: Product) => {
    router.post('/user/wishlist', {
        product_id: product.id
    }, {
        preserveScroll: true
    })
}

const removeFromCart = (id: number) => {
    router.delete(`/user/cart/${id}`, {
        preserveScroll: true
    })
}

const cartSubtotal = computed(() => {
    return cartItems.value.reduce((acc, item) => acc + (item.product.price * item.quantity), 0)
})

const categories: { label: string; value: ProductCategories | 'all' }[] = [
    { label: 'All Catalog', value: 'all' },
    { label: 'Monocrystalline Panels', value: 'panels' },
    { label: 'Battery Storage Walls', value: 'batteries' },
    { label: 'Smart Hybrid Inverters', value: 'inverters' },
    { label: 'Accessories & Wiring', value: 'accessories' }
]
</script>

<template>
    <Head title="SolarLink — Wholesale Parts Marketplace" />

    <CustomerLayout title="Wholesale Marketplace">
        <div class="flex flex-col gap-8 text-left relative">
            
            <!-- Shopping Cart Sidebar Drawer Overlay -->
            <div 
                v-if="isCartOpen" 
                class="fixed inset-0 z-50 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm flex justify-end"
            >
                <div class="w-full max-w-md bg-white dark:bg-solar-bg-dark h-full border-l border-solar-primary/10 flex flex-col justify-between text-left shadow-solar-lg relative animate-fade-in-up">
                    
                    <!-- Drawer Header -->
                    <div class="h-20 border-b border-solar-primary/10 dark:border-white/5 flex items-center justify-between px-6">
                        <div class="flex items-center gap-2.5">
                            <ShoppingBag class="h-5 w-5 text-solar-primary" />
                            <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Shopping Cart</h3>
                            <span class="px-2 py-0.5 rounded-full bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent text-[10px] font-bold">
                                {{ cartItems.length }}
                            </span>
                        </div>
                        <button 
                            @click="isCartOpen = false"
                            class="p-2 rounded-lg text-slate-450 hover:bg-slate-50 dark:hover:bg-solar-primary-dark/20"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <!-- Drawer Items List -->
                    <div class="flex-grow overflow-y-auto p-6 flex flex-col gap-4">
                        <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center gap-4 text-center h-[60%] opacity-60">
                            <ShoppingBag class="h-12 w-12 text-slate-350" />
                            <p class="text-xs text-slate-500">Your wholesale cart is empty.<br/>Browse direct items to add them to your cart.</p>
                        </div>

                        <div 
                            v-else
                            v-for="item in cartItems" 
                            :key="item.product.id"
                            class="p-3.5 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-100 dark:border-white/5 flex gap-3 relative"
                        >
                            <img :src="item.product.image" :alt="item.product.name" class="h-14 w-14 rounded-lg object-cover" />
                            <div class="flex-grow min-w-0">
                                <h4 class="font-bold text-xs text-slate-800 dark:text-white truncate pr-6">{{ item.product.name }}</h4>
                                <span class="text-[10px] font-bold text-solar-primary uppercase tracking-wider block mt-0.5">{{ item.product.category }}</span>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-slate-400">Qty: {{ item.quantity }}</span>
                                    <span class="text-sm font-extrabold text-slate-900 dark:text-white">${{ item.product.price * item.quantity }}</span>
                                </div>
                            </div>
                            <button 
                                @click="removeFromCart(item.product.id)"
                                class="absolute top-3.5 right-3.5 p-1 text-slate-400 hover:text-solar-danger"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Drawer Footer Summary -->
                    <div class="p-6 border-t border-solar-primary/10 dark:border-white/5 bg-slate-50/50 dark:bg-solar-primary-dark/10 flex flex-col gap-4">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Subtotal</span>
                            <span class="text-base font-extrabold text-slate-900 dark:text-white">${{ cartSubtotal }}</span>
                        </div>
                        
                        <Link 
                            href="/user/checkout" 
                            class="h-12 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white font-bold text-sm flex items-center justify-center gap-2 shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow"
                            @click="isCartOpen = false"
                        >
                            <span>Proceed to Checkout</span>
                            <ChevronRight class="h-4.5 w-4.5" />
                        </Link>
                    </div>

                </div>
            </div>

            <!-- Page actions header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                        <Sparkles class="h-3 w-3" />
                        <span>Direct Wholesaling Catalog</span>
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Wholesale Hardware Catalog</h2>
                    <p class="text-xs text-slate-450 mt-0.5">Shop verified direct manufacturer parts, solar cells, modular storage panels, and wiring packages.</p>
                </div>

                <button 
                    @click="isCartOpen = true"
                    class="px-4.5 h-11 rounded-xl bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-xs flex items-center justify-center gap-2 relative shadow animate-pulse-slow"
                >
                    <ShoppingBag class="h-4.5 w-4.5" />
                    <span>View Cart ({{ cartItems.length }})</span>
                </button>
            </div>

            <!-- Filters & Search tools -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <!-- Search bar -->
                <div class="md:col-span-4 relative flex items-center">
                    <Search class="absolute left-3.5 h-4.5 w-4.5 text-slate-400 pointer-events-none" />
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Search panel types, hybrid batteries..."
                        class="w-full h-11 pl-10 pr-4 rounded-xl bg-white dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs focus:outline-none focus:border-solar-primary transition-all duration-300"
                    />
                </div>

                <!-- Category filters -->
                <div class="md:col-span-8 flex flex-wrap items-center gap-2">
                    <button 
                        v-for="cat in categories" 
                        :key="cat.value"
                        @click="selectedCategory = cat.value"
                        class="px-4 h-9 rounded-full text-xs font-bold transition-all duration-200 uppercase tracking-wider"
                        :class="selectedCategory === cat.value 
                            ? 'bg-solar-primary text-white shadow' 
                            : 'bg-white dark:bg-solar-primary-dark/20 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/5 hover:bg-slate-50'"
                    >
                        {{ cat.label }}
                    </button>
                </div>
            </div>

            <!-- SKELETON GRID -->
            <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
                <div 
                    v-for="i in 6" 
                    :key="i"
                    class="glass-card overflow-hidden rounded-2xl text-left flex flex-col h-full bg-white dark:bg-solar-bg-dark/40 border border-slate-100 dark:border-white/5"
                >
                    <div class="h-48 bg-slate-200 dark:bg-slate-800/80 animate-pulse"></div>
                    <div class="p-5 flex flex-col flex-grow gap-4">
                        <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-2/3"></div>
                        <div class="space-y-2">
                            <div class="h-3.5 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
                            <div class="h-3.5 bg-slate-200 dark:bg-slate-800 rounded w-5/6"></div>
                        </div>
                        <div class="h-3.5 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
                        <div class="h-px bg-slate-100 dark:bg-slate-800/50 w-full mt-2"></div>
                        <div class="flex justify-between items-center mt-auto pt-2">
                            <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                            <div class="h-9 bg-slate-200 dark:bg-slate-800 rounded-xl w-24"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products catalog grid -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
                <div 
                    v-for="prod in filteredProducts" 
                    :key="prod.id"
                    class="glass-card overflow-hidden rounded-2xl text-left flex flex-col h-full bg-white dark:bg-solar-bg-dark/40"
                >
                    <div class="h-48 overflow-hidden relative">
                        <img :src="prod.image" :alt="prod.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        
                        <!-- Wishlist Toggle Button -->
                        <button 
                            @click.stop="toggleWishlist(prod)"
                            class="absolute top-3 left-3 h-8 w-8 rounded-xl bg-white/90 dark:bg-solar-bg-dark/95 flex items-center justify-center shadow transition-all duration-300 z-10"
                            :class="wishlistIds.includes(prod.id) ? 'text-red-500' : 'text-slate-400 hover:text-red-500'"
                            :title="wishlistIds.includes(prod.id) ? 'Remove from Wishlist' : 'Add to Wishlist'"
                        >
                            <Heart class="h-4 w-4" :class="{ 'fill-current': wishlistIds.includes(prod.id) }" />
                        </button>

                        <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full bg-solar-primary text-white font-bold text-[9px] uppercase tracking-wider shadow">
                            {{ prod.category }}
                        </span>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-grow gap-3">
                        <h3 class="font-bold text-base text-slate-800 dark:text-slate-100 line-clamp-1 leading-snug">{{ prod.name }}</h3>
                        <p class="text-xs text-slate-450 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ prod.description }}</p>
                        
                        <!-- Rating -->
                        <div class="flex items-center gap-1 text-amber-500 text-xs">
                            <Star class="h-3.5 w-3.5 fill-amber-500" />
                            <span class="font-bold">{{ prod.rating }}</span>
                            <span class="text-slate-400 dark:text-slate-500">({{ prod.reviewsCount }})</span>
                        </div>

                        <!-- Technical specs -->
                        <div class="flex flex-col gap-1 pt-1.5 border-t border-slate-100 dark:border-white/5">
                            <div 
                                v-for="(val, key) in Object.entries(prod.specs).slice(0, 2)" 
                                :key="key"
                                class="flex items-center justify-between text-[9px] text-slate-400 uppercase tracking-wider"
                            >
                                <span>{{ val[0] }}</span>
                                <span class="font-bold text-slate-650 dark:text-slate-200">{{ val[1] }}</span>
                            </div>
                        </div>

                        <!-- Pricing / Buy Button -->
                        <div class="mt-auto pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                            <div>
                                <span class="text-xl font-extrabold text-slate-900 dark:text-white">${{ prod.price }}</span>
                                <span v-if="prod.originalPrice" class="text-xs text-slate-400 line-through ml-1.5">${{ prod.originalPrice }}</span>
                            </div>
                            <button 
                                @click="addToCart(prod)"
                                class="px-4 h-9 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold flex items-center gap-1.5 shadow"
                            >
                                <ShoppingBag class="h-3.5 w-3.5" />
                                <span>Add to Cart</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </CustomerLayout>
</template>
