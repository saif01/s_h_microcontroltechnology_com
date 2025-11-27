<template>
    <div class="blog-detail-page bg-grey-lighten-5 min-vh-100 pb-16">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-16">
            <v-progress-circular indeterminate color="primary" size="64" />
            <p class="text-body-1 text-medium-emphasis mt-4">Loading blog post...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-16">
            <v-icon icon="mdi-alert-circle" size="64" color="error" class="mb-4" />
            <h3 class="text-h6 font-weight-bold mb-2">Post Not Found</h3>
            <p class="text-body-2 text-medium-emphasis mb-6">{{ error }}</p>
            <v-btn color="primary" :to="{ name: 'Blog' }">Back to Blog</v-btn>
        </div>

        <!-- Blog Post Content -->
        <div v-else-if="post">
            <!-- Hero Section -->
            <section 
                v-if="post.featured_image" 
                class="blog-hero position-relative d-flex align-end overflow-hidden"
                style="min-height: 500px;"
            >
                <v-img 
                    :src="post.featured_image" 
                    cover 
                    class="position-absolute top-0 left-0 w-100 h-100"
                >
                    <div class="hero-overlay position-absolute top-0 left-0 w-100 h-100"></div>
                </v-img>

                <v-container class="position-relative z-index-2 pb-8">
                    <v-breadcrumbs :items="breadcrumbs" class="px-0 mb-4">
                        <template v-slot:divider>
                            <v-icon icon="mdi-chevron-right" size="small" color="white"></v-icon>
                        </template>
                        <template v-slot:title="{ item }">
                            <span :class="item.disabled ? 'text-grey-lighten-2' : 'text-white'">
                                {{ item.title }}
                            </span>
                        </template>
                    </v-breadcrumbs>

                    <div class="d-flex align-center gap-3 mb-4 flex-wrap">
                        <v-chip 
                            v-for="category in post.categories" 
                            :key="category.id"
                            color="white" 
                            variant="flat" 
                            size="small"
                            class="font-weight-bold"
                        >
                            {{ category.name }}
                        </v-chip>
                    </div>

                    <h1 class="text-h4 text-lg-h3 font-weight-black text-white mb-4 lh-tight text-shadow-sm">
                        {{ post.title }}
                    </h1>

                    <div class="d-flex align-center gap-4 flex-wrap text-white">
                        <div v-if="post.author" class="d-flex align-center gap-2">
                            <v-avatar size="32" color="white">
                                <v-icon icon="mdi-account" color="primary" size="small" />
                            </v-avatar>
                            <span class="text-body-2 font-weight-medium">
                                {{ post.author.name || post.author.email }}
                            </span>
                        </div>
                        <div v-if="post.published_at" class="d-flex align-center gap-2">
                            <v-icon icon="mdi-calendar" size="small" />
                            <span class="text-body-2">{{ formatDate(post.published_at) }}</span>
                        </div>
                        <div v-if="post.views" class="d-flex align-center gap-2">
                            <v-icon icon="mdi-eye" size="small" />
                            <span class="text-body-2">{{ formatViews(post.views) }} views</span>
                        </div>
                    </div>
                </v-container>
            </section>

            <!-- Content Section -->
            <v-container class="mt-8">
                <v-row>
                    <!-- Main Content -->
                    <v-col cols="12" md="8">
                        <v-card class="blog-content-card pa-6 mb-6" elevation="2">
                            <!-- Meta Info (if no featured image) -->
                            <div v-if="!post.featured_image" class="mb-6">
                                <v-breadcrumbs :items="breadcrumbs" class="px-0 mb-4">
                                    <template v-slot:divider>
                                        <v-icon icon="mdi-chevron-right" size="small"></v-icon>
                                    </template>
                                </v-breadcrumbs>

                                <div class="d-flex align-center gap-3 mb-4 flex-wrap">
                                    <v-chip 
                                        v-for="category in post.categories" 
                                        :key="category.id"
                                        color="primary" 
                                        variant="flat" 
                                        size="small"
                                        class="font-weight-bold"
                                    >
                                        {{ category.name }}
                                    </v-chip>
                                </div>

                                <h1 class="text-h4 text-lg-h3 font-weight-black mb-4 lh-tight">
                                    {{ post.title }}
                                </h1>

                                <div class="d-flex align-center gap-4 flex-wrap mb-4">
                                    <div v-if="post.author" class="d-flex align-center gap-2">
                                        <v-avatar size="32" color="primary">
                                            <v-icon icon="mdi-account" color="white" size="small" />
                                        </v-avatar>
                                        <span class="text-body-2 font-weight-medium">
                                            {{ post.author.name || post.author.email }}
                                        </span>
                                    </div>
                                    <div v-if="post.published_at" class="d-flex align-center gap-2 text-medium-emphasis">
                                        <v-icon icon="mdi-calendar" size="small" />
                                        <span class="text-body-2">{{ formatDate(post.published_at) }}</span>
                                    </div>
                                    <div v-if="post.views" class="d-flex align-center gap-2 text-medium-emphasis">
                                        <v-icon icon="mdi-eye" size="small" />
                                        <span class="text-body-2">{{ formatViews(post.views) }} views</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <div v-if="post.excerpt" class="text-h6 text-medium-emphasis mb-6 font-weight-light">
                                {{ post.excerpt }}
                            </div>

                            <!-- Content -->
                            <div class="blog-content" v-html="post.content"></div>

                            <!-- Tags -->
                            <div v-if="post.tags && post.tags.length > 0" class="mt-8 pt-6 border-top">
                                <div class="d-flex align-center gap-2 mb-3">
                                    <v-icon icon="mdi-tag" size="small" />
                                    <span class="text-body-2 font-weight-bold">Tags:</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <v-chip 
                                        v-for="tag in post.tags" 
                                        :key="tag.id"
                                        size="small" 
                                        variant="outlined"
                                        color="primary"
                                    >
                                        {{ tag.name }}
                                    </v-chip>
                                </div>
                            </div>

                            <!-- Share Section -->
                            <div class="mt-8 pt-6 border-top">
                                <div class="d-flex align-center gap-2 mb-4">
                                    <v-icon icon="mdi-share-variant" size="small" />
                                    <span class="text-body-2 font-weight-bold">Share this post:</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <v-btn
                                        icon="mdi-facebook"
                                        variant="outlined"
                                        color="primary"
                                        size="small"
                                        @click="shareOnFacebook"
                                    ></v-btn>
                                    <v-btn
                                        icon="mdi-twitter"
                                        variant="outlined"
                                        color="primary"
                                        size="small"
                                        @click="shareOnTwitter"
                                    ></v-btn>
                                    <v-btn
                                        icon="mdi-linkedin"
                                        variant="outlined"
                                        color="primary"
                                        size="small"
                                        @click="shareOnLinkedIn"
                                    ></v-btn>
                                    <v-btn
                                        icon="mdi-content-copy"
                                        variant="outlined"
                                        color="primary"
                                        size="small"
                                        @click="copyLink"
                                    ></v-btn>
                                </div>
                            </div>
                        </v-card>
                    </v-col>

                    <!-- Sidebar -->
                    <v-col cols="12" md="4">
                        <!-- Author Card -->
                        <v-card v-if="post.author" class="mb-6" elevation="2">
                            <v-card-title class="bg-primary text-white">
                                <v-icon icon="mdi-account" class="mr-2"></v-icon>
                                About the Author
                            </v-card-title>
                            <v-card-text class="pa-4">
                                <div class="text-center">
                                    <v-avatar size="80" color="primary" class="mb-3">
                                        <v-icon icon="mdi-account" color="white" size="large" />
                                    </v-avatar>
                                    <h3 class="text-h6 font-weight-bold mb-2">
                                        {{ post.author.name || post.author.email }}
                                    </h3>
                                    <p class="text-body-2 text-medium-emphasis">
                                        Blog author and content creator
                                    </p>
                                </div>
                            </v-card-text>
                        </v-card>

                        <!-- Related Posts (Placeholder) -->
                        <v-card class="mb-6" elevation="2">
                            <v-card-title class="bg-primary text-white">
                                <v-icon icon="mdi-book-open-variant" class="mr-2"></v-icon>
                                Related Posts
                            </v-card-title>
                            <v-card-text class="pa-4">
                                <p class="text-body-2 text-medium-emphasis text-center">
                                    Related posts will appear here
                                </p>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();

