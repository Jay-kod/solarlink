<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import DeleteUserForm from './Partials/DeleteUserForm.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
import UpdatePreferencesForm from './Partials/UpdatePreferencesForm.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { 
    User, Lock, Settings, ShieldAlert,
    Cpu, Zap, Calendar, Wrench, Store, Shield,
    UserCheck, MapPin, Clock, BadgeAlert, ChevronRight
} from 'lucide-vue-next'

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>()

const page = usePage()
const authUser = computed(() => (page.props.auth as any)?.user)

const userRole = computed(() => {
    if (!authUser.value) return 'customer'
    return (authUser.value.role || 'customer') as 'customer' | 'technician' | 'vendor' | 'admin'
})

// Sections definition for Scrollspy
const sections = [
    { id: 'personal-info', name: 'Profile Details', icon: User, desc: 'Personal details & avatar' },
    { id: 'security', name: 'Security Credentials', icon: Lock, desc: 'Password & verification' },
    { id: 'preferences', name: 'App Preferences', icon: Settings, desc: 'Alerts, theme & telemetry' },
    { id: 'danger-zone', name: 'Danger Zone', icon: Trash2, desc: 'Delete user account', danger: true }
]

// Lucide Trash2 workaround if imported dynamically or statically
import { Trash2 } from 'lucide-vue-next'

const activeSection = ref('personal-info')
let observer: IntersectionObserver | null = null

const scrollToSection = (id: string) => {
    const el = document.getElementById(id)
    const mainEl = document.querySelector('main')
    if (el && mainEl) {
        const mainRect = mainEl.getBoundingClientRect()
        const elRect = el.getBoundingClientRect()
        const relativeTop = elRect.top - mainRect.top + mainEl.scrollTop
        
        mainEl.scrollTo({ 
            top: relativeTop - 16, 
            behavior: 'smooth' 
        })
        activeSection.value = id
    }
}

onMounted(() => {
    const mainEl = document.querySelector('main')
    const options = {
        root: mainEl,
        rootMargin: '-20px 0px -60% 0px',
        threshold: 0
    }
    
    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                activeSection.value = entry.target.id
            }
        })
    }, options)
    
    sections.forEach((sec) => {
        const el = document.getElementById(sec.id)
        if (el) observer?.observe(el)
    })
})

onBeforeUnmount(() => {
    observer?.disconnect()
})

// Dynamic Role specific configurations
const roleDetails = computed(() => {
    switch (userRole.value) {
        case 'admin':
            return {
                title: 'System Administrator',
                desc: 'Access Level: Tier-1 Root Clearance',
                badgeStyle: 'badge-admin',
                icon: Shield,
                stats: [
                    { label: 'System status', value: '100% Online', icon: ShieldAlert },
                    { label: 'Console logs', value: 'Active', icon: Cpu },
                    { label: 'Ops center', value: 'Global HQ', icon: MapPin }
                ]
            }
        case 'technician':
            return {
                title: 'Certified Field Engineer',
                desc: 'PV Deployment Specialist',
                badgeStyle: 'badge-tech',
                icon: Wrench,
                stats: [
                    { label: 'Approval Rating', value: '4.95 / 5', icon: Zap },
                    { label: 'Coverage Zone', value: 'Bay Area, CA', icon: MapPin }
                ]
            }
        case 'vendor':
            return {
                title: 'Procurement Supplier',
                desc: 'Certified Solar Manufacturer',
                badgeStyle: 'badge-vendor',
                icon: Store,
                stats: [
                    { label: 'Active listings', value: '12 Items', icon: Cpu },
                    { label: 'Store Verification', value: 'A+ Verified', icon: UserCheck },
                    { label: 'Warehouse depot', value: 'Oakland, CA', icon: MapPin }
                ]
            }
        default: // customer
            return {
                title: 'Solar System Owner',
                desc: 'Green Energy Producer',
                badgeStyle: 'badge-customer',
                icon: Cpu,
                stats: [
                    { label: 'Monitoring Panels', value: '24 Modules', icon: Cpu },
                    { label: 'Net Production', value: '9.8 kW Rating', icon: Zap },
                    { label: 'Active Tickets', value: '0 Open', icon: Clock }
                ]
            }
    }
})
</script>

