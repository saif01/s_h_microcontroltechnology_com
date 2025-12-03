<template>
    <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" max-width="1400"
        scrollable persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                <span class="text-h5 font-weight-bold">
                    {{ editingProduct ? 'Edit Product' : 'Create New Product' }}
                </span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="close" :disabled="loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading product data...</p>
                    </div>
                </div>

                <!-- Form Content -->
                <div v-else>
                    <v-tabs v-model="localFormTab" color="primary" bg-color="grey-lighten-4"
                        @update:model-value="updateFormTab">
                        <v-tab value="basic">
                            <v-icon icon="mdi-information" class="mr-2"></v-icon>
                            Basic Info
                        </v-tab>
                        <v-tab value="media">
                            <v-icon icon="mdi-image" class="mr-2"></v-icon>
                            Media
                        </v-tab>
                        <v-tab value="pricing">
                            <v-icon icon="mdi-cash" class="mr-2"></v-icon>
                            Pricing
                        </v-tab>
                        <v-tab value="categories">
                            <v-icon icon="mdi-folder" class="mr-2"></v-icon>
                            Categories & Tags
                        </v-tab>
                        <v-tab value="specs">
                            <v-icon icon="mdi-cog" class="mr-2"></v-icon>
                            Specifications
                        </v-tab>
                        <v-tab value="features">
                            <v-icon icon="mdi-star" class="mr-2"></v-icon>
                            Features
                        </v-tab>
                        <v-tab value="downloads">
                            <v-icon icon="mdi-download" class="mr-2"></v-icon>
                            Downloads
                        </v-tab>
                        <v-tab value="faq">
                            <v-icon icon="mdi-help-circle" class="mr-2"></v-icon>
                            FAQs
                        </v-tab>
                        <v-tab value="warranty">
                            <v-icon icon="mdi-shield-check" class="mr-2"></v-icon>
                            Warranty & Service
                        </v-tab>
                        <v-tab value="seo">
                            <v-icon icon="mdi-search-web" class="mr-2"></v-icon>
                            SEO
                        </v-tab>
                        <v-tab value="settings">
                            <v-icon icon="mdi-cog-outline" class="mr-2"></v-icon>
                            Settings
                        </v-tab>
                    </v-tabs>

                    <v-divider></v-divider>

                    <v-window v-model="localFormTab" class="pa-6">
                        <!-- Basic Info Tab -->
                        <v-window-item value="basic">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="localForm.title" label="Product Title *" variant="outlined"
                                        :rules="[v => !!v || 'Title is required']"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="localForm.slug" label="Slug *" readonly variant="outlined"
                                        hint="URL-friendly version of title" :rules="[v => !!v || 'Slug is required']"
                                        @update:model-value="$emit('update:form', localForm)">
                                        <template v-slot:append>
                                            <v-btn icon="mdi-refresh" size="small" variant="text"
                                                @click="$emit('generate-slug')"></v-btn>
                                        </template>
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="localForm.sku" label="SKU" variant="outlined"
                                        hint="Stock Keeping Unit"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="localForm.brand" label="Brand" variant="outlined"
                                        hint="Product manufacturer or brand name" prepend-inner-icon="mdi-tag"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="localForm.short_description" label="Short Description"
                                        variant="outlined" rows="2" hint="Brief description for listings"
                                        @update:model-value="$emit('update:form', localForm)"></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-label class="mb-2">Full Description</v-label>
                                    <RichTextEditor v-model="localForm.description"
                                        :active="modelValue && localFormTab === 'basic'"
                                        placeholder="Enter detailed product description..."
                                        @update:model-value="$emit('update:form', localForm)" />
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Media Tab -->
                        <v-window-item value="media">
                            <v-row>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">Thumbnail Image</div>
                                    <v-file-input v-model="localThumbnailFile" label="Select Thumbnail Image"
                                        accept="image/*" prepend-icon="mdi-camera" variant="outlined"
                                        @update:model-value="handleThumbnailChange" clearable show-size
                                        hint="Select an image file (will upload on save). Recommended size: 800x600px or 1200x900px (4:3 ratio)"
                                        persistent-hint class="mb-3"></v-file-input>

                                    <v-alert v-if="thumbnailError" type="error" variant="tonal" density="compact"
                                        class="mt-2 mb-3" closable @click:close="$emit('update:thumbnail-error', null)">
                                        <div class="text-body-2">
                                            <strong>Upload Error:</strong> {{ thumbnailError }}
                                        </div>
                                    </v-alert>

                                    <!-- Preview for selected file -->
                                    <div v-if="localThumbnailFile && thumbnailPreview" class="mt-3 mb-3">
                                        <v-card class="elevation-2" style="max-width: 300px;">
                                            <v-img :src="thumbnailPreview" max-height="200" contain
                                                class="rounded"></v-img>
                                            <v-card-text class="pa-2">
                                                <div class="text-caption text-medium-emphasis">
                                                    {{ localThumbnailFile.name || 'Selected file' }}
                                                    <span v-if="localThumbnailFile.size">
                                                        ({{ formatFileSize(localThumbnailFile.size) }})
                                                    </span>
                                                </div>
                                            </v-card-text>
                                        </v-card>
                                    </div>

                                    <!-- Preview for existing/uploaded thumbnail -->
                                    <div v-else-if="localForm.thumbnail" class="mt-2 mb-3">
                                        <v-card class="elevation-2" style="max-width: 300px;">
                                            <v-img :src="resolveImageUrl(localForm.thumbnail)" max-height="200" contain
                                                class="rounded"></v-img>
                                            <v-card-text class="pa-2">
                                                <div class="text-caption text-medium-emphasis">Current thumbnail
                                                </div>
                                            </v-card-text>
                                        </v-card>
                                    </div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">Product Images (Gallery)
                                    </div>

                                    <!-- Existing Gallery Images -->
                                    <div v-if="localForm.images && localForm.images.length > 0" class="mb-4">
                                        <div class="text-subtitle-2 text-medium-emphasis mb-2">Existing Images ({{
                                            localForm.images.length }})</div>
                                        <div class="d-flex flex-wrap gap-3">
                                            <v-card v-for="(img, index) in localForm.images" :key="`existing-${index}`"
                                                class="elevation-2" style="max-width: 200px; position: relative;">
                                                <v-img :src="resolveImageUrl(img)" height="150" cover></v-img>
                                                <v-card-text class="pa-2">
                                                    <div class="text-caption text-truncate">{{ img }}</div>
                                                </v-card-text>
                                                <v-btn icon="mdi-close" size="x-small" color="error" variant="flat"
                                                    class="position-absolute" style="top: 4px; right: 4px;"
                                                    @click="$emit('remove-image', index)"></v-btn>
                                            </v-card>
                                        </div>
                                    </div>

                                    <v-divider v-if="localForm.images && localForm.images.length > 0"
                                        class="my-4"></v-divider>

                                    <!-- Upload New Images -->
                                    <div class="text-subtitle-2 text-medium-emphasis mb-2">Upload New Images</div>
                                    <v-file-input v-model="localGalleryFiles" label="Select Gallery Images"
                                        accept="image/*" prepend-icon="mdi-image-multiple" variant="outlined" multiple
                                        @update:model-value="handleGalleryChange" clearable show-size
                                        hint="Select multiple image files (will upload on save). Recommended size: 1200x800px or 1600x1200px (3:2 or 4:3 ratio)"
                                        persistent-hint class="mb-3"></v-file-input>

                                    <v-alert v-if="galleryError" type="error" variant="tonal" density="compact"
                                        class="mt-2 mb-3" closable @click:close="$emit('update:gallery-error', null)">
                                        <div class="text-body-2">
                                            <strong>Upload Error:</strong> {{ galleryError }}
                                        </div>
                                    </v-alert>

                                    <!-- Preview for selected files -->
                                    <div v-if="galleryPreviews.length > 0" class="mt-3 mb-4">
                                        <div class="text-subtitle-2 text-medium-emphasis mb-2">New Images to Upload
                                            ({{
                                                galleryPreviews.length }})</div>
                                        <div class="d-flex flex-wrap gap-3">
                                            <v-card v-for="(preview, index) in galleryPreviews"
                                                :key="`preview-${index}`" class="elevation-2"
                                                style="max-width: 200px; position: relative;">
                                                <v-img :src="preview.url" height="150" cover></v-img>
                                                <v-card-text class="pa-2">
                                                    <div class="text-caption text-truncate">{{ preview.name }}</div>
                                                    <div class="text-caption text-medium-emphasis" v-if="preview.size">
                                                        {{ formatFileSize(preview.size) }}
                                                    </div>
                                                </v-card-text>
                                                <v-btn icon="mdi-close" size="x-small" color="error" variant="flat"
                                                    class="position-absolute" style="top: 4px; right: 4px;"
                                                    @click="$emit('remove-gallery-preview', index)"></v-btn>
                                            </v-card>
                                        </div>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Pricing Tab -->
                        <v-window-item value="pricing">
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="localForm.price" label="Price (Tk)" variant="outlined"
                                        type="number" step="0.01" prepend-inner-icon="mdi-cash"
                                        hint="Numeric price value in Bangladeshi Taka"
                                        @update:model-value="updateDiscountedPrice"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="localForm.price_range" label="Price Range" variant="outlined"
                                        hint="e.g., 'Tk 50 - Tk 100' or 'Contact for Price'"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>

                                <v-col cols="12">
                                    <v-divider class="my-2"></v-divider>
                                    <div class="text-subtitle-1 font-weight-bold mb-3">Discount Settings</div>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <v-text-field v-model.number="localForm.discount_percent" label="Discount (%)"
                                        variant="outlined" type="number" step="0.01" min="0" max="100"
                                        prepend-inner-icon="mdi-sale" hint="Discount percentage (0-100)"
                                        @update:model-value="updateDiscountedPrice"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-text-field v-model.number="localForm.discounted_price"
                                        label="Discounted Price (Tk)" variant="outlined" type="number" step="0.01"
                                        min="0" prepend-inner-icon="mdi-cash-multiple" hint="Final price after discount"
                                        :disabled="true"
                                        :style="{ opacity: localForm.discount_percent > 0 ? 1 : 0.6 }"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="4" class="d-flex align-center">
                                    <v-chip v-if="localForm.discount_percent > 0" color="success" variant="flat"
                                        class="mt-2">
                                        <v-icon icon="mdi-tag" size="small" class="mr-1"></v-icon>
                                        {{ localForm.discount_percent }}% OFF
                                    </v-chip>
                                    <v-chip v-else color="grey" variant="tonal" class="mt-2">
                                        No Discount
                                    </v-chip>
                                </v-col>

                                <v-col cols="12">
                                    <v-switch v-model="localForm.show_price" label="Show Price on Website"
                                        color="primary"
                                        @update:model-value="$emit('update:form', localForm)"></v-switch>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Categories & Tags Tab -->
                        <v-window-item value="categories">
                            <v-row>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">Categories</div>
                                    <v-select v-model="localForm.category_ids" :items="availableCategories"
                                        item-title="name" item-value="id" label="Select Categories" variant="outlined"
                                        multiple chips :loading="loadingCategories"
                                        @update:model-value="$emit('update:form', localForm)"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">Tags</div>
                                    <v-combobox v-model="localForm.tag_names" :items="availableTags" label="Tags"
                                        variant="outlined" multiple chips closable-chips hint="Type to add new tags"
                                        @update:model-value="$emit('update:form', localForm)"></v-combobox>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Specifications Tab -->
                        <v-window-item value="specs">
                            <div class="text-subtitle-1 font-weight-bold mb-4">Technical Specifications</div>
                            <div v-for="(spec, index) in specificationsList" :key="index" class="mb-3">
                                <v-row>
                                    <v-col cols="12" md="5">
                                        <v-text-field v-model="spec.key" label="Specification Name" variant="outlined"
                                            placeholder="e.g., Capacity, Input Voltage"
                                            @update:model-value="$emit('update:specifications-list', specificationsList)"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="spec.value" label="Value" variant="outlined"
                                            placeholder="e.g., 2000VA / 1800W"
                                            @update:model-value="$emit('update:specifications-list', specificationsList)"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="1">
                                        <v-btn icon="mdi-delete" color="error" variant="text"
                                            @click="$emit('remove-specification', index)"></v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                            <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                @click="$emit('add-specification')">Add
                                Specification</v-btn>
                        </v-window-item>

                        <!-- Features Tab -->
                        <v-window-item value="features">
                            <v-row>
                                <v-col cols="12">
                                    <v-alert type="info" variant="tonal" density="compact" class="mb-4">
                                        <div class="text-body-2">
                                            <strong>Filter Features:</strong> Select features for filtering products
                                            (e.g., wireless,
                                            waterproof).
                                            <br>
                                            <strong>Key Features:</strong> Detailed text features shown on product page.
                                        </div>
                                    </v-alert>
                                </v-col>

                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-3">
                                        <v-icon icon="mdi-filter-variant" class="mr-2"></v-icon>
                                        Filter Features
                                    </div>
                                    <v-combobox v-model="localForm.features" :items="filterFeatureOptions"
                                        label="Select or Add Filter Features" variant="outlined" multiple chips
                                        closable-chips hint="Choose from existing features or type to add new ones"
                                        persistent-hint @update:model-value="$emit('update:form', localForm)">
                                        <template v-slot:chip="{ item, props: chipProps }">
                                            <v-chip v-bind="chipProps" size="small" color="primary" variant="flat">
                                                <v-icon
                                                    :icon="getFeatureIcon(typeof item === 'string' ? item : item.value)"
                                                    size="14" class="mr-1"></v-icon>
                                                {{ typeof item === 'string' ? formatFeatureLabel(item) : item.title }}
                                            </v-chip>
                                        </template>
                                        <template v-slot:item="{ item, props: itemProps }">
                                            <v-list-item v-bind="itemProps">
                                                <template v-slot:prepend>
                                                    <v-icon :icon="getFeatureIcon(item.value)" color="primary"></v-icon>
                                                </template>
                                            </v-list-item>
                                        </template>
                                    </v-combobox>
                                </v-col>

                                <v-col cols="12">
                                    <v-divider class="my-4"></v-divider>
                                </v-col>

                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-3">
                                        <v-icon icon="mdi-star" class="mr-2"></v-icon>
                                        Key Features (Detailed)
                                    </div>
                                    <div v-for="(feature, index) in featuresList" :key="index" class="mb-3">
                                        <v-text-field v-model="featuresList[index]" :label="`Feature ${index + 1}`"
                                            variant="outlined"
                                            @update:model-value="$emit('update:features-list', featuresList)">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-delete" color="error" variant="text"
                                                    @click="$emit('remove-feature', index)"></v-btn>
                                            </template>
                                        </v-text-field>
                                    </div>
                                    <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                        @click="$emit('add-feature')">Add Key
                                        Feature</v-btn>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Downloads Tab -->
                        <v-window-item value="downloads">
                            <div class="text-subtitle-1 font-weight-bold mb-4">Product Downloads</div>
                            <div v-for="(download, index) in localDownloadsList" :key="index"
                                class="mb-4 pa-4 border rounded">
                                <v-row>
                                    <v-col cols="12" md="4">
                                        <v-text-field v-model="download.title" label="Title" variant="outlined"
                                            placeholder="e.g., Product Datasheet"
                                            @update:model-value="updateDownloadsList"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-select v-model="download.type" :items="downloadTypes" label="Type"
                                            variant="outlined" @update:model-value="updateDownloadsList"></v-select>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-text-field v-model="download.size" label="Size" variant="outlined"
                                            placeholder="e.g., 1.2 MB" :disabled="!!download.file"
                                            @update:model-value="updateDownloadsList"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="2">
                                        <v-btn icon="mdi-delete" color="error" variant="text"
                                            @click="handleRemoveDownload(index)"></v-btn>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-2 font-weight-bold mb-2">Upload File</div>
                                        <v-file-input v-model="download.file"
                                            :label="download.file ? 'File Selected' : 'Select File to Upload'"
                                            accept=".pdf,.zip,.doc,.docx,.xls,.xlsx" prepend-icon="mdi-file-upload"
                                            variant="outlined" show-size clearable
                                            @update:model-value="handleDownloadFileChange(index, $event)"
                                            hint="PDF, ZIP, DOC, DOCX, XLS, XLSX files (max 10MB)"></v-file-input>

                                        <!-- File Preview -->
                                        <div v-if="download.file" class="mt-2 mb-2">
                                            <v-card class="elevation-1 pa-2">
                                                <div class="d-flex align-center">
                                                    <v-icon icon="mdi-file" color="primary" class="mr-2"></v-icon>
                                                    <div class="flex-grow-1">
                                                        <div class="text-body-2 font-weight-bold">{{
                                                            download.file.name }}
                                                        </div>
                                                        <div class="text-caption text-medium-emphasis">
                                                            {{ getDownloadFileSize(download) }}
                                                            <span v-if="download.uploading" class="ml-2">
                                                                <v-progress-circular indeterminate size="16"
                                                                    color="primary"></v-progress-circular>
                                                                Uploading...
                                                            </span>
                                                            <span v-else-if="download.uploaded"
                                                                class="ml-2 text-success">
                                                                <v-icon icon="mdi-check-circle" size="small"></v-icon>
                                                                Uploaded
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <v-btn icon="mdi-close" size="small" variant="text" color="error"
                                                        @click="handleClearDownloadFile(index)"></v-btn>
                                                </div>
                                            </v-card>
                                        </div>
                                    </v-col>
                                </v-row>
                            </div>
                            <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                @click="$emit('add-download')">Add
                                Download</v-btn>
                        </v-window-item>

                        <!-- FAQs Tab -->
                        <v-window-item value="faq">
                            <div class="text-subtitle-1 font-weight-bold mb-4">Frequently Asked Questions</div>
                            <div v-for="(faq, index) in faqsList" :key="index" class="mb-4 pa-4 border rounded">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="faq.question" label="Question" variant="outlined"
                                            placeholder="What is the warranty period?"
                                            @update:model-value="$emit('update:faqs-list', faqsList)"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="faq.answer" label="Answer" variant="outlined" rows="3"
                                            placeholder="This product comes with..."
                                            @update:model-value="$emit('update:faqs-list', faqsList)"></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-btn color="error" variant="outlined" prepend-icon="mdi-delete"
                                            @click="$emit('remove-faq', index)">Remove
                                            FAQ</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                            <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                @click="$emit('add-faq')">Add
                                FAQ</v-btn>
                        </v-window-item>

                        <!-- Warranty & Service Tab -->
                        <v-window-item value="warranty">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="warrantyInfo.period" label="Warranty Period"
                                        variant="outlined" placeholder="e.g., 2 Years"
                                        @update:model-value="$emit('update:warranty-info', warrantyInfo)"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">What's Covered</div>
                                    <div v-for="(item, index) in warrantyInfo.coverage" :key="index" class="mb-2">
                                        <v-text-field v-model="warrantyInfo.coverage[index]" variant="outlined"
                                            :label="`Coverage Item ${index + 1}`"
                                            @update:model-value="$emit('update:warranty-info', warrantyInfo)">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-delete" size="small" color="error" variant="text"
                                                    @click="$emit('remove-coverage-item', index)"></v-btn>
                                            </template>
                                        </v-text-field>
                                    </div>
                                    <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                        @click="$emit('add-coverage-item')">Add
                                        Coverage
                                        Item</v-btn>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="warrantyInfo.terms" label="Terms & Conditions"
                                        variant="outlined" rows="4"
                                        @update:model-value="$emit('update:warranty-info', warrantyInfo)"></v-textarea>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- SEO Tab -->
                        <v-window-item value="seo">
                            <div class="pa-6">
                                <v-alert type="info" variant="tonal" class="mb-6" density="compact">
                                    <div class="text-body-2">
                                        <strong>SEO Best Practices:</strong> Fill in these fields to improve your
                                        product visibility in search engines and social media.
                                    </div>
                                </v-alert>

                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="localForm.meta_title" label="Meta Title"
                                            variant="outlined"
                                            hint="SEO title for search engines (recommended: 50-60 characters)"
                                            persistent-hint :counter="60" :color="getMetaTitleColor()"
                                            prepend-inner-icon="mdi-format-title"
                                            @update:model-value="$emit('update:form', localForm)">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateMetaTitle" :disabled="!localForm.title"
                                                    title="Auto-generate from product title">
                                                </v-btn>
                                            </template>
                                        </v-text-field>
                                        <div class="text-caption" :class="getMetaTitleColor() + '--text'">
                                            {{ (localForm.meta_title || '').length }}/60 characters
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-textarea v-model="localForm.meta_description" label="Meta Description"
                                            variant="outlined" rows="4"
                                            hint="SEO description for search results (recommended: 150-160 characters)"
                                            persistent-hint :counter="160" :color="getMetaDescriptionColor()"
                                            prepend-inner-icon="mdi-text"
                                            @update:model-value="$emit('update:form', localForm)">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-refresh" size="small" variant="text"
                                                    @click="generateMetaDescription"
                                                    :disabled="!localForm.short_description"
                                                    title="Auto-generate from short description">
                                                </v-btn>
                                            </template>
                                        </v-textarea>
                                        <div class="text-caption" :class="getMetaDescriptionColor() + '--text'">
                                            {{ (localForm.meta_description || '').length }}/160 characters
                                        </div>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="localForm.meta_keywords" label="Meta Keywords"
                                            variant="outlined" hint="Comma-separated keywords for SEO" persistent-hint
                                            prepend-inner-icon="mdi-tag-multiple"
                                            @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Open Graph (Social Media)</h3>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="localForm.og_image" label="Open Graph Image URL"
                                            variant="outlined"
                                            hint="Image for social media sharing (recommended size: 1200x630px)"
                                            persistent-hint prepend-inner-icon="mdi-image"
                                            @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Settings Tab -->
                        <v-window-item value="settings">
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-switch v-model="localForm.published" label="Published" color="success"
                                        @update:model-value="$emit('update:form', localForm)"></v-switch>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-switch v-model="localForm.featured" label="Featured Product" color="amber"
                                        @update:model-value="$emit('update:form', localForm)"></v-switch>
                                </v-col>

                                <v-col cols="12">
                                    <v-divider class="my-2"></v-divider>
                                    <div class="text-subtitle-1 font-weight-bold mb-3">Inventory & Availability</div>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="localForm.stock" label="Stock Quantity"
                                        variant="outlined" type="number" min="0"
                                        prepend-inner-icon="mdi-package-variant" hint="Available stock quantity"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-select v-model="localForm.availability" :items="availabilityOptions"
                                        label="Availability Status" variant="outlined"
                                        prepend-inner-icon="mdi-check-circle" hint="Product availability status"
                                        @update:model-value="$emit('update:form', localForm)">
                                        <template v-slot:item="{ item, props: itemProps }">
                                            <v-list-item v-bind="itemProps">
                                                <template v-slot:prepend>
                                                    <v-icon :icon="getAvailabilityIcon(item.value)"
                                                        :color="getAvailabilityColor(item.value)"></v-icon>
                                                </template>
                                            </v-list-item>
                                        </template>
                                    </v-select>
                                </v-col>

                                <v-col cols="12">
                                    <v-divider class="my-2"></v-divider>
                                    <div class="text-subtitle-1 font-weight-bold mb-3">Product Rating</div>

                                    <v-alert type="info" variant="tonal" density="compact" class="mb-4">
                                        <div class="text-body-2">
                                            <strong>Note:</strong> Ratings are automatically calculated from approved
                                            customer reviews.
                                            Manual input is only needed if you don't use the review system.
                                        </div>
                                    </v-alert>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <div class="mb-2">
                                        <v-label class="text-body-2 mb-2">Rating (Stars)</v-label>
                                        <v-rating v-model.number="localForm.rating" :length="5" :size="32"
                                            active-color="amber" color="grey-lighten-1" half-increments
                                            @update:model-value="$emit('update:form', localForm)"></v-rating>
                                        <div class="text-caption text-medium-emphasis mt-1">
                                            Current: {{ formatRating(localForm.rating) }} / 5 stars
                                        </div>
                                        <v-chip v-if="localForm.rating >= 4.5" color="success" size="small"
                                            class="mt-1">
                                            Excellent
                                        </v-chip>
                                        <v-chip v-else-if="localForm.rating >= 4.0" color="success" size="small"
                                            class="mt-1">
                                            Very Good
                                        </v-chip>
                                        <v-chip v-else-if="localForm.rating >= 3.0" color="warning" size="small"
                                            class="mt-1">
                                            Good
                                        </v-chip>
                                        <v-chip v-else-if="localForm.rating >= 2.0" color="orange" size="small"
                                            class="mt-1">
                                            Fair
                                        </v-chip>
                                        <v-chip v-else-if="localForm.rating > 0" color="error" size="small"
                                            class="mt-1">
                                            Poor
                                        </v-chip>
                                    </div>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="localForm.rating_count" label="Number of Ratings"
                                        variant="outlined" type="number" min="0" prepend-inner-icon="mdi-account-group"
                                        hint="Total number of customer ratings"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>

                                <v-col cols="12">
                                    <v-divider class="my-2"></v-divider>
                                    <div class="text-subtitle-1 font-weight-bold mb-3">Display Settings</div>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="localForm.order" label="Display Order"
                                        variant="outlined" type="number" prepend-inner-icon="mdi-sort-numeric-ascending"
                                        hint="Lower numbers appear first"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                            </v-row>
                        </v-window-item>
                    </v-window>
                </div>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-4" v-if="!loading">
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="close" :disabled="saving">Cancel</v-btn>
                <v-btn color="primary" variant="flat" @click="$emit('save')" :loading="saving" :disabled="loading">
                    {{ editingProduct ? 'Update' : 'Create' }} Product
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { resolveUploadUrl } from '../../../utils/uploads';
import RichTextEditor from '../../common/RichTextEditor.vue';

