<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { ShoppingBag, Star, ArrowLeft, Trash2, Heart, Sparkles } from 'lucide-vue-next'

interface WishlistItem {
    id: number;
    wishlist_item_id: number;
    name: string;
    price: number;
    originalPrice?: number | null;
    rating: number;
    reviewsCount: number;
    image: string;
    category: string;
    stock: number;
    description: string;
    specs: Record<string, string>;
}

const props = defineProps<{
    wishlistItems?: WishlistItem[]
}>()

const wishlistItems = computed(() => props.wishlistItems || [])

const removeFromWishlist = (productId: number) => {
    router.post('/user/wishlist', {
        product_id: productId
    }, {
        preserveScroll: true
    })
}

const moveToCart = (item: WishlistItem) => {
    // Add to cart
    router.post('/user/cart', {
        product_id: item.id,
        quantity: 1
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Remove from wishlist
            removeFromWishlist(item.id)
        }
    })
}
</script>

<template>
    <Head title="SolarLink — My Saved Items" />

    <CustomerLayout title="My Wishlist">
        <div class="flex flex-col gap-8 text-left max-w-5xl mx-auto">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Heart class="h-3 w-3 fill-solar-primary" />
                    <span>Saved Hardware Vault</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Your Wishlist</h2>
                <p class="text-xs text-slate-450 mt-0.5">Keep track of wholesale parts and batteries needed for your solar layout design.</p>
            </div>

            <!-- Empty Wishlist State -->
            <div v-if="wishlistItems.length === 0" class="glass-card p-10 flex flex-col items-center justify-center text-center gap-5 bg-white dark:bg-solar-bg-dark/40">
                <div class="h-16 w-16 rounded-full bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center shadow-sm">
                    <Heart class="h-8 w-8 fill-red-500" />
                </div>
                <div class="flex flex-col gap-1.5">
                    <h3 class="font-extrabold text-lg text-slate-800 dark:text-white">Your Wishlist is Empty</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
                        You haven't saved any hardware products yet. Browse our direct wholesale marketplace to add panels, battery storage walls, or charge controllers.
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

            <!-- Wishlist Content -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="item in wishlistItems" 
                    :key="item.id"
                    class="glass-card overflow-hidden rounded-2xl text-left flex flex-col h-full bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5"
                >
                    <div class="h-48 overflow-hidden relative">
                        <img :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                        
                        <button 
                            @click="removeFromWishlist(item.id)"
                            class="absolute top-3 left-3 h-8 w-8 rounded-xl bg-white/95 dark:bg-solar-bg-dark/95 flex items-center justify-center text-red-500 shadow hover:bg-red-500 hover:text-white transition-all duration-300"
                            title="Remove from Wishlist"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>

                        <span class="absolute top-3 right-3 px-2.5 py-0.5 rounded-full bg-solar-primary text-white font-bold text-[9px] uppercase tracking-wider shadow">
                            {{ item.category }}
                        </span>
                    </div>

                    <div class="p-5 flex flex-col flex-grow gap-3">
                        <h3 class="font-bold text-base text-slate-800 dark:text-slate-100 line-clamp-1 leading-snug">{{ item.name }}</h3>
                        <p class="text-xs text-slate-450 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ item.description }}</p>
                        
                        <!-- Rating -->
                        <div class="flex items-center gap-1 text-amber-500 text-xs">
                            <Star class="h-3.5 w-3.5 fill-amber-500" />
                            <span class="font-bold">{{ item.rating }}</span>
                            <span class="text-slate-400 dark:text-slate-500">({{ item.reviewsCount }})</span>
                        </div>

                        <!-- Technical specs -->
                        <div class="flex flex-col gap-1 pt-1.5 border-t border-slate-100 dark:border-white/5">
                            <div 
                                v-for="(val, key) in Object.entries(item.specs).slice(0, 2)" 
                                :key="key"
                                class="flex items-center justify-between text-[9px] text-slate-400 uppercase tracking-wider"
                            >
                                <span>{{ val[0] }}</span>
                                <span class="font-bold text-slate-650 dark:text-slate-200">{{ val[1] }}</span>
                            </div>
                        </div>

                        <!-- Pricing & Actions -->
                        <div class="mt-auto pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
                            <div>
                                <span class="text-xl font-extrabold text-slate-900 dark:text-white">${{ item.price.toLocaleString() }}</span>
                                <span v-if="item.originalPrice" class="text-xs text-slate-400 line-through ml-1.5">${{ item.originalPrice.toLocaleString() }}</span>
                            </div>
                            <button 
                                @click="moveToCart(item)"
                                class="px-3.5 h-9 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold flex items-center gap-1.5 shadow"
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
