<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Services Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create
                Service</v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="publishedFilter" :items="publishedOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadServices"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search services" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadServices"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Services Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Services</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="services.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                            pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th class="sortable" @click="onSort('title')">
                                <div class="d-flex align-center">
                                    Title
                                    <v-icon :icon="getSortIcon('title')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('slug')">
                                <div class="d-flex align-center">
                                    Slug
                                    <v-icon :icon="getSortIcon('slug')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('published')">
                                <div class="d-flex align-center">
                                    Published
                                    <v-icon :icon="getSortIcon('published')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="service in services" :key="service.id">
                            <td>{{ service.title }}</td>
                            <td>
                                <v-chip size="small" variant="outlined">{{ service.slug }}</v-chip>
                            </td>
                            <td>
                                <v-chip :color="service.published ? 'success' : 'error'" size="small">
                                    {{ service.published ? 'Published' : 'Draft' }}
                                </v-chip>
                            </td>
                            <td>
                                <v-btn size="small" icon="mdi-eye" @click="viewService(service)" variant="text"
                                    color="info" title="View Service"></v-btn>
                                <v-btn size="small" icon="mdi-pencil" @click="editService(service)" variant="text"
                                    title="Edit Service"></v-btn>
                                <v-btn size="small" icon="mdi-delete" @click="deleteService(service.id)" variant="text"
                                    color="error" title="Delete Service"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="services.length === 0">
                            <td colspan="4" class="text-center py-4">No services found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <div class="d-flex justify-space-between align-center mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="services.length > 0">
                            Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                                pagination.total) }} of {{ pagination.total }} records
                        </span>
                        <span v-else>
                            No records found
                        </span>
                    </div>
                    <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                        @update:model-value="loadServices"></v-pagination>
                </div>
            </v-card-text>
        </v-card>

        <!-- Service Form Dialog -->
        <v-dialog v-model="showDialog" max-width="1200" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">
                        {{ editingService ? 'Edit Service' : 'Create New Service' }}
                    </span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                        :disabled="saving"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0">
                    <!-- Loading State -->
                    <div v-if="loadingService" class="d-flex align-center justify-center pa-12">
                        <div class="text-center">
                            <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                            <p class="text-body-1 text-medium-emphasis mt-4">Loading service data...</p>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <v-form ref="formRef" v-else>
                        <v-tabs v-model="activeTab" bg-color="grey-lighten-4">
                            <v-tab value="basic">Basic Information</v-tab>
                            <v-tab value="content">Content</v-tab>
                            <v-tab value="seo">SEO & Meta</v-tab>
                        </v-tabs>

                        <v-window v-model="activeTab">
                            <!-- Basic Information Tab -->
                            <v-window-item value="basic">
                                <div class="pa-6">
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field v-model="form.title" label="Service Title *"
                                                variant="outlined" :rules="[v => !!v || 'Title is required']"
                                                hint="Enter the service title" @blur="generateSlug"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="form.slug" label="Slug *" variant="outlined"
                                                :rules="[v => !!v || 'Slug is required', v => !v || /^[a-z0-9-]+$/.test(v) || 'Slug can only contain lowercase letters, numbers, and hyphens']"
                                                hint="URL-friendly version (auto-generated from title)" persistent-hint>
                                                <template v-slot:append>
                                                    <v-btn icon="mdi-refresh" size="small" variant="text"
                                                        @click="generateSlug" :disabled="!form.title"></v-btn>
                                                </template>
                                            </v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="form.order" label="Order" type="number"
                                                variant="outlined" hint="Display order (lower numbers first)"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea v-model="form.short_description" label="Short Description"
                                                variant="outlined" rows="3"
                                                hint="Brief description shown in service listings"
                                                persistent-hint></v-textarea>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="form.icon" label="Icon" variant="outlined"
                                                hint="Material Design Icon name (e.g., mdi-battery-charging-high)"
                                                persistent-hint prepend-inner-icon="mdi-information-outline">
                                                <template v-slot:append>
                                                    <v-icon v-if="form.icon" :icon="form.icon" size="small"></v-icon>
                                                </template>
                                            </v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field v-model="form.price_range" label="Price Range"
                                                variant="outlined" hint="e.g., $100 - $500"
                                                persistent-hint></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-switch v-model="form.published" label="Published" color="success"
                                                hint="Publish this service on the public website"
                                                persistent-hint></v-switch>
                                        </v-col>
                                    </v-row>
                                </div>
                            </v-window-item>

                            <!-- Content Tab -->
                            <v-window-item value="content">
                                <div class="pa-6">
                                    <v-row>
                                        <v-col cols="12">
                                            <v-label class="mb-2">Full Description</v-label>
                                            <div class="rich-text-editor-wrapper">
                                                <div ref="editorContainer" class="rich-text-editor"></div>
                                            </div>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-label class="mb-2">Service Image</v-label>
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex flex-column flex-md-row align-start">
                                                    <!-- Image Preview -->
                                                    <div v-if="imagePreview" class="mr-md-4 mb-4 mb-md-0">
                                                        <v-img :src="imagePreview" max-width="300" max-height="300"
                                                            class="rounded elevation-2" cover></v-img>
                                                        <div class="mt-2">
                                                            <v-btn color="error" size="small" variant="text"
                                                                @click="removeImage" prepend-icon="mdi-delete">
                                                                Remove Image
                                                            </v-btn>
                                                        </div>
                                                    </div>

                                                    <!-- Upload Section -->
                                                    <div class="flex-grow-1">
                                                        <v-file-input v-model="imageFile" accept="image/*"
                                                            label="Select Service Image" variant="outlined"
                                                            prepend-icon="" prepend-inner-icon="mdi-image" show-size
                                                            clearable
                                                            hint="Recommended size: 1200x800px or larger. Max file size: 5MB"
                                                            persistent-hint @update:model-value="handleImageSelect">
                                                            <template v-slot:append>
                                                                <v-progress-circular v-if="uploadingImage" indeterminate
                                                                    color="primary" size="24">
                                                                </v-progress-circular>
                                                            </template>
                                                        </v-file-input>

                                                        <v-alert v-if="imageFile && imageFile.size > 5242880"
                                                            type="warning" variant="tonal" density="compact"
                                                            class="mt-2">
                                                            File size is larger than 5MB. Please choose a smaller image.
                                                        </v-alert>

                                                        <div v-if="!imagePreview && !imageFile"
                                                            class="text-caption text-medium-emphasis mt-2">
                                                            No image selected. Upload an image to display with this
                                                            service.
                                                        </div>
                                                    </div>
                                                </div>
                                            </v-card>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-label class="mb-2">Features</v-label>
                                            <v-textarea v-model="featuresText" variant="outlined" rows="4"
                                                hint="Enter one feature per line" persistent-hint
                                                @blur="updateFeaturesFromText"></v-textarea>
                                            <div v-if="form.features && form.features.length > 0" class="mt-2">
                                                <v-chip v-for="(feature, i) in form.features" :key="i" class="ma-1"
                                                    closable @click:close="removeFeature(i)">
                                                    {{ feature }}
                                                </v-chip>
                                            </div>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-label class="mb-2">Benefits</v-label>
                                            <v-textarea v-model="benefitsText" variant="outlined" rows="4"
                                                hint="Enter one benefit per line" persistent-hint
                                                @blur="updateBenefitsFromText"></v-textarea>
                                            <div v-if="form.benefits && form.benefits.length > 0" class="mt-2">
                                                <v-chip v-for="(benefit, i) in form.benefits" :key="i" class="ma-1"
                                                    closable @click:close="removeBenefit(i)" color="success"
                                                    variant="flat">
                                                    {{ benefit }}
                                                </v-chip>
                                            </div>
                                        </v-col>
                                    </v-row>
                                </div>
                            </v-window-item>

                            <!-- SEO Tab -->
                            <v-window-item value="seo">
                                <div class="pa-6">
                                    <v-alert type="info" variant="tonal" class="mb-6" density="compact">
                                        <div class="text-body-2">
                                            <strong>SEO Best Practices:</strong> Fill in these fields to improve your
                                            service visibility in search
                                            engines and social media.
                                            If left empty, default values from the service title and description will be
                                            used.
                                        </div>
                                    </v-alert>

                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field v-model="form.meta_title" label="Meta Title"
                                                variant="outlined"
                                                hint="SEO title for search engines (recommended: 50-60 characters)"
                                                persistent-hint :counter="60" :color="getMetaTitleColor()"
                                                prepend-inner-icon="mdi-format-title">
                                                <template v-slot:append>
                                                    <v-btn icon="mdi-refresh" size="small" variant="text"
                                                        @click="generateMetaTitle" :disabled="!form.title"
                                                        title="Auto-generate from service title">
                                                    </v-btn>
                                                </template>
                                            </v-text-field>
                                            <div class="text-caption" :class="getMetaTitleColor() + '--text'">
                                                {{ form.meta_title.length }}/60 characters
                                                <span v-if="form.meta_title.length < 50" class="ml-2">
                                                    (Too short - recommended: 50-60)
                                                </span>
                                                <span v-else-if="form.meta_title.length > 60" class="ml-2">
                                                    (Too long - may be truncated)
                                                </span>
                                                <span v-else class="ml-2 text-success">✓ Good length</span>
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
                                                        :disabled="!form.short_description"
                                                        title="Auto-generate from short description">
                                                    </v-btn>
                                                </template>
                                            </v-textarea>
                                            <div class="text-caption" :class="getMetaDescriptionColor() + '--text'">
                                                {{ form.meta_description.length }}/160 characters
                                                <span v-if="form.meta_description.length < 120" class="ml-2">
                                                    (Too short - recommended: 150-160)
                                                </span>
                                                <span v-else-if="form.meta_description.length > 160" class="ml-2">
                                                    (Too long - may be truncated)
                                                </span>
                                                <span v-else class="ml-2 text-success">✓ Good length</span>
                                            </div>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-text-field v-model="form.meta_keywords" label="Meta Keywords"
                                                variant="outlined"
                                                hint="Comma-separated keywords for SEO (e.g., power backup, UPS systems, battery)"
                                                persistent-hint prepend-inner-icon="mdi-tag-multiple">
                                            </v-text-field>
                                            <div class="text-caption text-medium-emphasis">
                                                {{(form.meta_keywords || '').split(',').filter(k => k.trim()).length}}
                                                keywords entered
                                            </div>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-divider class="my-4"></v-divider>
                                            <h3 class="text-h6 font-weight-bold mb-4">Open Graph (Social Media)</h3>
                                            <p class="text-body-2 text-medium-emphasis mb-4">
                                                This image will be displayed when your service is shared on social media
                                                platforms like Facebook,
                                                Twitter, LinkedIn, etc.
                                            </p>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-label class="mb-2">Open Graph Image</v-label>
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex flex-column flex-md-row align-start">
                                                    <!-- OG Image Preview -->
                                                    <div v-if="ogImagePreview" class="mr-md-4 mb-4 mb-md-0">
                                                        <v-img :src="ogImagePreview" max-width="300" max-height="300"
                                                            class="rounded elevation-2" cover></v-img>
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
                                                                <v-progress-circular v-if="uploadingOgImage"
                                                                    indeterminate color="primary" size="24">
                                                                </v-progress-circular>
                                                            </template>
                                                        </v-file-input>

                                                        <v-alert v-if="ogImageFile && ogImageFile.size > 5242880"
                                                            type="warning" variant="tonal" density="compact"
                                                            class="mt-2">
                                                            File size is larger than 5MB. Please choose a smaller image.
                                                        </v-alert>

                                                        <div v-if="!ogImagePreview && !ogImageFile"
                                                            class="text-caption text-medium-emphasis mt-2">
                                                            No image selected. If not provided, the service image will
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
                    <v-btn color="primary" variant="flat" @click="saveService" :loading="saving">
                        {{ editingService ? 'Update' : 'Create' }} Service
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Service Details View Dialog -->
        <v-dialog v-model="showDetailsDialog" max-width="1200" scrollable>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-info text-white pa-4">
                    <div class="d-flex align-center">
                        <v-icon icon="mdi-eye" class="mr-3"></v-icon>
                        <span class="text-h5 font-weight-bold">Service Details</span>
                    </div>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDetailsDialog"
                        :disabled="loadingServiceDetails"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0">
                    <!-- Loading State -->
                    <div v-if="loadingServiceDetails" class="d-flex align-center justify-center pa-12">
                        <div class="text-center">
                            <v-progress-circular indeterminate color="info" size="64"></v-progress-circular>
                            <p class="text-body-1 text-medium-emphasis mt-4">Loading service details...</p>
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div v-else-if="serviceDetails">
                        <v-tabs v-model="detailsTab" color="info" bg-color="grey-lighten-4">
                            <v-tab value="basic">
                                <v-icon icon="mdi-information" class="mr-2"></v-icon>
                                Basic Info
                            </v-tab>
                            <v-tab value="content">
                                <v-icon icon="mdi-text" class="mr-2"></v-icon>
                                Content
                            </v-tab>
                            <v-tab value="seo">
                                <v-icon icon="mdi-search-web" class="mr-2"></v-icon>
                                SEO & Meta
                            </v-tab>
                        </v-tabs>

                        <v-divider></v-divider>

                        <v-window v-model="detailsTab" class="pa-6">
                            <!-- Basic Info Tab -->
                            <v-window-item value="basic">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Service Title</div>
                                        <div class="text-h6 font-weight-bold mb-4">{{ serviceDetails.title || '-' }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Slug</div>
                                        <div class="text-body-1 mb-4">
                                            <v-chip size="small" variant="outlined">{{ serviceDetails.slug || '-'
                                                }}</v-chip>
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Order</div>
                                        <div class="text-body-1 mb-4">{{ serviceDetails.order || 0 }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Short Description</div>
                                        <div class="text-body-1 mb-4">{{ serviceDetails.short_description || '-' }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Icon</div>
                                        <div class="text-body-1 mb-4">
                                            <v-icon v-if="serviceDetails.icon" :icon="serviceDetails.icon"
                                                size="large"></v-icon>
                                            <span v-else class="text-grey">-</span>
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Price Range</div>
                                        <div class="text-body-1 mb-4">{{ serviceDetails.price_range || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Status</div>
                                        <div class="mb-4">
                                            <v-chip :color="serviceDetails.published ? 'success' : 'error'"
                                                size="small">
                                                {{ serviceDetails.published ? 'Published' : 'Draft' }}
                                            </v-chip>
                                        </div>
                                    </v-col>
                                    <v-col cols="12"
                                        v-if="serviceDetails.features && serviceDetails.features.length > 0">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Features</div>
                                        <div class="mb-4">
                                            <v-chip v-for="(feature, i) in serviceDetails.features" :key="i"
                                                class="ma-1" size="small" color="primary" variant="flat">
                                                {{ feature }}
                                            </v-chip>
                                        </div>
                                    </v-col>
                                    <v-col cols="12"
                                        v-if="serviceDetails.benefits && serviceDetails.benefits.length > 0">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Benefits</div>
                                        <div class="mb-4">
                                            <v-chip v-for="(benefit, i) in serviceDetails.benefits" :key="i"
                                                class="ma-1" size="small" color="success" variant="flat">
                                                {{ benefit }}
                                            </v-chip>
                                        </div>
                                    </v-col>
                                    <v-col cols="12" v-if="serviceDetails.created_at">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Created At</div>
                                        <div class="text-body-1 mb-4">{{ formatDateTime(serviceDetails.created_at) }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12" v-if="serviceDetails.updated_at">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Updated At</div>
                                        <div class="text-body-1 mb-4">{{ formatDateTime(serviceDetails.updated_at) }}
                                        </div>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- Content Tab -->
                            <v-window-item value="content">
                                <v-row>
                                    <v-col cols="12" v-if="serviceDetails.image">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Service Image</div>
                                        <v-img :src="serviceDetails.image" max-width="600" max-height="400"
                                            class="rounded elevation-2 mb-4" cover></v-img>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Full Description</div>
                                        <div class="text-body-1" v-html="serviceDetails.description || '-'"></div>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- SEO Tab -->
                            <v-window-item value="seo">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Title</div>
                                        <div class="text-body-1 mb-4">{{ serviceDetails.meta_title || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Description</div>
                                        <div class="text-body-1 mb-4">{{ serviceDetails.meta_description || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Keywords</div>
                                        <div class="text-body-1 mb-4">{{ serviceDetails.meta_keywords || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12" v-if="serviceDetails.og_image">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Open Graph Image</div>
                                        <v-img :src="serviceDetails.og_image" max-width="600" max-height="400"
                                            class="rounded elevation-2 mb-4" cover></v-img>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                        </v-window>
                    </div>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-5">
                    <v-spacer></v-spacer>
                    <v-btn color="primary" prepend-icon="mdi-pencil" @click="editFromDetails">
                        Edit Service
                    </v-btn>
                    <v-btn color="grey" variant="text" @click="closeDetailsDialog">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import Quill from 'quill';
// Import Quill styles
import 'quill/dist/quill.snow.css';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            services: [],
            showDialog: false,
            editingService: null,
            loadingService: false,
            activeTab: 'basic',
            showDetailsDialog: false,
            loadingServiceDetails: false,
            serviceDetails: null,
            detailsTab: 'basic',
            publishedFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ],
            imageFile: null,
            imagePreview: null,
            uploadingImage: false,
            ogImageFile: null,
            ogImagePreview: null,
            uploadingOgImage: false,
            featuresText: '',
            benefitsText: '',
            quillEditor: null,
            form: {
                title: '',
                slug: '',
                short_description: '',
                description: '',
                icon: '',
                image: '',
                price_range: '',
                features: [],
                benefits: [],
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: '',
                published: false,
                order: 0
            }
        };
    },
    async mounted() {
        await this.loadServices();
    },
    methods: {
        async loadServices() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }

                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                const response = await axios.get('/api/v1/services', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.services = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load services');
            } finally {
                this.loading = false;
            }
        },
        openDialog(service = null) {
            this.editingService = service;
            this.showDialog = true;
            this.activeTab = 'basic';

            if (service) {
                this.loadServiceForEdit(service);
            } else {
                this.resetForm();
            }

            // Don't initialize editor here - wait until content tab is active
            // Editor will be initialized when user switches to content tab
        },
        closeDialog() {
            if (!this.saving && !this.loadingService) {
                this.destroyEditor();
                this.showDialog = false;
                this.editingService = null;
                this.resetForm();
            }
        },
        initEditor() {
            // Destroy existing editor if any
            if (this.quillEditor) {
                this.destroyEditor();
            }

            // Wait for DOM to be ready and ensure dialog is fully rendered
            this.$nextTick(() => {
                // Double check that the container exists and is visible
                if (this.$refs.editorContainer && this.activeTab === 'content') {
                    try {
                        // Clear any existing content in the container
                        this.$refs.editorContainer.innerHTML = '';

                        this.quillEditor = new Quill(this.$refs.editorContainer, {
                            theme: 'snow',
                            modules: {
                                toolbar: [
                                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    [{ 'color': [] }, { 'background': [] }],
                                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                    [{ 'align': [] }],
                                    ['link', 'image'],
                                    ['blockquote', 'code-block'],
                                    ['clean']
                                ]
                            },
                            placeholder: 'Enter detailed description of the service...'
                        });

                        // Set initial content if editing
                        if (this.form.description) {
                            this.quillEditor.root.innerHTML = this.form.description;
                        }

                        // Update form.description when editor content changes
                        this.quillEditor.on('text-change', () => {
                            this.form.description = this.quillEditor.root.innerHTML;
                        });

                        // Also update on selection change to catch paste events
                        this.quillEditor.on('selection-change', () => {
                            this.form.description = this.quillEditor.root.innerHTML;
                        });
                    } catch (error) {
                        console.error('Error initializing Quill editor:', error);
                        this.showError('Failed to initialize text editor');
                    }
                }
            });
        },
        destroyEditor() {
            if (this.quillEditor) {
                try {
                    // Save content before destroying
                    if (this.quillEditor.root && this.quillEditor.root.innerHTML) {
                        const content = this.quillEditor.root.innerHTML.trim();
                        if (content && content !== '<p><br></p>') {
                            this.form.description = content;
                        }
                    }
                } catch (error) {
                    console.error('Error saving editor content:', error);
                }

                try {
                    // Remove editor instance and clear container
                    this.quillEditor = null;
                    if (this.$refs.editorContainer) {
                        this.$refs.editorContainer.innerHTML = '';
                    }
                } catch (error) {
                    console.error('Error destroying editor:', error);
                    this.quillEditor = null;
                }
            } else if (this.$refs.editorContainer) {
                // Clean up container even if editor wasn't initialized
                this.$refs.editorContainer.innerHTML = '';
            }
        },
        resetForm() {
            // Destroy editor before resetting form
            if (this.quillEditor) {
                this.destroyEditor();
            }

            this.form = {
                title: '',
                slug: '',
                short_description: '',
                description: '',
                icon: '',
                image: '',
                price_range: '',
                features: [],
                benefits: [],
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: '',
                published: false,
                order: 0
            };
            this.imageFile = null;
            this.imagePreview = null;
            this.ogImageFile = null;
            this.ogImagePreview = null;
            this.featuresText = '';
            this.benefitsText = '';

            if (this.$refs.formRef) {
                this.$refs.formRef.resetValidation();
            }
        },
        async loadServiceForEdit(service) {
            this.loadingService = true;
            try {
                const response = await axios.get(`/api/v1/services/${service.id}`, {
                    headers: this.getAuthHeaders()
                });
                const data = response.data;

                this.form = {
                    title: data.title || '',
                    slug: data.slug || '',
                    short_description: data.short_description || '',
                    description: data.description || '',
                    icon: data.icon || '',
                    image: data.image || '',
                    price_range: data.price_range || '',
                    features: Array.isArray(data.features) ? [...data.features] : [],
                    benefits: Array.isArray(data.benefits) ? [...data.benefits] : [],
                    meta_title: data.meta_title || '',
                    meta_description: data.meta_description || '',
                    meta_keywords: data.meta_keywords || '',
                    og_image: data.og_image || '',
                    published: data.published || false,
                    order: data.order || 0
                };

                // Set previews
                this.imagePreview = data.image || null;
                this.ogImagePreview = data.og_image || null;

                // Set text for features and benefits
                this.featuresText = Array.isArray(data.features) ? data.features.join('\n') : '';
                this.benefitsText = Array.isArray(data.benefits) ? data.benefits.join('\n') : '';

                // If editor is already initialized and content tab is active, update it
                this.$nextTick(() => {
                    if (this.quillEditor && this.activeTab === 'content' && this.form.description) {
                        try {
                            this.quillEditor.root.innerHTML = this.form.description;
                        } catch (error) {
                            console.error('Error updating editor content:', error);
                        }
                    }
                });
            } catch (error) {
                this.handleApiError(error, 'Failed to load service data');
                this.closeDialog();
            } finally {
                this.loadingService = false;
            }
        },
        editService(service) {
            this.openDialog(service);
        },
        async viewService(service) {
            this.showDetailsDialog = true;
            this.loadingServiceDetails = true;
            this.detailsTab = 'basic';

            try {
                const response = await axios.get(`/api/v1/services/${service.id}`, {
                    headers: this.getAuthHeaders()
                });

                const data = response.data;
                if (!data) {
                    throw new Error('No service data received');
                }

                this.serviceDetails = data;
            } catch (error) {
                this.handleApiError(error, 'Failed to load service details');
                this.closeDetailsDialog();
            } finally {
                this.loadingServiceDetails = false;
            }
        },
        closeDetailsDialog() {
            this.showDetailsDialog = false;
            this.serviceDetails = null;
            this.detailsTab = 'basic';
        },
        editFromDetails() {
            if (this.serviceDetails) {
                // Close details dialog
                this.closeDetailsDialog();
                // Open edit dialog with the service
                this.$nextTick(() => {
                    this.openDialog({ id: this.serviceDetails.id });
                });
            }
        },
        generateSlug() {
            if (this.form.title && !this.editingService) {
                this.form.slug = this.form.title
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }
        },
        handleImageSelect(file) {
            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                // Validate file size (5MB = 5242880 bytes)
                if (selectedFile && selectedFile.size > 5242880) {
                    this.showError('Image file size must be less than 5MB');
                    this.imageFile = null;
                    return;
                }

                // Validate file type
                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.showError('Please select a valid image file');
                    this.imageFile = null;
                    return;
                }

                this.imageFile = selectedFile;

                // Create preview
                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.onerror = () => {
                        this.showError('Failed to read image file');
                        this.imageFile = null;
                        this.imagePreview = null;
                    };
                    reader.readAsDataURL(selectedFile);
                } else {
                    // File cleared
                    this.imagePreview = null;
                }
            } else {
                // No file selected
                this.imageFile = null;
                if (!this.form.image) {
                    // Only clear preview if there's no existing image URL
                    this.imagePreview = null;
                }
            }
        },
        removeImage() {
            if (confirm('Are you sure you want to remove this image?')) {
                this.imagePreview = null;
                this.imageFile = null;
                this.form.image = '';
            }
        },
        handleOgImageSelect(file) {
            if (file) {
                const selectedFile = Array.isArray(file) ? file[0] : file;

                // Validate file size (5MB = 5242880 bytes)
                if (selectedFile && selectedFile.size > 5242880) {
                    this.showError('OG image file size must be less than 5MB');
                    this.ogImageFile = null;
                    return;
                }

                // Validate file type
                if (selectedFile && !selectedFile.type.startsWith('image/')) {
                    this.showError('Please select a valid image file');
                    this.ogImageFile = null;
                    return;
                }

                this.ogImageFile = selectedFile;

                // Create preview
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
                    // File cleared
                    this.ogImagePreview = null;
                }
            } else {
                // No file selected
                this.ogImageFile = null;
                if (!this.form.og_image) {
                    // Only clear preview if there's no existing image URL
                    this.ogImagePreview = null;
                }
            }
        },
        removeOgImage() {
            if (confirm('Are you sure you want to remove the Open Graph image?')) {
                this.ogImagePreview = null;
                this.ogImageFile = null;
                this.form.og_image = '';
            }
        },
        generateMetaTitle() {
            if (this.form.title) {
                // Generate meta title from service title (limit to 60 chars)
                this.form.meta_title = this.form.title.length > 60
                    ? this.form.title.substring(0, 57) + '...'
                    : this.form.title;
            }
        },
        generateMetaDescription() {
            if (this.form.short_description) {
                // Generate meta description from short description (limit to 160 chars)
                this.form.meta_description = this.form.short_description.length > 160
                    ? this.form.short_description.substring(0, 157) + '...'
                    : this.form.short_description;
            }
        },
        getMetaTitleColor() {
            const length = this.form.meta_title ? this.form.meta_title.length : 0;
            if (length === 0) return 'primary';
            if (length < 50) return 'warning';
            if (length > 60) return 'error';
            return 'success';
        },
        getMetaDescriptionColor() {
            const length = this.form.meta_description ? this.form.meta_description.length : 0;
            if (length === 0) return 'primary';
            if (length < 120) return 'warning';
            if (length > 160) return 'error';
            return 'success';
        },
        getPreviewTitle() {
            return this.form.meta_title || this.form.title || 'Service Title';
        },
        getPreviewDescription() {
            return this.form.meta_description || this.form.short_description || 'Service description will appear here in search results...';
        },
        getPreviewUrl() {
            const baseUrl = window.location.origin || 'https://example.com';
            const slug = this.form.slug || 'service-slug';
            return `${baseUrl}/services/${slug}`;
        },
        updateFeaturesFromText() {
            if (this.featuresText) {
                this.form.features = this.featuresText
                    .split('\n')
                    .map(f => f.trim())
                    .filter(f => f.length > 0);
            } else {
                this.form.features = [];
            }
        },
        updateBenefitsFromText() {
            if (this.benefitsText) {
                this.form.benefits = this.benefitsText
                    .split('\n')
                    .map(b => b.trim())
                    .filter(b => b.length > 0);
            } else {
                this.form.benefits = [];
            }
        },
        removeFeature(index) {
            this.form.features.splice(index, 1);
            this.featuresText = this.form.features.join('\n');
        },
        removeBenefit(index) {
            this.form.benefits.splice(index, 1);
            this.benefitsText = this.form.benefits.join('\n');
        },
        async uploadImage() {
            if (!this.imageFile) return null;

            const fileToUpload = Array.isArray(this.imageFile) ? this.imageFile[0] : this.imageFile;
            if (!fileToUpload) return null;

            this.uploadingImage = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'services');
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    return response.data.url;
                } else {
                    throw new Error(response.data.message || 'Failed to upload image');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to upload image';
                throw new Error(errorMessage);
            } finally {
                this.uploadingImage = false;
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
                formData.append('folder', 'services/og');
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    return response.data.url;
                } else {
                    throw new Error(response.data.message || 'Failed to upload OG image');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to upload OG image';
                throw new Error(errorMessage);
            } finally {
                this.uploadingOgImage = false;
            }
        },
        async saveService() {
            // Save editor content before validation
            if (this.quillEditor) {
                this.form.description = this.quillEditor.root.innerHTML;
            }

            // Validate form
            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            // Update arrays from text
            this.updateFeaturesFromText();
            this.updateBenefitsFromText();

            this.saving = true;

            try {
                // Upload images if new files are selected
                if (this.imageFile) {
                    try {
                        const imageUrl = await this.uploadImage();
                        if (imageUrl) {
                            this.form.image = imageUrl;
                        }
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload image');
                        return;
                    }
                }

                if (this.ogImageFile) {
                    try {
                        const ogImageUrl = await this.uploadOgImage();
                        if (ogImageUrl) {
                            this.form.og_image = ogImageUrl;
                        }
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload OG image');
                        return;
                    }
                }

                // Prepare payload
                const payload = {
                    title: this.form.title,
                    slug: this.form.slug,
                    short_description: this.form.short_description || null,
                    description: this.form.description || null,
                    icon: this.form.icon || null,
                    image: this.form.image || null,
                    price_range: this.form.price_range || null,
                    features: this.form.features.length > 0 ? this.form.features : null,
                    benefits: this.form.benefits.length > 0 ? this.form.benefits : null,
                    meta_title: this.form.meta_title || null,
                    meta_description: this.form.meta_description || null,
                    meta_keywords: this.form.meta_keywords || null,
                    og_image: this.form.og_image || null,
                    published: this.form.published || false,
                    order: this.form.order || 0
                };

                // Save service
                if (this.editingService) {
                    // Update existing service
                    await axios.put(`/api/v1/services/${this.editingService.id}`, payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service updated successfully');
                } else {
                    // Create new service
                    await axios.post('/api/v1/services', payload, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service created successfully');
                }

                // Reload services and close dialog
                await this.loadServices();
                this.closeDialog();
            } catch (error) {
                this.handleApiError(error, this.editingService ? 'Failed to update service' : 'Failed to create service');
            } finally {
                this.saving = false;
            }
        },
        async deleteService(id) {
            if (confirm('Are you sure you want to delete this service?')) {
                try {
                    await axios.delete(`/api/v1/services/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Service deleted successfully');
                    await this.loadServices();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting service');
                }
            }
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadServices();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadServices();
        }
    },
    watch: {
        activeTab(newTab) {
            // Initialize editor when switching to content tab
            if (newTab === 'content' && this.showDialog && !this.loadingService) {
                // Use setTimeout to ensure the tab transition is complete
                setTimeout(() => {
                    this.initEditor();
                }, 300);
            } else if (newTab !== 'content' && this.quillEditor) {
                // Save editor content when leaving content tab
                this.destroyEditor();
            }
        },
        showDialog(newVal) {
            // Clean up editor when dialog closes
            if (!newVal && this.quillEditor) {
                this.destroyEditor();
            }
        }
    }
};
</script>

<style scoped>
.rich-text-editor-wrapper {
    border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    border-radius: 4px;
    background-color: rgb(var(--v-theme-surface));
}

.rich-text-editor {
    min-height: 300px;
}

.rich-text-editor-wrapper :deep(.ql-container) {
    min-height: 300px;
    font-size: 14px;
}

.rich-text-editor-wrapper :deep(.ql-editor) {
    min-height: 300px;
}

.rich-text-editor-wrapper :deep(.ql-toolbar) {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    background-color: rgb(var(--v-theme-surface));
}

.rich-text-editor-wrapper :deep(.ql-container) {
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
}

.rich-text-editor-wrapper :deep(.ql-snow) {
    border: none;
}

.rich-text-editor-wrapper :deep(.ql-snow .ql-toolbar) {
    border: none;
    border-bottom: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.rich-text-editor-wrapper :deep(.ql-snow .ql-container) {
    border: none;
}

/* SEO Preview Styles */
.search-preview {
    max-width: 600px;
    padding: 16px;
    font-family: arial, sans-serif;
}

.search-url {
    font-size: 14px;
    line-height: 1.3;
    word-wrap: break-word;
}

.search-title {
    font-size: 20px;
    line-height: 1.3;
    font-weight: 400;
    cursor: pointer;
}

.search-title:hover {
    text-decoration: underline;
}

.search-description {
    line-height: 1.58;
    word-wrap: break-word;
}
</style>
