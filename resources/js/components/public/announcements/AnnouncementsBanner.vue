<template>
    <v-dialog v-model="dialogOpen" :max-width="dialogMaxWidth" persistent scrollable class="announcement-dialog">
        <v-card v-if="currentAnnouncement" class="announcement-dialog-card">
            <v-card-title class="d-flex align-center pa-4" :class="`bg-${getAlertColor(currentAnnouncement.type)}`">
                <v-icon class="mr-2" color="white">{{ getAnnouncementIcon(currentAnnouncement.type) }}</v-icon>
                <span class="text-white font-weight-bold">{{ currentAnnouncement.title }}</span>
                <v-spacer></v-spacer>
                <v-chip v-if="announcements.length > 1" size="small" variant="flat" color="white" class="mr-2">
                    {{ currentAnnouncementIndex + 1 }} / {{ announcements.length }}
                </v-chip>
                <v-btn icon="mdi-close" variant="text" color="white" size="small" @click="closeDialog"></v-btn>
            </v-card-title>

            <v-card-text class="pa-4 pa-md-6 announcement-content">
                <div v-if="resolvedImageUrl" class="mb-4 announcement-media">
                    <v-img :src="resolvedImageUrl" :alt="currentAnnouncement.title" :max-height="imageMaxHeight" contain
                        class="rounded-lg announcement-image" @error="handleImageError"></v-img>
                </div>

                <div v-if="resolvedVideoUrl" class="mb-4 announcement-media">
                    <video controls class="w-100 rounded-lg announcement-video"
                        :style="{ maxHeight: imageMaxHeight + 'px' }">
                        <source :src="resolvedVideoUrl" type="video/mp4">
                        <source :src="resolvedVideoUrl" type="video/webm">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div v-if="currentAnnouncement.short_description" class="text-body-1 mb-4 announcement-description">
                    {{ currentAnnouncement.short_description }}
                </div>

                <div v-if="currentAnnouncement.content" class="text-body-2 mb-4 announcement-content-html"
                    v-html="currentAnnouncement.content">
                </div>

                <v-chip size="small" :color="getAlertColor(currentAnnouncement.type)" class="mb-2">
                    {{ getAnnouncementTypeLabel(currentAnnouncement.type) }}
                </v-chip>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions class="pa-4">
                <v-checkbox v-model="dontShowAgain" label="Don't show again" density="compact" hide-details
                    class="mr-auto"></v-checkbox>
                <v-spacer></v-spacer>
                <v-btn v-if="announcements.length > 1 && currentAnnouncementIndex < announcements.length - 1"
                    variant="text" @click="nextAnnouncement">
                    Next
                    <v-icon right>mdi-arrow-right</v-icon>
                </v-btn>
                <v-btn variant="text" @click="closeDialog">{{ announcements.length > 1 && currentAnnouncementIndex <
                    announcements.length - 1 ? 'Skip All' : 'Close' }}</v-btn>
                        <v-btn v-if="currentAnnouncement.external_link" :href="currentAnnouncement.external_link"
                            :target="currentAnnouncement.open_in_new_tab ? '_blank' : '_self'" color="primary"
                            variant="flat" @click="closeDialog">
                            Learn More
                            <v-icon right>mdi-arrow-right</v-icon>
                        </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { resolveUploadUrl } from '../../../utils/uploads';

