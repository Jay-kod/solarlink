<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'
import { mockNotifications, type Notification } from '@/data/notifications'
import { 
    Sun, Moon, Menu, X, Bell, ChevronDown, User, LogOut, Settings, 
    UserCheck, Wrench, Store, ShieldAlert,
    Cpu, Zap, Calendar, DollarSign, ShoppingBag, FolderKanban, 
    MessageSquare, FileText, ClipboardList, CheckCircle2,
    AlertTriangle, AlertCircle, Info, ExternalLink, Shield, Heart
} from 'lucide-vue-next'

const props = defineProps<{
    role: 'customer' | 'technician' | 'vendor' | 'admin';
    title: string;
}>()

const { isDark, toggleDarkMode, initDarkMode } = useDarkMode()
const isSidebarOpen = ref(true)
const isMobileSidebarOpen = ref(false)
const isNotificationOpen = ref(false)
const isProfileOpen = ref(false)

onMounted(() => {
    initDarkMode()
    document.addEventListener('click', handleOutsideClick)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleOutsideClick)
})

const handleOutsideClick = (e: MouseEvent) => {
    const target = e.target as HTMLElement
    if (!target.closest('.notification-dropdown-zone')) {
        isNotificationOpen.value = false
    }
    if (!target.closest('.profile-dropdown-zone')) {
        isProfileOpen.value = false
    }
}

const toggleSidebar = () => {
    if (window.innerWidth < 768) {
        isMobileSidebarOpen.value = !isMobileSidebarOpen.value
    } else {
        isSidebarOpen.value = !isSidebarOpen.value
    }
}

// Configuration of links by role
const roleMenuConfigs = {
    customer: {
        theme: 'text-solar-primary dark:text-solar-primary-accent',
        logoText: 'Client Hub',
        links: [
            { name: 'Telemetry Dashboard', href: '/user', icon: Cpu },
            { name: 'Solar Appliances', href: '/user/appliances', icon: Cpu },
            { name: 'Technician Map', href: '/user/map', icon: Zap },
            { name: 'Service Bookings', href: '/user/bookings', icon: Calendar },
            { name: 'Marketplace', href: '/user/marketplace', icon: ShoppingBag },
            { name: 'Shopping Cart', href: '/user/cart', icon: ShoppingBag, badge: 'cartCount' },
            { name: 'My Wishlist', href: '/user/wishlist', icon: Heart, badge: 'wishlistCount' },
            { name: 'Service Chat', href: '/user/chat', icon: MessageSquare },
            { name: 'Billing & Payments', href: '/user/payments', icon: DollarSign },
            { name: 'Account Settings', href: '/user/settings', icon: Settings }
        ],
        profileName: 'Alice Johnson',
        profileRole: 'Solar Asset Owner',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150'
    },
    technician: {
        theme: 'text-emerald-500 dark:text-emerald-400',
        logoText: 'Tech Portal',
        links: [
            { name: 'Service Dashboard', href: '/technician', icon: Wrench },
            { name: 'Incoming & Active Jobs', href: '/technician/jobs', icon: ClipboardList },
            { name: 'Calendar Scheduling', href: '/technician/calendar', icon: Calendar },
            { name: 'Revenue & Payouts', href: '/technician/earnings', icon: DollarSign },
            { name: 'Service Chat', href: '/technician/chat', icon: MessageSquare }
        ],
        profileName: 'Marcus Vance',
        profileRole: 'NABCEP Engineer',
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150'
    },
    vendor: {
        theme: 'text-indigo-500 dark:text-indigo-400',
        logoText: 'Vendor Portal',
        links: [
            { name: 'Inventory Dashboard', href: '/vendor', icon: Store },
            { name: 'Product Listings', href: '/vendor/products', icon: ShoppingBag },
            { name: 'Dispatched Orders', href: '/vendor/orders', icon: FolderKanban },
            { name: 'Store Branding', href: '/vendor/store', icon: Settings },
            { name: 'Service Chat', href: '/vendor/chat', icon: MessageSquare }
        ],
        profileName: 'SolarLink Direct',
        profileRole: 'OEM Supplier',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150'
    },
    admin: {
        theme: 'text-purple-500 dark:text-purple-400',
        logoText: 'Global Ops',
        links: [
            { name: 'Overview Operations', href: '/admin', icon: ShieldAlert },
            { name: 'User Management', href: '/admin/users', icon: UserCheck },
            { name: 'Chat Audit Monitoring', href: '/admin/chat', icon: MessageSquare },
            { name: 'Technician Verifications', href: '/admin/technicians', icon: Wrench },
            { name: 'Vendor Moderation', href: '/admin/vendors', icon: Store },
            { name: 'Parts Approvals', href: '/admin/products', icon: ShoppingBag },
            { name: 'Blog CMS Edit', href: '/admin/blog', icon: FileText },
            { name: 'System Settings', href: '/admin/settings', icon: Settings }
        ],
        profileName: 'Admin Director',
        profileRole: 'Global Superuser',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150'
    }
}

