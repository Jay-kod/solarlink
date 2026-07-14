<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { FileText, Plus, Trash2, X, Sparkles, CheckCircle2, Eye, Edit, Search, Upload } from 'lucide-vue-next'

interface BlogPost {
    id: number;
    title: string;
    author: string;
    category: 'News' | 'Guide' | 'Advisory' | 'Announcement';
    date: string;
    views: string;
    status: 'published' | 'draft';
}

const blogPosts = ref<BlogPost[]>([
    { id: 801, title: 'How to Prevent Panel Dust Build-Up This Summer', author: 'Dr. Evelyn Carter', category: 'Guide', date: 'May 24, 2026', views: '2.4k', status: 'published' },
    { id: 802, title: 'Why LFP Battery Stacking is Changing Home Energy Storage', author: 'Siddharth Patel', category: 'News', date: 'May 20, 2026', views: '1.8k', status: 'published' },
    { id: 803, title: 'Net Metering Policies Update - California NEMA 3.0 Guidelines', author: 'Alex Thompson', category: 'Advisory', date: 'May 18, 2026', views: '3.1k', status: 'published' },
    { id: 804, title: 'Upcoming App Integration with smart telemetry arrays', author: 'Grace Hopper', category: 'Announcement', date: 'May 15, 2026', views: '720', status: 'draft' },
    { id: 805, title: 'Winterizing your solar power setups: Critical checklist', author: 'Dr. Evelyn Carter', category: 'Guide', date: 'Jan 10, 2026', views: '430', status: 'draft' }
])

const isCreateModalOpen = ref(false)
const searchQuery = ref('')

// Form states
const newTitle = ref('')
const newAuthor = ref('Alex Thompson')
const newCategory = ref<'News' | 'Guide' | 'Advisory' | 'Announcement'>('News')
const newContent = ref('')
const isDraft = ref(true)

const handleCreatePost = () => {
    const freshPost: BlogPost = {
        id: blogPosts.value.length + 801,
        title: newTitle.value || 'Untitled Grid Insight',
        author: newAuthor.value,
        category: newCategory.value,
        status: isDraft.value ? 'draft' : 'published',
        date: 'Today',
        views: '0'
    }
    blogPosts.value.unshift(freshPost)
    isCreateModalOpen.value = false
    
    // reset form
    newTitle.value = ''
    newCategory.value = 'News'
    newContent.value = ''
    isDraft.value = true
}

const handleDeletePost = (id: number) => {
    if (confirm('Are you sure you want to delete this blog post?')) {
        blogPosts.value = blogPosts.value.filter(p => p.id !== id)
    }
}

