<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">About Page Management</h1>
            <v-btn color="primary" prepend-icon="mdi-pencil" @click="openDialog()" class="add-button">
                Edit About Content
            </v-btn>
        </div>

        <!-- About Content Preview -->
        <v-card class="mb-4">
            <v-card-title class="d-flex justify-space-between align-center">
                <span>About Page Content</span>
                <v-chip v-if="aboutData" color="success" size="small">
                    Content Loaded
                </v-chip>
                <v-chip v-else color="warning" size="small">
                    No Content
                </v-chip>
            </v-card-title>
            <v-card-text>
                <div v-if="loading" class="text-center py-8">
                    <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
                    <p class="mt-4 text-body-2">Loading about content...</p>
                </div>

                <v-alert v-else-if="error" type="error" variant="tonal" class="mb-4">
                    {{ error }}
                </v-alert>

                <div v-else-if="aboutData" class="about-preview">
                    <!-- Hero Section Preview -->
                    <v-card variant="outlined" class="mb-4">
                        <v-card-title class="bg-primary text-white">
                            <v-icon icon="mdi-image-text" class="mr-2"></v-icon>
                            Hero Section
                        </v-card-title>
                        <v-card-text>
                            <div class="mb-2">
                                <strong>Overline:</strong> {{ aboutData.hero?.overline || 'N/A' }}
                            </div>
                            <div class="mb-2">
                                <strong>Title:</strong> {{ aboutData.hero?.title || 'N/A' }}
                            </div>
                            <div>
                                <strong>Subtitle:</strong> {{ aboutData.hero?.subtitle || 'N/A' }}
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Story Section Preview -->
                    <v-card v-if="aboutData.story" variant="outlined" class="mb-4">
                        <v-card-title class="bg-primary text-white">
                            <v-icon icon="mdi-book-open-page-variant" class="mr-2"></v-icon>
                            Our Story Section
                        </v-card-title>
                        <v-card-text>
                            <div class="mb-2">
                                <strong>Overline:</strong> {{ aboutData.story.overline || 'N/A' }}
                            </div>
                            <div class="mb-2">
                                <strong>Title:</strong> {{ aboutData.story.title || 'N/A' }}
                            </div>
                            <div class="mb-2">
                                <strong>Description:</strong>
                                <div v-html="aboutData.story.description || 'N/A'" class="text-body-2 mt-1"></div>
                            </div>
                            <div v-if="aboutData.story.stats && aboutData.story.stats.length > 0" class="mt-2">
                                <strong>Stats:</strong>
                                <div class="d-flex gap-4 mt-2">
                                    <v-chip v-for="(stat, i) in aboutData.story.stats" :key="i" size="small" variant="outlined">
                                        {{ stat.value }} - {{ stat.label }}
                                    </v-chip>
                                </div>
                            </div>
                            <div v-if="aboutData.story.image" class="mt-2">
                                <strong>Image:</strong>
                                <v-img :src="resolveImageUrl(aboutData.story.image)" max-width="200" max-height="150" class="mt-2 rounded"></v-img>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Values Section Preview -->
                    <v-card v-if="aboutData.values && aboutData.values.length > 0" variant="outlined" class="mb-4">
                        <v-card-title class="bg-primary text-white">
                            <v-icon icon="mdi-heart" class="mr-2"></v-icon>
                            Core Values Section
                        </v-card-title>
                        <v-card-text>
                            <div class="mb-2">
                                <strong>Title:</strong> {{ aboutData.valuesTitle || 'N/A' }}
                            </div>
                            <div class="mb-2">
                                <strong>Subtitle:</strong> {{ aboutData.valuesSubtitle || 'N/A' }}
                            </div>
                            <div class="mt-4">
                                <strong>Values ({{ aboutData.values.length }}):</strong>
                                <v-row class="mt-2">
                                    <v-col v-for="(value, i) in aboutData.values" :key="i" cols="12" md="4">
                                        <v-card variant="outlined" class="pa-3">
                                            <div class="d-flex align-center mb-2">
                                                <v-icon :icon="value.icon || 'mdi-check-circle'" class="mr-2"></v-icon>
                                                <strong>{{ value.title }}</strong>
                                            </div>
                                            <p class="text-body-2 text-medium-emphasis">{{ value.description || value.desc }}</p>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Team Section Preview -->
                    <v-card v-if="aboutData.team && aboutData.team.length > 0" variant="outlined">
                        <v-card-title class="bg-primary text-white">
                            <v-icon icon="mdi-account-group" class="mr-2"></v-icon>
                            Team Section
                        </v-card-title>
                        <v-card-text>
                            <div class="mb-2">
                                <strong>Overline:</strong> {{ aboutData.teamOverline || 'N/A' }}
                            </div>
                            <div class="mb-2">
                                <strong>Title:</strong> {{ aboutData.teamTitle || 'N/A' }}
                            </div>
                            <div class="mt-4">
                                <strong>Team Members ({{ aboutData.team.length }}):</strong>
                                <v-row class="mt-2">
                                    <v-col v-for="(member, i) in aboutData.team" :key="i" cols="12" sm="6" md="3">
                                        <v-card variant="outlined" class="pa-3 text-center">
                                            <v-avatar size="80" class="mb-2">
                                                <v-img :src="member.image ? resolveImageUrl(member.image) : 'https://i.pravatar.cc/300?img=' + i" cover></v-img>
                                            </v-avatar>
                                            <div class="font-weight-bold">{{ member.name }}</div>
                                            <div class="text-caption text-primary">{{ member.role }}</div>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-card-text>
                    </v-card>
                </div>

                <v-alert v-else type="info" variant="tonal">
                    No about content found. Click "Edit About Content" to create content.
                </v-alert>
            </v-card-text>
        </v-card>

        <!-- About Form Dialog -->
        <AboutFormDialog v-model="showDialog" :about-data="aboutData" @saved="handleAboutSaved" />
    </div>
</template>

<script>
import axios from 'axios';
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
                const response = await axios.get('/api/v1/about', {
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
        }
    }
};
</script>

<style scoped>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.page-title {
    font-weight: 600;
}

.about-preview {
    max-height: 600px;
    overflow-y: auto;
}
</style>

