<script setup lang="ts">
import { ref, computed, nextTick, watch, onMounted, onBeforeUnmount } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import axios from 'axios'
import { 
    MessageSquare, Send, Search, Sparkles, Phone, Video, 
    FileText, Image, MapPin, Star, Info, 
    CornerDownLeft, UserCheck, ShieldCheck, Download, CheckCircle2, Lock, Plus, X,
    CheckCheck, Paperclip, Mic
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
    created_by?: number;
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

const isCreator = computed(() => {
    if (!activeConversation.value || !authUser.value) return false
    return activeConversation.value.created_by === authUser.value.id
})

const creatorParticipant = computed(() => {
    if (!activeConversation.value) return null
    return activeConversation.value.participants.find((p: any) => p.id === activeConversation.value?.created_by) || activeConversation.value.participants[0]
})

const conversations = ref<Conversation[]>(props.initialConversations || [])
const activeConversation = ref<Conversation | null>(conversations.value[0] || null)
const messages = ref<Message[]>([])
const isTyping = ref(false)
const searchQuery = ref('')
const messageContainer = ref<HTMLElement | null>(null)
const newMessage = ref('')
const imageAttachmentInput = ref<HTMLInputElement | null>(null)
const fileAttachmentInput = ref<HTMLInputElement | null>(null)

const isProcessing = ref(false)
let pollingInterval: number | null = null

// New Chat / Add Member State
const modalMode = ref<'new' | 'add'>('new')
const showNewChatModal = ref(false)
const userSearchQuery = ref('')
const searchResults = ref<any[]>([])
const selectedUsers = ref<any[]>([])
const isSearching = ref(false)

const openNewChat = (mode: 'new' | 'add' = 'new') => {
    modalMode.value = mode
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
        
        if (modalMode.value === 'add' && activeConversation.value) {
            const res = await axios.post(`/api/conversations/${activeConversation.value.id}/members`, { user_ids: userIds })
            if (res.data.status === 'success') {
                activeConversation.value.participants = res.data.participants
                closeNewChat()
                fetchMessages() // to get the system message
            } else if (res.data.status === 'info') {
                closeNewChat()
            }
        } else {
            const res = await axios.post('/api/conversations', { user_ids: userIds })
            if (res.data.status === 'success') {
                conversations.value.unshift(res.data.conversation)
                selectConversation(res.data.conversation)
                closeNewChat()
            }
        }
    } catch (e) {
        console.error('Failed to create/update chat:', e)
        infoAlert('Failed to complete action.', 'Error', { type: 'error' })
    }
}

onMounted(() => {
    pollingInterval = window.setInterval(() => {
        if (activeConversation.value && !isTyping.value) {
            fetchMessages(false)
        }
    }, 3000)
})

onBeforeUnmount(() => {
    if (pollingInterval) {
        window.clearInterval(pollingInterval)
    }
})

const fetchMessages = async (autoScroll = true) => {
    if (!activeConversation.value) return
    try {
        const response = await axios.get(`/api/conversations/${activeConversation.value.id}/messages`)
        const prevLength = messages.value.length
        messages.value = response.data
        if (autoScroll || messages.value.length > prevLength) {
            scrollToBottom()
        }
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

    const tempId = Date.now()

    // Pre-emptively push my message locally for immediate user feedback
    messages.value.push({
        id: tempId,
        sender: 'me',
        sender_name: authUser.value?.name || 'You',
        sender_role: authUser.value?.role || 'customer',
        sender_avatar: authUser.value?.avatar || 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
        text: text,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        type: 'text'
    })
    scrollToBottom()

    isTyping.value = true

    router.post(`/api/conversations/${activeConversation.value.id}/messages`, {
        text: text,
        type: 'text'
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            isTyping.value = false
            fetchMessages()

            if (messages.value.length > 0 && activeConversation.value) {
                const last = messages.value[messages.value.length - 1]
                activeConversation.value.lastMessage = last.text
                activeConversation.value.time = 'Just now'
            }
        },
        onError: (err) => {
            isTyping.value = false
            messages.value = messages.value.filter(m => m.id !== tempId)
            infoAlert('Failed to send message. Please try again.', 'Error', { type: 'error' })
            console.error('Failed to send message:', err)
        }
    })
}

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