const toggleStatus = (id: number) => {
    const post = blogPosts.value.find(p => p.id === id)
    if (post) {
        post.status = post.status === 'published' ? 'draft' : 'published'
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
                class="fixed inset-0 z-50 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4 animate-fade-in-up"
            >
                <div class="glass-card p-6 flex flex-col gap-5 w-full max-w-lg bg-white dark:bg-solar-bg-dark border border-solar-primary/15 rounded-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between">
                        <h3 class="font-extrabold text-base text-slate-800 dark:text-white flex items-center gap-2">
                            <Plus class="h-5 w-5 text-solar-primary" />
                            <span>Write New Article</span>
                        </h3>
                        <button @click="isCreateModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5">
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="h-px bg-solar-primary/10 dark:bg-white/5"></div>

                    <form @submit.prevent="handleCreatePost" class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Article Title</label>
                            <input 
                                v-model="newTitle"
                                type="text"
                                required
                                placeholder="e.g. 5 Maintenance Mistakes Installers Avoid"
                                class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Author Name</label>
                                <input v-model="newAuthor" type="text" required class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white" />
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Category</label>
                                <select 
                                    v-model="newCategory"
                                    class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                                >
                                    <option value="News">News / Trends</option>
                                    <option value="Guide">Installer Guide</option>
                                    <option value="Advisory">Grid Advisory</option>
                                    <option value="Announcement">Product Announcement</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Featured Image Cover</label>
                            <div class="flex items-center justify-center border border-dashed border-slate-200 dark:border-white/5 h-20 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 cursor-pointer hover:bg-slate-100 dark:hover:bg-solar-primary-dark/30 transition-all text-slate-450">
                                <Upload class="h-5 w-5 mr-2" />
                                <span class="text-xs font-bold">Upload Header File (.png or .jpg)</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Post Body / Content</label>
                            <textarea 
                                v-model="newContent"
                                rows="4"
                                placeholder="Write the markdown-supported content body here..."
                                class="p-3 rounded-xl bg-slate-50 dark:bg-solar-primary-dark/20 border border-slate-200 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-800 dark:text-white"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-between mt-1">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Publish Immediately?</label>
                            <button 
                                type="button"
                                @click="isDraft = !isDraft"
                                class="px-4 h-9 rounded-xl text-xs font-bold transition-all border"
                                :class="!isDraft ? 'bg-solar-success text-white border-solar-success/20' : 'bg-slate-100 dark:bg-white/5 text-slate-500 border-transparent'"
                            >
                                {{ !isDraft ? 'Yes, Publish' : 'No, Keep Draft' }}
                            </button>
                        </div>

                        <button 
                            type="submit"
                            class="h-11 w-full rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow mt-2"
                        >
                            Create Editorial Post
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <div class="inline-flex items-center gap-1.5 self-start px-2 py-0.5 rounded bg-solar-primary-light dark:bg-solar-primary-dark text-solar-primary dark:text-solar-primary-accent font-bold text-[9px] uppercase tracking-wider">
                    <Sparkles class="h-3 w-3" />
                    <span>Editorial Node</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800 dark:text-white">CMS Articles & Knowledgebase</h2>
                        <p class="text-xs text-slate-450 mt-0.5">Author training guides, dispatch advisories, and release news articles directly to installation apps.</p>
                    </div>
                    <button 
                        @click="isCreateModalOpen = true"
                        class="px-5 py-2.5 rounded-xl bg-solar-primary hover:bg-solar-primary-active text-white text-xs font-bold shadow-solar hover:shadow-solar-glow transition-all duration-300 btn-glow uppercase tracking-wider flex items-center gap-1.5"
                    >
                        <Plus class="h-4 w-4" />
                        <span>Create Post</span>
                    </button>
                </div>
            </div>

            <!-- KPI Row (3 cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Total Articles</span>
                    <h3 class="text-2xl font-extrabold text-slate-850 dark:text-white mt-1.5">{{ totalPosts }} Posts</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">Published: {{ publishedCount }} | Drafts: {{ draftCount }}</p>
                </div>
                <div class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider font-bold">CMS Views Metric</span>
                    <h3 class="text-2xl font-extrabold text-solar-success mt-1.5">8.4k Total</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">Average: 1.6k per post</p>
                </div>
                <div class="glass-card p-5 bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-xl">
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Active Editors</span>
                    <h3 class="text-2xl font-extrabold text-solar-primary dark:text-solar-primary-accent mt-1.5">4 Authors</h3>
                    <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">Verification compliance met</p>
                </div>
            </div>

            <!-- Search Filter -->
            <div class="relative flex items-center max-w-sm">
                <Search class="absolute left-3.5 h-4 w-4 text-slate-400 pointer-events-none" />
                <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Search posts by title..."
                    class="w-full h-11 pl-10 pr-4 rounded-xl bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 text-xs font-semibold focus:outline-none focus:border-solar-primary transition-all text-slate-850 dark:text-white shadow-sm"
                />
            </div>

            <!-- Blog Posts CMS Table -->
            <div class="glass-card overflow-hidden bg-white dark:bg-solar-bg-dark/40 border border-solar-primary/10 dark:border-white/5 rounded-2xl shadow-solar">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-solar-primary/5 border-b border-solar-primary/10 text-left text-slate-850 dark:text-white font-bold">
                            <th class="p-4">Article Title</th>
                            <th class="p-4">Author</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Views</th>
                            <th class="p-4">Publication Date</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-solar-primary/5 text-xs text-slate-650 dark:text-slate-350">
                        <tr v-if="filteredPosts.length === 0">
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                No matching blog posts found.
                            </td>
                        </tr>
                        <tr v-for="post in filteredPosts" :key="post.id" class="hover:bg-slate-50/50 dark:hover:bg-solar-primary-dark/10 transition-all">
                            <td class="p-4 font-bold text-slate-850 dark:text-white truncate max-w-[220px]" :title="post.title">
                                {{ post.title }}
                            </td>
                            <td class="p-4 font-semibold">{{ post.author }}</td>
                            <td class="p-4 uppercase tracking-wider font-semibold text-[9px] text-solar-primary dark:text-solar-primary-accent">
                                {{ post.category }}
                            </td>
                            <td class="p-4 font-mono">{{ post.views }} views</td>
                            <td class="p-4 font-semibold text-slate-450">{{ post.date }}</td>
                            <td class="p-4">
                                <button 
                                    @click="toggleStatus(post.id)"
                                    class="px-2.5 py-0.5 rounded-full font-bold text-[8px] uppercase tracking-wider hover:scale-105 transition-all"
                                    :class="post.status === 'published' ? 'bg-solar-success/15 text-solar-success' : 'bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400'"
                                >
                                    {{ post.status }}
                                </button>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center gap-1.5 justify-end">
                                    <button class="p-2 rounded-lg border border-solar-primary/10 text-solar-primary hover:bg-solar-primary/10" title="Edit Article">
                                        <Edit class="h-3.5 w-3.5" />
                                    </button>
                                    <button 
                                        @click="handleDeletePost(post.id)"
                                        class="p-2.5 rounded-lg border border-solar-danger/10 text-solar-danger hover:bg-solar-danger/10"
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
