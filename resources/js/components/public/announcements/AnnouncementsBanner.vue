<template>
    <v-dialog v-model="dialogOpen" max-width="600" persistent scrollable>
        <v-card v-if="currentAnnouncement">
            <v-card-title class="d-flex align-center pa-4" :class="`bg-${getAlertColor(currentAnnouncement.type)}`">
                <v-icon class="mr-2" color="white">{{ getAnnouncementIcon(currentAnnouncement.type) }}</v-icon>
                <span class="text-white font-weight-bold">{{ currentAnnouncement.title }}</span>
                <v-spacer></v-spacer>
                <v-chip v-if="announcements.length > 1" size="small" variant="flat" color="white" class="mr-2">
                    {{ currentAnnouncementIndex + 1 }} / {{ announcements.length }}
                </v-chip>
                <v-btn icon="mdi-close" variant="text" color="white" size="small" @click="closeDialog"></v-btn>
            </v-card-title>

            <v-card-text class="pa-6">
                <div v-if="currentAnnouncement.image" class="mb-4">
                    <v-img :src="currentAnnouncement.image" :alt="currentAnnouncement.title" max-height="300" cover
                        class="rounded-lg"></v-img>
                </div>

                <div v-if="currentAnnouncement.short_description" class="text-body-1 mb-4">
                    {{ currentAnnouncement.short_description }}
                </div>

                <div v-if="currentAnnouncement.content" class="text-body-2 mb-4" v-html="currentAnnouncement.content">
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
export default {
    name: 'AnnouncementsBanner',
    data() {
        return {
            announcements: [],
            currentAnnouncementIndex: 0,
            dialogOpen: false,
            dontShowAgain: false,
            dismissedIds: [],
            loading: false
        };
    },
    computed: {
        currentAnnouncement() {
            if (this.announcements.length > 0 && this.currentAnnouncementIndex < this.announcements.length) {
                return this.announcements[this.currentAnnouncementIndex];
            }
            return null;
        }
    },
    async mounted() {
        await this.loadAnnouncements();
        this.loadDismissedAnnouncements();
        // Show dialog if there are announcements to display
        if (this.announcements.length > 0) {
            // Small delay to ensure page is loaded
            setTimeout(() => {
                this.dialogOpen = true;
            }, 500);
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
        }
    }
};
</script>

<style scoped>
/* Dialog styles are handled by Vuetify */
</style>