const sendAttachment = async (file: File, type: 'image' | 'file') => {
    if (!activeConversation.value || activeConversation.value.status === 'concluded') return

    isTyping.value = true

    const attachmentText = type === 'image' ? 'Uploaded panel diagram snippet.' : 'Attached system warranty document.'

    const tempId = Date.now()

    // Push locally for immediate feedback
    messages.value.push({
        id: tempId,
        sender: 'me',
        sender_name: authUser.value?.name || 'You',
        sender_role: authUser.value?.role || 'customer',
        sender_avatar: authUser.value?.avatar || 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
        text: attachmentText,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        type: type,
        fileName: file.name,
        fileSize: formatFileSize(file.size),
        imageUrl: type === 'image' ? URL.createObjectURL(file) : undefined
    })
    scrollToBottom()

    const formData = new FormData()
    formData.append('type', type)
    formData.append('text', attachmentText)
    formData.append('attachment', file, file.name)

    router.post(`/api/conversations/${activeConversation.value.id}/messages`, formData, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            isTyping.value = false
            fetchMessages()
        },
        onError: (err) => {
            isTyping.value = false
            messages.value = messages.value.filter(m => m.id !== tempId)
            infoAlert('Failed to send attachment. Please try again.', 'Error', { type: 'error' })
            console.error('Failed to send attachment:', err)
        }
    })
}

const handleAttachmentPicker = (type: 'image' | 'file') => {
    if (type === 'image') {
        imageAttachmentInput.value?.click()
        return
    }

    fileAttachmentInput.value?.click()
}

const handleAttachmentChange = async (event: Event, type: 'image' | 'file') => {
    const input = event.target as HTMLInputElement
    const file = input.files?.[0]

    if (!file) {
        return
    }

    await sendAttachment(file, type)
    input.value = ''
}

const selectConversation = (conv: Conversation) => {
    activeConversation.value = conv
    fetchMessages()
}

import { useAlert } from '@/composables/useAlert'
const { confirmAlert, infoAlert } = useAlert()

