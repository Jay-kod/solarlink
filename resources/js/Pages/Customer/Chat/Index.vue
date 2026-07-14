<script setup lang="ts">
import { ref, computed, nextTick, watch, onMounted } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'
import { 
    MessageSquare, Send, Search, Sparkles, Phone, Video, 
    FileText, Image, MapPin, Star, Info, 
    CornerDownLeft, UserCheck, ShieldCheck, Download, CheckCircle2, Lock
} from 'lucide-vue-next'

interface Participant {
    id: number;
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
    unread?: number;
    online?: boolean;
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
    initialConversations?: Conversation[]
}>()

const page = usePage()
const userRole = computed(() => {
    return (page.props.auth as any).user?.role || 'customer'
})
const authUser = computed(() => {
    return (page.props.auth as any).user
})

const layoutComponent = computed(() => {
    return userRole.value === 'customer' ? CustomerLayout : DashboardLayout
})

const conversations = ref<Conversation[]>(props.initialConversations || [])
const activeConversation = ref<Conversation | null>(conversations.value[0] || null)
const messages = ref<Message[]>([])
const isTyping = ref(false)
const showDetails = ref(true)
const searchQuery = ref('')
const messageContainer = ref<HTMLElement | null>(null)
const newMessage = ref('')

const fetchMessages = async () => {
    if (!activeConversation.value) return
    try {
        const response = await axios.get(`/api/conversations/${activeConversation.value.id}/messages`)
        messages.value = response.data
        scrollToBottom()
    } catch (err) {
        console.error('Failed to fetch messages:', err)
    }
}

// Filtered conversations based on search query
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

const sendMessage = async (textToSend?: string) => {
    if (!activeConversation.value || activeConversation.value.status === 'concluded') return
    
    const text = textToSend || newMessage.value.trim()
    if (!text) return

    if (!textToSend) {
        newMessage.value = ''
    }

    // Pre-emptively push my message locally for immediate user feedback
    messages.value.push({
        id: Date.now(),
        sender: 'me',
        sender_name: authUser.value?.name || 'You',
        sender_role: authUser.value?.role || 'customer',
        sender_avatar: authUser.value?.avatar || 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
        text: text,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        type: 'text'
    })
    scrollToBottom()

    // Show simulated typing state in the UI for realistic response delay
    isTyping.value = true

    try {
        await axios.post(`/api/conversations/${activeConversation.value.id}/messages`, {
            text: text,
            type: 'text'
        })
        
        // Wait 1.2 seconds to fetch the reply (saved in the database by the backend)
        setTimeout(async () => {
            isTyping.value = false
            await fetchMessages()
            
            // Update last message preview in list
            if (messages.value.length > 0 && activeConversation.value) {
                const last = messages.value[messages.value.length - 1]
                activeConversation.value.lastMessage = last.text
                activeConversation.value.time = 'Just now'
            }
        }, 1200)
    } catch (err) {
        isTyping.value = false
        console.error('Failed to send message:', err)
    }
}