export default {
    name: 'AnnouncementsBanner',
    data() {
        return {
            announcements: [],
            currentAnnouncementIndex: 0,
            dialogOpen: false,
            dontShowAgain: false,
            dismissedIds: [],
            loading: false,
            windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1200,
            windowHeight: typeof window !== 'undefined' ? window.innerHeight : 800
        };
    },
    computed: {
        currentAnnouncement() {
            if (this.announcements.length > 0 && this.currentAnnouncementIndex < this.announcements.length) {
                return this.announcements[this.currentAnnouncementIndex];
            }
            return null;
        },
        resolvedImageUrl() {
            if (!this.currentAnnouncement || !this.currentAnnouncement.image) {
                return null;
            }
            return this.getImageUrl(this.currentAnnouncement.image);
        },
        resolvedVideoUrl() {
            if (!this.currentAnnouncement || !this.currentAnnouncement.video) {
                return null;
            }
            return this.getVideoUrl(this.currentAnnouncement.video);
        },
        dialogMaxWidth() {
            // Responsive max-width based on screen size and content type
            if (this.windowWidth < 600) {
                // Small devices (< 600px): nearly full width for better mobile experience
                return '95vw';
            } else if (this.windowWidth < 960) {
                // Tablets (600px - 960px): 85% width
                return '85vw';
            }
            // Large screens (>= 960px): minimum 80%, can expand for media content
            if (this.resolvedImageUrl || this.resolvedVideoUrl) {
                return '90vw'; // Larger for media content
            }
            return '80vw'; // Minimum 80% for text-only content on large screens
        },
        imageMaxHeight() {
            // Responsive image height - max 50% of viewport height or 500px
            return Math.min(500, this.windowHeight * 0.5);
        }
    },
    async mounted() {
        await this.loadAnnouncements();
        this.loadDismissedAnnouncements();
        this.updateWindowSize();
        window.addEventListener('resize', this.updateWindowSize);
        // Show dialog if there are announcements to display
        if (this.announcements.length > 0) {
            // Small delay to ensure page is loaded
            setTimeout(() => {
                this.dialogOpen = true;
            }, 500);
        }
    },
    beforeUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('resize', this.updateWindowSize);
        }
    },
    methods: {
        async loadAnnouncements() {
            try {
                this.loading = true;
                // Load all active announcements to show as dialogs
                const response = await this.$axios.get('/api/openapi/announcements', {
                    params: {
                        per_page: 10
                    }
                });

                // Filter out dismissed announcements
                const dismissed = this.getDismissedIds();
                // Handle both paginated and direct array responses
                const announcementsData = Array.isArray(response.data)
                    ? response.data
                    : (response.data.data || []);
                this.announcements = announcementsData
                    .filter(ann => !dismissed.includes(ann.id))
                    .map(ann => {
                        // Ensure image and video fields are properly set
                        if (ann.image === '' || ann.image === null) {
                            ann.image = null;
                        }
                        if (ann.video === '' || ann.video === null) {
                            ann.video = null;
                        }
                        return ann;
                    })
                    .sort((a, b) => (a.priority || 100) - (b.priority || 100)); // Sort by priority
            } catch (error) {
                console.error('Error loading announcements:', error);
            } finally {
                this.loading = false;
            }
        },
        getDismissedIds() {
            const stored = localStorage.getItem('dismissed_announcements');
            return stored ? JSON.parse(stored) : [];
        },
        loadDismissedAnnouncements() {
            this.dismissedIds = this.getDismissedIds();
        },
        isDismissed(announcementId) {
            return this.dismissedIds.includes(announcementId);
        },
        closeDialog() {
            if (this.currentAnnouncement) {
                // Dismiss current announcement if "Don't show again" is checked
                if (this.dontShowAgain) {
                    this.dismissAnnouncement(this.currentAnnouncement.id);
                }

                // Close all dialogs
                this.dialogOpen = false;
            }
        },
        nextAnnouncement() {
            if (this.currentAnnouncement) {
                // Dismiss current announcement if "Don't show again" is checked
                if (this.dontShowAgain) {
                    this.dismissAnnouncement(this.currentAnnouncement.id);
                }

                // Move to next announcement
                if (this.currentAnnouncementIndex < this.announcements.length - 1) {
                    this.currentAnnouncementIndex++;
                    this.dontShowAgain = false; // Reset checkbox for next announcement
                } else {
                    // No more announcements, close dialog
                    this.dialogOpen = false;
                }
            }
        },
        dismissAnnouncement(announcementId) {
            if (!this.dismissedIds.includes(announcementId)) {
                this.dismissedIds.push(announcementId);
                localStorage.setItem('dismissed_announcements', JSON.stringify(this.dismissedIds));
            }
        },
        getAlertColor(type) {
            const colors = {
                'urgent_alerts': 'error',
                'offers_promotions': 'success',
                'events': 'info',
                'holidays': 'warning',
                'company_news': 'primary'
            };
            return colors[type] || 'primary';
        },
        getAnnouncementIcon(type) {
            const icons = {
                'urgent_alerts': 'mdi-alert-circle',
                'offers_promotions': 'mdi-tag',
                'events': 'mdi-calendar-star',
                'holidays': 'mdi-party-popper',
                'company_news': 'mdi-bullhorn'
            };
            return icons[type] || 'mdi-information';
        },
        getAnnouncementTypeLabel(type) {
            const labels = {
                'urgent_alerts': 'Urgent Alert',
                'offers_promotions': 'Special Offer',
                'events': 'Event',
                'holidays': 'Holiday',
                'company_news': 'Company News'
            };
            return labels[type] || type;
        },
        getImageUrl(imagePath) {
            if (!imagePath || imagePath === '' || imagePath === null) {
                return null;
            }
            // If already a full URL, return as is, otherwise resolve it
            if (imagePath.startsWith('http://') || imagePath.startsWith('https://') || imagePath.startsWith('//')) {
                return imagePath;
            }
            // Resolve URL using the utility function to ensure proper base URL
            return resolveUploadUrl(imagePath);
        },
        getVideoUrl(videoPath) {
            if (!videoPath || videoPath === '' || videoPath === null) {
                return null;
            }
            // If already a full URL, return as is, otherwise resolve it
            if (videoPath.startsWith('http://') || videoPath.startsWith('https://') || videoPath.startsWith('//')) {
                return videoPath;
            }
            // Resolve URL using the utility function to ensure proper base URL
            return resolveUploadUrl(videoPath);
        },
        handleImageError(event) {
            // Hide broken image
            if (event.target && event.target.parentElement) {
                event.target.parentElement.style.display = 'none';
            }
        },
        updateWindowSize() {
            if (typeof window !== 'undefined') {
                this.windowWidth = window.innerWidth;
                this.windowHeight = window.innerHeight;
            }
        }
    }
};
</script>