// Conclude group conversation
const concludeGroup = async () => {
    if (!activeConversation.value || isProcessing.value) return
    const confirmed = await confirmAlert(
        'Are you sure you want to conclude this conversation? This will lock the channel and make it read-only.',
        'Conclude Conversation',
        { type: 'warning', confirmText: 'Conclude Chat' }
    )
    if (confirmed) {
        router.post(`/api/conversations/${activeConversation.value.id}/conclude`, {}, {
            onStart: () => isProcessing.value = true,
            onFinish: () => isProcessing.value = false,
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
    if (!activeConversation.value || isProcessing.value) return
    const confirmed = await confirmAlert(
        'Are you sure you want to delete this group chat? This will remove all message logs from the system.',
        'Delete Conversation',
        { type: 'danger', confirmText: 'Delete Chat' }
    )
    if (confirmed) {
        router.delete(`/api/conversations/${activeConversation.value.id}`, {
            onStart: () => isProcessing.value = true,
            onFinish: () => isProcessing.value = false,
            onSuccess: () => {
                conversations.value = conversations.value.filter(c => c.id !== activeConversation.value?.id)
                activeConversation.value = conversations.value[0] || null
                fetchMessages()
            }
        })
    }
}

const removeMember = async (user: any) => {
    if (!activeConversation.value) return
    const confirmed = await confirmAlert(
        `Are you sure you want to remove ${user.name} from the conversation?`,
        'Remove Member',
        { type: 'danger', confirmText: 'Remove' }
    )
    if (!confirmed) return
    
    try {
        const response = await axios.delete(`/api/conversations/${activeConversation.value.id}/members/${user.id}`)
        if (response.data.status === 'success') {
            activeConversation.value.participants = response.data.participants
            fetchMessages()
        }
    } catch (e: any) {
        infoAlert(e.response?.data?.message || 'Failed to remove member.', 'Error', { type: 'error' })
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
    infoAlert(
        `Calling participants via ${isVideo ? 'Video Conference' : 'Audio Conference'}... (Simulation Mode)`,
        'Starting Call',
        { type: 'success' }
    )
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
    <component :is="layoutComponent" :role="userRole" :title="userRole === 'admin' ? 'Operations Audit' : 'Service Messaging'">
        
        <!-- Telegram Style Full Height Chat Container -->
        <div class="flex text-left h-[calc(100vh-80px)] -m-6 sm:-m-8">

            <!-- Chat grid container -->
            <div class="flex-grow overflow-hidden flex bg-white dark:bg-[#0E1628] w-full h-full relative">
                
                <!-- Left Sidebar (Group Channels list) -->
                <div class="w-[320px] md:w-[350px] border-r border-slate-200 dark:border-white/5 flex flex-col shrink-0 bg-white dark:bg-[#151b2b]">
                    <!-- Search and New Chat -->
                    <div class="p-3 flex items-center gap-2 border-b border-slate-100 dark:border-white/5 shrink-0">
                        <button @click="openNewChat('new')" class="h-10 w-10 shrink-0 bg-slate-100 dark:bg-white/5 text-slate-500 hover:text-solar-primary rounded-full flex items-center justify-center transition-colors" title="New Message">
                            <Plus class="h-5 w-5" />
                        </button>
                        <div class="relative flex-grow">
                            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" />
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Search..."
                                class="w-full h-10 pl-10 pr-4 rounded-full bg-slate-100 dark:bg-white/5 border-transparent text-sm focus:bg-white dark:focus:bg-[#0E1628] focus:border-solar-primary focus:ring-1 focus:ring-solar-primary transition-all shadow-none"
                            />
                        </div>
                    </div>

                    <!-- Threads -->
                    <div class="flex-grow overflow-y-auto">
                        <button 
                            v-for="conv in filteredConversations" 
                            :key="conv.id"
                            @click="selectConversation(conv)"
                            class="w-full p-2.5 flex items-center gap-3 text-left transition-all relative outline-none"
                            :class="activeConversation?.id === conv.id 
                                ? 'bg-solar-primary text-white' 
                                : 'hover:bg-slate-50 dark:hover:bg-white/5 text-slate-800 dark:text-white'"
                        >
                            <!-- Avatar -->
                            <div class="relative shrink-0 w-12 h-12 flex items-center justify-center">
                                <div v-if="conv.participants.length >= 2" class="w-full h-full relative">
                                    <img :src="conv.participants[0].avatar" class="absolute top-0 right-0 w-8 h-8 rounded-full object-cover border-2" :class="activeConversation?.id === conv.id ? 'border-solar-primary' : 'border-white dark:border-[#151b2b]'" />
                                    <img :src="conv.participants[1].avatar" class="absolute bottom-0 left-0 w-8 h-8 rounded-full object-cover border-2" :class="activeConversation?.id === conv.id ? 'border-solar-primary' : 'border-white dark:border-[#151b2b]'" />
                                </div>
                                <div v-else-if="conv.participants.length === 1">
                                    <img :src="conv.participants[0].avatar" class="w-12 h-12 rounded-full object-cover" />
                                </div>
                                <div v-else class="h-12 w-12 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300 font-bold">
                                    GP
                                </div>
                                <span 
                                    v-if="conv.status === 'concluded'"
                                    class="absolute -bottom-0.5 -right-0.5 h-4 w-4 rounded-full bg-slate-400 text-white flex items-center justify-center text-[9px] border-2"
                                    :class="activeConversation?.id === conv.id ? 'border-solar-primary' : 'border-white dark:border-[#151b2b]'"
                                    title="Concluded"
                                >
                                    <Lock class="h-2.5 w-2.5" />
                                </span>
                            </div>
                            
                            <div class="flex-grow min-w-0 flex flex-col justify-center h-full border-b border-slate-100 dark:border-white/5 pb-2 pt-1" :class="activeConversation?.id === conv.id ? 'border-transparent' : ''">
                                <div class="flex items-center justify-between mb-0.5">
                                    <h4 class="font-bold text-sm truncate" :class="activeConversation?.id === conv.id ? 'text-white' : 'text-slate-800 dark:text-white'">{{ conv.title || conv.participants.map(p => p.name).join(', ') }}</h4>
                                    <span class="text-[11px] shrink-0 ml-2" :class="activeConversation?.id === conv.id ? 'text-white/80' : 'text-slate-400'">{{ conv.time }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-[13px] truncate" :class="activeConversation?.id === conv.id ? 'text-white/80' : 'text-slate-500'">
                                        <span v-if="conv.messages && conv.messages.length > 0 && conv.messages[conv.messages.length-1].sender === 'me'" class="font-medium mr-1" :class="activeConversation?.id === conv.id ? 'text-white' : 'text-slate-800 dark:text-slate-200'">You:</span>
                                        {{ conv.lastMessage || 'No messages yet' }}
                                    </p>
                                    <div v-if="conv.unread > 0" class="h-5 min-w-[20px] rounded-full flex items-center justify-center text-[10px] font-bold px-1.5" :class="activeConversation?.id === conv.id ? 'bg-white text-solar-primary' : 'bg-solar-primary text-white'">
                                        {{ conv.unread }}
                                    </div>
                                </div>
                            </div>
                        </button>

                        <div v-if="filteredConversations.length === 0" class="p-6 text-center text-slate-400 dark:text-slate-500">
                            <Info class="h-8 w-8 mx-auto text-slate-350 dark:text-slate-650 mb-2" />
                            <p class="text-xs">No active chat groups found.</p>
                        </div>
                    </div>
                </div>

                <!-- Central Chat Canvas -->
                <div v-if="activeConversation" class="flex-grow flex flex-col min-w-0 relative" style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); background-color: rgba(248,250,252,0.95);">
                    <div class="absolute inset-0 bg-slate-50/95 dark:bg-[#0E1628]/95 z-0"></div>

                    <!-- Chat Header -->
                    <div class="h-14 border-b border-slate-200 dark:border-white/5 flex items-center justify-between px-4 bg-white/95 dark:bg-[#151b2b]/95 backdrop-blur-sm shrink-0 z-10 cursor-pointer hover:bg-slate-50 dark:hover:bg-[#181f32] transition-colors" @click="showDetails = !showDetails">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <!-- Header Avatar -->
                            <div class="relative shrink-0 w-10 h-10 flex items-center justify-center">
                                <div v-if="activeConversation.participants.length >= 2" class="w-full h-full relative">
                                    <img :src="activeConversation.participants[0].avatar" class="absolute top-0 right-0 w-6 h-6 rounded-full object-cover border-2 border-white dark:border-[#151b2b]" />
                                    <img :src="activeConversation.participants[1].avatar" class="absolute bottom-0 left-0 w-6 h-6 rounded-full object-cover border-2 border-white dark:border-[#151b2b]" />
                                </div>
                                <img v-else-if="activeConversation.participants.length === 1" :src="activeConversation.participants[0].avatar" class="w-10 h-10 rounded-full object-cover" />
                            </div>
                            <div class="min-w-0 flex flex-col justify-center">
                                <h4 class="font-bold text-[15px] text-slate-800 dark:text-white truncate leading-tight">{{ activeConversation.title || activeConversation.participants.map(p => p.name).join(', ') }}</h4>
                                <p class="text-xs text-slate-500 truncate mt-0.5">
                                    {{ activeConversation.participants.length }} members <span v-if="activeConversation.online" class="text-solar-primary ml-1">• online</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2" @click.stop>
                            <button 
                                @click="triggerCallSimulation(false)"
                                class="h-10 w-10 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-solar-primary transition-all flex items-center justify-center"
                                title="Start Audio Call"
                            >
                                <Phone class="h-5 w-5" />
                            </button>
                            <button 
                                @click="triggerCallSimulation(true)"
                                class="h-10 w-10 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-solar-primary transition-all flex items-center justify-center"
                                title="Start Video Call"
                            >
                                <Video class="h-5 w-5" />
                            </button>
                            <button 
                                @click="showDetails = !showDetails"
                                class="h-10 w-10 rounded-full transition-all flex items-center justify-center"
                                :class="showDetails ? 'text-solar-primary bg-solar-primary/10' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-solar-primary'"
                                title="Chat Info"
                            >
                                <Info class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Messages Stream -->
                    <div 
                        ref="messageContainer"
                        class="flex-grow overflow-y-auto p-4 md:p-6 flex flex-col gap-3 relative z-0"
                    >
                        <template v-for="msg in messages" :key="msg.id">
                            <!-- System Message -->
                            <div v-if="msg.type === 'system'" class="w-full flex justify-center items-center my-2">
                                <div class="flex-grow border-t border-dotted border-slate-300 dark:border-slate-600"></div>
                                <div class="mx-4 text-[11px] font-medium text-slate-500 bg-slate-100 dark:bg-[#151b2b] px-4 py-1.5 rounded-full text-center shrink-0 shadow-sm border border-slate-200 dark:border-white/5">
                                    {{ msg.text }} <span class="text-slate-400 font-normal ml-1">at {{ msg.time }}</span>
                                </div>
                                <div class="flex-grow border-t border-dotted border-slate-300 dark:border-slate-600"></div>
                            </div>

                            <!-- Normal Message -->
                            <div 
                                v-else
                                class="flex gap-2 max-w-[85%] md:max-w-[75%]"
                                :class="msg.sender === 'me' ? 'self-end flex-row-reverse' : 'self-start flex-row'"
                            >
                            <!-- Avatar -->
                            <img 
                                :src="msg.sender_avatar" 
                                :alt="msg.sender_name" 
                                class="h-8 w-8 rounded-full object-cover shrink-0 self-end mb-1" 
                            />
                            
                            <div class="flex flex-col min-w-0" :class="msg.sender === 'me' ? 'items-end' : 'items-start'">
                                <!-- Sender details -->
                                <span class="text-[11px] font-bold text-solar-primary mb-0.5" :class="msg.sender === 'me' ? 'mr-1' : 'ml-1'">
                                    {{ msg.sender_name }}
                                </span>
                                
                                <!-- Message bubble -->
                                <div class="flex flex-col relative group" :class="msg.sender === 'me' ? 'items-end' : 'items-start'">
                                    
                                    <!-- Text Bubble -->
                                    <div 
                                        v-if="!msg.type || msg.type === 'text'"
                                        class="px-3.5 py-2 rounded-2xl text-[14px] leading-relaxed shadow-sm font-medium relative"
                                        :class="msg.sender === 'me' 
                                            ? 'bg-solar-primary text-white rounded-br-none' 
                                            : 'bg-white dark:bg-[#151b2b] text-slate-800 dark:text-slate-200 rounded-bl-none'"
                                    >
                                        <p style="word-break: break-word;">{{ msg.text }}</p>
                                        <div class="flex items-center justify-end gap-1 mt-1 -mr-1">
                                            <span class="text-[10px]" :class="msg.sender === 'me' ? 'text-white/70' : 'text-slate-400'">{{ msg.time }}</span>
                                            <CheckCheck v-if="msg.sender === 'me'" class="h-3 w-3 text-white/70" />
                                        </div>
                                    </div>

                                    <!-- Image Bubble -->
                                    <div 
                                        v-else-if="msg.type === 'image'"
                                        class="rounded-2xl overflow-hidden shadow-sm flex flex-col max-w-[300px] relative"
                                        :class="msg.sender === 'me' ? 'rounded-br-none bg-solar-primary' : 'rounded-bl-none bg-white dark:bg-[#151b2b]'"
                                    >
                                        <img :src="msg.imageUrl" alt="Attachment" class="max-h-[300px] object-cover w-full" />
                                        <div class="px-3 py-2 text-[12px] w-full font-medium flex justify-between items-end gap-3" :class="msg.sender === 'me' ? 'text-white' : 'text-slate-800 dark:text-slate-200'">
                                            <span>{{ msg.text }}</span>
                                            <div class="flex items-center gap-1 shrink-0">
                                                <span class="text-[10px]" :class="msg.sender === 'me' ? 'text-white/70' : 'text-slate-400'">{{ msg.time }}</span>
                                                <CheckCheck v-if="msg.sender === 'me'" class="h-3 w-3 text-white/70" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- File Bubble -->
                                    <div 
                                        v-else-if="msg.type === 'file'"
                                        class="px-3 py-3 rounded-2xl shadow-sm flex items-center gap-3 min-w-[200px]"
                                        :class="msg.sender === 'me' 
                                            ? 'bg-solar-primary text-white rounded-br-none' 
                                            : 'bg-white dark:bg-[#151b2b] text-slate-800 dark:text-slate-200 rounded-bl-none'"
                                    >
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center shrink-0" :class="msg.sender === 'me' ? 'bg-white/20 text-white' : 'bg-solar-primary/10 text-solar-primary'">
                                            <FileText class="h-5 w-5" />
                                        </div>
                                        <div class="min-w-0 flex-grow flex flex-col justify-center">
                                            <h5 class="text-[13px] font-bold truncate leading-tight">{{ msg.fileName }}</h5>
                                            <div class="flex items-center justify-between gap-2 mt-0.5">
                                                <p class="text-[11px] font-medium uppercase tracking-wide" :class="msg.sender === 'me' ? 'text-white/70' : 'text-slate-500'">{{ msg.fileSize }}</p>
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px]" :class="msg.sender === 'me' ? 'text-white/70' : 'text-slate-400'">{{ msg.time }}</span>
                                                    <CheckCheck v-if="msg.sender === 'me'" class="h-3 w-3 text-white/70" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        </template>

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
                    <div class="p-3 md:p-4 bg-transparent shrink-0 z-10 w-full max-w-4xl mx-auto flex flex-col gap-2">
                        
                        <!-- Suggestion Chips -->
                        <div v-if="activeConversation.status !== 'concluded' && quickReplies.length > 0" class="flex flex-wrap gap-2 overflow-x-auto no-scrollbar pb-1">
                            <button 
                                v-for="reply in quickReplies" 
                                :key="reply.text"
                                @click="sendMessage(reply.text)"
                                class="px-3 py-1.5 rounded-full bg-white dark:bg-[#151b2b] text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 text-[12px] font-medium shadow-sm transition-all whitespace-nowrap"
                            >
                                {{ reply.label }}
                            </button>
                        </div>

                        <!-- Locked state for concluded conversations -->
                        <div v-if="activeConversation.status === 'concluded'" class="flex items-center justify-center gap-2 py-3 text-slate-500 font-bold text-xs uppercase tracking-wider bg-white/80 dark:bg-[#151b2b]/80 backdrop-blur-sm rounded-xl shadow-sm">
                            <Lock class="h-4 w-4" />
                            <span>This conversation group is locked</span>
                        </div>
                        
                        <!-- Form -->
                        <form v-else @submit.prevent="sendMessage()" class="flex items-end gap-2 bg-white dark:bg-[#151b2b] rounded-2xl md:rounded-full shadow-sm pr-2 pl-1 py-1 relative">
                            <!-- Attachments Menu -->
                            <div class="flex items-center shrink-0">
                                <button 
                                    type="button"
                                    @click="handleAttachmentPicker('image')"
                                    class="p-2.5 rounded-full text-slate-400 hover:text-solar-primary hover:bg-slate-50 dark:hover:bg-white/5 transition-all"
                                    title="Attach image"
                                >
                                    <Paperclip class="h-5 w-5" />
                                </button>
                                <input
                                    ref="imageAttachmentInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="(event) => handleAttachmentChange(event, 'image')"
                                />
                                <input
                                    ref="fileAttachmentInput"
                                    type="file"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
                                    class="hidden"
                                    @change="(event) => handleAttachmentChange(event, 'file')"
                                />
                            </div>

                            <div class="flex-grow flex items-center min-h-[44px]">
                                <input 
                                    v-model="newMessage"
                                    type="text"
                                    placeholder="Message..."
                                    class="w-full bg-transparent border-transparent text-[15px] focus:outline-none focus:ring-0 shadow-none px-2 py-2.5 text-slate-800 dark:text-white placeholder-slate-400"
                                />
                            </div>

                            <div class="shrink-0 pb-0.5">
                                <button 
                                    v-if="newMessage.trim()"
                                    type="submit"
                                    class="h-10 w-10 rounded-full bg-solar-primary hover:bg-solar-primary-active text-white flex items-center justify-center transition-transform active:scale-95"
                                >
                                    <Send class="h-4 w-4 ml-0.5" />
                                </button>
                                <button 
                                    v-else
                                    type="button"
                                    class="h-10 w-10 rounded-full text-slate-400 hover:text-solar-primary hover:bg-slate-50 dark:hover:bg-white/5 flex items-center justify-center transition-all"
                                >
                                    <Mic class="h-5 w-5" />
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Empty Chat placeholder -->
                <div v-else class="flex-grow flex flex-col items-center justify-center text-center bg-slate-50 dark:bg-[#0E1628]">
                    <div class="flex flex-col items-center gap-4">
                        <div class="h-24 w-24 rounded-full bg-solar-primary/10 flex items-center justify-center">
                            <MessageSquare class="h-12 w-12 text-solar-primary" />
                        </div>
                        <div>
                            <h3 class="font-bold text-xl text-slate-800 dark:text-white">Select a chat to start messaging</h3>
                            <p class="text-sm text-slate-500 mt-1 max-w-md">Choose from your existing conversations or start a new one.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Context sidebar (Participant Details) -->
                <div 
                    v-if="activeConversation"
                    class="w-[300px] border-l border-slate-200 dark:border-white/5 flex flex-col shrink-0 bg-white dark:bg-[#151b2b] overflow-y-auto"
                >
                    <div class="p-5 flex flex-col gap-5">
                        <!-- Chat Group Header -->
                        <div class="flex flex-col items-center gap-3 text-center pt-2">
                            <div class="relative w-20 h-20">
                                <img v-if="creatorParticipant" :src="creatorParticipant.avatar" class="w-full h-full rounded-full object-cover border-4 border-slate-100 dark:border-white/5 shadow-sm" />
                            </div>
                            <div>
                                <h4 class="font-bold text-[15px] text-slate-800 dark:text-white">{{ activeConversation.title || activeConversation.participants.map(p => p.name).join(', ') }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">{{ activeConversation.participants.length }} members</p>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-white/5" />

                        <div class="flex items-center justify-between">
                            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-left">Members</h4>
                            <button v-if="isCreator" @click="openNewChat('add')" class="text-[11px] font-bold text-solar-primary hover:text-solar-primary-active transition-colors flex items-center gap-1">
                                <Plus class="h-3 w-3" /> Add
                            </button>
                        </div>
                        
                        <!-- List of participants -->
                        <div class="flex flex-col gap-1">
                            <div 
                                v-for="part in activeConversation.participants" 
                                :key="part.id"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-white/5 text-left transition-colors cursor-pointer group"
                            >
                                <img :src="part.avatar" :alt="part.name" class="h-10 w-10 rounded-full object-cover" />
                                <div class="min-w-0 flex-grow">
                                    <h5 class="font-bold text-[13px] text-slate-800 dark:text-white truncate leading-snug">{{ part.name }}</h5>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <p class="text-[11px] text-slate-500 capitalize">{{ part.role }}</p>
                                        <span v-if="creatorParticipant && part.id === creatorParticipant.id" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-solar-primary/10 text-solar-primary uppercase tracking-wider">Admin</span>
                                    </div>
                                </div>
                                <button 
                                    v-if="part.id !== authUser?.id && isCreator"
                                    @click.stop="removeMember(part)"
                                    class="text-[11px] font-bold text-red-500 hover:text-white hover:bg-red-500 bg-red-50 dark:bg-red-500/10 px-2 py-1 rounded transition-colors"
                                    title="Remove from chat"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-white/5" />

                        <!-- Channel Info -->
                        <div class="flex flex-col gap-2.5 text-left">
                            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                                <ShieldCheck class="h-5 w-5 text-solar-primary shrink-0" />
                                <span>Encrypted</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                                <Star class="h-5 w-5 text-amber-500 shrink-0 fill-amber-500" />
                                <span>SLA Routing</span>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-white/5" />

                        <!-- Actions -->
                        <div class="flex flex-col gap-2">
                            <button 
                                v-if="activeConversation.status === 'active' && isCreator"
                                @click="concludeGroup"
                                :disabled="isProcessing"
                                class="w-full py-2.5 rounded-xl text-sm font-medium text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <CheckCircle2 v-if="!isProcessing" class="h-4 w-4" />
                                <span v-if="isProcessing" class="h-4 w-4 rounded-full border-2 border-emerald-600 border-t-transparent animate-spin"></span>
                                {{ isProcessing ? 'Concluding...' : 'Conclude Chat' }}
                            </button>
                            <button 
                                v-if="isCreator"
                                @click="deleteGroup"
                                :disabled="isProcessing"
                                class="w-full py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                Delete Chat
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- New Chat Modal -->
        <div v-if="showNewChatModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-fade-in">
            <div class="bg-white dark:bg-solar-bg-dark rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-white/5 flex flex-col max-h-[85vh]">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-solar-primary-dark/5">
                    <h3 class="font-extrabold text-sm text-slate-800 dark:text-white">{{ modalMode === 'add' ? 'Add Members' : 'Start New Conversation' }}</h3>
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
                            class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/30 border border-slate-200 dark:border-white/5 text-xs focus:outline-none focus:border-solar-primary transition-all"
                        />
                        <div v-if="isSearching" class="absolute right-3.5 flex items-center justify-center">
                            <span class="h-4 w-4 border-2 border-solar-primary border-t-transparent rounded-full animate-spin"></span>
                        </div>
                    </div>

                    <!-- Selected Participants Chips -->
                    <div v-if="selectedUsers.length > 0" class="flex flex-wrap gap-2">
                        <div v-for="u in selectedUsers" :key="u.id" class="px-2.5 py-1 bg-solar-primary/10 text-solar-primary text-[10px] font-bold rounded-lg flex items-center gap-1.5 border border-solar-primary/20">
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
                            :class="selectedUsers.find(su => su.id === user.id) ? 'bg-solar-primary/5 border-solar-primary' : 'bg-white dark:bg-solar-primary-dark/10 border-slate-100 dark:border-white/5 hover:border-slate-300'"
                        >
                            <img :src="user.avatar" class="w-10 h-10 rounded-full object-cover" />
                            <div class="flex-grow">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-white">{{ user.name }}</h4>
                                <p class="text-[10px] text-slate-500">{{ user.email }} • <span class="uppercase tracking-wider font-bold text-solar-primary">{{ user.role }}</span></p>
                            </div>
                            <div v-if="selectedUsers.find(su => su.id === user.id)" class="text-solar-primary">
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
                        class="px-5 py-2 rounded-lg text-xs font-bold bg-solar-primary text-white hover:bg-solar-primary-active transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        {{ modalMode === 'add' ? 'Add Members' : 'Start Chat' }} <span v-if="selectedUsers.length > 0">({{ selectedUsers.length }})</span>
                    </button>
                </div>
            </div>
        </div>
    </component>
</template>