<template>
    <Head title="Profile Settings" />

    <DashboardLayout :role="userRole" title="Profile Settings">
        <div class="profile-layout-container">
            <!-- Decorative Header Banner (Premium & Sleek) -->
            <div class="profile-banner">
                <div class="banner-overlay"></div>
                <div class="banner-grid-art"></div>
                <div class="banner-content">
                    <div class="user-meta-header">
                        <div class="user-details-group">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="greeting-text">Welcome back,</span>
                                <h2 class="user-profile-name">{{ authUser?.name }}</h2>
                                <span :class="['user-role-label', roleDetails.badgeStyle]">
                                    {{ userRole }}
                                </span>
                            </div>
                            <p class="user-profile-email">{{ authUser?.email }}</p>
                            <p class="user-profile-desc">
                                <component :is="roleDetails.icon" :size="13" class="inline mr-1" />
                                {{ roleDetails.title }} • {{ roleDetails.desc }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Premium Glassmorphic Stats Grid -->
                    <div class="banner-stats-grid">
                        <div v-for="(stat, idx) in roleDetails.stats" :key="idx" class="banner-stat-card">
                            <div class="stat-card-icon">
                                <component :is="stat.icon" :size="15" />
                            </div>
                            <div>
                                <p class="stat-card-label">{{ stat.label }}</p>
                                <p class="stat-card-val">{{ stat.value }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout with Sticky Mini Sidebar Scrollspy -->
            <div class="settings-grid">
                <!-- Mini Sidebar (Scrollspy Navigation - Flawlessly Sticky) -->
                <div class="navigation-sidebar">
                    <div class="mini-sidebar-card">
                        <h3 class="mini-sidebar-title">Profile Navigation</h3>
                        
                        <nav class="mini-sidebar-links">
                            <button 
                                v-for="sec in sections" 
                                :key="sec.id"
                                @click="scrollToSection(sec.id)"
                                :class="[
                                    'mini-tab-btn', 
                                    activeSection === sec.id ? 'mini-tab-btn--active' : '',
                                    sec.danger ? 'mini-tab-btn--danger' : ''
                                ]"
                            >
                                <div class="mini-tab-icon-box">
                                    <component :is="sec.icon" :size="15" />
                                </div>
                                <div class="mini-tab-text">
                                    <span class="mini-tab-name">{{ sec.name }}</span>
                                    <span class="mini-tab-desc">{{ sec.desc }}</span>
                                </div>
                                <ChevronRight :size="14" class="mini-tab-arrow" />
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Combined Scrolling Content Column -->
                <div class="settings-content-wrapper">
                    <!-- Section 1: Profile Information -->
                    <div id="personal-info" class="content-card scroll-margin">
                        <div class="content-card-header">
                            <div class="header-icon-pill bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                <User :size="18" />
                            </div>
                            <div>
                                <h3 class="content-card-title">Profile Details</h3>
                                <p class="content-card-desc">Update your personal account details, contact email, and profile avatar.</p>
                            </div>
                        </div>
                        <div class="content-card-body">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </div>
                    </div>

                    <!-- Section 2: Security Credentials -->
                    <div id="security" class="content-card scroll-margin">
                        <div class="content-card-header">
                            <div class="header-icon-pill bg-amber-500/10 text-amber-600 dark:text-amber-400">
                                <Lock :size="18" />
                            </div>
                            <div>
                                <h3 class="content-card-title">Security Credentials</h3>
                                <p class="content-card-desc">Ensure your dashboard stays safe by keeping your credentials updated.</p>
                            </div>
                        </div>
                        <div class="content-card-body">
                            <UpdatePasswordForm />
                        </div>
                    </div>

                    <!-- Section 3: Preferences -->
                    <div id="preferences" class="content-card scroll-margin">
                        <div class="content-card-header">
                            <div class="header-icon-pill bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                                <Settings :size="18" />
                            </div>
                            <div>
                                <h3 class="content-card-title">Application Preferences</h3>
                                <p class="content-card-desc">Configure your system-wide notifications, theme settings, and telemetry logs.</p>
                            </div>
                        </div>
                        <div class="content-card-body">
                            <UpdatePreferencesForm />
                        </div>
                    </div>

                    <!-- Section 4: Danger Zone -->
                    <div id="danger-zone" class="content-card border-danger-card scroll-margin">
                        <div class="content-card-header">
                            <div class="header-icon-pill bg-red-500/10 text-red-600 dark:text-red-400">
                                <BadgeAlert :size="18" />
                            </div>
                            <div>
                                <h3 class="content-card-title">Danger Zone</h3>
                                <p class="content-card-desc">Permanent actions. Erase your solar link profile and configurations.</p>
                            </div>
                        </div>
                        <div class="content-card-body">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.profile-layout-container {
    display: flex;
    flex-direction: column;
    gap: 32px;
    padding-bottom: 48px;
    max-width: 1200px;
    margin: 0 auto;
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Scroll Offset Margin */
.scroll-margin {
    scroll-margin-top: 24px;
}

/* Banner Design (Premium Carbon Mesh / Glassmorphic) */
.profile-banner {
    position: relative;
    border-radius: 16px;
    background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
    overflow: hidden;
    padding: 36px;
    box-shadow: 0 4px 30px rgba(2, 44, 34, 0.15);
    border: 1px solid rgba(16, 185, 129, 0.12);
}

:root.dark .profile-banner,
.dark .profile-banner {
    background: linear-gradient(135deg, #0b0f19 0%, #151b2d 100%);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.04);
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 80% 20%, rgba(52, 211, 153, 0.15) 0%, transparent 60%);
    pointer-events: none;
}

.banner-grid-art {
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 100%;
    opacity: 0.04;
    background-image: radial-gradient(#ffffff 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}

.banner-content {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 36px;
    flex-wrap: wrap;
    z-index: 10;
}

@media (max-width: 1024px) {
    .banner-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 28px;
    }
}

.user-meta-header {
    display: flex;
    align-items: center;
    gap: 24px;
}

.user-details-group {
    display: flex;
    flex-direction: column;
}

.greeting-text {
    font-size: 1rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.6);
    letter-spacing: -0.01em;
}

.user-profile-name {
    font-size: 1.8rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.03em;
    line-height: 1.1;
}

.user-role-label {
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    border-radius: 9999px;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.badge-admin {
    background: rgba(168, 85, 247, 0.2);
    border: 1px solid rgba(168, 85, 247, 0.4);
    color: #e9d5ff;
}
.badge-tech {
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.badge-vendor {
    background: rgba(79, 70, 229, 0.2);
    border: 1px solid rgba(79, 70, 229, 0.4);
    color: #e0e7ff;
}
.badge-customer {
    background: rgba(245, 158, 11, 0.2);
    border: 1px solid rgba(245, 158, 11, 0.4);
    color: #fef3c7;
}

.user-profile-email {
    font-size: 0.92rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 6px;
    font-weight: 400;
}

.user-profile-desc {
    font-size: 0.82rem;
    color: #34d399;
    margin-top: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

:root.dark .user-profile-desc,
.dark .user-profile-desc {
    color: #34d399;
}

/* Stats grid */
.banner-stats-grid {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

@media (max-width: 640px) {
    .banner-stats-grid {
        width: 100%;
        flex-direction: column;
    }
}

.banner-stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 12px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 160px;
    backdrop-filter: blur(8px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.banner-stat-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

.stat-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: rgba(52, 211, 153, 0.15);
    color: #34d399;
    transition: transform 0.3s;
}

.banner-stat-card:hover .stat-card-icon {
    transform: scale(1.05);
}

.stat-card-label {
    font-size: 0.65rem;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 700;
}

.stat-card-val {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
    margin-top: 2px;
}

/* Two Column Layout spacing */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 32px;
}

@media (max-width: 1024px) {
    .settings-grid {
        display: flex;
        flex-direction: column;
    }
}

/* Mini Sidebar (Scrollspy Navigation - Premium Minimalist Floating) */
.navigation-sidebar {
    grid-column: span 4;
    position: sticky;
    top: 16px; /* Align flawlessly with the top of the <main> scroll view */
    height: fit-content;
    z-index: 10;
}

@media (max-width: 1024px) {
    .navigation-sidebar {
        position: sticky;
        top: -24px;
        height: auto;
        z-index: 10;
        margin-left: -24px;
        margin-right: -24px;
        padding: 12px 24px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    
    @media (min-width: 640px) {
        .navigation-sidebar {
            top: -32px;
            margin-left: -32px;
            margin-right: -32px;
            padding: 16px 32px;
        }
    }
    
    :root.dark .navigation-sidebar,
    .dark .navigation-sidebar {
        background: #0B0F19;
        border-bottom-color: #1e293b;
    }
}

.mini-sidebar-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

:root.dark .mini-sidebar-card,
.dark .mini-sidebar-card {
    background: #0f1322;
    border-color: #1e293b;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.mini-sidebar-title {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
    margin-bottom: 16px;
    padding-left: 8px;
}

:root.dark .mini-sidebar-title,
.dark .mini-sidebar-title {
    color: #475569;
}

.mini-sidebar-links {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Mini Tab Button */
.mini-tab-btn {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid transparent;
    background: transparent;
    text-align: left;
    cursor: pointer;
    width: 100%;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.mini-tab-icon-box {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: #f8fafc;
    color: #64748b;
    transition: all 0.25s ease;
    flex-shrink: 0;
}

:root.dark .mini-tab-icon-box,
.dark .mini-tab-icon-box {
    background: #151b2d;
    color: #475569;
}

.mini-tab-text {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-w: 0;
}

.mini-tab-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    transition: color 0.2s;
}

:root.dark .mini-tab-name,
.dark .mini-tab-name {
    color: #94a3b8;
}

.mini-tab-desc {
    font-size: 0.72rem;
    color: #94a3b8;
    margin-top: 1px;
    transition: color 0.2s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

:root.dark .mini-tab-desc,
.dark .mini-tab-desc {
    color: #475569;
}

.mini-tab-arrow {
    color: #cbd5e1;
    opacity: 0;
    transform: translateX(-4px);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

:root.dark .mini-tab-arrow,
.dark .mini-tab-arrow {
    color: #334155;
}

/* Hover effects */
.mini-tab-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

:root.dark .mini-tab-btn:hover,
.dark .mini-tab-btn:hover {
    background: #151b2d;
    border-color: #1e293b;
}

.mini-tab-btn:hover .mini-tab-name {
    color: #0f172a;
}

:root.dark .mini-tab-btn:hover .mini-tab-name,
.dark .mini-tab-btn:hover .mini-tab-name {
    color: #f1f5f9;
}

.mini-tab-btn:hover .mini-tab-icon-box {
    background: #ffffff;
    color: #10b981;
}

:root.dark .mini-tab-btn:hover .mini-tab-icon-box,
.dark .mini-tab-btn:hover .mini-tab-icon-box {
    background: #0f1322;
    color: #34d399;
}

/* Active states (Premium Highlight Style) */
.mini-tab-btn--active {
    background: #f0fdf4 !important;
    border-color: #bbf7d0 !important;
}

:root.dark .mini-tab-btn--active,
.dark .mini-tab-btn--active {
    background: rgba(16, 185, 129, 0.08) !important;
    border-color: rgba(16, 185, 129, 0.2) !important;
}

.mini-tab-btn--active .mini-tab-icon-box {
    background: #10b981 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
}

:root.dark .mini-tab-btn--active .mini-tab-icon-box,
.dark .mini-tab-btn--active .mini-tab-icon-box {
    background: #10b981 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
}

.mini-tab-btn--active .mini-tab-name {
    color: #10b981 !important;
    font-weight: 700;
}

:root.dark .mini-tab-btn--active .mini-tab-name,
.dark .mini-tab-btn--active .mini-tab-name {
    color: #34d399 !important;
}

.mini-tab-btn--active .mini-tab-arrow {
    opacity: 1;
    transform: translateX(0);
    color: #10b981;
}

:root.dark .mini-tab-btn--active .mini-tab-arrow,
.dark .mini-tab-btn--active .mini-tab-arrow {
    color: #34d399;
}

/* Danger Specific Hover/Active state */
.mini-tab-btn--danger:hover {
    border-color: #fee2e2;
    background: #fef2f2;
}

:root.dark .mini-tab-btn--danger:hover {
    background: rgba(239, 68, 68, 0.05);
    border-color: rgba(239, 68, 68, 0.15);
}

.mini-tab-btn--danger:hover .mini-tab-name {
    color: #ef4444;
}

.mini-tab-btn--danger.mini-tab-btn--active {
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
}

:root.dark .mini-tab-btn--danger.mini-tab-btn--active {
    background: rgba(239, 68, 68, 0.08) !important;
    border-color: rgba(239, 68, 68, 0.2) !important;
}

.mini-tab-btn--danger.mini-tab-btn--active .mini-tab-icon-box {
    background: #ef4444 !important;
    color: white !important;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
}

.mini-tab-btn--danger.mini-tab-btn--active .mini-tab-name {
    color: #ef4444 !important;
}

.mini-tab-btn--danger.mini-tab-btn--active .mini-tab-arrow {
    color: #ef4444;
}

/* Tablet & Mobile horizontal sliding sidebar sections */
@media (max-width: 1024px) {
    .mini-sidebar-card {
        padding: 12px;
        position: static;
        overflow: visible;
    }
    
    .mini-sidebar-title {
        display: none;
    }
    
    .mini-sidebar-links {
        flex-direction: row;
        overflow-x: auto;
        padding-bottom: 4px;
        gap: 12px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    
    .mini-sidebar-links::-webkit-scrollbar {
        display: none;
    }
    
    .mini-tab-btn {
        width: auto;
        flex-shrink: 0;
        padding: 8px 16px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
    }
    
    :root.dark .mini-tab-btn {
        border-color: #1e293b;
        background: #0f1322;
    }
    
    .mini-tab-desc {
        display: none;
    }
    
    .mini-tab-arrow {
        display: none;
    }
}

/* Content Area Cards Stack */
.settings-content-wrapper {
    grid-column: span 8;
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.content-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.01);
    overflow: hidden;
    transition: border-color 0.25s;
}

:root.dark .content-card,
.dark .content-card {
    background: #0f1322;
    border-color: #1e293b;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.content-card:hover {
    border-color: #cbd5e1;
}

:root.dark .content-card:hover,
.dark .content-card:hover {
    border-color: rgba(255, 255, 255, 0.08);
}

.border-danger-card {
    border-color: #fee2e2;
}

:root.dark .border-danger-card,
.dark .border-danger-card {
    border-color: rgba(239, 68, 68, 0.15);
}

.border-danger-card:hover {
    border-color: #fca5a5;
}

:root.dark .border-danger-card:hover,
.dark .border-danger-card:hover {
    border-color: rgba(239, 68, 68, 0.25);
}

.content-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px;
    border-bottom: 1px solid #f1f5f9;
}

:root.dark .content-card-header,
.dark .content-card-header {
    border-bottom-color: #1e293b;
}

.header-icon-pill {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    flex-shrink: 0;
}

.content-card-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.01em;
}

:root.dark .content-card-title,
.dark .content-card-title {
    color: #f1f5f9;
}

.content-card-desc {
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 2px;
}

:root.dark .content-card-desc,
.dark .content-card-desc {
    color: #475569;
}

.content-card-body {
    padding: 24px;
}
</style>
