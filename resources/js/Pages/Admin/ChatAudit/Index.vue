<script setup lang="ts">
import { ref, computed, nextTick, watch, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'
import { 
    MessageSquare, Eye, Trash2, Search, Info, 
    CheckCircle2, Lock, ShieldAlert, Sparkles, User, FileText, Download, Calendar,
    Send, Plus, X, Image
} from 'lucide-vue-next'

interface Participant {
    name: string;
    role: string;
    avatar: string;
}

interface Conversation {
    id: number;
    title: string;
    status: 'active' | 'concluded';
    participants: Participant[];
    lastMessage: string;
    time: string;
}

interface Message {
    id: number;
    sender: 'me' | 'them';
    sender_name: string;
    sender_role: string;
    sender_avatar: string;
    text: string;
    time: string;
    type?: 'text' | 'image' | 'file';
    fileName?: string;
    fileSize?: string;
    imageUrl?: string;
}

const props = defineProps<{
    conversations?: Conversation[]
}>()

const conversations = ref<Conversation[]>(props.conversations || [])
const activeConversation = ref<Conversation | null>(conversations.value[0] || null)
const messages = ref<Message[]>([])
const searchQuery = ref('')
const messageContainer = ref<HTMLElement | null>(null)

// Messaging State
const newMessage = ref('')
const isSending = ref(false)

// New Chat State
const showNewChatModal = ref(false)
const userSearchQuery = ref('')
const searchResults = ref<any[]>([])
const selectedUsers = ref<any[]>([])
const isSearching = ref(false)

const openNewChat = () => {
    showNewChatModal.value = true
    userSearchQuery.value = ''
    searchResults.value = []
    selectedUsers.value = []
}

const closeNewChat = () => {
    showNewChatModal.value = false
}

let searchTimeout: any = null
const searchForUsers = () => {
    if (searchTimeout) clearTimeout(searchTimeout)
    if (!userSearchQuery.value) {
        searchResults.value = []
        return
    }
    
    isSearching.value = true
    searchTimeout = setTimeout(async () => {
        try {
            const res = await axios.get('/api/users/search', { params: { q: userSearchQuery.value } })
            searchResults.value = res.data
        } catch (e) {
            console.error(e)
        } finally {
            isSearching.value = false
        }
    }, 500)
}

const toggleUserSelection = (user: any) => {
    const idx = selectedUsers.value.findIndex(u => u.id === user.id)
    if (idx > -1) {
        selectedUsers.value.splice(idx, 1)
    } else {
        selectedUsers.value.push(user)
    }
}

const startNewConversation = async () => {
    if (selectedUsers.value.length === 0) return
    try {
        const userIds = selectedUsers.value.map(u => u.id)
        const res = await axios.post('/api/conversations', { user_ids: userIds })
        if (res.data.status === 'success') {
            conversations.value.unshift(res.data.conversation)
            activeConversation.value = res.data.conversation
            fetchMessages()
            closeNewChat()
        }
    } catch (e) {
        console.error('Failed to create chat:', e)
    }
}

// Sending messages
const sendMessage = async () => {
    if (!newMessage.value.trim() || !activeConversation.value) return

    const text = newMessage.value.trim()
    newMessage.value = ''
    isSending.value = true

    try {
        await axios.post(`/api/conversations/${activeConversation.value.id}/messages`, {
            text: text,
            type: 'text'
        })
        await fetchMessages()
    } catch (err) {
        console.error('Failed to send message:', err)
    } finally {
        isSending.value = false
    }
}

const fetchMessages = async () => {
    if (!activeConversation.value) return
    try {
        const response = await axios.get(`/api/conversations/${activeConversation.value.id}/messages`)
        messages.value = response.data
        scrollToBottom()
    } catch (err) {
        console.error('Failed to fetch messages for audit:', err)
    }
}

// Filter conversations by title, last message, or participant names
const filteredConversations = computed(() => {
    if (!searchQuery.value.trim()) return conversations.value
    const query = searchQuery.value.toLowerCase()
    return conversations.value.filter(c => 
        c.title.toLowerCase().includes(query) || 
        c.lastMessage.toLowerCase().includes(query) ||
        c.participants.some(p => p.name.toLowerCase().includes(query))
    )
})

const scrollToBottom = async () => {
    await nextTick()
    if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight
    }
}

