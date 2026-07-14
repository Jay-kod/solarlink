<script setup lang="ts">
import { ref, computed, nextTick, watch, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'
import { 
    MessageSquare, Eye, Trash2, Search, Info, 
    CheckCircle2, Lock, ShieldAlert, Sparkles, User, FileText, Download, Calendar
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

// Conclude group conversation
const concludeGroup = () => {
    if (!activeConversation.value) return
    if (confirm('CONCLUDE CHAT: Are you sure you want to mark this conversation as concluded? This will lock it for the participants.')) {
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
const deleteGroup = () => {
    if (!activeConversation.value) return
    if (confirm('DELETE GROUP: Are you sure you want to permanently delete this group chat? This will remove all database logs and cannot be undone.')) {
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
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-purple-500/10 text-purple-600 dark:text-purple-400 font-bold text-[9px] uppercase tracking-wider">
                    <ShieldAlert class="h-3 w-3" />
                    <span>Shadow Auditing Node</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Tripartite Group Chat Audit</h2>
                <p class="text-xs text-slate-450 mt-0.5">Silently monitor discussions between customers, technicians, and vendors. Terminate or lock conversation channels when business is concluded.</p>
            </div>

            <!-- Main Split Workspace -->
            <div class="glass-card flex-grow overflow-hidden flex border border-purple-500/10 dark:border-white/5 bg-white/50 dark:bg-solar-bg-dark/20 backdrop-blur-md rounded-2xl relative min-h-[500px]">
                
                <!-- Left Sidebar: Conversations Audit List -->
                <div class="w-80 border-r border-slate-100 dark:border-white/5 flex flex-col shrink-0 bg-white/60 dark:bg-solar-primary-dark/5">
                    <!-- Search -->
                    <div class="p-4 border-b border-slate-100 dark:border-white/5">
                        <div class="relative flex items-center">
                            <Search class="absolute left-3.5 h-4 w-4 text-slate-450 pointer-events-none" />
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Filter audited groups..."
                                class="w-full h-10 pl-10 pr-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs focus:outline-none focus:border-purple-500 transition-all duration-300"
                            />
                        </div>
                    </div>

                    <!-- Groups List -->
                    <div class="flex-grow overflow-y-auto divide-y divide-slate-100 dark:divide-white/3">
                        <button 
                            v-for="conv in filteredConversations" 
                            :key="conv.id"
                            @click="activeConversation = conv"
                            class="w-full p-4 flex items-start gap-3.5 text-left transition-all relative"
                            :class="activeConversation?.id === conv.id 
                                ? 'bg-purple-500/10 dark:bg-purple-500/10 border-l-4 border-l-purple-500' 
                                : 'hover:bg-slate-50 dark:hover:bg-white/3'"
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
                                    <h4 class="font-extrabold text-xs text-slate-800 dark:text-white truncate">{{ conv.title }}</h4>
                                    <span class="text-[9px] text-slate-400 shrink-0 ml-1">{{ conv.time }}</span>
                                </div>
                                <p class="text-[8px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest mt-0.5">Audited Group</p>
                                <p class="text-[10px] text-slate-450 dark:text-slate-400 truncate mt-1 leading-snug font-medium">{{ conv.lastMessage }}</p>
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
                        class="flex-grow overflow-y-auto p-5 flex flex-col gap-4 bg-slate-50/50 dark:bg-solar-bg-dark/10"
                    >
                        <div 
                            v-for="msg in messages" 
                            :key="msg.id"
                            class="flex gap-2.5 max-w-[80%] self-start"
                        >
                            <!-- Avatar -->
                            <img :src="msg.sender_avatar" :alt="msg.sender_name" class="h-7 w-7 rounded-full object-cover shrink-0 mt-0.5 border border-slate-100 dark:border-white/10" />
                            
                            <div class="flex flex-col text-left">
                                <!-- Metadata -->
                                <span class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-0.5">
                                    {{ msg.sender_name }} • {{ msg.sender_role }}
                                </span>

                                <!-- Text Bubble -->
                                <div 
                                    v-if="!msg.type || msg.type === 'text'"
                                    class="px-4 py-3 rounded-2xl text-xs leading-relaxed shadow-sm bg-white dark:bg-solar-primary-dark/20 border border-slate-100 dark:border-white/5 text-slate-700 dark:text-slate-200 rounded-bl-sm font-medium"
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
                                    class="px-4 py-3.5 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 flex items-center gap-3.5 bg-white dark:bg-solar-primary-dark/20 text-slate-700 dark:text-slate-200 rounded-bl-sm"
                                >
                                    <div class="h-10 w-10 rounded-xl bg-solar-primary/20 text-solar-primary flex items-center justify-center shrink-0">
                                        <FileText class="h-5 w-5" />
                                    </div>
                                    <div class="min-w-0 flex-grow text-left">
                                        <h5 class="text-xs font-bold truncate leading-snug">{{ msg.fileName }}</h5>
                                        <p class="text-[9px] text-slate-400 font-medium mt-0.5 uppercase tracking-wide">{{ msg.fileSize }}</p>
                                    </div>
                                </div>

                                <!-- Time -->
                                <span class="text-[8px] font-bold text-slate-450 uppercase tracking-widest mt-1">
                                    {{ msg.time }}
                                </span>
                            </div>
                        </div>

                        <div v-if="messages.length === 0" class="text-center py-10 text-slate-450 dark:text-slate-555">
                            No logs loaded for this channel yet.
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
                        <div class="flex flex-col gap-4">
                            <div 
                                v-for="part in activeConversation.participants" 
                                :key="part.name"
                                class="flex items-center gap-3 bg-white dark:bg-white/3 border border-slate-100 dark:border-white/5 p-3 rounded-xl text-left animate-fade-in"
                            >
                                <img :src="part.avatar" :alt="part.name" class="h-10 w-10 rounded-lg object-cover" />
                                <div class="min-w-0 flex-grow">
                                    <h5 class="font-extrabold text-xs text-slate-850 dark:text-white truncate leading-snug">{{ part.name }}</h5>
                                    <p class="text-[8px] text-purple-600 dark:text-purple-400 font-extrabold uppercase tracking-widest mt-0.5">{{ part.role }}</p>
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
    </DashboardLayout>
</template>
