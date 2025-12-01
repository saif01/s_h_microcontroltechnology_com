<template>
    <div class="blog-page-modern">
        <!-- Hero Section -->
        <section
            class="mb-10 page-hero position-relative d-flex align-center justify-center text-center overflow-hidden">
            <div class="hero-bg-gradient"></div>
            <div class="hero-pattern"></div>

            <!-- Animated gradient orbs -->
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>

            <v-container class="position-relative z-index-2">
                <v-fade-transition appear>
                    <div>
                        <div class="glass-pill d-inline-flex align-center px-5 py-3 rounded-pill mb-8 animate-float">
                            <div class="pulse-dot mr-2"></div>
                            <v-icon icon="mdi-book-open-page-variant" color="amber-accent-4" size="small"
                                class="mr-2"></v-icon>
                            <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">LATEST
                                INSIGHTS</span>
                        </div>
                        <h1
                            class="text-h4 text-lg-h2 font-weight-black text-white mb-6 lh-tight text-shadow-sm animate-slide-up">
                            Our <span class="text-amber-accent-3">Blog</span>
                        </h1>
                        <p
                            class="text-h6 text-white opacity-90 mw-700 mx-auto font-weight-light animate-slide-up-delay">
                            Stay updated with the latest news, insights, and updates from our team.
                        </p>
                    </div>
                </v-fade-transition>
            </v-container>
        </section>

        <!-- Main Content -->
        <section class="py-12 bg-grey-lighten-5 min-vh-100">
            <v-container>
                <!-- Search and Filter Bar -->
                <v-card class="mb-6" elevation="2">
                    <v-card-text class="pa-4">
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field v-model="searchQuery" label="Search posts"
                                    prepend-inner-icon="mdi-magnify" variant="outlined" density="compact" clearable
                                    @update:model-value="handleSearch"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select v-model="selectedCategory" :items="categoryOptions" label="Category"
                                    prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                                    @update:model-value="loadPosts"></v-select>
                            </v-col>
                            <v-col cols="12" md="3">
                                <v-select v-model="sortBy" :items="sortOptions" label="Sort by"
                                    prepend-inner-icon="mdi-sort" variant="outlined" density="compact"
                                    @update:model-value="loadPosts"></v-select>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>

                <!-- Categories Sidebar (Desktop) -->
                <v-row>
                    <v-col cols="12" md="3" class="d-none d-md-block">
                        <v-card class="mb-6" elevation="2" sticky>
                            <v-card-title class="bg-primary text-white">
                                <v-icon icon="mdi-folder" class="mr-2"></v-icon>
                                Categories
                            </v-card-title>
                            <v-card-text class="pa-0">
                                <v-list density="compact">
                                    <v-list-item :active="!selectedCategory" @click="setCategory(null)"
                                        class="cursor-pointer">
                                        <v-list-item-title>All Categories</v-list-item-title>
                                    </v-list-item>
                                    <v-list-item v-for="category in categories" :key="category.id"
                                        :active="selectedCategory === category.slug" @click="setCategory(category.slug)"
                                        class="cursor-pointer">
                                        <v-list-item-title>{{ category.name }}</v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <!-- Blog Posts Grid -->
                    <v-col cols="12" md="9">
                        <!-- Loading State -->
                        <div v-if="loading" class="text-center py-16">
                            <v-progress-circular indeterminate color="primary" size="64" />
                            <p class="text-body-1 text-medium-emphasis mt-4">Loading blog posts...</p>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="posts.length === 0" class="text-center py-16">
                            <v-icon icon="mdi-book-open-variant" size="64" color="grey-lighten-1" class="mb-4" />
                            <h3 class="text-h6 font-weight-bold text-grey-darken-2 mb-2">No blog posts found</h3>
                            <p class="text-body-2 text-medium-emphasis mb-6">
                                Try adjusting your search or filters.
                            </p>
                            <v-btn variant="outlined" color="primary" @click="clearFilters">
                                Clear Filters
                            </v-btn>
                        </div>

                        <!-- Posts Grid -->
                        <v-row v-else>
                            <v-col v-for="post in posts" :key="post.id" cols="12" sm="6" lg="4">
                                <BlogCard :post="post" />
                            </v-col>
                        </v-row>

                        <!-- Pagination -->
                        <div v-if="pagination.last_page > 1" class="d-flex justify-center mt-8">
                            <v-pagination v-model="currentPage" :length="pagination.last_page" :total-visible="7"
                                density="comfortable" @update:model-value="loadPosts"></v-pagination>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import BlogCard from './BlogCard.vue';

const posts = ref([]);
const categories = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const selectedCategory = ref(null);
const sortBy = ref('published_at');
const currentPage = ref(1);
const pagination = ref({
    total: 0,
    last_page: 1,
    per_page: 12,
    current_page: 1
});

const categoryOptions = computed(() => {
    return categories.value.map(cat => ({
        title: cat.name,
        value: cat.slug
    }));
});

const sortOptions = [
    { title: 'Latest First', value: 'published_at' },
    { title: 'Oldest First', value: 'published_at_asc' },
    { title: 'Most Views', value: 'views' },
    { title: 'Title A-Z', value: 'title' }
];