export default {
    name: 'ProductFormDialog',
    components: {
        RichTextEditor
    },
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        editingProduct: {
            type: Object,
            default: null
        },
        loading: {
            type: Boolean,
            default: false
        },
        saving: {
            type: Boolean,
            default: false
        },
        form: {
            type: Object,
            required: true
        },
        formTab: {
            type: String,
            default: 'basic'
        },
        availableCategories: {
            type: Array,
            default: () => []
        },
        availableTags: {
            type: Array,
            default: () => []
        },
        loadingCategories: {
            type: Boolean,
            default: false
        },
        specificationsList: {
            type: Array,
            default: () => []
        },
        featuresList: {
            type: Array,
            default: () => []
        },
        downloadsList: {
            type: Array,
            default: () => []
        },
        faqsList: {
            type: Array,
            default: () => []
        },
        warrantyInfo: {
            type: Object,
            required: true
        },
        downloadTypes: {
            type: Array,
            default: () => ['PDF', 'ZIP', 'DOC', 'DOCX', 'XLS', 'XLSX', 'Other']
        },
        thumbnailFile: {
            type: [Object, Array],
            default: null
        },
        galleryFiles: {
            type: Array,
            default: () => []
        },
        thumbnailPreview: {
            type: String,
            default: null
        },
        galleryPreviews: {
            type: Array,
            default: () => []
        },
        imageUrlInputs: {
            type: Array,
            default: () => []
        },
        thumbnailError: {
            type: String,
            default: null
        },
        galleryError: {
            type: String,
            default: null
        }
    },
    emits: [
        'update:modelValue',
        'update:form',
        'update:form-tab',
        'update:specifications-list',
        'update:features-list',
        'update:downloads-list',
        'update:faqs-list',
        'update:warranty-info',
        'update:thumbnail-file',
        'update:gallery-files',
        'update:thumbnail-preview',
        'update:gallery-previews',
        'update:image-url-inputs',
        'update:thumbnail-error',
        'update:gallery-error',
        'close',
        'save',
        'generate-slug',
        'add-image-url',
        'remove-image-url',
        'remove-image',
        'preview-thumbnail',
        'preview-gallery-images',
        'remove-gallery-preview',
        'add-specification',
        'remove-specification',
        'add-feature',
        'remove-feature',
        'add-download',
        'remove-download',
        'handle-download-file-change',
        'clear-download-file',
        'add-faq',
        'remove-faq',
        'add-coverage-item',
        'remove-coverage-item'
    ],
    data() {
        return {
            localForm: { ...this.form },
            localFormTab: this.formTab,
            localThumbnailFile: this.thumbnailFile,
            localGalleryFiles: this.galleryFiles,
            localDownloadsList: [],
            availabilityOptions: [
                { value: 'in_stock', title: 'In Stock' },
                { value: 'out_of_stock', title: 'Out of Stock' },
                { value: 'pre_order', title: 'Pre-Order' },
                { value: 'coming_soon', title: 'Coming Soon' }
            ]
        };
    },
    watch: {
        form: {
            handler(newVal) {
                this.localForm = { ...newVal };
            },
            deep: true,
            immediate: false
        },
        formTab(newVal) {
            this.localFormTab = newVal;
        },
        thumbnailFile(newVal) {
            this.localThumbnailFile = newVal;
        },
        galleryFiles(newVal) {
            this.localGalleryFiles = newVal;
        },
        downloadsList: {
            handler(newVal) {
                // Create a deep copy to avoid mutating props
                if (Array.isArray(newVal)) {
                    this.localDownloadsList = newVal.map((download, index) => {
                        // Preserve existing file object if it exists in local list
                        const existingDownload = this.localDownloadsList[index];
                        return {
                            title: download.title || '',
                            type: download.type || 'PDF',
                            size: download.size || (download.file && download.file.size ? this.formatFileSize(download.file.size) : ''),
                            url: download.url || '',
                            file: download.file || (existingDownload && existingDownload.file ? existingDownload.file : null),
                            uploading: download.uploading || false,
                            uploaded: download.uploaded || false
                        };
                    });
                } else {
                    this.localDownloadsList = [];
                }
            },
            deep: true,
            immediate: true
        },
        modelValue(newVal) {
            if (!newVal) {
                this.localFormTab = 'basic';
                return;
            }
            // Keep local state in sync when dialog is reopened with new data
            this.localForm = { ...this.form };
        }
    },
    computed: {
        filterFeatureOptions() {
            // Common/default features
            const defaultFeatures = [
                { value: 'wireless', title: 'Wireless' },
                { value: 'waterproof', title: 'Waterproof' },
                { value: 'bluetooth', title: 'Bluetooth' },
                { value: 'rechargeable', title: 'Rechargeable' },
                { value: 'warranty', title: 'Warranty' },
                { value: 'eco_friendly', title: 'Eco-Friendly' }
            ];

            return defaultFeatures;
        }
    },
    methods: {
        close() {
            this.$emit('update:modelValue', false);
            this.$emit('close');
        },
        handleThumbnailChange(file) {
            this.$emit('update:thumbnail-file', file);
            this.$emit('preview-thumbnail', file);
        },
        handleGalleryChange(files) {
            this.$emit('update:gallery-files', files);
            this.$emit('preview-gallery-images', files);
        },
        updateFormTab(value) {
            this.localFormTab = value;
            this.$emit('update:form-tab', value);
        },
        formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },
        getDownloadFileSize(download) {
            // Priority 1: Use download.size if it exists and is not empty
            if (download.size && download.size !== '' && download.size !== '0 Bytes') {
                // If it's already formatted (contains KB, MB, etc), return as is
                if (typeof download.size === 'string' && (download.size.includes('KB') || download.size.includes('MB') || download.size.includes('GB') || download.size.includes('Bytes'))) {
                    return download.size;
                }
                // If it's a number, format it
                if (typeof download.size === 'number' && download.size > 0) {
                    return this.formatFileSize(download.size);
                }
            }
            // Priority 2: Use file.size if file exists and has size
            if (download.file && download.file.size && download.file.size > 0) {
                return this.formatFileSize(download.file.size);
            }
            // Priority 3: If uploaded but no size, try to get from file if still exists
            if (download.uploaded && download.file && download.file.size) {
                return this.formatFileSize(download.file.size);
            }
            return 'Size unknown';
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        },
        updateDownloadsList() {
            // Emit updated downloads list to parent
            this.$emit('update:downloads-list', [...this.localDownloadsList]);
        },
        handleDownloadFileChange(index, file) {
            const download = this.localDownloadsList[index];
            if (download) {
                download.file = file;
                if (file && file.size) {
                    // Auto-detect file type from extension
                    const extension = file.name.split('.').pop().toUpperCase();
                    if (['PDF', 'ZIP', 'DOC', 'DOCX', 'XLS', 'XLSX'].includes(extension)) {
                        download.type = extension;
                    }
                    // Auto-fill size only if not already set (preserve uploaded size)
                    if (!download.size || download.size === '') {
                        download.size = this.formatFileSize(file.size);
                    }
                }
                this.updateDownloadsList();
                // Also emit the event for parent to handle
                this.$emit('handle-download-file-change', index);
            }
        },
        handleClearDownloadFile(index) {
            const download = this.localDownloadsList[index];
            if (download) {
                download.file = null;
                download.uploading = false;
                download.uploaded = false;
                if (!download.url) {
                    download.size = '';
                }
                this.updateDownloadsList();
                // Also emit the event for parent to handle
                this.$emit('clear-download-file', index);
            }
        },
        handleRemoveDownload(index) {
            this.localDownloadsList.splice(index, 1);
            this.updateDownloadsList();
            // Also emit the event for parent to handle
            this.$emit('remove-download', index);
        },
        generateMetaTitle() {
            if (this.localForm.title) {
                this.localForm.meta_title = this.localForm.title.length > 60
                    ? this.localForm.title.substring(0, 57) + '...'
                    : this.localForm.title;
                this.$emit('update:form', this.localForm);
            }
        },
        generateMetaDescription() {
            if (this.localForm.short_description) {
                let description = this.localForm.short_description;
                if (description.length > 160) {
                    description = description.substring(0, 157);
                    const lastSpace = description.lastIndexOf(' ');
                    if (lastSpace > 120) {
                        description = description.substring(0, lastSpace) + '...';
                    } else {
                        description += '...';
                    }
                }
                this.localForm.meta_description = description;
                this.$emit('update:form', this.localForm);
            }
        },
        getMetaTitleColor() {
            const length = this.localForm.meta_title ? this.localForm.meta_title.length : 0;
            if (length === 0) return 'primary';
            if (length < 50) return 'warning';
            if (length > 60) return 'error';
            return 'success';
        },
        getMetaDescriptionColor() {
            const length = this.localForm.meta_description ? this.localForm.meta_description.length : 0;
            if (length === 0) return 'primary';
            if (length < 120) return 'warning';
            if (length > 160) return 'error';
            return 'success';
        },
        updateDiscountedPrice() {
            // Calculate discounted price automatically when price or discount changes
            if (this.localForm.price && this.localForm.discount_percent > 0) {
                const price = parseFloat(this.localForm.price) || 0;
                const discount = parseFloat(this.localForm.discount_percent) || 0;
                this.localForm.discounted_price = Math.round(price * (1 - discount / 100) * 100) / 100;
            } else {
                this.localForm.discounted_price = null;
            }
            this.$emit('update:form', this.localForm);
        },
        getFeatureIcon(feature) {
            const iconMap = {
                'wireless': 'mdi-wifi',
                'waterproof': 'mdi-water',
                'bluetooth': 'mdi-bluetooth',
                'rechargeable': 'mdi-battery-charging',
                'warranty': 'mdi-shield-check',
                'eco_friendly': 'mdi-leaf'
            };
            return iconMap[feature] || 'mdi-check-circle';
        },
        formatFeatureLabel(feature) {
            // Convert underscore-separated to title case
            if (typeof feature === 'string') {
                return feature.split('_').map(word =>
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
            }
            return feature;
        },
        getAvailabilityIcon(availability) {
            const iconMap = {
                'in_stock': 'mdi-check-circle',
                'out_of_stock': 'mdi-close-circle',
                'pre_order': 'mdi-clock-outline',
                'coming_soon': 'mdi-new-box'
            };
            return iconMap[availability] || 'mdi-help-circle';
        },
        getAvailabilityColor(availability) {
            const colorMap = {
                'in_stock': 'success',
                'out_of_stock': 'error',
                'pre_order': 'warning',
                'coming_soon': 'info'
            };
            return colorMap[availability] || 'grey';
        },
        formatRating(rating) {
            return rating ? Number(rating).toFixed(1) : '0.0';
        }
    }
};
</script>

<style scoped>
.border {
    border: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
