<template>
    <v-dialog v-model="dialog" max-width="1400" scrollable persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                <span class="text-h5 font-weight-bold">Edit About Page Content</span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                    :disabled="saving || loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading about content...</p>
                    </div>
                </div>

                <!-- Form Content -->
                <v-form ref="formRef" v-else>
                    <v-tabs v-model="activeTab" bg-color="grey-lighten-4">
                        <v-tab value="hero">Hero Section</v-tab>
                        <v-tab value="story">Our Story</v-tab>
                        <v-tab value="values">Core Values</v-tab>
                        <v-tab value="team">Team</v-tab>
                        <v-tab value="seo">SEO & Meta</v-tab>
                    </v-tabs>

                    <v-window v-model="activeTab">
                        <!-- Hero Section Tab -->
                        <v-window-item value="hero">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.hero.overline" label="Hero Overline"
                                            variant="outlined" hint="Small text above the title (e.g., 'WHO WE ARE')"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.hero.title" label="Hero Title *" variant="outlined"
                                            :rules="[v => !!v || 'Title is required']"
                                            hint="Main heading for the hero section" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.hero.subtitle" label="Hero Subtitle *"
                                            variant="outlined" rows="3" :rules="[v => !!v || 'Subtitle is required']"
                                            hint="Subtitle text below the main title" persistent-hint></v-textarea>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Our Story Tab -->
                        <v-window-item value="story">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.story.overline" label="Story Overline"
                                            variant="outlined" hint="Small text above the story title"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.story.title" label="Story Title" variant="outlined"
                                            hint="Main heading for the story section" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Story Description</v-label>
                                        <RichTextEditor v-model="form.story.description"
                                            :active="dialog && activeTab === 'story'"
                                            placeholder="Enter story description..." />
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Story Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="d-flex flex-column flex-md-row align-start">
                                                <div v-if="storyImagePreview || form.story.image"
                                                    class="mr-md-4 mb-4 mb-md-0">
                                                    <v-img :src="storyImagePreview || resolveImageUrl(form.story.image)"
                                                        max-width="300" max-height="300" class="rounded elevation-2"
                                                        cover></v-img>
                                                    <div class="mt-2">
                                                        <v-btn color="error" size="small" variant="text"
                                                            @click="removeStoryImage" prepend-icon="mdi-delete">
                                                            Remove Image
                                                        </v-btn>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <v-file-input v-model="storyImageFile" accept="image/*"
                                                        label="Select Story Image" variant="outlined" prepend-icon=""
                                                        prepend-inner-icon="mdi-image" show-size clearable
                                                        hint="Recommended size: 800x500px. Max file size: 5MB"
                                                        persistent-hint @update:model-value="handleStoryImageSelect">
                                                        <template v-slot:append>
                                                            <v-progress-circular v-if="uploadingStoryImage"
                                                                indeterminate color="primary"
                                                                size="24"></v-progress-circular>
                                                        </template>
                                                    </v-file-input>

                                                    <v-alert v-if="storyImageFile && storyImageFile.size > 5242880"
                                                        type="warning" variant="tonal" density="compact" class="mt-2">
                                                        File size is larger than 5MB. Please choose a smaller image.
                                                    </v-alert>

                                                    <v-alert v-if="storyImageError" type="error" variant="tonal"
                                                        density="compact" class="mt-2" closable
                                                        @click:close="storyImageError = null">
                                                        <div class="text-body-2">
                                                            <strong>Upload Error:</strong> {{ storyImageError }}
                                                        </div>
                                                    </v-alert>

                                                    <div v-if="!storyImagePreview && !storyImageFile && !form.story.image"
                                                        class="text-caption text-medium-emphasis mt-2">
                                                        No image selected. Upload an image to display with the story
                                                        section.
                                                    </div>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Statistics</h3>
                                        <div v-for="(stat, index) in form.story.stats" :key="index" class="mb-4">
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex justify-space-between align-center mb-2">
                                                    <strong>Stat {{ index + 1 }}</strong>
                                                    <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                        @click="removeStat(index)"></v-btn>
                                                </div>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="stat.value" label="Value"
                                                            variant="outlined" hint="e.g., 15+, 500+"
                                                            persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="stat.label" label="Label"
                                                            variant="outlined" hint="e.g., Years Experience"
                                                            persistent-hint></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-card>
                                        </div>
                                        <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                            @click="addStat">Add Statistic</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Core Values Tab -->
                        <v-window-item value="values">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.valuesTitle" label="Values Section Title"
                                            variant="outlined" hint="Main heading for the values section"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.valuesSubtitle" label="Values Section Subtitle"
                                            variant="outlined" rows="2" hint="Subtitle text for the values section"
                                            persistent-hint></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Core Values</h3>
                                        <div v-for="(value, index) in form.values" :key="index" class="mb-4">
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex justify-space-between align-center mb-2">
                                                    <strong>Value {{ index + 1 }}</strong>
                                                    <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                        @click="removeValue(index)"></v-btn>
                                                </div>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="value.title" label="Title *"
                                                            variant="outlined"
                                                            :rules="[v => !!v || 'Title is required']"
                                                            hint="Value title" persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="value.icon" label="Icon"
                                                            variant="outlined"
                                                            hint="Material Design Icon name (e.g., mdi-shield-check)"
                                                            persistent-hint
                                                            prepend-inner-icon="mdi-information-outline">
                                                            <template v-slot:append>
                                                                <v-icon v-if="value.icon" :icon="value.icon"
                                                                    size="small"></v-icon>
                                                            </template>
                                                        </v-text-field>
                                                    </v-col>
                                                    <v-col cols="12">
                                                        <v-textarea v-model="value.description" label="Description *"
                                                            variant="outlined" rows="3"
                                                            :rules="[v => !!v || 'Description is required']"
                                                            hint="Value description" persistent-hint></v-textarea>
                                                    </v-col>
                                                </v-row>
                                            </v-card>
                                        </div>
                                        <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                            @click="addValue">Add Value</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Team Tab -->
                        <v-window-item value="team">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.teamOverline" label="Team Section Overline"
                                            variant="outlined" hint="Small text above the team title"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.teamTitle" label="Team Section Title"
                                            variant="outlined" hint="Main heading for the team section"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Team Members</h3>
                                        <div v-for="(member, index) in form.team" :key="index" class="mb-4">
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex justify-space-between align-center mb-2">
                                                    <strong>Team Member {{ index + 1 }}</strong>
                                                    <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                        @click="removeTeamMember(index)"></v-btn>
                                                </div>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.name" label="Name *"
                                                            variant="outlined" :rules="[v => !!v || 'Name is required']"
                                                            hint="Team member name" persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.role" label="Role *"
                                                            variant="outlined" :rules="[v => !!v || 'Role is required']"
                                                            hint="Team member role" persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12">
                                                        <v-label class="mb-2">Member Image</v-label>
                                                        <v-card variant="outlined" class="pa-4">
                                                            <div class="d-flex flex-column flex-md-row align-start">
                                                                <div v-if="member.imagePreview || member.image"
                                                                    class="mr-md-4 mb-4 mb-md-0">
                                                                    <v-img
                                                                        :src="member.imagePreview || resolveImageUrl(member.image)"
                                                                        max-width="150" max-height="150"
                                                                        class="rounded elevation-2" cover></v-img>
                                                                    <div class="mt-2">
                                                                        <v-btn color="error" size="small" variant="text"
                                                                            @click="removeTeamMemberImage(index)"
                                                                            prepend-icon="mdi-delete">
                                                                            Remove Image
                                                                        </v-btn>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <v-file-input :ref="`teamImageFile_${index}`"
                                                                        @update:model-value="(file) => handleTeamMemberImageSelect(file, index)"
                                                                        accept="image/*"
                                                                        label="Select Team Member Image"
                                                                        variant="outlined" prepend-icon=""
                                                                        prepend-inner-icon="mdi-image" show-size
                                                                        clearable
                                                                        hint="Recommended: Square image. Max file size: 5MB"
                                                                        persistent-hint>
                                                                        <template v-slot:append>
                                                                            <v-progress-circular
                                                                                v-if="member.uploadingImage"
                                                                                indeterminate color="primary"
                                                                                size="24"></v-progress-circular>
                                                                        </template>
                                                                    </v-file-input>

                                                                    <v-alert
                                                                        v-if="member.imageFile && member.imageFile.size > 5242880"
                                                                        type="warning" variant="tonal" density="compact"
                                                                        class="mt-2">
                                                                        File size is larger than 5MB. Please choose a
                                                                        smaller image.
                                                                    </v-alert>

                                                                    <v-alert v-if="member.imageError" type="error"
                                                                        variant="tonal" density="compact" class="mt-2"
                                                                        closable
                                                                        @click:close="member.imageError = null">
                                                                        <div class="text-body-2">
                                                                            <strong>Upload Error:</strong> {{
                                                                                member.imageError }}
                                                                        </div>
                                                                    </v-alert>

                                                                    <div v-if="!member.imagePreview && !member.imageFile && !member.image"
                                                                        class="text-caption text-medium-emphasis mt-2">
                                                                        No image selected. Upload an image to display
                                                                        with
                                                                        this team member.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </v-card>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.linkedin" label="LinkedIn URL"
                                                            variant="outlined" hint="Optional LinkedIn profile URL"
                                                            persistent-hint
                                                            prepend-inner-icon="mdi-linkedin"></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.twitter" label="Twitter URL"
                                                            variant="outlined" hint="Optional Twitter profile URL"
                                                            persistent-hint
                                                            prepend-inner-icon="mdi-twitter"></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-card>
                                        </div>
                                        <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                            @click="addTeamMember">Add Team
                                            Member</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>


                        <!-- SEO & Meta Tab -->
                        <v-window-item value="seo">
                            <div class="pa-6">
                                <v-alert type="info" variant="tonal" class="mb-6" density="compact">
                                    <div class="text-body-2">
                                        <strong>SEO Best Practices:</strong> Fill in these fields to improve your
                                        about page visibility in search
                                        engines and social media.
                                        If left empty, default values from the page content will be
                                        used.
                                    </div>
                                </v-alert>

                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.meta_title" label="Meta Title" variant="outlined"
                                            hint="SEO title for search engines (recommended: 50-60 characters)"
                                            persistent-hint :counter="60" :color="getMetaTitleColor()"
                                            prepend-inner-icon="mdi-format-title">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateMetaTitle" :disabled="!form.hero.title"
                                                    title="Auto-generate from hero title">
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        <!-- Suggestion for Meta Title -->
                                        <v-alert v-if="!form.meta_title && suggestedMetaTitle" type="info"
                                            variant="tonal" density="compact" class="mt-2" closable>
                                            <div class="d-flex align-center justify-space-between">
                                                <span class="text-body-2">
                                                    <strong>Suggested:</strong> {{ suggestedMetaTitle }}
                                                </span>
                                                <v-btn size="small" color="primary" variant="text"
                                                    @click="applySuggestedMetaTitle" class="ml-2">
                                                    Apply
                                                </v-btn>
                                            </div>
                                        </v-alert>
                                        <div class="text-caption" :class="getMetaTitleColor() + '--text'">
                                            {{ (form.meta_title || '').length }}/60 characters
                                            <span v-if="(form.meta_title || '').length < 50 && form.meta_title"
                                                class="ml-2">
                                                (Too short - recommended: 50-60)
                                            </span>
                                            <span v-else-if="(form.meta_title || '').length > 60" class="ml-2">
                                                (Too long - may be truncated)
                                            </span>
                                            <span v-else-if="form.meta_title" class="ml-2 text-success">✓ Good
                                                length</span>
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-textarea v-model="form.meta_description" label="Meta Description"
                                            variant="outlined" rows="4"
                                            hint="SEO description for search results (recommended: 150-160 characters)"
                                            persistent-hint :counter="160" :color="getMetaDescriptionColor()"
                                            prepend-inner-icon="mdi-text">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateMetaDescription"
                                                    :disabled="!form.hero.subtitle && !form.story.description"
                                                    title="Auto-generate from hero subtitle or story description">
                                                </v-btn>
                                            </template>
                                        </v-textarea>
                                        <!-- Suggestion for Meta Description -->
                                        <v-alert v-if="!form.meta_description && suggestedMetaDescription" type="info"
                                            variant="tonal" density="compact" class="mt-2" closable>
                                            <div class="d-flex align-center justify-space-between flex-wrap">
                                                <span class="text-body-2 mb-2">
                                                    <strong>Suggested:</strong> {{ suggestedMetaDescription }}
                                                </span>
                                                <v-btn size="small" color="primary" variant="text"
                                                    @click="applySuggestedMetaDescription">
                                                    Apply
                                                </v-btn>
                                            </div>
                                        </v-alert>
                                        <div class="text-caption" :class="getMetaDescriptionColor() + '--text'">
                                            {{ (form.meta_description || '').length }}/160 characters
                                            <span
                                                v-if="(form.meta_description || '').length < 120 && form.meta_description"
                                                class="ml-2">
                                                (Too short - recommended: 150-160)
                                            </span>
                                            <span v-else-if="(form.meta_description || '').length > 160" class="ml-2">
                                                (Too long - may be truncated)
                                            </span>
                                            <span v-else-if="form.meta_description" class="ml-2 text-success">✓ Good
                                                length</span>
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="form.meta_keywords" label="Meta Keywords"
                                            variant="outlined"
                                            hint="Comma-separated keywords for SEO (e.g., about us, company history, team)"
                                            persistent-hint prepend-inner-icon="mdi-tag-multiple">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-auto-fix" size="small" variant="text"
                                                    @click="generateMetaKeywords"
                                                    :disabled="!form.hero.title && !form.values.length"
                                                    title="Auto-generate from hero title and values">
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        <!-- Suggestion for Meta Keywords -->
                                        <v-alert v-if="!form.meta_keywords && suggestedMetaKeywords" type="info"
                                            variant="tonal" density="compact" class="mt-2" closable>
                                            <div class="d-flex align-center justify-space-between flex-wrap">
                                                <span class="text-body-2 mb-2">
                                                    <strong>Suggested:</strong> {{ suggestedMetaKeywords }}
                                                </span>
                                                <v-btn size="small" color="primary" variant="text"
                                                    @click="applySuggestedMetaKeywords">
                                                    Apply
                                                </v-btn>
                                            </div>
                                        </v-alert>
                                        <div class="text-caption text-medium-emphasis">
                                            {{(form.meta_keywords || '').split(',').filter(k => k.trim()).length}}
                                            keywords entered
                                        </div>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Open Graph (Social Media)</h3>
                                        <p class="text-body-2 text-medium-emphasis mb-4">
                                            This image will be displayed when your about page is shared on social media
                                            platforms like Facebook,
                                            Twitter, LinkedIn, etc.
                                        </p>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-label class="mb-2">Open Graph Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="d-flex flex-column flex-md-row align-start">
                                                <!-- OG Image Preview -->
                                                <div v-if="ogImagePreview || form.og_image"
                                                    class="mr-md-4 mb-4 mb-md-0">
                                                    <v-img :src="ogImagePreview || resolveImageUrl(form.og_image)"
                                                        max-width="300" max-height="300" class="rounded elevation-2"
                                                        cover></v-img>
                                                    <div class="mt-2">
                                                        <v-btn color="error" size="small" variant="text"
                                                            @click="removeOgImage" prepend-icon="mdi-delete">
                                                            Remove Image
                                                        </v-btn>
                                                    </div>
                                                    <div class="text-caption text-medium-emphasis mt-2">
                                                        Recommended: 1200x630px (1.91:1 ratio)
                                                    </div>
                                                </div>

                                                <!-- Upload Section -->
                                                <div class="flex-grow-1">
                                                    <v-file-input v-model="ogImageFile" accept="image/*"
                                                        label="Select Open Graph Image" variant="outlined"
                                                        prepend-icon="" prepend-inner-icon="mdi-image" show-size
                                                        clearable
                                                        hint="Recommended size: 1200x630px. Max file size: 5MB"
                                                        persistent-hint @update:model-value="handleOgImageSelect">
                                                        <template v-slot:append>
                                                            <v-progress-circular v-if="uploadingOgImage" indeterminate
                                                                color="primary" size="24">
                                                            </v-progress-circular>
                                                        </template>
                                                    </v-file-input>

                                                    <v-alert v-if="ogImageFile && ogImageFile.size > 5242880"
                                                        type="warning" variant="tonal" density="compact" class="mt-2">
                                                        File size is larger than 5MB. Please choose a smaller image.
                                                    </v-alert>

                                                    <v-alert v-if="ogImageError" type="error" variant="tonal"
                                                        density="compact" class="mt-2" closable
                                                        @click:close="ogImageError = null">
                                                        <div class="text-body-2">
                                                            <strong>Upload Error:</strong> {{ ogImageError }}
                                                        </div>
                                                    </v-alert>

                                                    <div v-if="!ogImagePreview && !ogImageFile && !form.og_image"
                                                        class="text-caption text-medium-emphasis mt-2">
                                                        No image selected. If not provided, the story image will
                                                        be used.
                                                    </div>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>

                                    <!-- SEO Preview -->
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Search Engine Preview</h3>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="search-preview">
                                                <div class="search-url mb-1 text-caption" style="color: #006621;">
                                                    {{ getPreviewUrl() }}
                                                </div>
                                                <div class="search-title mb-1 text-h6"
                                                    :class="getMetaTitleColor() + '--text'"
                                                    style="color: #1a0dab; cursor: pointer;">
                                                    {{ getPreviewTitle() }}
                                                </div>
                                                <div class="search-description text-body-2"
                                                    :class="getMetaDescriptionColor() + '--text'"
                                                    style="color: #545454;">
                                                    {{ getPreviewDescription() }}
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>
                    </v-window>
                </v-form>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-4">
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="closeDialog" :disabled="saving">Cancel</v-btn>
                <v-btn color="primary" variant="flat" @click="saveAbout" :loading="saving">
                    Save About Content
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import RichTextEditor from '../../common/RichTextEditor.vue';
import { normalizeUploadPath, resolveUploadUrl } from '../../../utils/uploads';

