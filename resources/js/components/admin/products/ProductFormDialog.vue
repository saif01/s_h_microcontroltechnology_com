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
                            <v-icon icon="mdi-currency-usd" class="mr-2"></v-icon>
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
                                    <v-text-field v-model="localForm.slug" label="Slug *" variant="outlined"
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
                                <v-col cols="12">
                                    <v-textarea v-model="localForm.short_description" label="Short Description"
                                        variant="outlined" rows="2" hint="Brief description for listings"
                                        @update:model-value="$emit('update:form', localForm)"></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-label class="mb-2">Full Description</v-label>
                                    <div class="rich-text-editor-wrapper">
                                        <div ref="editorContainer" class="rich-text-editor"></div>
                                    </div>
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

                                    <v-text-field v-model="localForm.thumbnail" label="Or Enter Image URL"
                                        variant="outlined" prepend-inner-icon="mdi-link"
                                        hint="Alternative: Paste image URL" class="mt-2"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
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

                                    <v-divider class="my-4"></v-divider>

                                    <!-- Add Image URLs -->
                                    <div class="text-subtitle-2 text-medium-emphasis mb-2">Or Add Image URLs</div>
                                    <div v-for="(img, index) in imageUrlInputs" :key="`url-${index}`" class="mb-3">
                                        <v-text-field v-model="imageUrlInputs[index]" :label="`Image URL ${index + 1}`"
                                            variant="outlined" prepend-inner-icon="mdi-link"
                                            placeholder="https://example.com/image.jpg">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                    @click="$emit('remove-image-url', index)"></v-btn>
                                            </template>
                                        </v-text-field>
                                        <div v-if="imageUrlInputs[index]" class="mt-2">
                                            <v-img :src="resolveImageUrl(imageUrlInputs[index])" max-height="150"
                                                contain class="rounded elevation-1"></v-img>
                                        </div>
                                    </div>
                                    <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                        @click="$emit('add-image-url')">Add Image
                                        URL</v-btn>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Pricing Tab -->
                        <v-window-item value="pricing">
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="localForm.price" label="Price" variant="outlined"
                                        type="number" step="0.01" prepend-inner-icon="mdi-currency-usd"
                                        hint="Numeric price value"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="localForm.price_range" label="Price Range" variant="outlined"
                                        hint="e.g., '$50 - $100' or 'Contact for Price'"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
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
                            <div class="text-subtitle-1 font-weight-bold mb-4">Key Features</div>
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
                                @click="$emit('add-feature')">Add
                                Feature</v-btn>
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

                                        <v-divider class="my-3"></v-divider>
                                        <div class="text-subtitle-2 text-medium-emphasis mb-2">Or Enter File URL
                                        </div>
                                        <v-text-field v-model="download.url" label="File URL" variant="outlined"
                                            placeholder="https://..." :disabled="!!download.file"
                                            @update:model-value="updateDownloadsList"></v-text-field>
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
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="localForm.meta_title" label="Meta Title" variant="outlined"
                                        hint="SEO title (50-60 characters recommended)"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="localForm.meta_description" label="Meta Description"
                                        variant="outlined" rows="3"
                                        hint="SEO description (150-160 characters recommended)"
                                        @update:model-value="$emit('update:form', localForm)"></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field v-model="localForm.meta_keywords" label="Meta Keywords"
                                        variant="outlined" hint="Comma-separated keywords"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field v-model="localForm.og_image" label="Open Graph Image URL"
                                        variant="outlined" hint="Image for social media sharing"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                            </v-row>
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
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="localForm.stock" label="Stock Quantity"
                                        variant="outlined" type="number"
                                        @update:model-value="$emit('update:form', localForm)"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="localForm.order" label="Display Order"
                                        variant="outlined" type="number" hint="Lower numbers appear first"
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
import Quill from 'quill';
// Import Quill styles
import 'quill/dist/quill.snow.css';