import { useAlert } from '@/composables/useAlert'
const { confirmAlert } = useAlert()

// Conclude group conversation
const concludeGroup = async () => {
    if (!activeConversation.value) return
    const confirmed = await confirmAlert(
        'Are you sure you want to mark this conversation as concluded? This will lock it for the participants.',
        'Conclude Chat',
        { type: 'warning', confirmText: 'Conclude Chat' }
    )
    if (confirmed) {
        router.post(`/api/conversations/${activeConversation.value.id}/conclude`, {}, {
            onSuccess: () => {
                if (activeConversation.value) {
                    activeConversation.value.status = 'concluded'
                }
            }
        })
    }
}

// Delete group conversation
const deleteGroup = async () => {
    if (!activeConversation.value) return
    const confirmed = await confirmAlert(
        'Are you sure you want to permanently delete this group chat? This will remove all database logs and cannot be undone.',
        'Delete Group Chat',
        { type: 'danger', confirmText: 'Delete Chat' }
    )
    if (confirmed) {
        router.delete(`/api/conversations/${activeConversation.value.id}`, {
            onSuccess: () => {
                conversations.value = conversations.value.filter(c => c.id !== activeConversation.value?.id)
                activeConversation.value = conversations.value[0] || null
                fetchMessages()
            }
        })
    }
}

onMounted(() => {
    if (activeConversation.value) {
        fetchMessages()
    }
})

watch(activeConversation, () => {
    fetchMessages()
})
</script>