export default {
    name: 'AboutFormDialog',
    components: {
        RichTextEditor
    },
    mixins: [adminPaginationMixin],
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        aboutData: {
            type: Object,
            default: null
        }
    },
    emits: ['update:modelValue', 'saved'],
    data() {
        return {
            activeTab: 'hero',
            loading: false,
            saving: false,
            storyImageFile: null,
            storyImagePreview: null,
            uploadingStoryImage: false,
            storyImageError: null,
            ogImageFile: null,
            ogImagePreview: null,
            uploadingOgImage: false,
            ogImageError: null,
            form: {
                hero: {
                    overline: 'WHO WE ARE',
                    title: 'Empowering Your World',
                    subtitle: 'We are dedicated to providing reliable, efficient, and sustainable power solutions for businesses and homes across the globe.'
                },
                story: {
                    overline: 'OUR STORY',
                    title: 'Decades of Excellence in Power Solutions',
                    description: '',
                    image: '',
                    stats: []
                },
                valuesTitle: 'Our Core Values',
                valuesSubtitle: 'The principles that guide every project we undertake.',
                values: [],
                teamOverline: 'OUR TEAM',
                teamTitle: 'Meet the Experts',
                team: [],
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: ''
            }
        };
    },
    computed: {
        dialog: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        },
        suggestedMetaTitle() {
            if (this.form.meta_title || !this.form.hero.title) return null;
            return this.form.hero.title.length > 60
                ? this.form.hero.title.substring(0, 57) + '...'
                : this.form.hero.title;
        },
        suggestedMetaDescription() {
            if (this.form.meta_description) return null;
            let suggestion = null;
            if (this.form.hero.subtitle) {
                suggestion = this.form.hero.subtitle;
            } else if (this.form.story.description) {
                // Strip HTML tags for suggestion
                const textContent = this.form.story.description.replace(/<[^>]*>/g, '').trim();
                suggestion = textContent;
            }
            if (suggestion && suggestion.length > 160) {
                // Try to cut at a word boundary
                suggestion = suggestion.substring(0, 157);
                const lastSpace = suggestion.lastIndexOf(' ');
                if (lastSpace > 120) {
                    suggestion = suggestion.substring(0, lastSpace) + '...';
                } else {
                    suggestion += '...';
                }
            }
            return suggestion;
        },
        suggestedMetaKeywords() {
            if (this.form.meta_keywords) return null;
            return this.generateSuggestedKeywords();
        }
    },
    watch: {
        modelValue(newVal) {
            if (newVal) {
                this.activeTab = 'hero';
                if (this.aboutData) {
                    this.loadAboutForEdit();
                } else {
                    this.resetForm();
                }
            }
        },
        aboutData: {
            handler(newVal) {
                if (newVal && this.dialog) {
                    this.loadAboutForEdit();
                } else if (!newVal && this.dialog) {
                    this.resetForm();
                }
            },
            immediate: false
        }
    },
    methods: {
        resetForm() {
            this.form = {
                hero: {
                    overline: 'WHO WE ARE',
                    title: 'Empowering Your World',
                    subtitle: 'We are dedicated to providing reliable, efficient, and sustainable power solutions for businesses and homes across the globe.'
                },
                story: {
                    overline: 'OUR STORY',
                    title: 'Decades of Excellence in Power Solutions',
                    description: '',
                    image: '',
                    stats: []
                },
                valuesTitle: 'Our Core Values',
                valuesSubtitle: 'The principles that guide every project we undertake.',
                values: [],
                teamOverline: 'OUR TEAM',
                teamTitle: 'Meet the Experts',
                team: [],
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: ''
            };

            this.storyImageFile = null;
            this.storyImagePreview = null;
            this.storyImageError = null;
            this.ogImageFile = null;
            this.ogImagePreview = null;
            this.ogImageError = null;
        },
        loadAboutForEdit() {
            if (!this.aboutData) {
                this.resetForm();
                return;
            }

            this.loading = true;

            try {
                // Load hero data
                if (this.aboutData.hero) {
                    this.form.hero = { ...this.aboutData.hero };
                }

                // Load story data
                if (this.aboutData.story) {
                    this.form.story = {
                        overline: this.aboutData.story.overline || '',
                        title: this.aboutData.story.title || '',
                        description: this.aboutData.story.description || '',
                        image: this.aboutData.story.image || '',
                        stats: this.aboutData.story.stats ? [...this.aboutData.story.stats] : []
                    };
                    // Set preview for existing story image
                    if (this.form.story.image) {
                        this.storyImagePreview = this.resolveImageUrl(this.form.story.image);
                    }
                }

                // Load values data
                this.form.valuesTitle = this.aboutData.valuesTitle || 'Our Core Values';
                this.form.valuesSubtitle = this.aboutData.valuesSubtitle || '';
                this.form.values = this.aboutData.values ? this.aboutData.values.map(v => ({
                    title: v.title || '',
                    description: v.description || v.desc || '',
                    icon: v.icon || ''
                })) : [];

                // Load team data
                this.form.teamOverline = this.aboutData.teamOverline || 'OUR TEAM';
                this.form.teamTitle = this.aboutData.teamTitle || 'Meet the Experts';
                this.form.team = this.aboutData.team ? this.aboutData.team.map(m => ({
                    name: m.name || '',
                    role: m.role || '',
                    image: m.image || '',
                    linkedin: m.linkedin || '',
                    twitter: m.twitter || '',
                    imagePreview: m.image ? this.resolveImageUrl(m.image) : null,
                    imageFile: null,
                    uploadingImage: false,
                    imageError: null
                })) : [];

                // Load SEO data
                this.form.meta_title = this.aboutData.meta_title || '';
                this.form.meta_description = this.aboutData.meta_description || '';
                this.form.meta_keywords = this.aboutData.meta_keywords || '';
                this.form.og_image = this.aboutData.og_image || '';
                if (this.form.og_image) {
                    this.ogImagePreview = this.resolveImageUrl(this.form.og_image);
                }

                // Initialize editor with content if on story tab
                this.$nextTick(() => {
                    if (this.activeTab === 'story') {
                        setTimeout(() => {
                            this.initStoryEditor();
                        }, 500);
                    }
                });
            } catch (error) {
                console.error('Error loading about data:', error);
                this.handleApiError(error, 'Failed to load about content');
            } finally {
                this.loading = false;
            }
        },
        handleStoryImageSelect(file) {
            // Clear previous error
            this.storyImageError = null;

            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.storyImageError = 'Image file size must be less than 5MB';
                    this.storyImageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.storyImageError = 'Please select a valid image file (jpeg, jpg, png, gif, webp)';
                    this.storyImageFile = null;
                    return;
                }

                this.storyImageFile = selectedFile;

                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.storyImagePreview = e.target.result;
                    };
                    reader.onerror = () => {
                        this.showError('Failed to read image file');
                        this.storyImageFile = null;
                        this.storyImagePreview = null;
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    this.storyImagePreview = null;
                }
            } else {
                this.storyImageFile = null;
                if (!this.form.story.image) {
                    this.storyImagePreview = null;
                } else {
                    // Update preview when image URL is directly entered (if no file is selected)
                    this.storyImagePreview = this.resolveImageUrl(this.form.story.image);
                }
            }
        },
        removeStoryImage() {
            if (confirm('Are you sure you want to remove this image?')) {
                this.storyImagePreview = null;
                this.storyImageFile = null;
                this.form.story.image = '';
                this.storyImageError = null; // Clear error
            }
        },
        async uploadStoryImage() {
            if (!this.storyImageFile) return null;

            const fileToUpload = Array.isArray(this.storyImageFile) ? this.storyImageFile[0] : this.storyImageFile;
            if (!fileToUpload) return null;

            this.uploadingStoryImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'about');

                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = normalizeUploadPath(response.data.path || response.data.url);
                    this.form.story.image = uploadedPath;
                    this.storyImagePreview = resolveUploadUrl(response.data.url || uploadedPath);
                    this.storyImageFile = null;
                    this.storyImageError = null; // Clear error on success
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload image');
                }
            } catch (error) {
                console.error('Error uploading story image:', error);
                console.error('Error response:', error.response?.data);

                // Extract validation errors
                let errorMessage = 'Failed to upload image';
                if (error.response?.status === 422) {
                    // Validation error
                    const errors = error.response.data.errors;
                    if (errors && errors.image) {
                        errorMessage = Array.isArray(errors.image) ? errors.image.join(', ') : errors.image;
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                } else if (error.message) {
                    errorMessage = error.message;
                }

                this.storyImageError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingStoryImage = false;
            }
        },
        addStat() {
            this.form.story.stats.push({ value: '', label: '' });
        },
        removeStat(index) {
            this.form.story.stats.splice(index, 1);
        },
        addValue() {
            this.form.values.push({ title: '', description: '', icon: 'mdi-check-circle' });
        },
        removeValue(index) {
            this.form.values.splice(index, 1);
        },
        addTeamMember() {
            this.form.team.push({
                name: '',
                role: '',
                image: '',
                linkedin: '',
                twitter: '',
                imagePreview: null,
                imageFile: null,
                uploadingImage: false,
                imageError: null
            });
        },
        removeTeamMember(index) {
            this.form.team.splice(index, 1);
        },
        handleTeamMemberImageSelect(file, index) {
            // Clear previous error
            if (this.form.team[index]) {
                this.form.team[index].imageError = null;
            }

            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.form.team[index].imageError = 'Image file size must be less than 5MB';
                    this.form.team[index].imageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.form.team[index].imageError = 'Please select a valid image file (jpeg, jpg, png, gif, webp)';
                    this.form.team[index].imageFile = null;
                    return;
                }

                this.form.team[index].imageFile = selectedFile;

                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.form.team[index].imagePreview = e.target.result;
                    };
                    reader.onerror = () => {
                        this.showError('Failed to read image file');
                        this.form.team[index].imageFile = null;
                        this.form.team[index].imagePreview = null;
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    this.form.team[index].imagePreview = null;
                }
            } else {
                this.form.team[index].imageFile = null;
                if (!this.form.team[index].image) {
                    this.form.team[index].imagePreview = null;
                } else {
                    // Update preview when image URL is directly entered (if no file is selected)
                    this.form.team[index].imagePreview = this.resolveImageUrl(this.form.team[index].image);
                }
            }
        },
        removeTeamMemberImage(index) {
            if (confirm('Are you sure you want to remove this image?')) {
                this.form.team[index].imagePreview = null;
                this.form.team[index].imageFile = null;
                this.form.team[index].image = '';
                this.form.team[index].imageError = null; // Clear error
            }
        },
        async uploadTeamMemberImage(index) {
            const member = this.form.team[index];
            if (!member.imageFile) return null;

            const fileToUpload = Array.isArray(member.imageFile) ? member.imageFile[0] : member.imageFile;
            if (!fileToUpload) return null;

            member.uploadingImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'about/team');
                if (member.name) {
                    formData.append('prefix', member.name);
                }

                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                // Check if response is successful
                if (response.status === 200 && (response.data.success || response.data.path)) {
                    const uploadedPath = normalizeUploadPath(response.data.path || response.data.url);
                    member.image = uploadedPath;
                    member.imagePreview = resolveUploadUrl(response.data.url || uploadedPath);
                    member.imageFile = null;
                    member.imageError = null; // Clear error on success
                    return uploadedPath;
                } else {
                    const errorMsg = response.data?.message || response.data?.error || 'Failed to upload image';
                    throw new Error(errorMsg);
                }
            } catch (error) {
                console.error('Error uploading team member image:', error);
                console.error('Error response:', error.response?.data);

                // Extract validation errors
                let errorMessage = 'Failed to upload image';
                if (error.response?.status === 422) {
                    // Validation error
                    const errors = error.response.data.errors;
                    if (errors && errors.image) {
                        errorMessage = Array.isArray(errors.image) ? errors.image.join(', ') : errors.image;
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                } else if (error.message) {
                    errorMessage = error.message;
                }

                member.imageError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                member.uploadingImage = false;
            }
        },
        async saveAbout() {
            // Validate form
            if (!this.$refs.formRef) {
                this.showError('Form reference not found');
                return;
            }

            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            this.saving = true;

            try {
                // Upload story image if a new file is selected
                if (this.storyImageFile) {
                    try {
                        await this.uploadStoryImage();
                    } catch (error) {
                        const errorMessage = error.message || 'Failed to upload story image';
                        this.showError(errorMessage);
                        this.saving = false;
                        return;
                    }
                }

                // Upload team member images if new files are selected
                for (let i = 0; i < this.form.team.length; i++) {
                    if (this.form.team[i].imageFile) {
                        try {
                            await this.uploadTeamMemberImage(i);
                        } catch (error) {
                            const errorMessage = error.message || `Failed to upload image for ${this.form.team[i].name || 'team member'}`;
                            this.showError(errorMessage);
                            this.saving = false;
                            return;
                        }
                    }
                }

                // Upload OG image if a new file is selected
                if (this.ogImageFile) {
                    try {
                        await this.uploadOgImage();
                    } catch (error) {
                        const errorMessage = error.message || 'Failed to upload OG image';
                        this.showError(errorMessage);
                        this.saving = false;
                        return;
                    }
                }

                const payload = { ...this.form };

                try {
                    await this.$axios.put('/api/v1/about', payload, {
                        headers: this.getAuthHeaders()
                    });

                    this.showSuccess('About content saved successfully');
                    this.$emit('saved');
                    this.closeDialog();
                } catch (putError) {
                    // If 404, try POST instead
                    if (putError.response?.status === 404) {
                        await this.$axios.post('/api/v1/about', payload, {
                            headers: this.getAuthHeaders()
                        });

                        this.showSuccess('About content created successfully');
                        this.$emit('saved');
                        this.closeDialog();
                    } else {
                        this.handleApiError(putError, 'Failed to save about content');
                    }
                }
            } catch (error) {
                this.handleApiError(error, 'Failed to save about content');
            } finally {
                this.saving = false;
            }
        },
        generateMetaTitle() {
            if (this.form.hero.title) {
                this.form.meta_title = this.form.hero.title.length > 60
                    ? this.form.hero.title.substring(0, 57) + '...'
                    : this.form.hero.title;
            }
        },
        generateMetaDescription() {
            let description = '';
            if (this.form.hero.subtitle) {
                description = this.form.hero.subtitle;
            } else if (this.form.story.description) {
                // Strip HTML tags
                description = this.form.story.description.replace(/<[^>]*>/g, '').trim();
            }
            if (description) {
                if (description.length > 160) {
                    // Try to cut at a word boundary
                    description = description.substring(0, 157);
                    const lastSpace = description.lastIndexOf(' ');
                    if (lastSpace > 120) {
                        description = description.substring(0, lastSpace) + '...';
                    } else {
                        description += '...';
                    }
                }
                this.form.meta_description = description;
            }
        },
        generateMetaKeywords() {
            const keywords = this.generateSuggestedKeywords();
            if (keywords) {
                this.form.meta_keywords = keywords;
            }
        },
        generateSuggestedKeywords() {
            const keywords = [];

            // Extract keywords from hero title
            if (this.form.hero.title) {
                const titleWords = this.form.hero.title
                    .toLowerCase()
                    .replace(/[^\w\s]/g, ' ')
                    .split(/\s+/)
                    .filter(word => word.length > 3)
                    .slice(0, 5);
                keywords.push(...titleWords);
            }

            // Extract keywords from hero subtitle
            if (this.form.hero.subtitle) {
                const descWords = this.form.hero.subtitle
                    .toLowerCase()
                    .replace(/[^\w\s]/g, ' ')
                    .split(/\s+/)
                    .filter(word => word.length > 4)
                    .slice(0, 5);
                keywords.push(...descWords);
            }

            // Add values titles as keywords
            if (this.form.values && this.form.values.length > 0) {
                this.form.values.forEach(value => {
                    if (value.title) {
                        const valueWords = value.title
                            .toLowerCase()
                            .replace(/[^\w\s]/g, ' ')
                            .split(/\s+/)
                            .filter(word => word.length > 3)
                            .slice(0, 2);
                        keywords.push(...valueWords);
                    }
                });
            }

            // Remove duplicates and common words
            const commonWords = ['the', 'and', 'for', 'with', 'from', 'this', 'that', 'your', 'our', 'about', 'company', 'team'];
            const uniqueKeywords = [...new Set(keywords)]
                .filter(word => !commonWords.includes(word))
                .slice(0, 10);

            return uniqueKeywords.length > 0 ? uniqueKeywords.join(', ') : null;
        },
        applySuggestedMetaTitle() {
            if (this.suggestedMetaTitle) {
                this.form.meta_title = this.suggestedMetaTitle;
            }
        },
        applySuggestedMetaDescription() {
            if (this.suggestedMetaDescription) {
                this.form.meta_description = this.suggestedMetaDescription;
            }
        },
        applySuggestedMetaKeywords() {
            if (this.suggestedMetaKeywords) {
                this.form.meta_keywords = this.suggestedMetaKeywords;
            }
        },
        getMetaTitleColor() {
            const length = this.form.meta_title ? this.form.meta_title.length : 0;
            if (length === 0) return 'grey';
            if (length < 50) return 'warning';
            if (length > 60) return 'error';
            return 'success';
        },
        getMetaDescriptionColor() {
            const length = this.form.meta_description ? this.form.meta_description.length : 0;
            if (length === 0) return 'grey';
            if (length < 120) return 'warning';
            if (length > 160) return 'error';
            return 'success';
        },
        getPreviewTitle() {
            return this.form.meta_title || this.form.hero.title || 'About Us';
        },
        getPreviewDescription() {
            return this.form.meta_description || this.form.hero.subtitle || 'Learn more about our company, our story, and our team...';
        },
        getPreviewUrl() {
            return window.location.origin + '/about';
        },
        handleOgImageSelect(file) {
            // Clear previous error
            this.ogImageError = null;

            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                if (selectedFile && selectedFile.size > 5242880) {
                    this.ogImageError = 'OG image file size must be less than 5MB';
                    this.ogImageFile = null;
                    return;
                }

                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.ogImageError = 'Please select a valid image file (jpeg, jpg, png, gif, webp)';
                    this.ogImageFile = null;
                    return;
                }

                this.ogImageFile = selectedFile;

                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.ogImagePreview = e.target.result;
                    };
                    reader.onerror = () => {
                        this.showError('Failed to read image file');
                        this.ogImageFile = null;
                        this.ogImagePreview = null;
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    this.ogImagePreview = null;
                }
            } else {
                this.ogImageFile = null;
                if (!this.form.og_image) {
                    this.ogImagePreview = null;
                } else {
                    // Update preview when OG image URL is directly entered (if no file is selected)
                    this.ogImagePreview = this.resolveImageUrl(this.form.og_image);
                }
            }
        },
        removeOgImage() {
            if (confirm('Are you sure you want to remove the Open Graph image?')) {
                this.ogImagePreview = null;
                this.ogImageFile = null;
                this.form.og_image = '';
                this.ogImageError = null; // Clear error
            }
        },
        async uploadOgImage() {
            if (!this.ogImageFile) return null;

            const fileToUpload = Array.isArray(this.ogImageFile) ? this.ogImageFile[0] : this.ogImageFile;
            if (!fileToUpload) return null;

            this.uploadingOgImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'about/og');

                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = normalizeUploadPath(response.data.path || response.data.url);
                    this.form.og_image = uploadedPath;
                    this.ogImagePreview = resolveUploadUrl(response.data.url || uploadedPath);
                    this.ogImageFile = null;
                    this.ogImageError = null; // Clear error on success
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload OG image');
                }
            } catch (error) {
                console.error('Error uploading OG image:', error);
                console.error('Error response:', error.response?.data);

                // Extract validation errors
                let errorMessage = 'Failed to upload OG image';
                if (error.response?.status === 422) {
                    // Validation error
                    const errors = error.response.data.errors;
                    if (errors && errors.image) {
                        errorMessage = Array.isArray(errors.image) ? errors.image.join(', ') : errors.image;
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                } else if (error.response?.data?.error) {
                    errorMessage = error.response.data.error;
                } else if (error.message) {
                    errorMessage = error.message;
                }

                this.ogImageError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingOgImage = false;
            }
        },
        closeDialog() {
            this.dialog = false;
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        }
    }
};
</script>

<style scoped>
/* SEO Preview Styles */
.search-preview {
    font-family: Arial, sans-serif;
    max-width: 600px;
}

.search-url {
    font-size: 14px;
    line-height: 1.3;
}

.search-title {
    font-size: 20px;
    line-height: 1.3;
    font-weight: normal;
    text-decoration: none;
}

.search-title:hover {
    text-decoration: underline;
}

.search-description {
    font-size: 14px;
    line-height: 1.58;
    color: #545454;
}
</style>