const sendMockAttachment = async (type: 'image' | 'file') => {
    if (!activeConversation.value || activeConversation.value.status === 'concluded') return

    isTyping.value = true

    // Push locally
    messages.value.push({
        id: Date.now(),
        sender: 'me',
        sender_name: authUser.value?.name || 'You',
        sender_role: authUser.value?.role || 'customer',
        sender_avatar: authUser.value?.avatar || 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
        text: type === 'image' ? 'Uploaded panel diagram snippet.' : 'Attached system warranty document.',
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        type: type,
        fileName: type === 'file' ? 'warranty_docs.pdf' : undefined,
        fileSize: type === 'file' ? '2.4 MB' : undefined
    })
    scrollToBottom()

    try {
        const formData = new FormData()
        formData.append('type', type)
        formData.append('text', type === 'image' ? 'Uploaded panel diagram snippet.' : 'Attached system warranty document.')
        
        // Create mock binary blob file
        const blob = new Blob(['Mock attachment file'], { type: type === 'image' ? 'image/jpeg' : 'application/pdf' })
        formData.append('attachment', blob, type === 'image' ? 'panel_diagram.jpg' : 'warranty_docs.pdf')

        await axios.post(`/api/conversations/${activeConversation.value.id}/messages`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        setTimeout(async () => {
            isTyping.value = false
            await fetchMessages()
        }, 1200)
    } catch (err) {
        isTyping.value = false
        console.error('Failed to send attachment:', err)
    }
}

const selectConversation = (conv: Conversation) => {
    activeConversation.value = conv
    fetchMessages()
}

// Conclude group conversation
const concludeGroup = () => {
    if (!activeConversation.value) return
    if (confirm('Are you sure you want to conclude this conversation? This will lock the channel and make it read-only.')) {
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
    if (confirm('Are you sure you want to delete this group chat? This will remove all message logs from the system.')) {
        router.delete(`/api/conversations/${activeConversation.value.id}`, {
            onSuccess: () => {
                conversations.value = conversations.value.filter(c => c.id !== activeConversation.value?.id)
                activeConversation.value = conversations.value[0] || null
                fetchMessages()
            }
        })
    }
}

// Quick response suggestions
const quickReplies = computed(() => {
    if (!activeConversation.value) return []
    const role = userRole.value
    if (role === 'customer') {
        if (activeConversation.value.id === 1) {
            return [
                { text: "Breaker is unlocked & ready.", label: "Breaker access" },
                { text: "Are we on schedule for 2:00 PM?", label: "ETA check" },
                { text: "Yes, please inspect the battery.", label: "Battery inspection" }
            ]
        } else {
            return [
                { text: "Please send over the pricing invoice.", label: "Invoice request" },
                { text: "Sounds good, thanks for concluding this.", label: "Conclude job" }
            ]
        }
    } else if (role === 'technician') {
        return [
            { text: "I am picking up the parts and will head over shortly.", label: "On my way" },
            { text: "Inverter swap complete. Testing the telemetry stream now.", label: "Completed" },
            { text: "Could you please verify if you can access the battery dashboard?", label: "Verify telemetry" }
        ]
    } else if (role === 'vendor') {
        return [
            { text: "I've verified the serial numbers and updated the warranty records.", label: "Verify Serial" },
            { text: "The parts invoice is ready for checkout in the marketplace.", label: "Invoice Ready" },
            { text: "Let us know if you need any additional mounting accessories.", label: "Accessories Info" }
        ]
    }
    return []
})

const triggerCallSimulation = (isVideo = false) => {
    alert(`Calling participants via ${isVideo ? 'Video Conference' : 'Audio Conference'}... (Simulation Mode)`)
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
    <Head title="SolarLink — Service Chat" />

    <component :is="layoutComponent" :role="userRole" title="Service Messaging">
        <div class="flex flex-col gap-6 text-left max-w-7xl mx-auto h-[calc(100vh-140px)] animate-fade-in">
            
            <div class="flex flex-col gap-1 shrink-0">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Real-time Support Network</span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">Active Service Group Channels</h2>
            </div>

            <!-- Chat grid container -->
            <div class="glass-card flex-grow overflow-hidden flex border border-solar-primary/10 dark:border-white/5 bg-white/50 dark:bg-solar-bg-dark/20 backdrop-blur-md rounded-2xl relative min-h-[500px]">
                
                <!-- Left Sidebar (Group Channels list) -->
                <div class="w-80 border-r border-solar-primary/10 dark:border-white/5 flex flex-col shrink-0 bg-white/60 dark:bg-solar-primary-dark/5">
                    <!-- Search -->
                    <div class="p-4 border-b border-solar-primary/10 dark:border-white/5">
                        <div class="relative flex items-center">
                            <Search class="absolute left-3.5 h-4 w-4 text-slate-400 pointer-events-none" />
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Search active threads..."
                                class="w-full h-10 pl-10 pr-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs focus:outline-none focus:border-solar-primary transition-all duration-300"
                            />
                        </div>
                    </div>

                    <!-- Threads -->
                    <div class="flex-grow overflow-y-auto divide-y divide-slate-100 dark:divide-white/3">
                        <button 
                            v-for="conv in filteredConversations" 
                            :key="conv.id"
                            @click="selectConversation(conv)"
                            class="w-full p-4 flex items-start gap-3.5 text-left transition-all relative"
                            :class="activeConversation?.id === conv.id 
                                ? 'bg-solar-primary/10 dark:bg-solar-primary/10 border-l-4 border-l-solar-primary' 
                                : 'hover:bg-slate-50 dark:hover:bg-white/3'"
                        >
                            <!-- Triple participant overlapping bubble avatar -->
                            <div class="relative shrink-0 w-10 h-10 flex items-center justify-center">
                                <div v-if="conv.participants.length >= 2" class="w-full h-full relative">
                                    <img :src="conv.participants[0].avatar" class="absolute top-0 left-0 w-6 h-6 rounded-full object-cover border border-white dark:border-[#0f1322] shadow" />
                                    <img :src="conv.participants[1].avatar" class="absolute bottom-0 right-0 w-6 h-6 rounded-full object-cover border border-white dark:border-[#0f1322] shadow" />
                                </div>
                                <div v-else-if="conv.participants.length === 1">
                                    <img :src="conv.participants[0].avatar" class="w-8 h-8 rounded-full object-cover" />
                                </div>
                                <div v-else class="h-8 w-8 rounded-full bg-slate-200 dark:bg-solar-primary-dark flex items-center justify-center text-slate-550 dark:text-solar-primary-accent text-xs font-bold">
                                    GP
                                </div>
                                <span 
                                    v-if="conv.status === 'concluded'"
                                    class="absolute -top-1 -right-1 h-3.5 w-3.5 rounded-full bg-slate-400 text-white flex items-center justify-center text-[7px]"
                                    title="Concluded"
                                >
                                    <Lock class="h-2 w-2" />
                                </span>
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-extrabold text-xs text-slate-800 dark:text-white truncate">{{ conv.title }}</h4>
                                    <span class="text-[9px] text-slate-400 shrink-0 ml-1">{{ conv.time }}</span>
                                </div>
                                <p class="text-[9px] text-slate-400 truncate mt-0.5">
                                    {{ conv.participants.map(p => p.name).join(', ') }}
                                </p>
                                <p class="text-[10px] text-slate-450 dark:text-slate-400 truncate mt-1 leading-snug font-medium">{{ conv.lastMessage }}</p>
                            </div>
                        </button>

                        <div v-if="filteredConversations.length === 0" class="p-6 text-center text-slate-400 dark:text-slate-500">
                            <Info class="h-8 w-8 mx-auto text-slate-350 dark:text-slate-650 mb-2" />
                            <p class="text-xs">No active chat groups found.</p>
                        </div>
                    </div>
                </div>

                <!-- Central Chat Canvas -->
                <div v-if="activeConversation" class="flex-grow flex flex-col min-w-0 bg-white/30 dark:bg-solar-primary-dark/2">
                    
                    <!-- Chat Header -->
                    <div class="h-16 border-b border-solar-primary/10 dark:border-white/5 flex items-center justify-between px-5 bg-white/70 dark:bg-solar-bg-dark/40 shrink-0">
                        <div class="flex items-center gap-3">
                            <h4 class="font-extrabold text-xs text-slate-800 dark:text-white leading-tight">{{ activeConversation.title }}</h4>
                            <span 
                                class="px-2 py-0.5 rounded-full text-[8px] font-extrabold uppercase tracking-wider"
                                :class="activeConversation.status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-400/10 text-slate-500'"
                            >
                                {{ activeConversation.status }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button 
                                @click="triggerCallSimulation(false)"
                                class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-solar-primary-dark/30 hover:text-solar-primary transition-all"
                                title="Start Audio Conference"
                            >
                                <Phone class="h-4 w-4" />
                            </button>
                            <button 
                                @click="triggerCallSimulation(true)"
                                class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-solar-primary-dark/30 hover:text-solar-primary transition-all"
                                title="Start Video Conference"
                            >
                                <Video class="h-4 w-4" />
                            </button>
                            <div class="w-px h-6 bg-slate-200 dark:bg-white/10 mx-1"></div>
                            
                            <!-- Conclude chat action -->
                            <button 
                                v-if="activeConversation.status === 'active' && userRole === 'customer'"
                                @click="concludeGroup"
                                class="px-2.5 h-8 rounded-lg bg-emerald-500/10 hover:bg-emerald-500 text-emerald-600 hover:text-white text-[10px] font-bold transition-all flex items-center gap-1"
                                title="Conclude chat group"
                            >
                                <CheckCircle2 class="h-3.5 w-3.5" />
                                <span>Conclude</span>
                            </button>

                            <!-- Delete group action -->
                            <button 
                                v-if="userRole === 'customer'"
                                @click="deleteGroup"
                                class="px-2.5 h-8 rounded-lg bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-[10px] font-bold transition-all flex items-center gap-1"
                                title="Delete group chat"
                            >
                                <span>Delete Group</span>
                            </button>

                            <button 
                                @click="showDetails = !showDetails"
                                class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-solar-primary-dark/30 transition-all"
                                :class="showDetails ? 'bg-solar-primary/10 text-solar-primary dark:text-solar-primary-accent' : ''"
                                title="Toggle Contact Details"
                            >
                                <UserCheck class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Messages Stream -->
                    <div 
                        ref="messageContainer"
                        class="flex-grow overflow-y-auto p-5 flex flex-col gap-4 bg-slate-50/50 dark:bg-solar-bg-dark/10"
                    >
                        <div 
                            v-for="msg in messages" 
                            :key="msg.id"
                            class="flex gap-2.5 max-w-[80%]"
                            :class="msg.sender === 'me' ? 'self-end flex-row-reverse' : 'self-start flex-row'"
                        >
                            <!-- Avatar -->
                            <img 
                                v-if="msg.sender === 'them'" 
                                :src="msg.sender_avatar" 
                                :alt="msg.sender_name" 
                                class="h-7 w-7 rounded-full object-cover shrink-0 mt-0.5 border border-slate-100 dark:border-white/10" 
                            />
                            
                            <div class="flex flex-col">
                                <!-- Sender details for group identification -->
                                <span v-if="msg.sender === 'them'" class="text-[8px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-0.5">
                                    {{ msg.sender_name }} • {{ msg.sender_role }}
                                </span>
                                
                                <!-- Message bubble -->
                                <div class="flex flex-col" :class="msg.sender === 'me' ? 'items-end' : 'items-start'">
                                    <!-- Text Bubble -->
                                    <div 
                                        v-if="!msg.type || msg.type === 'text'"
                                        class="px-4 py-3 rounded-2xl text-xs leading-relaxed shadow-sm font-medium"
                                        :class="msg.sender === 'me' 
                                            ? 'bg-gradient-to-r from-solar-primary to-solar-primary-active text-white rounded-br-sm' 
                                            : 'bg-white dark:bg-solar-primary-dark/20 border border-slate-100 dark:border-white/5 text-slate-700 dark:text-slate-200 rounded-bl-sm'"
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
                                        class="px-4 py-3.5 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 flex items-center gap-3.5"
                                        :class="msg.sender === 'me' 
                                            ? 'bg-solar-primary/10 dark:bg-solar-primary-dark/20 text-slate-800 dark:text-white rounded-br-sm' 
                                            : 'bg-white dark:bg-solar-primary-dark/20 text-slate-700 dark:text-slate-200 rounded-bl-sm'"
                                    >
                                        <div class="h-10 w-10 rounded-xl bg-solar-primary/20 text-solar-primary flex items-center justify-center shrink-0">
                                            <FileText class="h-5 w-5" />
                                        </div>
                                        <div class="min-w-0 flex-grow text-left">
                                            <h5 class="text-xs font-bold truncate leading-snug">{{ msg.fileName }}</h5>
                                            <p class="text-[9px] text-slate-400 font-medium mt-0.5 uppercase tracking-wide">{{ msg.fileSize }}</p>
                                        </div>
                                    </div>
                                    
                                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 px-1">
                                        {{ msg.time }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Typing Simulator -->
                        <div v-if="isTyping" class="flex items-start gap-2.5 animate-pulse">
                            <div class="h-7 w-7 rounded-full bg-slate-250 dark:bg-white/5 shrink-0"></div>
                            <div class="flex flex-col">
                                <div class="px-4 py-3 bg-white dark:bg-solar-primary-dark/20 border border-slate-100 dark:border-white/5 text-slate-700 dark:text-slate-200 rounded-2xl rounded-bl-sm flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 bg-solar-primary rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                    <span class="h-1.5 w-1.5 bg-solar-primary rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                    <span class="h-1.5 w-1.5 bg-solar-primary rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Controls -->
                    <div class="border-t border-solar-primary/10 dark:border-white/5 p-4 bg-white/70 dark:bg-solar-bg-dark/40 flex flex-col gap-3 shrink-0">
                        <!-- Locked state for concluded conversations -->
                        <div v-if="activeConversation.status === 'concluded'" class="flex items-center justify-center gap-2 py-2 text-slate-500 font-bold text-xs uppercase tracking-wider bg-slate-100 dark:bg-white/5 rounded-xl">
                            <Lock class="h-4 w-4" />
                            <span>This conversation group is concluded and locked.</span>
                        </div>
                        
                        <div v-else class="flex flex-col gap-3">
                            <!-- Suggestion Chips -->
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    v-for="reply in quickReplies" 
                                    :key="reply.text"
                                    @click="sendMessage(reply.text)"
                                    class="px-3 py-1.5 rounded-xl border border-solar-primary/20 hover:border-solar-primary text-slate-650 dark:text-slate-350 hover:text-solar-primary dark:hover:text-solar-primary-accent text-[10px] font-bold transition-all bg-solar-primary/5 hover:bg-solar-primary/10 hover:-translate-y-0.5 active:translate-y-0"
                                >
                                    {{ reply.label }}
                                </button>
                            </div>

                            <!-- Form -->
                            <form @submit.prevent="sendMessage()" class="flex items-center gap-3">
                                <div class="flex items-center gap-1 shrink-0">
                                    <button 
                                        type="button"
                                        @click="sendMockAttachment('image')"
                                        class="p-2 rounded-xl text-slate-500 hover:text-solar-primary hover:bg-slate-100 dark:hover:bg-white/5 transition-all"
                                        title="Attach mock panel diagram image"
                                    >
                                        <Image class="h-4.5 w-4.5" />
                                    </button>
                                    <button 
                                        type="button"
                                        @click="sendMockAttachment('file')"
                                        class="p-2 rounded-xl text-slate-500 hover:text-solar-primary hover:bg-slate-100 dark:hover:bg-white/5 transition-all"
                                        title="Attach mock warranty docs PDF"
                                    >
                                        <FileText class="h-4.5 w-4.5" />
                                    </button>
                                </div>

                                <div class="flex-grow relative flex items-center">
                                    <input 
                                        v-model="newMessage"
                                        type="text"
                                        placeholder="Write a message to group participants..."
                                        class="w-full h-11 pl-4 pr-12 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all"
                                    />
                                    <span class="absolute right-3.5 text-[9px] font-bold text-slate-400 flex items-center gap-1 uppercase tracking-wider hidden sm:flex">
                                        <span>Enter</span>
                                        <CornerDownLeft class="h-3 w-3" />
                                    </span>
                                </div>

                                <button 
                                    type="submit"
                                    class="h-11 w-11 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white flex items-center justify-center shadow-solar hover:shadow-solar-glow transition-all"
                                >
                                    <Send class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Empty Chat placeholder -->
                <div v-else class="flex-grow flex flex-col items-center justify-center text-center p-10 bg-slate-50/50 dark:bg-solar-bg-dark/10 text-slate-450 dark:text-slate-500">
                    <MessageSquare class="h-16 w-16 text-slate-350 dark:text-slate-750 mb-3.5" />
                    <h3 class="font-extrabold text-base text-slate-800 dark:text-white">No active thread</h3>
                    <p class="text-xs max-w-sm mt-1">Select one of your tripartite support or hardware group chats in the sidebar to review logs or send queries.</p>
                </div>

                <!-- Right Context sidebar (Participant Details) -->
                <div 
                    v-if="showDetails && activeConversation"
                    class="w-72 border-l border-solar-primary/10 dark:border-white/5 flex flex-col shrink-0 bg-white/50 dark:bg-solar-bg-dark/30 hidden lg:flex animate-fade-in-right overflow-y-auto"
                >
                    <div class="p-5 flex flex-col gap-6">
                        <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-left">Group Participants</h4>
                        
                        <!-- List of participants -->
                        <div class="flex flex-col gap-4">
                            <div 
                                v-for="part in activeConversation.participants" 
                                :key="part.id"
                                class="flex items-center gap-3 bg-slate-50/50 dark:bg-white/3 border border-slate-100 dark:border-white/5 p-3 rounded-xl text-left"
                            >
                                <img :src="part.avatar" :alt="part.name" class="h-10 w-10 rounded-lg object-cover ring-1 ring-solar-primary/10 shadow-sm" />
                                <div class="min-w-0 flex-grow">
                                    <h5 class="font-extrabold text-xs text-slate-850 dark:text-white truncate leading-snug">{{ part.name }}</h5>
                                    <p class="text-[8px] text-solar-primary dark:text-solar-primary-accent font-extrabold uppercase tracking-widest mt-0.5">{{ part.role }}</p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-white/5" />

                        <!-- Specifications and licensing info -->
                        <div class="flex flex-col gap-3 text-left">
                            <h4 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Specifications</h4>
                            
                            <div class="flex flex-col gap-2.5 text-xs text-slate-650 dark:text-slate-350">
                                <div class="flex items-center gap-2">
                                    <ShieldCheck class="h-4 w-4 text-solar-primary shrink-0" />
                                    <span class="font-semibold">Fully Encrypted Channel</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Star class="h-4 w-4 text-amber-500 shrink-0 fill-amber-500" />
                                    <span class="font-semibold">SLA-backed SLA Routing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </component>
</template>
