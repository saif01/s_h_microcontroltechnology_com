<template>
    <div class="about-page">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
            <p class="mt-4 text-body-1">Loading about content...</p>
        </div>

        <!-- Error State -->
        <v-alert v-else-if="error" type="warning" variant="tonal" class="ma-4">
            {{ error }}
        </v-alert>

        <!-- Content -->
        <template v-else>
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
                            <div
                                class="glass-pill d-inline-flex align-center px-5 py-3 rounded-pill mb-8 animate-float">
                                <div class="pulse-dot mr-2"></div>
                                <v-icon icon="mdi-information-outline" color="amber-accent-4" size="small"
                                    class="mr-2"></v-icon>
                                <span class="text-subtitle-2 font-weight-bold tracking-wide text-white">{{
                                    aboutData?.hero?.overline || 'WHO WE ARE' }}</span>
                            </div>
                            <h1
                                class="text-h4 text-lg-h2 font-weight-black text-white mb-6 lh-tight text-shadow-sm animate-slide-up">
                                {{ aboutData?.hero?.title || 'Empowering Your World' }}
                            </h1>
                            <p
                                class="text-h6 text-white opacity-90 mw-700 mx-auto font-weight-light animate-slide-up-delay">
                                {{ aboutData?.hero?.subtitle || defaultSubtitle }}
                            </p>
                        </div>
                    </v-fade-transition>
                </v-container>
            </section>

            <!-- Our Story Section -->
            <section v-if="aboutData?.story" class="py-20 bg-surface">
                <v-container>
                    <v-row align="center">
                        <v-col cols="12" md="6" class="pr-md-12">
                            <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">{{
                                aboutData.story.overline || 'OUR STORY' }}</div>
                            <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">{{ aboutData.story.title ||
                                'Decades of Excellence in Power Solutions' }}</h2>
                            <div v-if="aboutData.story.description"
                                class="text-body-1 text-medium-emphasis mb-6 lh-relaxed"
                                v-html="aboutData.story.description"></div>
                            <p v-else class="text-body-1 text-medium-emphasis mb-6 lh-relaxed">
                                Founded in 2010, Micro Control Technology began with a simple mission: to ensure that no
                                business or home has to suffer from power interruptions. What started as a small local
                                service has grown into a leading provider of comprehensive power support solutions.
                            </p>

                            <div v-if="aboutData.story.stats && aboutData.story.stats.length > 0"
                                class="d-flex gap-8 mt-8">
                                <div v-for="(stat, i) in aboutData.story.stats" :key="i">
                                    <div class="text-h3 font-weight-black text-primary mb-1">{{ stat.value }}</div>
                                    <div class="text-caption font-weight-bold text-uppercase text-medium-emphasis">{{
                                        stat.label }}</div>
                                </div>
                            </div>
                        </v-col>
                        <v-col v-if="aboutData.story.image" cols="12" md="6">
                            <div class="position-relative pl-md-8">
                                <div class="image-border-effect"></div>
                                <v-img :src="resolveImageUrl(aboutData.story.image)"
                                    class="rounded-xl elevation-10 position-relative z-index-2" cover
                                    height="500"></v-img>
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Core Values -->
            <section v-if="aboutData?.values && aboutData.values.length > 0"
                class="py-20 bg-grey-lighten-5 position-relative">
                <div class="bg-pattern-dots"></div>
                <v-container class="position-relative z-index-1">
                    <div class="text-center mb-16">
                        <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">{{ aboutData.valuesTitle ||
                            'Our Core Values' }}</h2>
                        <p v-if="aboutData.valuesSubtitle"
                            class="text-h6 text-medium-emphasis mx-auto mw-600 font-weight-regular">
                            {{ aboutData.valuesSubtitle }}
                        </p>
                    </div>

                    <v-row>
                        <v-col v-for="(value, i) in aboutData.values" :key="i" cols="12" md="4">
                            <v-card class="value-card h-100 pa-8 rounded-xl elevation-2 border-thin text-center">
                                <div
                                    class="value-icon mb-6 mx-auto rounded-circle bg-white elevation-4 text-primary d-flex align-center justify-center">
                                    <v-icon :icon="value.icon || 'mdi-check-circle'" size="36"></v-icon>
                                </div>
                                <h3 class="text-h5 font-weight-bold mb-4 text-grey-darken-3">{{ value.title }}</h3>
                                <p class="text-body-1 text-medium-emphasis lh-relaxed">{{ value.description ||
                                    value.desc
                                    }}</p>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Team Section -->
            <section v-if="aboutData?.team && aboutData.team.length > 0" class="py-20 bg-surface">
                <v-container>
                    <div class="text-center mb-16">
                        <div class="text-overline text-primary font-weight-bold mb-2 tracking-widest">{{
                            aboutData.teamOverline || 'OUR TEAM' }}</div>
                        <h2 class="text-h3 font-weight-bold mb-6 text-grey-darken-3">{{ aboutData.teamTitle ||
                            'Meet the Experts' }}</h2>
                    </div>

                    <v-row justify="center">
                        <v-col v-for="(member, i) in aboutData.team" :key="i" cols="12" sm="6" md="3">
                            <v-card class="team-card text-center elevation-0 bg-transparent">
                                <div
                                    class="team-img-wrapper mb-6 mx-auto rounded-circle overflow-hidden elevation-6 position-relative">
                                    <v-img
                                        :src="member.image ? resolveImageUrl(member.image) : 'https://i.pravatar.cc/300?img=' + i"
                                        cover height="100%"></v-img>
                                    <div v-if="member.linkedin || member.twitter"
                                        class="team-socials d-flex align-center justify-center gap-2">
                                        <v-btn v-if="member.linkedin" :href="member.linkedin" target="_blank"
                                            icon="mdi-linkedin" size="small" color="white" variant="text"></v-btn>
                                        <v-btn v-if="member.twitter" :href="member.twitter" target="_blank"
                                            icon="mdi-twitter" size="small" color="white" variant="text"></v-btn>
                                    </div>
                                </div>
                                <h4 class="text-h6 font-weight-bold text-grey-darken-3">{{ member.name }}</h4>
                                <div class="text-subtitle-2 text-primary font-weight-bold text-uppercase tracking-wide">
                                    {{
                                        member.role }}</div>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </section>
        </template>
    </div>
