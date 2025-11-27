<template>
    <div class="admin-about-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-title-section">
                    <v-icon icon="mdi-information-outline" size="32" color="primary" class="header-icon"></v-icon>
                    <div>
                        <h1 class="text-h4 page-title">About Page Management</h1>
                        <p class="text-body-2 text-medium-emphasis page-subtitle">Manage and preview your about page
                            content</p>
                    </div>
                </div>
                <v-btn color="primary" prepend-icon="mdi-pencil" @click="openDialog()" class="edit-button" size="large"
                    elevation="2">
                    Edit About Content
                </v-btn>
            </div>
        </div>

        <!-- Loading State -->
        <v-card v-if="loading" class="content-card" elevation="2">
            <v-card-text>
                <div class="loading-container">
                    <v-progress-circular indeterminate color="primary" size="64" width="6"></v-progress-circular>
                    <p class="mt-6 text-h6">Loading about content...</p>
                    <p class="mt-2 text-body-2 text-medium-emphasis">Please wait while we fetch your content</p>
                </div>
            </v-card-text>
        </v-card>

        <!-- Error State -->
        <v-alert v-else-if="error" type="error" variant="tonal" class="mb-4" prominent>
            <template v-slot:prepend>
                <v-icon icon="mdi-alert-circle" size="24"></v-icon>
            </template>
            <div class="text-h6 mb-2">Error Loading Content</div>
            <div>{{ error }}</div>
        </v-alert>

        <!-- Content Preview -->
        <div v-else-if="aboutData" class="content-preview">
            <!-- Status Banner -->
            <v-card class="status-banner mb-6" elevation="2">
                <v-card-text class="d-flex align-center justify-space-between pa-4">
                    <div class="d-flex align-center">
                        <v-icon icon="mdi-check-circle" color="success" size="28" class="mr-3"></v-icon>
                        <div>
                            <div class="text-h6 font-weight-bold">Content Active</div>
                            <div class="text-body-2 text-medium-emphasis">Your about page content is live and ready
                            </div>
                        </div>
                    </div>
                    <v-chip color="success" size="large" prepend-icon="mdi-check">
                        Content Loaded
                    </v-chip>
                </v-card-text>
            </v-card>

            <!-- Hero Section Preview -->
            <v-card class="section-card hero-section mb-6" elevation="3">
                <div class="section-header">
                    <div class="section-icon-wrapper hero-icon">
                        <v-icon icon="mdi-image-text" size="24"></v-icon>
                    </div>
                    <div class="section-title-wrapper">
                        <h2 class="text-h5 section-title">Hero Section</h2>
                        <p class="text-body-2 text-medium-emphasis section-subtitle">Main banner content</p>
                    </div>
                </div>
                <v-divider class="my-4"></v-divider>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="6">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-format-text" size="16" class="mr-1"></v-icon>
                                    Overline
                                </div>
                                <div class="content-value">{{ aboutData.hero?.overline || 'Not set' }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-format-title" size="16" class="mr-1"></v-icon>
                                    Title
                                </div>
                                <div class="content-value">{{ aboutData.hero?.title || 'Not set' }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-subtitles" size="16" class="mr-1"></v-icon>
                                    Subtitle
                                </div>
                                <div class="content-value">{{ aboutData.hero?.subtitle || 'Not set' }}</div>
                            </div>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Story Section Preview -->
            <v-card v-if="aboutData.story" class="section-card story-section mb-6" elevation="3">
                <div class="section-header">
                    <div class="section-icon-wrapper story-icon">
                        <v-icon icon="mdi-book-open-page-variant" size="24"></v-icon>
                    </div>
                    <div class="section-title-wrapper">
                        <h2 class="text-h5 section-title">Our Story Section</h2>
                        <p class="text-body-2 text-medium-emphasis section-subtitle">Company history and background</p>
                    </div>
                </div>
                <v-divider class="my-4"></v-divider>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="6">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-format-text" size="16" class="mr-1"></v-icon>
                                    Overline
                                </div>
                                <div class="content-value">{{ aboutData.story.overline || 'Not set' }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-format-title" size="16" class="mr-1"></v-icon>
                                    Title
                                </div>
                                <div class="content-value">{{ aboutData.story.title || 'Not set' }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-text" size="16" class="mr-1"></v-icon>
                                    Description
                                </div>
                                <div class="content-value description-content"
                                    v-html="aboutData.story.description || 'Not set'"></div>
                            </div>
                        </v-col>
                        <v-col
                            v-if="(aboutData.story.stats && aboutData.story.stats.length > 0) || aboutData.story.image"
                            cols="12">
                            <v-row>
                                <!-- Statistics - Left Side -->
                                <v-col v-if="aboutData.story.stats && aboutData.story.stats.length > 0" cols="12"
                                    md="8">
                                    <div class="content-item">
                                        <div class="content-label mb-3">
                                            <v-icon icon="mdi-chart-line" size="16" class="mr-1"></v-icon>
                                            Statistics
                                        </div>
                                        <v-row>
                                            <v-col v-for="(stat, i) in aboutData.story.stats" :key="i" cols="12" sm="6"
                                                md="4">
                                                <v-card class="stat-card" elevation="1">
                                                    <v-card-text class="text-center pa-4">
                                                        <div class="stat-value">{{ stat.value }}</div>
                                                        <div class="stat-label">{{ stat.label }}</div>
                                                    </v-card-text>
                                                </v-card>
                                            </v-col>
                                        </v-row>
                                    </div>
                                </v-col>
                                <!-- Image - Right Side -->
                                <v-col v-if="aboutData.story.image" cols="12"
                                    :md="aboutData.story.stats && aboutData.story.stats.length > 0 ? 4 : 12">
                                    <div class="content-item">
                                        <div class="content-label mb-3">
                                            <v-icon icon="mdi-image" size="16" class="mr-1"></v-icon>
                                            Image
                                        </div>
                                        <v-card class="image-preview-card" elevation="2">
                                            <v-img :src="resolveImageUrl(aboutData.story.image)" max-height="180"
                                                max-width="400" cover class="rounded compact-image">
                                                <template v-slot:placeholder>
                                                    <div class="d-flex align-center justify-center fill-height">
                                                        <v-progress-circular indeterminate
                                                            color="primary"></v-progress-circular>
                                                    </div>
                                                </template>
                                            </v-img>
                                        </v-card>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Values Section Preview -->
            <v-card v-if="aboutData.values && aboutData.values.length > 0" class="section-card values-section mb-6"
                elevation="3">
                <div class="section-header">
                    <div class="section-icon-wrapper values-icon">
                        <v-icon icon="mdi-heart" size="24"></v-icon>
                    </div>
                    <div class="section-title-wrapper">
                        <h2 class="text-h5 section-title">Core Values Section</h2>
                        <p class="text-body-2 text-medium-emphasis section-subtitle">{{ aboutData.values.length }}
                            value{{
                                aboutData.values.length !== 1 ? 's' : '' }} defined</p>
                    </div>
                </div>
                <v-divider class="my-4"></v-divider>
                <v-card-text>
                    <div class="mb-6">
                        <v-row>
                            <v-col cols="12" md="6">
                                <div class="content-item">
                                    <div class="content-label">
                                        <v-icon icon="mdi-format-title" size="16" class="mr-1"></v-icon>
                                        Section Title
                                    </div>
                                    <div class="content-value">{{ aboutData.valuesTitle || 'Not set' }}</div>
                                </div>
                            </v-col>
                            <v-col cols="12" md="6">
                                <div class="content-item">
                                    <div class="content-label">
                                        <v-icon icon="mdi-subtitles" size="16" class="mr-1"></v-icon>
                                        Section Subtitle
                                    </div>
                                    <div class="content-value">{{ aboutData.valuesSubtitle || 'Not set' }}</div>
                                </div>
                            </v-col>
                        </v-row>
                    </div>
                    <div class="content-item">
                        <div class="content-label mb-4">
                            <v-icon icon="mdi-star-circle" size="16" class="mr-1"></v-icon>
                            Core Values
                        </div>
                        <v-row>
                            <v-col v-for="(value, i) in aboutData.values" :key="i" cols="12" md="4">
                                <v-card class="value-card" elevation="2" hover>
                                    <v-card-text class="pa-4">
                                        <div class="d-flex align-center mb-3">
                                            <v-avatar :color="getValueColor(i)" size="40" class="mr-3">
                                                <v-icon :icon="value.icon || 'mdi-check-circle'" color="white"
                                                    size="20"></v-icon>
                                            </v-avatar>
                                            <h3 class="text-h6 value-title">{{ value.title }}</h3>
                                        </div>
                                        <p class="text-body-2 text-medium-emphasis value-description">
                                            {{ value.description || value.desc || 'No description provided' }}
                                        </p>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Team Section Preview -->
            <v-card v-if="aboutData.team && aboutData.team.length > 0" class="section-card team-section" elevation="3">
                <div class="section-header">
                    <div class="section-icon-wrapper team-icon">
                        <v-icon icon="mdi-account-group" size="24"></v-icon>
                    </div>
                    <div class="section-title-wrapper">
                        <h2 class="text-h5 section-title">Team Section</h2>
                        <p class="text-body-2 text-medium-emphasis section-subtitle">{{ aboutData.team.length }} team
                            member{{
                                aboutData.team.length !== 1 ? 's' : '' }}</p>
                    </div>
                </div>
                <v-divider class="my-4"></v-divider>
                <v-card-text>
                    <v-row class="mb-4">
                        <v-col cols="12" md="6">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-format-text" size="16" class="mr-1"></v-icon>
                                    Overline
                                </div>
                                <div class="content-value">{{ aboutData.teamOverline || 'Not set' }}</div>
                            </div>
                        </v-col>
                        <v-col cols="12" md="6">
                            <div class="content-item">
                                <div class="content-label">
                                    <v-icon icon="mdi-format-title" size="16" class="mr-1"></v-icon>
                                    Title
                                </div>
                                <div class="content-value">{{ aboutData.teamTitle || 'Not set' }}</div>
                            </div>
                        </v-col>
                    </v-row>
                    <div class="content-item">
                        <div class="content-label mb-4">
                            <v-icon icon="mdi-account-multiple" size="16" class="mr-1"></v-icon>
                            Team Members
                        </div>
                        <v-row>
                            <v-col v-for="(member, i) in aboutData.team" :key="i" cols="12" sm="6" md="4" lg="3">
                                <v-card class="team-member-card" elevation="2" hover>
                                    <v-card-text class="text-center pa-4">
                                        <v-avatar size="100" class="mb-3 team-avatar">
                                            <v-img
                                                :src="member.image ? resolveImageUrl(member.image) : 'https://i.pravatar.cc/300?img=' + i"
                                                cover>
                                                <template v-slot:placeholder>
                                                    <div class="d-flex align-center justify-center fill-height">
                                                        <v-icon icon="mdi-account" size="48" color="grey"></v-icon>
                                                    </div>
                                                </template>
                                            </v-img>
                                        </v-avatar>
                                        <h3 class="text-h6 font-weight-bold mb-1 team-member-name">{{ member.name }}
                                        </h3>
                                        <div class="text-body-2 text-primary font-weight-medium team-member-role">{{
                                            member.role
                                            }}</div>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </div>
                </v-card-text>
            </v-card>
        </div>

        <!-- Empty State -->
        <v-card v-else class="content-card empty-state" elevation="2">
            <v-card-text class="text-center py-12">
                <v-icon icon="mdi-information-outline" size="80" color="grey-lighten-1" class="mb-4"></v-icon>
                <h2 class="text-h5 mb-2">No Content Found</h2>
                <p class="text-body-1 text-medium-emphasis mb-6">
                    Your about page doesn't have any content yet. Click the button below to get started.
                </p>
                <v-btn color="primary" prepend-icon="mdi-plus-circle" @click="openDialog()" size="large" elevation="2">
                    Create About Content
                </v-btn>
            </v-card-text>
        </v-card>

        <!-- About Form Dialog -->
        <AboutFormDialog v-model="showDialog" :about-data="aboutData" @saved="handleAboutSaved" />
    </div>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import AboutFormDialog from './AboutFormDialog.vue';
import { resolveUploadUrl } from '../../../utils/uploads';

export default {
    components: {
        AboutFormDialog
    },
    mixins: [adminPaginationMixin],
    data() {
        return {
            aboutData: null,
            showDialog: false,
            loading: false,
            error: null
        };
    },
    async mounted() {
        await this.loadAboutData();
    },
    methods: {
        async loadAboutData() {
            this.loading = true;
            this.error = null;

            try {
                const response = await this.$axios.get('/api/v1/about', {
                    headers: this.getAuthHeaders()
                });
                this.aboutData = response.data || null;
            } catch (error) {
                console.error('Error loading about data:', error);
                if (error.response?.status === 404) {
                    // No content exists yet, that's okay
                    this.aboutData = null;
                } else {
                    this.error = 'Failed to load about content';
                    this.handleApiError(error, 'Failed to load about content');
                }
            } finally {
                this.loading = false;
            }
        },
        openDialog() {
            this.showDialog = true;
        },
        handleAboutSaved() {
            this.loadAboutData();
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        },
        getValueColor(index) {
            const colors = ['primary', 'secondary', 'success', 'info', 'warning', 'error'];
            return colors[index % colors.length];
        }
    }
};
</script>

<style scoped>
.admin-about-container {
    padding: 8px;
}

/* Page Header */
.page-header {
    margin-bottom: 32px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.header-title-section {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-icon {
    opacity: 0.9;
}

.page-title {
    font-weight: 700;
    letter-spacing: -0.5px;
    margin-bottom: 4px;
}

.page-subtitle {
    margin-top: 0;
}

.edit-button {
    text-transform: none;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Content Cards */
.content-card {
    border-radius: 12px;
    overflow: hidden;
}

.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 24px;
}

/* Status Banner */
.status-banner {
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(var(--v-theme-success), 0.1) 0%, rgba(var(--v-theme-success), 0.05) 100%);
    border-left: 4px solid rgb(var(--v-theme-success));
}

/* Section Cards */
.section-card {
    border-radius: 12px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.section-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px 0;
}

.section-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.hero-icon {
    background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.2) 0%, rgba(var(--v-theme-primary), 0.1) 100%);
    color: rgb(var(--v-theme-primary));
}

.story-icon {
    background: linear-gradient(135deg, rgba(var(--v-theme-info), 0.2) 0%, rgba(var(--v-theme-info), 0.1) 100%);
    color: rgb(var(--v-theme-info));
}

.values-icon {
    background: linear-gradient(135deg, rgba(var(--v-theme-error), 0.2) 0%, rgba(var(--v-theme-error), 0.1) 100%);
    color: rgb(var(--v-theme-error));
}

.team-icon {
    background: linear-gradient(135deg, rgba(var(--v-theme-success), 0.2) 0%, rgba(var(--v-theme-success), 0.1) 100%);
    color: rgb(var(--v-theme-success));
}

.section-title-wrapper {
    flex: 1;
}

.section-title {
    font-weight: 700;
    letter-spacing: -0.3px;
    margin-bottom: 4px;
}

.section-subtitle {
    margin-top: 0;
}

/* Content Items */
.content-item {
    margin-bottom: 24px;
}

.content-item:last-child {
    margin-bottom: 0;
}

.content-label {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    font-weight: 600;
    color: rgb(var(--v-theme-on-surface));
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.content-value {
    font-size: 1rem;
    color: rgb(var(--v-theme-on-surface));
    line-height: 1.6;
    padding: 12px 16px;
    background: rgba(var(--v-theme-surface-variant), 0.3);
    border-radius: 8px;
    border-left: 3px solid rgb(var(--v-theme-primary));
}

.description-content {
    max-height: 200px;
    overflow-y: auto;
}

/* Stat Cards */
.stat-card {
    border-radius: 8px;
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: rgb(var(--v-theme-on-surface));
    line-height: 1.2;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.875rem;
    color: rgb(var(--v-theme-on-surface));
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
    opacity: 0.7;
}

/* Image Preview */
.image-preview-card {
    border-radius: 8px;
    overflow: hidden;
    max-width: 100%;
    width: 100%;
    display: block;
}

.compact-image {
    max-width: 100%;
    max-height: 180px;
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
}

/* Value Cards */
.value-card {
    border-radius: 12px;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.value-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
}

.value-title {
    font-weight: 600;
    margin: 0;
}

.value-description {
    line-height: 1.6;
    margin-top: 8px;
}

/* Team Member Cards */
.team-member-card {
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.team-member-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
}

.team-avatar {
    border: 3px solid rgb(var(--v-theme-primary));
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.team-member-name {
    color: rgb(var(--v-theme-on-surface));
}

.team-member-role {
    font-weight: 500;
}

/* Empty State */
.empty-state {
    border-radius: 12px;
    border: 2px dashed rgba(var(--v-theme-on-surface), 0.2);
}

/* Responsive Design */
@media (max-width: 960px) {
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .edit-button {
        width: 100%;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .section-icon-wrapper {
        width: 40px;
        height: 40px;
    }
}

@media (max-width: 600px) {
    .admin-about-container {
        padding: 4px;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .section-card {
        margin-bottom: 16px !important;
    }

    .content-value {
        font-size: 0.9rem;
        padding: 10px 12px;
    }
}
</style>