export default {
    name: 'ProductFormDialog',
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
            quillEditor: null,
            localDownloadsList: []
        };
    },
    watch: {
        form: {
            handler(newVal, oldVal) {
                this.localForm = { ...newVal };

                // If description changed and editor exists, update it
                if (this.quillEditor) {
                    const currentContent = this.quillEditor.root.innerHTML.trim();
                    const newContent = (newVal.description || '').trim();
                    // Only update if content actually changed and is not empty
                    if (newContent && currentContent !== newContent) {
                        this.quillEditor.root.innerHTML = newVal.description || '';
                    }
                }
                // If editor doesn't exist, description exists, we're on basic tab, dialog is open, and not loading
                else if (newVal.description && this.localFormTab === 'basic' && this.modelValue && !this.loading) {
                    // Initialize editor with the description
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.initEditor();
                        }, 300);
                    });
                }
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
                this.destroyEditor();
            } else if (newVal && this.localFormTab === 'basic' && !this.loading) {
                // Initialize editor when dialog opens and we're on basic tab (only if not loading)
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.initEditor();
                    }, 300);
                });
            }
        },
        'localFormTab'(newTab) {
            // Initialize editor when switching to basic tab
            if (newTab === 'basic' && this.modelValue && !this.loading) {
                setTimeout(() => {
                    this.initEditor();
                }, 300);
            } else if (newTab !== 'basic' && this.quillEditor) {
                // Save editor content when leaving basic tab
                this.destroyEditor();
            }
        },
        loading(newVal, oldVal) {
            // When loading finishes and we're on basic tab, initialize or update editor
            if (oldVal === true && newVal === false && this.localFormTab === 'basic' && this.modelValue) {
                this.$nextTick(() => {
                    setTimeout(() => {
                        // If editor exists, update content; otherwise initialize
                        if (this.quillEditor && this.localForm.description) {
                            this.quillEditor.root.innerHTML = this.localForm.description || '';
                        } else {
                            this.initEditor();
                        }
                    }, 300);
                });
            }
        }
    },
    methods: {
        close() {
            this.destroyEditor();
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
        initEditor() {
            if (this.quillEditor) {
                this.destroyEditor();
            }

            // Wait for DOM and ensure we're not loading
            this.$nextTick(() => {
                // Double check conditions
                if (!this.$refs.editorContainer) {
                    console.warn('Editor container not found');
                    return;
                }

                if (this.localFormTab !== 'basic' || !this.modelValue || this.loading) {
                    return;
                }

                try {
                    // Clear container
                    this.$refs.editorContainer.innerHTML = '';

                    // Create new Quill instance
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
                        placeholder: 'Enter detailed product description...'
                    });

                    // Set initial content from form description (use current form value)
                    const description = this.localForm.description || '';
                    if (description) {
                        // Use a small delay to ensure Quill is fully initialized
                        setTimeout(() => {
                            if (this.quillEditor) {
                                this.quillEditor.root.innerHTML = description;
                            }
                        }, 100);
                    }

                    // Update form when editor content changes
                    this.quillEditor.on('text-change', () => {
                        if (!this.quillEditor) return;
                        const content = this.quillEditor.root.innerHTML.trim();
                        // Only update if content is not empty
                        if (content && content !== '<p><br></p>' && content !== '<p></p>') {
                            this.localForm.description = this.quillEditor.root.innerHTML;
                            this.$emit('update:form', this.localForm);
                        } else if (!content || content === '<p><br></p>' || content === '<p></p>') {
                            // Clear description if editor is empty
                            this.localForm.description = '';
                            this.$emit('update:form', this.localForm);
                        }
                    });

                    // Also update on selection change (for better sync)
                    this.quillEditor.on('selection-change', () => {
                        if (!this.quillEditor) return;
                        const content = this.quillEditor.root.innerHTML.trim();
                        if (content && content !== '<p><br></p>' && content !== '<p></p>') {
                            this.localForm.description = this.quillEditor.root.innerHTML;
                            this.$emit('update:form', this.localForm);
                        }
                    });
                } catch (error) {
                    console.error('Error initializing Quill editor:', error);
                }
            });
        },
        destroyEditor() {
            if (this.quillEditor) {
                try {
                    if (this.quillEditor.root && this.quillEditor.root.innerHTML) {
                        const content = this.quillEditor.root.innerHTML.trim();
                        if (content && content !== '<p><br></p>') {
                            this.localForm.description = content;
                            this.$emit('update:form', this.localForm);
                        }
                    }
                } catch (error) {
                    console.error('Error saving editor content:', error);
                }

                try {
                    this.quillEditor = null;
                    if (this.$refs.editorContainer) {
                        this.$refs.editorContainer.innerHTML = '';
                    }
                } catch (error) {
                    console.error('Error destroying editor:', error);
                }
            }
        }
    },
    beforeUnmount() {
        this.destroyEditor();
    }
};
</script>

<style scoped>
.border {
    border: 1px solid rgba(0, 0, 0, 0.12);
}

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
</style>
