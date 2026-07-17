<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { FileText, Plus, Trash2, X, Sparkles, CheckCircle2, Eye, Edit, Search, Upload } from 'lucide-vue-next'

import { router } from '@inertiajs/vue3'

interface BlogPost {
    id: number;
    title: string;
    author: string;
    category: 'News' | 'Guide' | 'Advisory' | 'Announcement';
    date: string;
    views: string;
    status: 'published' | 'draft';
    content: string;
}

const props = defineProps<{
    blogPosts?: BlogPost[]
}>()

const blogPosts = ref<BlogPost[]>(props.blogPosts || [])

const isCreateModalOpen = ref(false)
const searchQuery = ref('')

// Form states
const newTitle = ref('')
const newAuthor = ref('Alex Thompson')
const newCategory = ref<'News' | 'Guide' | 'Advisory' | 'Announcement'>('News')
const newContent = ref('')
const isDraft = ref(true)

const handleCreatePost = () => {
    const payload = {
        title: newTitle.value || 'Untitled Grid Insight',
        author: newAuthor.value,
        category: newCategory.value,
        status: isDraft.value ? 'draft' : 'published',
        content: newContent.value || 'Coming soon...'
    }
    
    router.post('/admin/blog', payload, {
        onSuccess: () => {
            isCreateModalOpen.value = false
            newTitle.value = ''
            newCategory.value = 'News'
            newContent.value = ''
            isDraft.value = true
        }
    })
}

import { useAlert } from '@/composables/useAlert'
const { confirmAlert } = useAlert()

const handleDeletePost = async (id: number) => {
    const confirmed = await confirmAlert(
        'Are you sure you want to delete this blog post?',
        'Confirm Deletion',
        { type: 'danger', confirmText: 'Delete Post' }
    )
    if (confirmed) {
        router.delete(`/admin/blog/${id}`)
    }
}

const toggleStatus = (id: number) => {
    const post = blogPosts.value.find(p => p.id === id)
    if (post) {
        const newStatus = post.status === 'published' ? 'draft' : 'published'
        router.patch(`/admin/blog/${id}/status`, { status: newStatus }, {
            preserveScroll: true
        })
    }
}

const totalPosts = computed(() => blogPosts.value.length)
const publishedCount = computed(() => blogPosts.value.filter(p => p.status === 'published').length)
const draftCount = computed(() => blogPosts.value.filter(p => p.status === 'draft').length)

const filteredPosts = computed(() => {
    if (!searchQuery.value.trim()) return blogPosts.value
    return blogPosts.value.filter(p => p.title.toLowerCase().includes(searchQuery.value.toLowerCase()))
})
</script>