</template>

<script>
import { resolveUploadUrl } from '../../../utils/uploads';

export default {
    name: 'AboutPage',
    data() {
        return {
            defaultSubtitle: 'We are dedicated to providing reliable, efficient, and sustainable power solutions for businesses and homes across the globe.',
            aboutData: null,
            loading: true,
            error: null
        };
    },
    async mounted() {
        await this.loadAboutData();
        this.updateMetaTags();
    },
    beforeUnmount() {
        this.cleanupMetaTags();
    },
    methods: {
        async loadAboutData() {
            this.loading = true;
            this.error = null;

            try {
                const response = await this.$axios.get('/api/openapi/about');
                this.aboutData = response.data || {};
            } catch (error) {
                console.error('Error loading about data:', error);
                this.error = 'Failed to load about content. Please try again later.';
                this.aboutData = null;
            } finally {
                this.loading = false;
            }
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        },
        updateMetaTags() {
            const appName = import.meta.env.VITE_APP_NAME || 'Micro Control Technology';
            const baseUrl = window.location.origin;

            // Title
            const title = this.aboutData?.meta_title || `About Us - ${appName}`;
            document.title = title;

            // Description
            const description = this.aboutData?.meta_description || this.aboutData?.hero?.subtitle || 'Learn about our company, mission, values, and team.';
            this.updateMetaTag('name', 'description', description);

            // Keywords
            const keywords = this.aboutData?.meta_keywords || 'about us, company, team, mission, values, Micro Control Technology';
            this.updateMetaTag('name', 'keywords', keywords);

            // OG Tags
            this.updateMetaTag('property', 'og:title', title);
            this.updateMetaTag('property', 'og:description', description);
            this.updateMetaTag('property', 'og:type', 'website');
            this.updateMetaTag('property', 'og:url', `${baseUrl}${this.$route.fullPath}`);
            
            if (this.aboutData?.og_image) {
                this.updateMetaTag('property', 'og:image', this.resolveImageUrl(this.aboutData.og_image));
            }

            // Twitter Tags
            this.updateMetaTag('name', 'twitter:card', 'summary_large_image');
            this.updateMetaTag('name', 'twitter:title', title);
            this.updateMetaTag('name', 'twitter:description', description);
            
            if (this.aboutData?.og_image) {
                this.updateMetaTag('name', 'twitter:image', this.resolveImageUrl(this.aboutData.og_image));
            }
        },
        updateMetaTag(attr, value, content) {
            if (!content) return;

            const metaTag = document.querySelector(`meta[${attr}="${value}"]`);
            if (metaTag) {
                metaTag.setAttribute('content', content);
            } else {
                const tag = document.createElement('meta');
                tag.setAttribute(attr, value);
                tag.setAttribute('content', content);
                document.head.appendChild(tag);
            }
        },
        cleanupMetaTags() {
            const appName = import.meta.env.VITE_APP_NAME || 'Micro Control Technology';
            document.title = `${appName}`;

            const ogTags = ['og:title', 'og:description', 'og:image', 'og:url'];
            const twitterTags = ['twitter:title', 'twitter:description', 'twitter:image'];

            [...ogTags, ...twitterTags].forEach(tag => {
                const meta = document.querySelector(`meta[property="${tag}"], meta[name="${tag}"]`);
                if (meta) meta.remove();
            });
        }
    }
};
</script>

<style scoped>
/* Hero section styles moved to app.css */

.value-card {
    background: white;
}

/* Common styles (image-border-effect, value-card, value-icon, team-img-wrapper, team-socials) moved to app.css */

/* Responsive Styles */
@media (max-width: 960px) {
    .image-border-effect {
        display: none;
    }

    .pl-md-8 {
        padding-left: 0 !important;
    }

    .pr-md-12 {
        padding-right: 0 !important;
        margin-bottom: 32px;
    }

    .gap-8 {
        gap: 24px;
    }

    .team-img-wrapper {
        width: 160px;
        height: 160px;
    }
}

@media (max-width: 600px) {
    .value-card {
        padding: 24px !important;
        margin-bottom: 16px;
    }

    .value-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 16px !important;
    }

    .value-icon .v-icon {
        font-size: 28px !important;
    }

    .team-img-wrapper {
        width: 140px;
        height: 140px;
    }

    .gap-8 {
        gap: 16px;
        flex-direction: column;
    }

    .gap-8>div {
        text-align: center;
    }

    .mb-16 {
        margin-bottom: 32px !important;
    }
}
</style>