const loadPosts = async () => {
    try {
        loading.value = true;
        const params = {
            page: currentPage.value,
            per_page: 12
        };

        if (searchQuery.value) {
            params.search = searchQuery.value;
        }

        if (selectedCategory.value) {
            params.category = selectedCategory.value;
        }

        if (sortBy.value === 'published_at_asc') {
            params.sort_by = 'published_at';
            params.sort_direction = 'asc';
        } else {
            params.sort_by = sortBy.value;
            params.sort_direction = sortBy.value === 'title' ? 'asc' : 'desc';
        }

        const response = await this.$axios.get('/api/openapi/blog', { params });
        posts.value = response.data.data || [];
        pagination.value = {
            total: response.data.total || 0,
            last_page: response.data.last_page || 1,
            per_page: response.data.per_page || 12,
            current_page: response.data.current_page || 1
        };
        currentPage.value = response.data.current_page || 1;
    } catch (error) {
        console.error('Error loading blog posts:', error);
        posts.value = [];
    } finally {
        loading.value = false;
    }
};

const loadCategories = async () => {
    try {
        const response = await this.$axios.get('/api/openapi/blog/categories');
        categories.value = response.data || [];
    } catch (error) {
        console.error('Error loading categories:', error);
        // Set empty array on error so the page still works
        categories.value = [];
        // Optionally show a user-friendly message
        if (error.response?.status !== 404) {
            console.warn('Categories could not be loaded. The page will continue to work without category filtering.');
        }
    }
};

const setCategory = (slug) => {
    selectedCategory.value = slug;
    currentPage.value = 1;
    loadPosts();
};

const handleSearch = () => {
    currentPage.value = 1;
    loadPosts();
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = null;
    sortBy.value = 'published_at';
    currentPage.value = 1;
    loadPosts();
};

onMounted(async () => {
    await Promise.all([loadCategories(), loadPosts()]);
});
</script>

<style scoped>
.blog-page-modern {
    min-height: 100vh;
}

.page-hero {
    min-height: 400px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}

.hero-bg-gradient {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
    z-index: 1;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 2px 2px, rgba(255, 255, 255, 0.15) 1px, transparent 0);
    background-size: 40px 40px;
    z-index: 1;
}

.gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.3;
    animation: float 20s infinite ease-in-out;
    z-index: 1;
}

.orb-1 {
    width: 300px;
    height: 300px;
    background: rgba(255, 193, 7, 0.5);
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.orb-2 {
    width: 250px;
    height: 250px;
    background: rgba(33, 150, 243, 0.5);
    top: 60%;
    right: 15%;
    animation-delay: 5s;
}

.orb-3 {
    width: 200px;
    height: 200px;
    background: rgba(156, 39, 176, 0.5);
    bottom: 20%;
    left: 50%;
    animation-delay: 10s;
}

@keyframes float {

    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    33% {
        transform: translate(30px, -30px) scale(1.1);
    }

    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.glass-pill {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.pulse-dot {
    width: 8px;
    height: 8px;
    background: #ffc107;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.5;
        transform: scale(1.2);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out;
}

.animate-slide-up-delay {
    animation: slideUp 0.8s ease-out 0.2s both;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.text-shadow-sm {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.mw-700 {
    max-width: 700px;
}

.cursor-pointer {
    cursor: pointer;
}

/* Responsive Styles */
@media (max-width: 960px) {
    .page-hero {
        min-height: 400px !important;
        padding: 48px 16px !important;
    }

    .page-hero h1 {
        font-size: 2rem !important;
        line-height: 1.2 !important;
        margin-bottom: 16px !important;
    }

    .page-hero p {
        font-size: 1.1rem !important;
        line-height: 1.5 !important;
    }

    .glass-pill {
        padding: 10px 20px !important;
        margin-bottom: 24px !important;
    }

    .glass-pill span {
        font-size: 0.75rem !important;
    }

    .gradient-orb {
        width: 200px !important;
        height: 200px !important;
    }
}

@media (max-width: 600px) {
    .page-hero {
        min-height: 350px !important;
        padding: 32px 12px !important;
    }

    .page-hero h1 {
        font-size: 1.75rem !important;
        line-height: 1.3 !important;
        margin-bottom: 12px !important;
    }

    .page-hero p {
        font-size: 1rem !important;
        line-height: 1.4 !important;
        padding: 0 8px !important;
    }

    .glass-pill {
        padding: 8px 16px !important;
        margin-bottom: 20px !important;
    }

    .glass-pill .v-icon {
        font-size: 16px !important;
    }

    .glass-pill span {
        font-size: 0.7rem !important;
    }

    .gradient-orb {
        width: 150px !important;
        height: 150px !important;
    }

    .orb-1 {
        top: -75px !important;
        left: -75px !important;
    }

    .orb-2 {
        bottom: -75px !important;
        right: -75px !important;
    }

    .orb-3 {
        top: 50% !important;
        right: -75px !important;
    }
}
</style>