const post = ref(null);
const loading = ref(true);
const error = ref(null);

const breadcrumbs = computed(() => {
    return [
        { title: 'Home', to: '/', disabled: false },
        { title: 'Blog', to: '/blog', disabled: false },
        { title: post.value?.title || 'Post', disabled: true }
    ];
});

const loadPost = async () => {
    try {
        loading.value = true;
        error.value = null;
        const slug = route.params.slug;
        const response = await this.$axios.get(`/api/openapi/blog/${slug}`);
        post.value = response.data;
    } catch (err) {
        console.error('Error loading blog post:', err);
        if (err.response?.status === 404) {
            error.value = 'The blog post you are looking for does not exist.';
        } else {
            error.value = 'Failed to load blog post. Please try again later.';
        }
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
};

const formatViews = (views) => {
    if (!views) return '0';
    if (views >= 1000) {
        return (views / 1000).toFixed(1) + 'K';
    }
    return views.toString();
};

const shareOnFacebook = () => {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
};

const shareOnTwitter = () => {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(post.value?.title || '');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
};

const shareOnLinkedIn = () => {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
};

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        // You could show a snackbar here
        alert('Link copied to clipboard!');
    } catch (err) {
        console.error('Failed to copy link:', err);
    }
};

onMounted(() => {
    loadPost();
});
</script>

<style scoped>
.blog-detail-page {
    min-height: 100vh;
}

.blog-hero {
    position: relative;
}

.hero-overlay {
    background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
}

.text-shadow-sm {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.blog-content-card {
    border-radius: 12px;
}

.blog-content {
    line-height: 1.8;
    font-size: 1.1rem;
    color: rgb(var(--v-theme-on-surface));
}

.blog-content :deep(h1),
.blog-content :deep(h2),
.blog-content :deep(h3),
.blog-content :deep(h4),
.blog-content :deep(h5),
.blog-content :deep(h6) {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 700;
    line-height: 1.3;
}

.blog-content :deep(h1) {
    font-size: 2.5rem;
}

.blog-content :deep(h2) {
    font-size: 2rem;
}

.blog-content :deep(h3) {
    font-size: 1.75rem;
}

.blog-content :deep(p) {
    margin-bottom: 1.5rem;
}

.blog-content :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 2rem 0;
}

.blog-content :deep(ul),
.blog-content :deep(ol) {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.blog-content :deep(li) {
    margin-bottom: 0.5rem;
}

.blog-content :deep(blockquote) {
    border-left: 4px solid rgb(var(--v-theme-primary));
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: rgb(var(--v-theme-on-surface-variant));
}

.blog-content :deep(code) {
    background: rgba(var(--v-theme-surface-variant), 0.5);
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}

.blog-content :deep(pre) {
    background: rgba(var(--v-theme-surface-variant), 0.5);
    padding: 1rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 2rem 0;
}

.blog-content :deep(pre code) {
    background: none;
    padding: 0;
}
</style>