<template>
    <Head title="SolarLink — CMS Content Manager" />

    <DashboardLayout role="admin" title="Editorial CMS">
        <div class="flex flex-col gap-8 text-left relative">
            
            <!-- Create Post Modal Overlay -->
            <div 
                v-if="isCreateModalOpen"
                class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 animate-scale-in"
            >
                <div class="relative w-full max-w-lg bg-white/95 dark:bg-[#0B0F19]/95 backdrop-blur-2xl border border-indigo-500/30 rounded-3xl shadow-[0_0_60px_-15px_rgba(99,102,241,0.4)] overflow-hidden flex flex-col gap-5 max-h-[90vh]">
                    <div class="h-1.5 bg-gradient-to-r from-indigo-600 via-purple-500 to-pink-500 w-full"></div>
                    <div class="px-6 pt-2 pb-6 overflow-y-auto">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-black text-xl text-slate-800 dark:text-white flex items-center gap-2">
                                <Plus class="h-5 w-5 text-indigo-500" />
                                <span>Write New Article</span>
                            </h3>
                        <button @click="isCreateModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5">
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="h-px bg-solar-primary/10 dark:bg-white/5"></div>

                    <form @submit.prevent="handleCreatePost" class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Article Title</label>
                            <input 
                                v-model="newTitle"
                                type="text"
                                required
                                placeholder="e.g. 5 Maintenance Mistakes Installers Avoid"
                                class="h-11 px-4 rounded-xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Author Name</label>
                                <input v-model="newAuthor" type="text" required class="h-11 px-4 rounded-xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</label>
                                <select 
                                    v-model="newCategory"
                                    class="h-11 px-4 rounded-xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white"
                                >
                                    <option value="News">News / Trends</option>
                                    <option value="Guide">Installer Guide</option>
                                    <option value="Advisory">Grid Advisory</option>
                                    <option value="Announcement">Product Announcement</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5 mt-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Featured Image Cover</label>
                            <div class="flex items-center justify-center border border-dashed border-indigo-500/30 h-24 rounded-2xl bg-indigo-50/50 dark:bg-indigo-500/5 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all text-indigo-500 dark:text-indigo-400">
                                <Upload class="h-5 w-5 mr-2" />
                                <span class="text-xs font-bold">Upload Header File (.png or .jpg)</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5 mt-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Post Body / Content</label>
                            <textarea 
                                v-model="newContent"
                                rows="4"
                                placeholder="Write the markdown-supported content body here..."
                                class="p-4 rounded-xl bg-slate-50/80 dark:bg-white/5 border border-slate-200/50 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white resize-none"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-between mt-4 p-4 rounded-2xl border border-slate-200/50 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                            <label class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Publish Immediately?</label>
                            <button 
                                type="button"
                                @click="isDraft = !isDraft"
                                class="px-5 h-9 rounded-xl text-xs font-bold transition-all border"
                                :class="!isDraft ? 'bg-emerald-500 text-white border-emerald-500/20 shadow-[0_0_15px_-3px_rgba(16,185,129,0.4)]' : 'bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 border-transparent'"
                            >
                                {{ !isDraft ? 'Yes, Publish' : 'No, Keep Draft' }}
                            </button>
                        </div>

                        <button 
                            type="submit"
                            class="h-12 w-full mt-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-500 hover:from-indigo-700 hover:to-purple-600 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all duration-300"
                        >
                            Create Editorial Post
                        </button>
                    </form>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2.5 py-1 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 font-extrabold text-[9px] uppercase tracking-widest border border-purple-500/20 shadow-sm animate-pulse-slow">
                    <Sparkles class="h-3 w-3" />
                    <span>Editorial Node</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-1">
                    <div>
                        <h2 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-500 dark:from-indigo-400 dark:to-purple-400 tracking-tight">CMS Articles & Knowledgebase</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">Author training guides, dispatch advisories, and release news articles directly to installation apps.</p>
                    </div>
                    <button 
                        @click="isCreateModalOpen = true"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-500 hover:from-indigo-700 hover:to-purple-600 text-white text-[11px] font-black shadow-[0_0_20px_-5px_rgba(99,102,241,0.4)] transition-all duration-300 uppercase tracking-widest flex items-center gap-1.5"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Create Post</span>
                    </button>
                </div>
            </div>

            <!-- KPI Row (3 cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="glass-card p-5 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200/50 dark:border-white/5 rounded-2xl shadow-sm">
                    <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Total Articles</span>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ totalPosts }} <span class="text-lg text-slate-400 font-bold">Posts</span></h3>
                    <p class="text-[10px] text-slate-500 mt-2 uppercase tracking-wider font-bold">Published: {{ publishedCount }} | Drafts: {{ draftCount }}</p>
                </div>
                <div class="glass-card p-5 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200/50 dark:border-white/5 rounded-2xl shadow-sm">
                    <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest">CMS Views Metric</span>
                    <h3 class="text-3xl font-black text-emerald-500 mt-1">8.4k <span class="text-lg text-emerald-500/70 font-bold">Total</span></h3>
                    <p class="text-[10px] text-slate-500 mt-2 uppercase tracking-wider font-bold">Average: 1.6k per post</p>
                </div>
                <div class="glass-card p-5 bg-white/60 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200/50 dark:border-white/5 rounded-2xl shadow-sm">
                    <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Active Editors</span>
                    <h3 class="text-3xl font-black text-indigo-500 dark:text-indigo-400 mt-1">4 <span class="text-lg text-indigo-500/70 font-bold">Authors</span></h3>
                    <p class="text-[10px] text-slate-500 mt-2 uppercase tracking-wider font-bold">Verification compliance met</p>
                </div>
            </div>

            <!-- Search Filter -->
            <div class="relative flex items-center max-w-sm">
                <Search class="absolute left-3.5 h-4 w-4 text-slate-400 pointer-events-none" />
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Search posts by title..."
                    class="w-full h-11 pl-10 pr-4 rounded-xl bg-white/80 dark:bg-[#0B0F19]/60 backdrop-blur-md border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-slate-800 dark:text-white shadow-sm"
                />
            </div>

            <!-- Blog Posts CMS Table -->
            <div class="glass-card overflow-hidden bg-white/60 dark:bg-[#0B0F19]/70 backdrop-blur-2xl border border-indigo-500/20 dark:border-white/5 rounded-3xl shadow-[0_0_40px_-15px_rgba(99,102,241,0.2)]">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-indigo-500/5 border-b border-indigo-500/10 text-left text-slate-850 dark:text-white font-black">
                            <th class="p-4">Article Title</th>
                            <th class="p-4">Author</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Views</th>
                            <th class="p-4">Publication Date</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-if="filteredPosts.length === 0">
                            <td colspan="7" class="p-8 text-center text-slate-400 font-bold">
                                No matching blog posts found.
                            </td>
                        </tr>
                        <tr v-for="post in filteredPosts" :key="post.id" class="hover:bg-slate-50/50 dark:hover:bg-indigo-900/10 transition-all">
                            <td class="p-4 font-bold text-slate-850 dark:text-white truncate max-w-[220px]" :title="post.title">
                                {{ post.title }}
                            </td>
                            <td class="p-4 font-semibold">{{ post.author }}</td>
                            <td class="p-4 uppercase tracking-wider font-bold text-[9px] text-indigo-500 dark:text-indigo-400">
                                {{ post.category }}
                            </td>
                            <td class="p-4 font-mono font-bold">{{ post.views }} <span class="text-slate-400">views</span></td>
                            <td class="p-4 font-semibold text-slate-450">{{ post.date }}</td>
                            <td class="p-4">
                                <button 
                                    @click="toggleStatus(post.id)"
                                    class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider hover:scale-105 transition-all shadow-sm"
                                    :class="post.status === 'published' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300'"
                                >
                                    {{ post.status }}
                                </button>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button class="p-2 rounded-lg border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/10 transition-all" title="Edit Article">
                                        <Edit class="h-3.5 w-3.5" />
                                    </button>
                                    <button 
                                        @click="handleDeletePost(post.id)"
                                        class="p-2.5 rounded-lg border border-red-500/20 text-red-500 hover:bg-red-500/10 transition-all"
                                        title="Delete Post"
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

<style scoped>
@keyframes scale-in {
    from {
        transform: scale(0.92);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
</style>