<template>
    <Head title="SolarLink — Silent Audit Dashboard" />

    <DashboardLayout role="admin" title="Operations Control">
        <div class="flex flex-col gap-6 text-left h-[calc(100vh-140px)]">
            
            <div class="flex flex-col gap-1 shrink-0">
                <div class="inline-flex items-center gap-1.5 self-start px-2.5 py-1 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 font-extrabold text-[9px] uppercase tracking-widest border border-purple-500/20 shadow-sm animate-pulse-slow">
                    <ShieldAlert class="h-3 w-3" />
                    <span>Shadow Auditing Node</span>
                </div>
                <h2 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-500 dark:from-purple-400 dark:to-indigo-400 tracking-tight mt-1">Tripartite Group Chat Audit</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">Silently monitor discussions between customers, technicians, and vendors. Terminate or lock conversation channels when business is concluded.</p>
            </div>

            <!-- Main Split Workspace -->
            <div class="glass-card flex-grow overflow-hidden flex border border-purple-500/20 dark:border-purple-500/40 bg-white/60 dark:bg-[#0B0F19]/70 backdrop-blur-2xl rounded-3xl relative min-h-[500px] shadow-[0_0_40px_-15px_rgba(168,85,247,0.3)]">
                
                <!-- Left Sidebar: Conversations Audit List -->
                <div class="w-80 border-r border-slate-100 dark:border-white/5 flex flex-col shrink-0 bg-white/60 dark:bg-solar-primary-dark/5">
                    <!-- Search and New Chat -->
                    <div class="p-4 border-b border-slate-100 dark:border-white/5 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-widest">Audited Groups</h3>
                            <button @click="openNewChat" class="h-8 px-2.5 bg-purple-500 text-white rounded-lg text-xs font-bold shadow hover:bg-purple-600 transition flex items-center gap-1.5">
                                <Plus class="h-3.5 w-3.5" />
                                <span>New</span>
                            </button>
                        </div>
                        <div class="relative flex items-center">
                            <Search class="absolute left-3.5 h-4 w-4 text-purple-500 pointer-events-none" />
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Filter audited groups..."
                                class="w-full h-10 pl-10 pr-4 rounded-xl bg-white/50 dark:bg-[#151b2b]/80 border border-purple-100 dark:border-purple-500/20 text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all duration-300 shadow-sm"
                            />
                        </div>
                    </div>

                    <!-- Groups List -->
                    <div class="flex-grow overflow-y-auto divide-y divide-slate-100 dark:divide-white/3">
                        <button 
                            v-for="conv in filteredConversations" 
                            :key="conv.id"
                            @click="activeConversation = conv"
                            class="w-full p-4 flex items-start gap-3.5 text-left transition-all duration-300 relative group"
                            :class="activeConversation?.id === conv.id 
                                ? 'bg-gradient-to-r from-purple-500/15 to-transparent border-l-4 border-l-purple-500 shadow-inner shadow-purple-500/5' 
                                : 'hover:bg-slate-50 dark:hover:bg-white/5 border-l-4 border-l-transparent'"
                        >
                            <!-- Overlapping Group Avatars -->
                            <div class="relative shrink-0 w-10 h-10 flex items-center justify-center">
                                <div v-if="conv.participants.length >= 2" class="w-full h-full relative">
                                    <img :src="conv.participants[0].avatar" class="absolute top-0 left-0 w-6 h-6 rounded-full object-cover border border-white dark:border-[#0f1322] shadow" />
                                    <img :src="conv.participants[1].avatar" class="absolute bottom-0 right-0 w-6 h-6 rounded-full object-cover border border-white dark:border-[#0f1322] shadow" />
                                </div>
                                <div v-else-if="conv.participants.length === 1">
                                    <img :src="conv.participants[0].avatar" class="w-8 h-8 rounded-full object-cover" />
                                </div>
                                <span 
                                    v-if="conv.status === 'concluded'"
                                    class="absolute -top-1 -right-1 h-3.5 w-3.5 rounded-full bg-slate-400 text-white flex items-center justify-center text-[7px]"
                                >
                                    <Lock class="h-2 w-2" />
                                </span>
                            </div>

                            <div class="flex-grow min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-extrabold text-xs text-slate-800 dark:text-white truncate group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ conv.title }}</h4>
                                    <span class="text-[9px] text-slate-400 shrink-0 ml-1 font-bold">{{ conv.time }}</span>
                                </div>
                                <p class="text-[8px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest mt-0.5">Audited Group</p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-1 leading-relaxed font-medium">{{ conv.lastMessage }}</p>
                            </div>
                        </button>

                        <div v-if="filteredConversations.length === 0" class="p-6 text-center text-slate-400 dark:text-slate-500">
                            <Info class="h-8 w-8 mx-auto text-slate-350 dark:text-slate-650 mb-2" />
                            <p class="text-xs">No active chat groups audited.</p>
                        </div>
                    </div>
                </div>

                <!-- Central Shadow Monitor -->
                <div v-if="activeConversation" class="flex-grow flex flex-col min-w-0 bg-slate-50/20 dark:bg-solar-primary-dark/2">
                    
                    <!-- Shadow auditing banner warning -->
                    <div class="bg-amber-500/10 text-amber-600 dark:text-amber-400 border-b border-amber-500/20 px-5 py-2.5 flex items-center gap-2.5 text-xs font-bold shrink-0">
                        <Eye class="h-4.5 w-4.5 animate-pulse" />
                        <span>SHADOW OBSERVATION INTERFACE: Participants Clara, Marcus, and Sarah cannot see you on their panels.</span>
                    </div>

                    <!-- Header -->
                    <div class="h-16 border-b border-slate-100 dark:border-white/5 flex items-center justify-between px-5 bg-white/70 dark:bg-solar-bg-dark/40 shrink-0">
                        <div>
                            <h4 class="font-extrabold text-xs text-slate-800 dark:text-white leading-tight">Auditing: {{ activeConversation.title }}</h4>
                            <span 
                                class="px-2 py-0.5 rounded-full text-[8px] font-extrabold uppercase tracking-wider mt-0.5 inline-block"
                                :class="activeConversation.status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-400/10 text-slate-500'"
                            >
                                {{ activeConversation.status }}
                            </span>
                        </div>

                        <!-- Lifecycle Controls -->
                        <div class="flex items-center gap-2">
                            <!-- Conclude -->
                            <button 
                                v-if="activeConversation.status === 'active'"
                                @click="concludeGroup"
                                class="px-3 h-8 rounded-lg bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 hover:text-white text-[10px] font-bold transition-all flex items-center gap-1"
                                title="Force mark as concluded"
                            >
                                <CheckCircle2 class="h-3.5 w-3.5" />
                                <span>Force Conclude</span>
                            </button>

                            <!-- Delete -->
                            <button 
                                @click="deleteGroup"
                                class="px-3 h-8 rounded-lg bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-[10px] font-bold transition-all flex items-center gap-1"
                                title="Hard purge conversation"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                                <span>Hard Purge Group</span>
                            </button>
                        </div>
                    </div>

                    <!-- Message Feed -->
                    <div 
                        ref="messageContainer"
                        class="flex-grow overflow-y-auto p-6 flex flex-col gap-6 bg-slate-50/30 dark:bg-[#0B0F19]/40 relative"
                    >
                        <!-- Ambient background glow -->
                        <div class="absolute inset-0 pointer-events-none opacity-20 dark:opacity-40" style="background-image: radial-gradient(circle at center, rgba(168, 85, 247, 0.4) 0%, transparent 60%);"></div>
                        
                        <div 
                            v-for="msg in messages" 
                            :key="msg.id"
                            class="flex gap-2.5 max-w-[80%]"
                            :class="msg.sender === 'me' ? 'self-end flex-row-reverse' : 'self-start flex-row'"
                        >
                            <!-- Avatar -->
                            <img v-if="msg.sender === 'them'" :src="msg.sender_avatar" :alt="msg.sender_name" class="h-7 w-7 rounded-full object-cover shrink-0 mt-0.5 border border-slate-100 dark:border-white/10" />
                            
                            <div class="flex flex-col" :class="msg.sender === 'me' ? 'items-end' : 'items-start'">
                                <!-- Metadata -->
                                <span v-if="msg.sender === 'them'" class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-0.5">
                                    {{ msg.sender_name }} • {{ msg.sender_role }}
                                </span>

                                <!-- Text Bubble -->
                                <div 
                                    v-if="!msg.type || msg.type === 'text'"
                                    class="px-4 py-3 rounded-2xl text-xs leading-relaxed shadow-md border font-medium backdrop-blur-md relative z-10"
                                    :class="msg.sender === 'me' 
                                        ? 'bg-purple-600 text-white border-purple-500 rounded-br-sm'
                                        : (msg.sender_role === 'customer' 
                                            ? 'bg-blue-50/90 dark:bg-blue-600/20 border-blue-200/60 dark:border-blue-500/30 text-blue-900 dark:text-blue-100 rounded-bl-sm' 
                                            : msg.sender_role === 'technician' 
                                            ? 'bg-emerald-50/90 dark:bg-emerald-600/20 border-emerald-200/60 dark:border-emerald-500/30 text-emerald-900 dark:text-emerald-100 rounded-bl-sm' 
                                            : 'bg-indigo-50/90 dark:bg-indigo-600/20 border-indigo-200/60 dark:border-indigo-500/30 text-indigo-900 dark:text-indigo-100 rounded-bl-sm')"
                                >
                                    <p>{{ msg.text }}</p>
                                </div>

                                <!-- Image Bubble -->
                                <div 
                                    v-else-if="msg.type === 'image'"
                                    class="rounded-2xl overflow-hidden border border-slate-100 dark:border-white/5 shadow-md flex flex-col max-w-[280px]"
                                >
                                    <img :src="msg.imageUrl" alt="Attachment" class="max-h-48 object-cover w-full" />
                                    <div class="px-3 py-2 bg-white dark:bg-solar-primary-dark/40 border-t border-slate-100 dark:border-white/5 text-[10px] w-full text-slate-650 dark:text-slate-350 font-bold">
                                        {{ msg.text }}
                                    </div>
                                </div>

                                <!-- File Bubble -->
                                <div 
                                    v-else-if="msg.type === 'file'"
                                    class="px-4 py-3.5 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 flex items-center gap-3.5 bg-white dark:bg-solar-primary-dark/20 text-slate-700 dark:text-slate-200"
                                    :class="msg.sender === 'me' ? 'rounded-br-sm' : 'rounded-bl-sm'"
                                >
                                    <div class="h-10 w-10 rounded-xl bg-purple-500/20 text-purple-500 flex items-center justify-center shrink-0">
                                        <FileText class="h-5 w-5" />
                                    </div>
                                    <div class="min-w-0 flex-grow text-left">
                                        <h5 class="text-xs font-bold truncate leading-snug">{{ msg.fileName }}</h5>
                                        <p class="text-[9px] text-slate-400 font-medium mt-0.5 uppercase tracking-wide">{{ msg.fileSize }}</p>
                                    </div>
                                </div>

                                <!-- Time -->
                                <span class="text-[8px] font-bold text-slate-450 uppercase tracking-widest mt-1 px-1">
                                    {{ msg.time }}
                                </span>
                            </div>
                        </div>

                        <div v-if="messages.length === 0" class="text-center py-10 text-slate-450 dark:text-slate-555">
                            No logs loaded for this channel yet.
                        </div>
                    </div>

                    <!-- Admin Chat Input (Settle Dispute) -->
                    <div class="border-t border-purple-500/10 dark:border-white/5 p-4 bg-white/70 dark:bg-solar-bg-dark/40 flex flex-col gap-3 shrink-0">
                        <div v-if="activeConversation.status === 'concluded'" class="flex items-center justify-center gap-2 py-2 text-slate-500 font-bold text-xs uppercase tracking-wider bg-slate-100 dark:bg-white/5 rounded-xl">
                            <Lock class="h-4 w-4" />
                            <span>This conversation group is concluded and locked.</span>
                        </div>
                        <div v-else>
                            <form @submit.prevent="sendMessage()" class="flex items-center gap-3">
                                <div class="flex-grow relative">
                                    <input 
                                        v-model="newMessage"
                                        type="text" 
                                        placeholder="Type an official admin message to settle dispute..."
                                        class="w-full min-h-[44px] pl-4 pr-12 rounded-xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-solar-primary-dark/30 text-xs text-slate-800 dark:text-white focus:outline-none focus:border-purple-500 transition-all shadow-inner"
                                        :disabled="isSending"
                                    />
                                </div>
                                <button 
                                    type="submit" 
                                    :disabled="!newMessage.trim() || isSending"
                                    class="h-11 px-6 rounded-xl bg-purple-600 text-white font-extrabold text-xs shadow-md hover:bg-purple-700 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                                >
                                    <Send v-if="!isSending" class="h-4 w-4" />
                                    <span v-else class="h-4 w-4 border-2 border-white/50 border-t-white rounded-full animate-spin"></span>
                                    <span class="hidden sm:inline">{{ isSending ? 'Sending...' : 'Send Message' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Empty Chat placeholder -->
                <div v-else class="flex-grow flex flex-col items-center justify-center text-center p-10 bg-slate-50/50 dark:bg-solar-bg-dark/10 text-slate-450 dark:text-slate-500">
                    <MessageSquare class="h-16 w-16 text-slate-350 dark:text-slate-750 mb-3.5" />
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">Audit Queue Idle</h3>
                    <p class="text-xs max-w-sm mt-1">Select any tripartite chat in the left list to start real-time monitoring and lifecycle operations.</p>
                </div>

                <!-- Right Sidebar: Audit details -->
                <div 
                    v-if="activeConversation"
                    class="w-72 border-l border-slate-100 dark:border-white/5 flex flex-col shrink-0 bg-white/50 dark:bg-solar-bg-dark/30 hidden lg:flex animate-fade-in-right overflow-y-auto"
                >
                    <div class="p-5 flex flex-col gap-6">
                        <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-left">Peer Participants</h4>
                        
                        <!-- List of participants -->
                        <div class="flex flex-col gap-3">
                            <div 
                                v-for="part in activeConversation.participants" 
                                :key="part.name"
                                class="flex items-center gap-3 bg-white/60 dark:bg-[#151b2b]/80 border border-slate-200/50 dark:border-white/10 p-3 rounded-2xl text-left hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 backdrop-blur-md"
                            >
                                <img :src="part.avatar" :alt="part.name" class="h-10 w-10 rounded-xl object-cover ring-2 ring-white dark:ring-[#0B0F19] shadow-sm bg-[#0B0F19]" />
                                <div class="min-w-0 flex-grow">
                                    <h5 class="font-extrabold text-xs text-slate-850 dark:text-white truncate leading-snug">{{ part.name }}</h5>
                                    <p class="text-[8px] font-black uppercase tracking-widest mt-0.5"
                                        :class="part.role === 'customer' ? 'text-blue-500 dark:text-blue-400' : part.role === 'technician' ? 'text-emerald-500 dark:text-emerald-400' : 'text-indigo-500 dark:text-indigo-400'"
                                    >{{ part.role }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-white/5" />

                        <!-- Audit Ledger Details -->
                        <div class="text-left flex flex-col gap-3">
                            <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">System Audit Log</h4>
                            
                            <div class="flex flex-col gap-2.5 text-xs text-slate-650 dark:text-slate-350">
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-slate-400" />
                                    <span>Created: {{ activeConversation.time }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <ShieldAlert class="h-4 w-4 text-purple-555" />
                                    <span>Monitoring Active: Yes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- New Chat Modal for Admin -->
        <div v-if="showNewChatModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-solar-bg-dark rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-purple-500/20 flex flex-col max-h-[85vh]">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-white/5 flex justify-between items-center bg-purple-50 dark:bg-purple-900/10">
                    <h3 class="font-extrabold text-sm text-purple-900 dark:text-purple-300">Create Dispute Resolution Group</h3>
                    <button @click="closeNewChat" class="text-slate-400 hover:text-red-500 transition-colors">
                        <X class="h-5 w-5" />
                    </button>
                </div>
                
                <div class="p-5 flex flex-col gap-4 overflow-y-auto">
                    <!-- Search input -->
                    <div class="relative flex items-center">
                        <Search class="absolute left-3.5 h-4 w-4 text-slate-400 pointer-events-none" />
                        <input 
                            v-model="userSearchQuery"
                            @input="searchForUsers"
                            type="text" 
                            placeholder="Search by name, email, or role..."
                            class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs focus:outline-none focus:border-purple-500 transition-all"
                        />
                        <div v-if="isSearching" class="absolute right-3.5 flex items-center justify-center">
                            <span class="h-4 w-4 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                    </div>

                    <!-- Selected Participants Chips -->
                    <div v-if="selectedUsers.length > 0" class="flex flex-wrap gap-2">
                        <div v-for="u in selectedUsers" :key="u.id" class="px-2.5 py-1 bg-purple-500/10 text-purple-600 dark:text-purple-400 text-[10px] font-bold rounded-lg flex items-center gap-1.5 border border-purple-500/20">
                            <img :src="u.avatar" class="w-4 h-4 rounded-full object-cover" />
                            <span>{{ u.name }}</span>
                            <button @click="toggleUserSelection(u)" class="hover:text-red-500"><X class="h-3 w-3" /></button>
                        </div>
                    </div>

                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Search Results</div>
                    
                    <div v-if="searchResults.length === 0 && userSearchQuery" class="text-xs text-slate-500 text-center py-4">
                        No users found.
                    </div>
                    
                    <div v-else class="flex flex-col gap-2">
                        <button 
                            v-for="user in searchResults" 
                            :key="user.id"
                            @click="toggleUserSelection(user)"
                            class="flex items-center gap-3 p-3 rounded-xl border text-left transition-all"
                            :class="selectedUsers.find(su => su.id === user.id) ? 'bg-purple-500/5 border-purple-500' : 'bg-white dark:bg-solar-primary-dark/10 border-slate-100 dark:border-white/5 hover:border-slate-300'"
                        >
                            <img :src="user.avatar" class="w-10 h-10 rounded-full object-cover" />
                            <div class="flex-grow">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-white">{{ user.name }}</h4>
                                <p class="text-[10px] text-slate-500">{{ user.email }} • <span class="uppercase tracking-wider font-bold text-purple-500">{{ user.role }}</span></p>
                            </div>
                            <div v-if="selectedUsers.find(su => su.id === user.id)" class="text-purple-500">
                                <CheckCircle2 class="h-5 w-5" />
                            </div>
                        </button>
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-slate-100 dark:border-white/5 flex justify-end gap-3 bg-slate-50/50 dark:bg-solar-primary-dark/5">
                    <button @click="closeNewChat" class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:bg-slate-200 dark:hover:bg-white/10 transition">Cancel</button>
                    <button 
                        @click="startNewConversation" 
                        :disabled="selectedUsers.length === 0"
                        class="px-5 py-2 rounded-lg text-xs font-bold bg-purple-600 text-white hover:bg-purple-700 transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        Create Group <span v-if="selectedUsers.length > 0">({{ selectedUsers.length }})</span>
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
