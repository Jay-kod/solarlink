<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Users, Search, UserCheck, ShieldAlert, Sparkles, Eye, Trash2, ShieldCheck, HelpCircle } from 'lucide-vue-next'

interface UserItem {
    id: number;
    name: string;
    email: string;
    role: 'customer' | 'technician' | 'vendor' | 'admin';
    status: 'active' | 'blocked';
    registered: string;
    avatar: string;
}

const systemUsers = ref<UserItem[]>([
    { id: 201, name: 'Alice Johnson', email: 'alice.johnson@gmail.com', role: 'customer', status: 'active', registered: 'May 20, 2026', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150' },
    { id: 202, name: 'Timothy Vance', email: 'tim.vance@gmail.com', role: 'customer', status: 'active', registered: 'May 18, 2026', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150' },
    { id: 203, name: 'Clara Oswald', email: 'clara.oswald@gmail.com', role: 'customer', status: 'blocked', registered: 'May 15, 2026', avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=150' },
    { id: 204, name: 'Marcus Vance', email: 'marcus.vance@solarlink.io', role: 'technician', status: 'active', registered: 'May 10, 2026', avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150' },
    { id: 205, name: 'Sarah Jenkins', email: 'sarah.jenkins@gmail.com', role: 'technician', status: 'active', registered: 'May 12, 2026', avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150' },
    { id: 206, name: 'EcoGrid Logistics', email: 'logistics@ecogrid-direct.com', role: 'vendor', status: 'active', registered: 'May 05, 2026', avatar: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&q=80&w=150' },
    { id: 207, name: 'Alex Thompson', email: 'alex.thompson@solarlink.io', role: 'admin', status: 'active', registered: 'Jan 01, 2026', avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=150' },
    { id: 208, name: 'Grace Hopper', email: 'grace.hopper@gmail.com', role: 'customer', status: 'active', registered: 'May 22, 2026', avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=150' }
])

const searchQuery = ref('')
const selectedRoleFilter = ref<string>('all')

const toggleUserStatus = (id: number) => {
    const user = systemUsers.value.find(u => u.id === id)
    if (user) {
        user.status = user.status === 'active' ? 'blocked' : 'active'
    }
}

const handleDeleteUser = (id: number) => {
    if (confirm('Are you sure you want to delete this user registration?')) {
        systemUsers.value = systemUsers.value.filter(u => u.id !== id)
    }
}

const customerCount = computed(() => systemUsers.value.filter(u => u.role === 'customer').length)
const technicianCount = computed(() => systemUsers.value.filter(u => u.role === 'technician').length)
const vendorCount = computed(() => systemUsers.value.filter(u => u.role === 'vendor').length)
const adminCount = computed(() => systemUsers.value.filter(u => u.role === 'admin').length)

const filteredUsers = computed(() => {
    return systemUsers.value.filter(user => {
        const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                             user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesRole = selectedRoleFilter.value === 'all' || user.role === selectedRoleFilter.value
        return matchesSearch && matchesRole
    })
})

const getRoleBadgeClass = (role: string) => {
    switch (role) {
        case 'admin': return 'bg-solar-danger/15 text-solar-danger'
        case 'vendor': return 'bg-purple-100 text-purple-800 dark:bg-purple-950/40 dark:text-purple-300'
        case 'technician': return 'bg-solar-primary-light text-solar-primary dark:bg-solar-primary-dark dark:text-solar-primary-accent'
        case 'customer': return 'bg-solar-success/15 text-solar-success'
        default: return 'bg-slate-100 text-slate-500'
    }
}
</script>

<template>
    <Head title="SolarLink — User Control" />

    <DashboardLayout role="admin" title="User Management">
        <div class="flex flex-col gap-8 text-left">
            
            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Control Matrix</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Registered Accounts Control</h2>
                <p class="text-xs text-slate-450 mt-0.5">Audit user access, block or unblock profiles, and assign administrative roles.</p>
            </div>

            <!-- Summary Chips -->
            <div class="flex flex-wrap gap-4 text-xs font-bold">
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Total: <span class="text-solar-primary font-extrabold ml-1">{{ systemUsers.length }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Customers: <span class="text-solar-success font-extrabold ml-1">{{ customerCount }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Technicians: <span class="text-solar-primary font-extrabold ml-1">{{ technicianCount }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Vendors: <span class="text-purple-600 font-extrabold ml-1">{{ vendorCount }}</span>
                </div>
                <div class="px-4 py-2 bg-slate-50 dark:bg-solar-primary-dark/20 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    Admins: <span class="text-solar-danger font-extrabold ml-1">{{ adminCount }}</span>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="relative flex items-center w-full sm:max-w-sm">
                    <Search class="absolute left-3.5 h-4 w-4 text-slate-400 pointer-events-none" />
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Search users by name or email..."
                        class="w-full h-11 pl-10 pr-4 rounded-xl bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-850 dark:text-white shadow-sm"
                    />
                </div>
                
                <select 
                    v-model="selectedRoleFilter"
                    class="h-11 px-4 rounded-xl bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white shadow-sm"
                >
                    <option value="all">All Roles</option>
                    <option value="customer">Customer</option>
                    <option value="technician">Technician</option>
                    <option value="vendor">Vendor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <!-- Table Matrix -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-850 dark:text-white font-bold">
                            <th class="p-4">User details</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Role</th>
                            <th class="p-4">Registered Date</th>
                            <th class="p-4">Access Status</th>
                            <th class="p-4 text-right">Moderator Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-if="filteredUsers.length === 0">
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                No matching registrations found.
                            </td>
                        </tr>
                        <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-slate-50/50 dark:hover:bg-solar-primary-dark/10 transition-all">
                            <td class="p-4 flex items-center gap-3">
                                <img :src="user.avatar" :alt="user.name" class="h-9 w-9 rounded-full object-cover border border-solar-primary/10" />
                                <span class="font-bold text-slate-850 dark:text-white">{{ user.name }}</span>
                            </td>
                            <td class="p-4 font-semibold">{{ user.email }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="getRoleBadgeClass(user.role)"
                                >
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="p-4 font-semibold text-slate-450">{{ user.registered }}</td>
                            <td class="p-4">
                                <span 
                                    class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                    :class="user.status === 'active' ? 'bg-solar-success/15 text-solar-success' : 'bg-solar-danger/15 text-solar-danger'"
                                >
                                    {{ user.status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button class="p-2 rounded-lg border border-solar-primary/10 text-solar-primary hover:bg-solar-primary/10" title="View Profile Details">
                                        <Eye class="h-3.5 w-3.5" />
                                    </button>
                                    <button 
                                        @click="toggleUserStatus(user.id)"
                                        class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all"
                                        :class="user.status === 'active' ? 'border border-solar-danger/25 text-solar-danger hover:bg-solar-danger/5' : 'bg-solar-success text-white hover:bg-emerald-650'"
                                    >
                                        {{ user.status === 'active' ? 'Block' : 'Unblock' }}
                                    </button>
                                    <button 
                                        @click="handleDeleteUser(user.id)"
                                        class="p-2 rounded-lg border border-solar-danger/10 text-solar-danger hover:bg-solar-danger/10"
                                        title="Delete Registration"
                                    >
                                        <Trash2 class="h-3.5 w-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </DashboardLayout>
</template>