<style scoped>
/* Announcement Dialog Styles */
.announcement-dialog-card {
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

.announcement-content {
    flex: 1;
    overflow-y: auto;
    min-height: 0;
}

.announcement-media {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.announcement-image {
    width: 100%;
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

.announcement-video {
    width: 100%;
    max-width: 100%;
    height: auto;
}

.announcement-description {
    word-wrap: break-word;
    line-height: 1.6;
}

.announcement-content-html {
    word-wrap: break-word;
    line-height: 1.6;
}

.announcement-content-html :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 8px 0;
}

.announcement-content-html :deep(p) {
    margin-bottom: 12px;
}

.announcement-content-html :deep(h1),
.announcement-content-html :deep(h2),
.announcement-content-html :deep(h3),
.announcement-content-html :deep(h4) {
    margin-top: 16px;
    margin-bottom: 12px;
}

/* Small device responsiveness (< 600px) */
@media (max-width: 599px) {
    .announcement-dialog-card {
        max-height: 95vh;
    }

    .announcement-content {
        padding: 16px !important;
    }

    .announcement-dialog :deep(.v-overlay__content) {
        width: 95vw !important;
        max-width: 95vw !important;
        min-width: auto !important;
    }
}

/* Ensure minimum 80% width on screens >= 600px */
@media (min-width: 600px) {
    .announcement-dialog :deep(.v-overlay__content) {
        min-width: 80vw !important;
        width: auto !important;
    }
}

/* Tablet styles (600px - 959px) */
@media (min-width: 600px) and (max-width: 959px) {
    .announcement-dialog :deep(.v-overlay__content) {
        min-width: 80vw !important;
        max-width: 85vw !important;
    }
}

/* Large screen styles (>= 960px) - minimum 80% width */
@media (min-width: 960px) {
    .announcement-dialog :deep(.v-overlay__content) {
        min-width: 80vw !important;
    }
}

/* Ensure dialog adapts to content */
:deep(.v-dialog) {
    margin: 24px;
}

:deep(.v-dialog > .v-card) {
    overflow: hidden;
}

/* Small device adjustments */
@media (max-width: 600px) {
    :deep(.v-dialog) {
        margin: 8px;
    }
}
</style>