const currentConfig = roleMenuConfigs[props.role]

// Dynamic profile details from backend
const page = usePage()
const authUser = computed(() => {
    const auth = page.props.auth as any
    return auth ? auth.user : null
})

const displayName = computed(() => {
    return authUser.value ? authUser.value.name : currentConfig.profileName
})

const displayRole = computed(() => {
    if (authUser.value) {
        const r = authUser.value.role
        return r.charAt(0).toUpperCase() + r.slice(1) + ' Account'
    }
    return currentConfig.profileRole
})

const displayEmail = computed(() => {
    return authUser.value?.email || `${currentConfig.profileName.toLowerCase().replace(/\s+/g, '.')}@solarlink.io`
})

const displayAvatar = computed(() => {
    return authUser.value?.avatar || currentConfig.avatar
})

const rolesSwitcher = [
    { name: 'Customer App', href: '/user', icon: UserCheck, desc: 'Client portal & bookings', color: 'text-blue-500' },
    { name: 'Technician Panel', href: '/technician', icon: Wrench, desc: 'Jobs & scheduling', color: 'text-emerald-500' },
    { name: 'Vendor Portal', href: '/vendor', icon: Store, desc: 'Store & inventory', color: 'text-indigo-500' },
    { name: 'Admin Dashboard', href: '/admin', icon: ShieldAlert, desc: 'Global settings & moderation', color: 'text-purple-500' }
]

// Notifications
const notificationsList = ref<Notification[]>([...mockNotifications])

const unreadCount = computed(() => notificationsList.value.filter(n => !n.read).length)

const markAllRead = () => {
    notificationsList.value.forEach(n => n.read = true)
}

const dismissNotification = (id: number) => {
    notificationsList.value = notificationsList.value.filter(n => n.id !== id)
}

const markAsRead = (id: number) => {
    const n = notificationsList.value.find(n => n.id === id)
    if (n) n.read = true
}

const getNotifIcon = (type: string) => {
    switch (type) {
        case 'success': return CheckCircle2
        case 'warning': return AlertTriangle
        case 'error': return AlertCircle
        default: return Info
    }
}

const getNotifColor = (type: string) => {
    switch (type) {
        case 'success': return 'text-emerald-500 dark:text-emerald-400 bg-emerald-500/10'
        case 'warning': return 'text-amber-500 dark:text-amber-400 bg-amber-500/10'
        case 'error': return 'text-red-500 dark:text-red-400 bg-red-500/10'
        default: return 'text-solar-primary dark:text-solar-primary-accent bg-slate-500/10'
    }
}

const getRoleBadgeClass = computed(() => {
    switch (props.role) {
        case 'customer': return 'bg-blue-500/10 text-blue-600 dark:text-blue-400'
        case 'technician': return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
        case 'vendor': return 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
        case 'admin': return 'bg-purple-500/10 text-purple-600 dark:text-purple-400'
        default: return 'bg-slate-500/10 text-slate-600'
    }
})
</script>

<template>
    <div class="relative h-screen overflow-hidden flex bg-slate-50 dark:bg-[#0B0F19] text-slate-800 dark:text-slate-100 transition-colors duration-300">
        
        <!-- Mobile Sidebar Overlay (Drawer) -->
        <div 
            v-if="isMobileSidebarOpen"
            class="md:hidden fixed inset-0 z-50 flex"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div 
                @click="isMobileSidebarOpen = false"
                class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
            ></div>

            <!-- Drawer Content -->
            <div class="relative flex flex-col w-72 max-w-xs bg-[#0F1322] border-r border-slate-800/80 h-full z-10 transition-transform animate-fade-in-right">
                <!-- Header -->
                <div class="flex items-center justify-between p-5 border-b border-slate-800/50">
                    <Link :href="`/${role}`" @click="isMobileSidebarOpen = false" class="flex items-center gap-2.5">
                        <div class="h-8 w-8 rounded-lg bg-solar-primary flex items-center justify-center shadow-sm shrink-0">
                            <span class="text-white font-extrabold text-sm">SL</span>
                        </div>
                        <span class="font-bold text-xs text-white tracking-widest uppercase">{{ currentConfig.logoText }}</span>
                    </Link>
                    <button 
                        @click="isMobileSidebarOpen = false"
                        class="p-2 rounded-lg text-slate-400 hover:bg-white/5"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-grow p-4 flex flex-col gap-1 overflow-y-auto">
                    <Link 
                        v-for="link in currentConfig.links"
                        :key="link.name"
                        :href="link.href"
                        @click="isMobileSidebarOpen = false"
                        class="flex items-center rounded-lg px-3.5 h-11 gap-3 w-full transition-all duration-200"
                        :class="[
                            $page.url === link.href
                                ? 'bg-solar-primary text-white font-semibold'
                                : 'text-slate-400 hover:bg-white/5 hover:text-white font-medium'
                        ]"
                    >
                        <component :is="link.icon" class="h-4 w-4 shrink-0" />
                        <span class="text-xs truncate">{{ link.name }}</span>
                        <span 
                            v-if="link.badge && $page.props[link.badge] > 0" 
                            class="ml-auto px-2 py-0.5 rounded-full bg-solar-primary/20 text-solar-primary-accent text-[9px] font-bold border border-solar-primary/30"
                        >
                            {{ $page.props[link.badge] }}
                        </span>
                    </Link>
                </nav>

                <!-- Mobile Sidebar Bottom: Profile + Logout -->
                <div class="mt-auto border-t border-slate-800/50 p-4">
                    <!-- Profile Card -->
                    <Link href="/profile" @click="isMobileSidebarOpen = false" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-white/5 transition-colors group mb-3">
                        <img :src="displayAvatar" :alt="displayName" class="h-9 w-9 rounded-lg object-cover ring-1 ring-white/10 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-xs text-white truncate">{{ displayName }}</p>
                            <p class="text-[9px] text-slate-400 font-medium uppercase tracking-wider truncate">{{ displayRole }}</p>
                        </div>
                    </Link>
                    <!-- Logout Button -->
                    <Link 
                        method="post" 
                        as="button" 
                        href="/logout" 
                        class="w-full flex items-center justify-center gap-2 h-10 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition-all duration-200"
                    >
                        <LogOut class="h-4 w-4" />
                        <span>Sign Out</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Sidebar (Desktop) -->
        <aside 
            class="hidden md:flex flex-col h-screen sticky top-0 shrink-0 border-r border-slate-200/80 dark:border-slate-800/60 bg-white dark:bg-[#0F1322] transition-all duration-300 relative z-30"
            :class="isSidebarOpen ? 'w-64' : 'w-16'"
        >
            <!-- Logo Section -->
            <div 
                class="h-16 flex items-center border-b border-slate-200/80 dark:border-slate-800/60 transition-all duration-300"
                :class="isSidebarOpen ? 'px-5 justify-between' : 'px-0 justify-center'"
            >
                <Link :href="`/${role}`" class="flex items-center gap-2.5 group overflow-hidden">
                    <div class="h-8 w-8 rounded-lg bg-solar-primary flex items-center justify-center shrink-0">
                        <span class="text-white font-extrabold text-sm">SL</span>
                    </div>
                    <span v-if="isSidebarOpen" class="font-bold text-xs tracking-wider text-slate-900 dark:text-white uppercase whitespace-nowrap">{{ currentConfig.logoText }}</span>
                </Link>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-grow overflow-y-auto p-3 flex flex-col gap-1 transition-all duration-300" :class="{ 'px-2': !isSidebarOpen }">
                <Link 
                    v-for="link in currentConfig.links"
                    :key="link.name"
                    :href="link.href"
                    class="flex items-center rounded-lg transition-all duration-150"
                    :class="[
                        $page.url === link.href
                            ? 'bg-solar-primary text-white font-semibold shadow-sm'
                            : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-white font-medium',
                        isSidebarOpen ? 'px-3 h-10 gap-3 w-full' : 'p-0 h-10 w-10 justify-center mx-auto gap-0'
                    ]"
                    :title="isSidebarOpen ? '' : link.name"
                >
                    <div class="relative flex items-center justify-center shrink-0">
                        <component :is="link.icon" class="h-4 w-4" />
                        <span 
                            v-if="!isSidebarOpen && link.badge && $page.props[link.badge] > 0" 
                            class="absolute -top-1.5 -right-1.5 h-3.5 min-w-[14px] px-0.5 rounded-full bg-red-500 text-white text-[7px] font-bold flex items-center justify-center"
                        >
                            {{ $page.props[link.badge] }}
                        </span>
                    </div>
                    <span v-if="isSidebarOpen" class="text-xs truncate">{{ link.name }}</span>
                    <span 
                        v-if="isSidebarOpen && link.badge && $page.props[link.badge] > 0" 
                        class="ml-auto px-1.5 py-0.5 rounded-full text-[9px] font-bold transition-colors"
                        :class="[
                            $page.url === link.href
                                ? 'bg-white text-solar-primary'
                                : 'bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent border border-solar-primary/20'
                        ]"
                    >
                        {{ $page.props[link.badge] }}
                    </span>
                </Link>
            </nav>

            <!-- Bottom: Profile Card + Collapse + Logout -->
            <div class="border-t border-slate-200/80 dark:border-slate-800/60 transition-all duration-300" :class="isSidebarOpen ? 'p-4' : 'p-2'">
                <!-- Profile Card (expanded sidebar) -->
                <Link 
                    v-if="isSidebarOpen" 
                    href="/profile" 
                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-white/5 transition-colors group mb-3"
                >
                    <img :src="displayAvatar" :alt="displayName" class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-100 dark:ring-white/10 shrink-0" />
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-slate-900 dark:text-white truncate">{{ displayName }}</p>
                        <p class="text-[9px] text-slate-400 font-medium uppercase tracking-wider truncate">{{ displayRole }}</p>
                    </div>
                </Link>
                <!-- Profile icon only (collapsed sidebar) -->
                <Link 
                    v-else
                    href="/profile"
                    class="flex items-center justify-center h-10 w-10 mx-auto rounded-lg hover:bg-slate-50 dark:hover:bg-white/5 transition-colors mb-2"
                    title="Profile"
                >
                    <img :src="displayAvatar" :alt="displayName" class="h-8 w-8 rounded-lg object-cover ring-1 ring-slate-100 dark:ring-white/10 shrink-0" />
                </Link>

                <!-- Logout Button -->
                <Link 
                    method="post" 
                    as="button" 
                    href="/logout" 
                    class="w-full flex items-center justify-center gap-2 rounded-lg transition-all duration-200 font-semibold text-xs text-red-500 hover:text-white hover:bg-red-500"
                    :class="isSidebarOpen 
                        ? 'h-9 bg-red-500/5 dark:bg-red-500/10' 
                        : 'h-10 w-10 mx-auto bg-red-500/5 dark:bg-red-500/10'"
                    :title="isSidebarOpen ? '' : 'Sign Out'"
                >
                    <LogOut class="h-4 w-4 shrink-0" />
                    <span v-if="isSidebarOpen">Sign Out</span>
                </Link>
            </div>
        </aside>

        <!-- Main Body Frame -->
        <div class="flex-grow flex flex-col min-w-0">
            <!-- Header bar -->
            <header class="h-16 bg-white/80 dark:bg-[#0B0F19]/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/60 flex items-center justify-between px-6 sticky top-0 z-20 transition-all duration-300">
                <div class="flex items-center gap-3">
                    <!-- Hamburger Toggle Button -->
                    <button 
                        @click="toggleSidebar"
                        class="flex items-center justify-center h-9 w-9 rounded-lg border border-slate-200/80 dark:border-slate-800/60 bg-slate-50 hover:bg-slate-100 dark:bg-[#0F1322] dark:hover:bg-[#151B2E] text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-all duration-150"
                        aria-label="Toggle Sidebar"
                        title="Toggle Sidebar"
                    >
                        <Menu class="h-4.5 w-4.5" />
                    </button>
                    <h1 class="font-semibold text-xs tracking-wider text-slate-900 dark:text-white uppercase">{{ title }}</h1>
                </div>

                <div class="flex items-center gap-2">

                    <!-- Theme Toggle -->
                    <button 
                        @click="toggleDarkMode"
                        class="h-9 w-9 rounded-lg border border-slate-200/80 dark:border-slate-800/60 bg-slate-50 hover:bg-slate-100 dark:bg-[#0F1322] dark:hover:bg-[#151B2E] text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white flex items-center justify-center transition-all duration-150"
                        title="Toggle Theme"
                    >
                        <Sun v-if="isDark" class="h-4 w-4" />
                        <Moon v-else class="h-4 w-4" />
                    </button>

                    <!-- Notifications Dropdown -->
                    <div class="relative notification-dropdown-zone">
                        <button 
                            @click.stop="isNotificationOpen = !isNotificationOpen; isProfileOpen = false"
                            class="h-9 w-9 rounded-lg border border-slate-200/80 dark:border-slate-800/60 bg-slate-50 hover:bg-slate-100 dark:bg-[#0F1322] dark:hover:bg-[#151B2E] text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white flex items-center justify-center transition-all duration-150 relative"
                            title="Notifications"
                        >
                            <Bell class="h-4 w-4" />
                            <span 
                                v-if="unreadCount > 0" 
                                class="absolute top-2 right-2 h-1.5 w-1.5 rounded-full bg-red-500"
                            ></span>
                        </button>

                        <!-- Notification Panel -->
                        <Transition
                            enter-active-class="transition ease-out duration-150"
                            enter-from-class="opacity-0 translate-y-1.5 scale-98"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition ease-in duration-100"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 translate-y-1.5 scale-98"
                        >
                            <div 
                                v-if="isNotificationOpen"
                                class="absolute right-0 mt-2.5 w-80 max-w-[calc(100vw-2rem)] rounded-xl bg-white dark:bg-slate-900 p-0 shadow-lg border border-slate-200/80 dark:border-white/5 overflow-hidden"
                            >
                                <!-- Header -->
                                <div class="flex items-center justify-between px-4 py-3 bg-slate-50/50 dark:bg-slate-900 border-b border-slate-100 dark:border-white/5">
                                    <span class="font-semibold text-xs text-slate-800 dark:text-white">Notifications ({{ unreadCount }})</span>
                                    <button 
                                        v-if="unreadCount > 0"
                                        @click="markAllRead" 
                                        class="text-[10px] font-semibold text-solar-primary hover:text-solar-primary-accent"
                                    >
                                        Mark all read
                                    </button>
                                </div>

                                <!-- Notification List -->
                                <div class="flex flex-col max-h-72 overflow-y-auto">
                                    <template v-if="notificationsList.length > 0">
                                        <div 
                                            v-for="notif in notificationsList" 
                                            :key="notif.id"
                                            @click="markAsRead(notif.id)"
                                            class="group flex items-start gap-2.5 px-4 py-3 border-b border-slate-50 dark:border-white/5 last:border-b-0 cursor-pointer transition-colors hover:bg-slate-50/50 dark:hover:bg-white/[.02]"
                                            :class="!notif.read ? 'bg-slate-50/20 dark:bg-white/[.01]' : ''"
                                        >
                                            <!-- Type Icon -->
                                            <div class="shrink-0 mt-0.5 h-6 w-6 rounded-md flex items-center justify-center" :class="getNotifColor(notif.type)">
                                                <component :is="getNotifIcon(notif.type)" class="h-3.5 w-3.5" />
                                            </div>
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0 text-left">
                                                <div class="flex items-center justify-between gap-2 mb-0.5">
                                                    <span class="font-bold text-xs text-slate-700 dark:text-slate-200 truncate">{{ notif.title }}</span>
                                                    <span class="text-[9px] text-slate-400 font-medium whitespace-nowrap shrink-0">{{ notif.timestamp }}</span>
                                                </div>
                                                <p class="text-slate-500 dark:text-slate-450 text-[10px] leading-relaxed line-clamp-2">{{ notif.description }}</p>
                                            </div>
                                            <!-- Dismiss -->
                                            <button 
                                                @click.stop="dismissNotification(notif.id)" 
                                                class="shrink-0 mt-0.5 p-1 rounded hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity"
                                                title="Dismiss"
                                            >
                                                <X class="h-3 w-3" />
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Empty State -->
                                    <div v-else class="flex flex-col items-center justify-center py-10 px-4">
                                        <Bell class="h-6 w-6 text-slate-300 dark:text-slate-650 mb-2" />
                                        <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">No new notifications</p>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative profile-dropdown-zone">
                        <button 
                            @click.stop="isProfileOpen = !isProfileOpen; isNotificationOpen = false"
                            class="flex items-center gap-2 p-0.5 rounded-lg border border-slate-200/80 dark:border-slate-800/60 bg-slate-50 hover:bg-slate-100 dark:bg-[#0F1322] dark:hover:bg-[#151B2E] transition-colors"
                        >
                            <!-- FIXED PROFILE PIC SIZE WITH STRICT Tailwind CLASSES (h-8 w-8) -->
                            <img :src="displayAvatar" :alt="displayName" class="h-8 w-8 rounded-md object-cover shrink-0" />
                            <span class="hidden sm:inline pr-2 font-semibold text-xs text-slate-600 dark:text-slate-300">{{ displayName.split(' ')[0] }}</span>
                        </button>

                        <!-- Profile Panel -->
                        <Transition
                            enter-active-class="transition ease-out duration-150"
                            enter-from-class="opacity-0 translate-y-1.5 scale-98"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition ease-in duration-100"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 translate-y-1.5 scale-98"
                        >
                            <div 
                                v-if="isProfileOpen"
                                class="absolute right-0 mt-2.5 w-64 rounded-xl bg-white dark:bg-slate-900 shadow-lg border border-slate-200/80 dark:border-white/5 overflow-hidden"
                            >
                                <!-- Profile Header -->
                                <div class="px-4 py-4 bg-slate-50/50 dark:bg-slate-900 border-b border-slate-100 dark:border-white/5">
                                    <div class="flex items-center gap-3">
                                        <img :src="displayAvatar" :alt="displayName" class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-100 dark:ring-white/10 shrink-0" />
                                        <div class="flex-1 min-w-0 text-left">
                                            <h4 class="font-semibold text-xs text-slate-800 dark:text-white truncate">{{ displayName }}</h4>
                                            <p class="text-[9px] text-slate-400 font-medium truncate uppercase tracking-wide mt-0.5">{{ displayRole }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Links -->
                                <div class="p-1 flex flex-col gap-0.5">
                                    <Link 
                                        href="/profile" 
                                        class="flex items-center gap-2.5 p-2 rounded-lg text-left text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white"
                                    >
                                        <User class="h-4 w-4 text-slate-400" />
                                        <span>Profile Settings</span>
                                    </Link>
                                    <Link 
                                        :href="role === 'customer' ? '/user/settings' : `/${role}/settings`"
                                        class="flex items-center gap-2.5 p-2 rounded-lg text-left text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white"
                                    >
                                        <Settings class="h-4 w-4 text-slate-400" />
                                        <span>App Settings</span>
                                    </Link>
                                </div>

                                <!-- Switch Portal Section -->
                                <div class="px-1.5 pb-1.5 border-t border-slate-100 dark:border-white/5">
                                    <div class="px-2.5 pt-2 pb-1 text-left">
                                        <p class="text-[9px] font-semibold text-slate-400 uppercase tracking-widest">Switch Portal</p>
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <Link 
                                            v-for="rs in rolesSwitcher"
                                            :key="rs.name"
                                            :href="rs.href"
                                            class="flex items-center gap-2.5 p-2 rounded-lg text-[11px] font-semibold transition-colors"
                                            :class="[
                                                rs.href === `/${role}` || (role === 'customer' && rs.href === '/user')
                                                    ? 'bg-solar-primary/5 text-solar-primary dark:bg-solar-primary/10'
                                                    : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-800 dark:hover:text-slate-200'
                                            ]"
                                        >
                                            <component :is="rs.icon" class="h-3.5 w-3.5" :class="rs.color" />
                                            <span>{{ rs.name }}</span>
                                            <CheckCircle2 v-if="rs.href === `/${role}` || (role === 'customer' && rs.href === '/user')" class="h-3.5 w-3.5 ml-auto text-solar-primary" />
                                        </Link>
                                    </div>
                                </div>

                                <!-- Logout -->
                                <div class="p-1 border-t border-slate-100 dark:border-white/5">
                                    <Link 
                                        method="post" 
                                        as="button" 
                                        href="/logout" 
                                        class="w-full flex items-center justify-center gap-2 h-9 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white font-semibold text-xs transition-colors"
                                    >
                                        <LogOut class="h-3.5 w-3.5" />
                                        <span>Sign Out</span>
                                    </Link>
                                </div>
                            </div>
                        </Transition>
                    </div>

                </div>
            </header>

            <!-- Inside dashboard scroll layout -->
            <main class="flex-grow p-6 sm:p-8 overflow-y-auto relative">
                <slot />
            </main>
        </div>
    </div>
</template>
