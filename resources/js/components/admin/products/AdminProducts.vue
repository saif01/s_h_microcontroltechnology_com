<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Products Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create
                Product</v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="3">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="publishedFilter" :items="publishedOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadProducts"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="featuredFilter" :items="featuredOptions" label="Filter by Featured"
                            prepend-inner-icon="mdi-star" variant="outlined" density="compact" clearable
                            @update:model-value="loadProducts"></v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field v-model="search" label="Search products" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="onSearchChange"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Products Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Products</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="products.length > 0">
                        | Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                            pagination.total) }} of {{ pagination.total }}
                    </span>
                </span>
            </v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th class="sortable" @click="onSort('title')">
                                <div class="d-flex align-center">
                                    Title
                                    <v-icon :icon="getSortIcon('title')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('sku')">
                                <div class="d-flex align-center">
                                    SKU
                                    <v-icon :icon="getSortIcon('sku')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Categories</th>
                            <th class="sortable" @click="onSort('price')">
                                <div class="d-flex align-center">
                                    Price
                                    <v-icon :icon="getSortIcon('price')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('published')">
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('published')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Skeleton Loaders -->
                        <tr v-if="loading" v-for="n in 5" :key="`skeleton-${n}`">
                            <td>
                                <v-skeleton-loader type="avatar" width="40" height="40"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="200" class="mb-1"></v-skeleton-loader>
                                <v-skeleton-loader type="chip" width="60" height="20"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="100" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <v-skeleton-loader type="chip" width="80" height="20"
                                        class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="chip" width="70" height="20"></v-skeleton-loader>
                                </div>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="80"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="70" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <v-skeleton-loader type="button" width="32" height="32"
                                        class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"
                                        class="mr-1"></v-skeleton-loader>
                                    <v-skeleton-loader type="button" width="32" height="32"></v-skeleton-loader>
                                </div>
                            </td>
                        </tr>
                        <!-- Actual Product Data -->
                        <template v-else>
                            <tr v-for="product in products" :key="product.id">
                                <td>
                                    <v-avatar size="40" v-if="product.thumbnail">
                                        <v-img :src="resolveImageUrl(product.thumbnail)" cover></v-img>
                                    </v-avatar>
                                    <v-avatar size="40" v-else color="grey-lighten-2">
                                        <v-icon icon="mdi-package-variant"></v-icon>
                                    </v-avatar>
                                </td>
                                <td>
                                    <div class="font-weight-medium">{{ product.title }}</div>
                                    <v-chip v-if="product.featured" color="amber" size="x-small"
                                        class="mt-1">Featured</v-chip>
                                </td>
                                <td>
                                    <v-chip size="small" variant="outlined">{{ product.sku || '-' }}</v-chip>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <v-chip v-for="cat in (product.categories || [])" :key="cat.id" size="x-small"
                                            color="primary" variant="tonal">
                                            {{ cat.name }}
                                        </v-chip>
                                        <span v-if="!product.categories || product.categories.length === 0"
                                            class="text-caption text-grey">-</span>
                                    </div>
                                </td>
                                <td>{{ formatProductPrice(product) }}</td>
                                <td>
                                    <v-chip :color="product.published ? 'success' : 'error'" size="small">
                                        {{ product.published ? 'Published' : 'Draft' }}
                                    </v-chip>
                                </td>
                                <td>
                                    <v-btn size="small" icon="mdi-eye" @click="viewProduct(product)" variant="text"
                                        color="info" class="mr-1"></v-btn>
                                    <v-btn size="small" icon="mdi-pencil" @click="editProduct(product)" variant="text"
                                        color="primary" class="mr-1"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="deleteProduct(product.id)"
                                        variant="text" color="error"></v-btn>
                                </td>
                            </tr>
                            <tr v-if="products.length === 0">
                                <td colspan="7" class="text-center py-4">No products found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <div
                    class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="products.length > 0 && pagination.total > 0">
                            Showing <strong>{{ ((currentPage - 1) * perPage) + 1 }}</strong> to
                            <strong>{{ Math.min(currentPage * perPage, pagination.total) }}</strong> of
                            <strong>{{ pagination.total.toLocaleString() }}</strong> records
                            <span v-if="pagination.last_page > 1" class="ml-2">
                                (Page {{ currentPage }} of {{ pagination.last_page }})
                            </span>
                        </span>
                        <span v-else>No records found</span>
                    </div>
                    <div v-if="pagination.last_page > 1" class="d-flex align-center gap-2">
                        <v-pagination v-model="currentPage" :length="pagination.last_page" :total-visible="7"
                            density="comfortable" @update:model-value="loadProducts">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Product Details Dialog -->
        <ProductDetailsDialog v-model="showDetailsDialog" :product-details="productDetails"
            :loading="loadingProductDetails" :specifications-list="detailsSpecificationsList"
            :features-list="detailsFeaturesList" :downloads-list="detailsDownloadsList" :faqs-list="detailsFaqsList"
            :warranty-info="detailsWarrantyInfo" @edit="editProductFromDetails" />

        <!-- Product Form Dialog -->
        <ProductFormDialog v-model="showDialog" :editing-product="editingProduct" :loading="loadingProduct"
            :saving="saving" :form="form" :form-tab="formTab" :available-categories="availableCategories"
            :available-tags="availableTags" :loading-categories="loadingCategories"
            :specifications-list="specificationsList" :features-list="featuresList" :downloads-list="downloadsList"
            :faqs-list="faqsList" :warranty-info="warrantyInfo" :download-types="downloadTypes"
            :thumbnail-file="thumbnailFile" :gallery-files="galleryFiles" :thumbnail-preview="thumbnailPreview"
            :gallery-previews="galleryPreviews" :image-url-inputs="imageUrlInputs" :thumbnail-error="thumbnailError"
            :gallery-error="galleryError" @update:form="form = $event" @update:form-tab="formTab = $event"
            @update:specifications-list="specificationsList = $event" @update:features-list="featuresList = $event"
            @update:downloads-list="downloadsList = $event" @update:faqs-list="faqsList = $event"
            @update:warranty-info="warrantyInfo = $event" @update:thumbnail-file="thumbnailFile = $event"
            @update:gallery-files="galleryFiles = $event" @update:thumbnail-preview="thumbnailPreview = $event"
            @update:gallery-previews="galleryPreviews = $event" @update:image-url-inputs="imageUrlInputs = $event"
            @update:thumbnail-error="thumbnailError = $event" @update:gallery-error="galleryError = $event"
            @close="closeDialog" @save="saveProduct" @generate-slug="generateSlug" @add-image-url="addImageUrl"
            @remove-image-url="removeImageUrl" @remove-image="removeImage" @preview-thumbnail="previewThumbnail"
            @preview-gallery-images="previewGalleryImages" @remove-gallery-preview="removeGalleryPreview"
            @add-specification="addSpecification" @remove-specification="removeSpecification" @add-feature="addFeature"
            @remove-feature="removeFeature" @add-download="addDownload" @remove-download="removeDownload"
            @handle-download-file-change="handleDownloadFileChange" @clear-download-file="clearDownloadFile"
            @add-faq="addFAQ" @remove-faq="removeFAQ" @add-coverage-item="addCoverageItem"
            @remove-coverage-item="removeCoverageItem" />
    </div>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import { normalizeUploadPath, resolveUploadUrl } from '../../../utils/uploads';
import { formatProductPrice } from '../../../utils/formatters';
import ProductDetailsDialog from './ProductDetailsDialog.vue';
import ProductFormDialog from './ProductFormDialog.vue';

export default {
    components: {
        ProductDetailsDialog,
        ProductFormDialog
    },
    mixins: [adminPaginationMixin],
    data() {
        return {
            products: [],
            showDialog: false,
            showDetailsDialog: false,
            formTab: 'basic',
            detailsTab: 'basic',
            editingProduct: null,
            productDetails: null,
            saving: false,
            loadingProduct: false,
            loadingProductDetails: false,
            publishedFilter: null,
            featuredFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ],
            featuredOptions: [
                { title: 'Featured', value: true },
                { title: 'Not Featured', value: false }
            ],
            form: {
                title: '',
                slug: '',
                sku: '',
                brand: '',
                short_description: '',
                description: '',
                thumbnail: '',
                images: [],
                price: null,
                discount_percent: 0,
                discounted_price: null,
                price_range: '',
                show_price: true,
                availability: 'in_stock',
                rating: 0,
                rating_count: 0,
                features: [],
                category_ids: [],
                tag_names: [],
                specifications: {},
                key_features: [],
                downloads: [],
                published: false,
                featured: false,
                stock: null,
                order: 0,
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: ''
            },
            availableCategories: [],
            availableTags: [],
            loadingCategories: false,
            specificationsList: [],
            featuresList: [],
            downloadsList: [],
            faqsList: [],
            warrantyInfo: {
                period: '2 Years',
                coverage: [],
                terms: ''
            },
            searchDebounceTimer: null,
            downloadTypes: ['PDF', 'ZIP', 'DOC', 'DOCX', 'XLS', 'XLSX', 'Other'],
            detailsSpecificationsList: [],
            detailsFeaturesList: [],
            detailsDownloadsList: [],
            detailsFaqsList: [],
            detailsWarrantyInfo: {
                period: '2 Years',
                coverage: [],
                terms: ''
            },
            thumbnailFile: null,
            galleryFiles: [],
            uploadingThumbnail: false,
            uploadingGallery: false,
            thumbnailPreview: null,
            galleryPreviews: [],
            thumbnailError: null,
            galleryError: null,
            imageUrlInputs: [],
            // Cache product details to avoid re-fetching when opening edit dialog repeatedly
            productDetailsCache: {}
        };
    },
    async mounted() {
        await Promise.all([
            this.loadProducts(),
            this.loadCategories(),
            this.loadTags()
        ]);
    },
    watch: {
        'form.title': {
            handler(newTitle) {
                // Auto-generate slug from title only when creating a new product
                // Don't auto-update slug when editing to preserve existing URLs
                if (newTitle && !this.editingProduct) {
                    this.generateSlug();
                }
            },
            immediate: false
        }
    },
    methods: {
        async loadProducts() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                if (this.search) {
                    params.search = this.search;
                }
                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }
                if (this.featuredFilter !== null) {
                    params.featured = this.featuredFilter;
                }

                const response = await this.$axios.get('/api/v1/products', {
                    params,
                    headers: this.getAuthHeaders()
                });

                const products = response.data.data || [];
                this.products = products.map(product => ({
                    ...product,
                    thumbnail: this.resolveImageUrl(product.thumbnail)
                }));
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load products');
            } finally {
                this.loading = false;
            }
        },
        onSearchChange(value) {
            // Debounce search requests to avoid hammering the API on every keystroke
            this.search = value;
            this.resetPagination();

            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            this.searchDebounceTimer = setTimeout(() => {
                this.loadProducts();
            }, 350);
        },
        async loadCategories() {
            try {
                this.loadingCategories = true;
                // Try multiple possible endpoints
                let response = await this.$axios.get('/api/v1/categories?type=product', {
                    headers: this.getAuthHeaders()
                }).catch(() => null);

                if (!response) {
                    // Try without type filter
                    response = await this.$axios.get('/api/v1/categories', {
                        headers: this.getAuthHeaders()
                    }).catch(() => null);
                }

                if (response && response.data) {
                    const categories = response.data?.data || response.data || [];
                    // Filter by type if not already filtered
                    this.availableCategories = Array.isArray(categories)
                        ? categories.filter(c => !c.type || c.type === 'product')
                        : [];
                } else {
                    this.availableCategories = [];
                }
            } catch (error) {
                console.error('Error loading categories:', error);
                this.availableCategories = [];
            } finally {
                this.loadingCategories = false;
            }
        },
        async loadTags() {
            try {
                // Try multiple possible endpoints
                let response = await this.$axios.get('/api/v1/tags?type=product', {
                    headers: this.getAuthHeaders()
                }).catch(() => null);

                if (!response) {
                    // Try without type filter
                    response = await this.$axios.get('/api/v1/tags', {
                        headers: this.getAuthHeaders()
                    }).catch(() => null);
                }

                if (response && response.data) {
                    const tags = response.data?.data || response.data || [];
                    // Filter by type if not already filtered
                    const filteredTags = Array.isArray(tags)
                        ? tags.filter(t => !t.type || t.type === 'product')
                        : [];
                    this.availableTags = filteredTags.map(t => t.name || t);
                } else {
                    this.availableTags = [];
                }
            } catch (error) {
                console.error('Error loading tags:', error);
                this.availableTags = [];
            }
        },
        async openDialog(product = null) {
            this.editingProduct = product;
            this.showDialog = true;

            if (product) {
                try {
                    await this.loadProductForEdit(product);
                } catch (error) {
                    console.error('Error loading product for edit:', error);
                    // Keep dialog open but show error
                }
            } else {
                this.resetForm();
            }
        },
        async loadProductForEdit(product) {
            this.loadingProduct = true;
            try {
                // Use cached details if available to avoid another API call
                const cached = this.productDetailsCache[product.id];
                if (cached) {
                    this.applyProductDataToForm(cached);
                    this.loadingProduct = false;
                    return;
                }

                // Try to use product data from list if it has enough information
                // This avoids an API call for products that were just loaded
                if (product && product.id && this.hasCompleteProductData(product)) {
                    // Use the product from the list, but we still need full details
                    // So we'll make the API call but with error handling for memory issues
                }

                const response = await this.$axios.get(`/api/v1/products/${product.id}`, {
                    headers: this.getAuthHeaders(),
                    timeout: 30000, // 30 second timeout
                    // Add params to potentially reduce response size if backend supports it
                });

                // Handle different response structures
                const data = response.data?.data || response.data;

                if (!data) {
                    throw new Error('No product data received from server');
                }

                // Cache the data before processing to avoid re-fetching
                this.productDetailsCache[data.id] = data;
                this.applyProductDataToForm(data);
            } catch (error) {
                console.error('Error loading product:', error);

                // Check for memory-related errors
                if (error.response?.status === 500 ||
                    error.message?.includes('memory') ||
                    error.response?.data?.message?.includes('memory')) {
                    this.showError('Product data is too large to load. Please try refreshing the page or contact support.');
                } else {
                    const errorMessage = error.response?.data?.message ||
                        error.response?.data?.error ||
                        error.message ||
                        'Failed to load product data';
                    this.showError(errorMessage);
                }

                // Close dialog on error
                this.closeDialog();
                throw error;
            } finally {
                this.loadingProduct = false;
            }
        },
        hasCompleteProductData(product) {
            // Check if product has all necessary fields for editing
            return product && product.title && product.slug;
        },
        resetForm() {
            this.form = {
                title: '',
                slug: '',
                sku: '',
                brand: '',
                short_description: '',
                description: '',
                thumbnail: '',
                images: [],
                price: null,
                discount_percent: 0,
                discounted_price: null,
                price_range: '',
                show_price: true,
                availability: 'in_stock',
                rating: 0,
                rating_count: 0,
                features: [],
                category_ids: [],
                tag_names: [],
                published: false,
                featured: false,
                stock: null,
                order: 0,
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                og_image: ''
            };
            this.specificationsList = [];
            this.featuresList = [];
            // Clear download files before resetting list
            this.downloadsList.forEach(download => {
                if (download.file) {
                    download.file = null;
                    download.uploading = false;
                    download.uploaded = false;
                }
            });
            this.downloadsList = [];
            this.faqsList = [];
            this.warrantyInfo = {
                period: '2 Years',
                coverage: [],
                terms: ''
            };
            this.thumbnailFile = null;
            this.thumbnailPreview = null;
            this.thumbnailError = null;
            this.galleryFiles = [];
            this.galleryPreviews = [];
            this.galleryError = null;
            this.imageUrlInputs = [];
            this.formTab = 'basic';
        },
        generateSlug() {
            if (this.form.title) {
                this.form.slug = this.form.title
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
            }
        },
        addImageUrl() {
            this.imageUrlInputs.push('');
        },
        removeImageUrl(index) {
            this.imageUrlInputs.splice(index, 1);
        },
        removeImage(index) {
            this.form.images.splice(index, 1);
        },
        previewThumbnail(file) {
            // Clear previous error
            this.thumbnailError = null;

            if (!file) {
                this.thumbnailPreview = null;
                return;
            }

            // Handle both single file and array
            const fileToPreview = Array.isArray(file) ? file[0] : file;
            if (!fileToPreview) {
                this.thumbnailPreview = null;
                return;
            }

            // Validate file type
            if (!fileToPreview.type.startsWith('image/')) {
                this.thumbnailError = 'Please select a valid image file (jpeg, jpg, png, gif, webp)';
                return;
            }

            // Validate file size
            if (fileToPreview.size > 5242880) {
                this.thumbnailError = 'Image file size must be less than 5MB';
                return;
            }

            // Create preview URL
            const reader = new FileReader();
            reader.onload = (e) => {
                this.thumbnailPreview = e.target.result;
            };
            reader.onerror = () => {
                this.thumbnailError = 'Failed to read image file';
            };
            reader.readAsDataURL(fileToPreview);
        },
        previewGalleryImages(files) {
            // Clear previous error
            this.galleryError = null;

            if (!files) {
                this.galleryPreviews = [];
                this.galleryFiles = [];
                return;
            }

            // Handle both single file and array
            const filesToPreview = Array.isArray(files) ? files : [files];
            if (filesToPreview.length === 0) {
                this.galleryPreviews = [];
                this.galleryFiles = [];
                return;
            }

            // Validate all files
            const invalidFiles = [];
            filesToPreview.forEach((file, index) => {
                if (!file.type.startsWith('image/')) {
                    invalidFiles.push(`File ${index + 1}: Invalid image type`);
                } else if (file.size > 5242880) {
                    invalidFiles.push(`File ${index + 1}: File size exceeds 5MB`);
                }
            });

            if (invalidFiles.length > 0) {
                this.galleryError = invalidFiles.join('. ');
                return;
            }

            // Store files for upload
            this.galleryFiles = filesToPreview.filter(f => f);

            // Create previews for all selected files
            const newPreviews = [];
            const filePromises = filesToPreview.map((file) => {
                if (!file) return Promise.resolve(null);

                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        resolve({
                            url: e.target.result,
                            name: file.name,
                            size: file.size,
                            file: file
                        });
                    };
                    reader.onerror = () => resolve(null);
                    reader.readAsDataURL(file);
                });
            });

            // Wait for all previews to be created
            Promise.all(filePromises).then(previews => {
                this.galleryPreviews = previews.filter(p => p !== null);
            });
        },
        removeGalleryPreview(index) {
            // Remove from previews
            this.galleryPreviews.splice(index, 1);
            // Also remove from galleryFiles array
            if (Array.isArray(this.galleryFiles) && this.galleryFiles.length > index) {
                this.galleryFiles.splice(index, 1);
            }
            // If no previews left, clear files
            if (this.galleryPreviews.length === 0) {
                this.galleryFiles = [];
            }
        },
        formatFileSize(bytes) {
            if (!bytes) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },
        async uploadThumbnailFile() {
            if (!this.thumbnailFile) return null;

            const fileToUpload = Array.isArray(this.thumbnailFile) ? this.thumbnailFile[0] : this.thumbnailFile;
            if (!fileToUpload) return null;

            this.uploadingThumbnail = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'products');
                // Add product title as prefix if available
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await this.$axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = this.normalizeImageInput(response.data.path || response.data.url);
                    this.thumbnailError = null; // Clear error on success
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload thumbnail');
                }
            } catch (error) {
                console.error('Error uploading thumbnail:', error);
                console.error('Error response:', error.response?.data);

                // Extract validation errors
                let errorMessage = 'Failed to upload thumbnail';
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

                this.thumbnailError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingThumbnail = false;
            }
        },
        async uploadGalleryFiles() {
            if (!this.galleryPreviews || this.galleryPreviews.length === 0) return [];

            const uploadedUrls = [];
            this.uploadingGallery = true;

            try {
                // Collect all files from previews
                const filesToUpload = this.galleryPreviews
                    .filter(preview => preview.file)
                    .map(preview => preview.file);

                if (filesToUpload.length === 0) return [];

                // Upload all files in one request
                const formData = new FormData();
                filesToUpload.forEach(file => {
                    formData.append('images[]', file);
                });
                formData.append('folder', 'products');
                // Add product title as prefix if available
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await this.$axios.post('/api/v1/upload/images', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success && response.data.images && response.data.images.length > 0) {
                    const normalizedPaths = response.data.images.map(img =>
                        this.normalizeImageInput(img.path || img.url)
                    );
                    uploadedUrls.push(...normalizedPaths);
                    this.galleryError = null; // Clear error on success
                } else {
                    throw new Error(response.data.message || 'Failed to upload images');
                }

                return uploadedUrls;
            } catch (error) {
                console.error('Error uploading gallery images:', error);
                console.error('Error response:', error.response?.data);

                // Extract validation errors
                let errorMessage = 'Failed to upload images';
                if (error.response?.status === 422) {
                    // Validation error
                    const errors = error.response.data.errors;
                    if (errors && errors['images.0']) {
                        // Handle array validation errors
                        const imageErrors = [];
                        Object.keys(errors).forEach(key => {
                            if (key.startsWith('images.')) {
                                if (Array.isArray(errors[key])) {
                                    imageErrors.push(...errors[key]);
                                } else {
                                    imageErrors.push(errors[key]);
                                }
                            }
                        });
                        errorMessage = imageErrors.length > 0 ? imageErrors.join(', ') : error.response.data.message;
                    } else if (errors && errors.images) {
                        errorMessage = Array.isArray(errors.images) ? errors.images.join(', ') : errors.images;
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

                this.galleryError = errorMessage;
                throw new Error(errorMessage);
            } finally {
                this.uploadingGallery = false;
            }
        },
        addSpecification() {
            this.specificationsList.push({ key: '', value: '' });
        },
        removeSpecification(index) {
            this.specificationsList.splice(index, 1);
        },
        addFeature() {
            this.featuresList.push('');
        },
        removeFeature(index) {
            this.featuresList.splice(index, 1);
        },
        addDownload() {
            this.downloadsList.push({
                title: '',
                type: 'PDF',
                size: '',
                url: '',
                file: null,
                uploading: false,
                uploaded: false
            });
        },
        removeDownload(index) {
            this.downloadsList.splice(index, 1);
        },
        handleDownloadFileChange(index) {
            const download = this.downloadsList[index];
            if (download.file) {
                // Auto-detect file type from extension
                const extension = download.file.name.split('.').pop().toUpperCase();
                if (['PDF', 'ZIP', 'DOC', 'DOCX', 'XLS', 'XLSX'].includes(extension)) {
                    download.type = extension;
                }
                // Auto-fill size
                download.size = this.formatFileSize(download.file.size);
            }
        },
        clearDownloadFile(index) {
            const download = this.downloadsList[index];
            download.file = null;
            download.uploading = false;
            download.uploaded = false;
            if (!download.url) {
                download.size = '';
            }
        },
        async uploadDownloadFile(index) {
            const download = this.downloadsList[index];
            if (!download.file) return null;

            // Preserve file size before upload (in case API doesn't return it)
            const fileSize = download.file.size || 0;

            download.uploading = true;
            try {
                const formData = new FormData();
                formData.append('file', download.file);
                formData.append('folder', 'downloads');
                // Add product title as prefix if available
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await this.$axios.post('/api/v1/upload/file', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    const uploadedPath = this.normalizeImageInput(response.data.path || response.data.url);
                    download.url = uploadedPath;
                    // Use API response size if available, otherwise use file size
                    const size = response.data.size || fileSize;
                    download.size = this.formatFileSize(size);
                    download.uploaded = true;
                    return uploadedPath;
                } else {
                    throw new Error(response.data.message || 'Failed to upload file');
                }
            } catch (error) {
                console.error('Error uploading download file:', error);
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to upload file';
                throw new Error(errorMessage);
            } finally {
                download.uploading = false;
            }
        },
        addFAQ() {
            this.faqsList.push({ question: '', answer: '' });
        },
        removeFAQ(index) {
            this.faqsList.splice(index, 1);
        },
        addCoverageItem() {
            this.warrantyInfo.coverage.push('');
        },
        removeCoverageItem(index) {
            this.warrantyInfo.coverage.splice(index, 1);
        },
        async saveProduct() {
            try {
                // Validate required fields
                if (!this.form.title || !this.form.slug) {
                    this.showError('Title and Slug are required');
                    return;
                }

                this.saving = true;

                // Upload thumbnail if a new file is selected
                let thumbnailPath = this.form.thumbnail;
                if (this.thumbnailFile) {
                    try {
                        const uploadedPath = await this.uploadThumbnailFile();
                        if (uploadedPath) {
                            thumbnailPath = uploadedPath;
                            this.form.thumbnail = thumbnailPath;
                        }
                    } catch (error) {
                        // Error is already set in uploadThumbnailFile
                        // Show error in the form dialog instead of global error
                        this.saving = false;
                        return;
                    }
                }

                // Collect existing images
                let galleryPaths = [...this.form.images.filter(img => img).map(img => this.normalizeImageInput(img))];

                // Upload new gallery images if files are selected
                if (this.galleryPreviews && this.galleryPreviews.length > 0) {
                    try {
                        const uploadedPaths = await this.uploadGalleryFiles();
                        galleryPaths = [...galleryPaths, ...uploadedPaths];
                    } catch (error) {
                        // Error is already set in uploadGalleryFiles
                        // Show error in the form dialog instead of global error
                        this.saving = false;
                        return;
                    }
                }

                // Add image URLs from inputs
                const imageUrls = this.imageUrlInputs.filter(url => url && url.trim()).map(url => this.normalizeImageInput(url.trim()));
                galleryPaths = [...galleryPaths, ...imageUrls];

                // Upload download files if new files are selected
                for (let i = 0; i < this.downloadsList.length; i++) {
                    const download = this.downloadsList[i];
                    if (download.file && !download.uploaded) {
                        try {
                            await this.uploadDownloadFile(i);
                        } catch (error) {
                            this.showError(`Failed to upload download file "${download.title || download.file.name}": ${error.message}`);
                            return;
                        }
                    }
                }

                // Build specifications object
                // Filter out numeric keys that look like array indices (likely corrupted data)
                const specifications = {};
                this.specificationsList.forEach(spec => {
                    if (spec.key && spec.value) {
                        // Skip pure numeric keys (likely from corrupted array data)
                        // Allow keys that are numeric but have other characters (e.g., "0.5", "1a", etc.)
                        const keyStr = String(spec.key);
                        if (!/^\d+$/.test(keyStr)) {
                            specifications[spec.key] = spec.value;
                        }
                    }
                });

                // Normalize features - handle both string values (from combobox) and objects
                const normalizedFeatures = this.form.features ? this.form.features.map(f => {
                    if (typeof f === 'string') return f.toLowerCase().replace(/\s+/g, '_');
                    if (typeof f === 'object' && f.value) return f.value;
                    return f;
                }).filter(f => f) : [];

                // Prepare form data - ensure all fields are included
                const formData = {
                    title: this.form.title,
                    slug: this.form.slug,
                    sku: this.form.sku || null,
                    brand: this.form.brand || null,
                    short_description: this.form.short_description || null,
                    description: this.form.description || null,
                    thumbnail: this.normalizeImageInput(thumbnailPath) || null,
                    images: galleryPaths.map(img => this.normalizeImageInput(img)),
                    price: this.form.price ? parseFloat(this.form.price) : null,
                    discount_percent: this.form.discount_percent ? parseFloat(this.form.discount_percent) : 0,
                    discounted_price: this.form.discounted_price ? parseFloat(this.form.discounted_price) : null,
                    price_range: this.form.price_range || null,
                    show_price: this.form.show_price !== false,
                    availability: this.form.availability || 'in_stock',
                    rating: this.form.rating ? parseFloat(this.form.rating) : 0,
                    rating_count: this.form.rating_count ? parseInt(this.form.rating_count) : 0,
                    features: normalizedFeatures.length > 0 ? normalizedFeatures : null,
                    specifications: Object.keys(specifications).length > 0 ? specifications : null,
                    downloads: this.downloadsList.filter(d => d.title && d.url).length > 0
                        ? this.downloadsList.filter(d => d.title && d.url)
                        : null,
                    key_features: this.featuresList.filter(f => f).length > 0
                        ? this.featuresList.filter(f => f)
                        : null,
                    faqs: this.faqsList.filter(f => f.question && f.answer).length > 0
                        ? this.faqsList.filter(f => f.question && f.answer)
                        : null,
                    warranty_info: this.warrantyInfo,
                    meta_title: this.form.meta_title || null,
                    meta_description: this.form.meta_description || null,
                    meta_keywords: this.form.meta_keywords || null,
                    og_image: this.normalizeImageInput(this.form.og_image) || null,
                    published: this.form.published || false,
                    featured: this.form.featured || false,
                    stock: this.form.stock ? parseInt(this.form.stock) : null,
                    order: this.form.order ? parseInt(this.form.order) : 0,
                    category_ids: this.form.category_ids || [],
                    tag_names: this.form.tag_names || []
                };

                if (this.editingProduct) {
                    const response = await this.$axios.put(`/api/v1/products/${this.editingProduct.id}`, formData, {
                        headers: this.getAuthHeaders()
                    });
                    // Clear cache for updated product to force fresh data on next load
                    if (this.editingProduct.id) {
                        delete this.productDetailsCache[this.editingProduct.id];
                    }
                    this.showSuccess('Product updated successfully');
                } else {
                    const response = await this.$axios.post('/api/v1/products', formData, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Product created successfully');
                }

                // Clear file inputs and previews
                this.thumbnailFile = null;
                this.thumbnailPreview = null;
                this.thumbnailError = null;
                this.galleryFiles = [];
                this.galleryPreviews = [];
                this.galleryError = null;
                this.imageUrlInputs = [];

                this.closeDialog();
                await this.loadProducts();
            } catch (error) {
                console.error('Error saving product:', error);
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    error.message ||
                    'Failed to save product';
                this.showError(errorMessage);
            } finally {
                this.saving = false;
            }
        },
        editProduct(product) {
            this.openDialog(product);
        },
        async viewProduct(product) {
            this.showDetailsDialog = true;
            this.loadingProductDetails = true;
            this.detailsTab = 'basic';

            try {
                // Check cache first to avoid unnecessary API calls
                const cached = this.productDetailsCache[product.id];
                if (cached) {
                    // Use cached data, but ensure image URLs are resolved
                    const data = { ...cached };
                    if (data.thumbnail) {
                        data.thumbnail = this.resolveImageUrl(data.thumbnail);
                    }
                    if (Array.isArray(data.images)) {
                        data.images = data.images.map(img => this.resolveImageUrl(img));
                    }
                    if (data.og_image) {
                        data.og_image = this.resolveImageUrl(data.og_image);
                    }
                    this.productDetails = data;
                    this.loadingProductDetails = false;
                    this.parseProductDetailsData(data);
                    return;
                }

                const response = await this.$axios.get(`/api/v1/products/${product.id}`, {
                    headers: this.getAuthHeaders(),
                    timeout: 30000 // 30 second timeout
                });

                const data = response.data?.data || response.data;

                if (!data) {
                    throw new Error('No product data received');
                }

                // Cache the raw data first (before URL resolution)
                if (data.id) {
                    this.productDetailsCache[data.id] = { ...data };
                }

                // Resolve image URLs for display
                if (data.thumbnail) {
                    data.thumbnail = this.resolveImageUrl(data.thumbnail);
                }
                if (Array.isArray(data.images)) {
                    data.images = data.images.map(img => this.resolveImageUrl(img));
                }
                if (data.og_image) {
                    data.og_image = this.resolveImageUrl(data.og_image);
                }

                this.productDetails = data;

                this.parseProductDetailsData(data);
            } catch (error) {
                console.error('Error loading product details:', error);

                // Check for memory-related errors
                if (error.response?.status === 500 ||
                    error.message?.includes('memory') ||
                    error.response?.data?.message?.includes('memory')) {
                    this.showError('Product data is too large to load. Please try refreshing the page or contact support.');
                } else {
                    const errorMessage = error.response?.data?.message ||
                        error.response?.data?.error ||
                        error.message ||
                        'Failed to load product details';
                    this.showError(errorMessage);
                }
                this.closeDetailsDialog();
            } finally {
                this.loadingProductDetails = false;
            }
        },
        parseProductDetailsData(data) {
            // Parse specifications
            this.detailsSpecificationsList = [];
            if (data.specifications && typeof data.specifications === 'object') {
                Object.keys(data.specifications).forEach(key => {
                    // Skip special fields and pure numeric keys (likely from corrupted array data)
                    if (!key.startsWith('_') && !/^\d+$/.test(String(key))) {
                        this.detailsSpecificationsList.push({
                            key: key,
                            value: data.specifications[key]
                        });
                    }
                });
            }

            // Parse features
            if (data.key_features && Array.isArray(data.key_features)) {
                this.detailsFeaturesList = [...data.key_features];
            } else if (data.specifications?._key_features && Array.isArray(data.specifications._key_features)) {
                this.detailsFeaturesList = [...data.specifications._key_features];
            } else {
                this.detailsFeaturesList = [];
            }

            // Parse downloads
            this.detailsDownloadsList = Array.isArray(data.downloads) ? [...data.downloads] : [];

            // Parse FAQs
            if (data.faqs && Array.isArray(data.faqs)) {
                this.detailsFaqsList = [...data.faqs];
            } else if (data.specifications?._faqs && Array.isArray(data.specifications._faqs)) {
                this.detailsFaqsList = [...data.specifications._faqs];
            } else {
                this.detailsFaqsList = [];
            }

            // Parse warranty info
            if (data.warranty_info && typeof data.warranty_info === 'object') {
                this.detailsWarrantyInfo = {
                    period: data.warranty_info.period || '2 Years',
                    coverage: Array.isArray(data.warranty_info.coverage) ? [...data.warranty_info.coverage] : [],
                    terms: data.warranty_info.terms || ''
                };
            } else if (data.specifications?._warranty_info && typeof data.specifications._warranty_info === 'object') {
                this.detailsWarrantyInfo = {
                    period: data.specifications._warranty_info.period || '2 Years',
                    coverage: Array.isArray(data.specifications._warranty_info.coverage) ? [...data.specifications._warranty_info.coverage] : [],
                    terms: data.specifications._warranty_info.terms || ''
                };
            } else {
                this.detailsWarrantyInfo = {
                    period: '2 Years',
                    coverage: [],
                    terms: ''
                };
            }
        },
        closeDetailsDialog() {
            this.showDetailsDialog = false;
            this.productDetails = null;
            this.detailsTab = 'basic';
            this.detailsSpecificationsList = [];
            this.detailsFeaturesList = [];
            this.detailsDownloadsList = [];
            this.detailsFaqsList = [];
            this.detailsWarrantyInfo = {
                period: '2 Years',
                coverage: [],
                terms: ''
            };
        },
        async editProductFromDetails() {
            if (this.productDetails) {
                // Store the product details data
                const productData = { ...this.productDetails };

                // Close details dialog first
                this.closeDetailsDialog();

                // Wait a bit to ensure dialog is closed
                await this.$nextTick();

                // Open edit dialog with the product from list (for ID reference)
                const product = this.products.find(p => p.id === productData.id);
                if (product) {
                    this.editingProduct = product;
                    this.showDialog = true;

                    // Populate form directly from productDetails (no API call needed)
                    try {
                        this.loadingProduct = true;
                        await this.$nextTick(); // Ensure dialog is rendered

                        // Use the already loaded productDetails data
                        // Cache the details and reuse the same application path as the fetch flow
                        if (productData.id) {
                            this.productDetailsCache[productData.id] = productData;
                        }
                        this.applyProductDataToForm(productData);
                    } catch (error) {
                        console.error('Error populating edit form:', error);
                        this.showError('Failed to load product data for editing');
                    } finally {
                        this.loadingProduct = false;
                    }
                }
            }
        },
        async deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                try {
                    await this.$axios.delete(`/api/v1/products/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    // Clear cache for deleted product
                    if (id) {
                        delete this.productDetailsCache[id];
                    }
                    this.showSuccess('Product deleted successfully');
                    await this.loadProducts();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting product');
                }
            }
        },
        closeDialog() {
            if (this.loadingProduct || this.saving) {
                return; // Prevent closing while loading or saving
            }
            this.showDialog = false;
            this.editingProduct = null;
            this.loadingProduct = false;
            // Clear file inputs and previews
            this.thumbnailFile = null;
            this.thumbnailPreview = null;
            this.thumbnailError = null;
            this.galleryFiles = [];
            this.galleryPreviews = [];
            this.galleryError = null;
            this.resetForm();
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadProducts();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadProducts();
        },
        normalizeImageInput(imageValue) {
            return normalizeUploadPath(imageValue);
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        },
        formatProductPrice(product) {
            return formatProductPrice(product, '-');
        },
        applyProductDataToForm(data) {
            const normalizedThumbnail = this.normalizeImageInput(data.thumbnail || '');
            const normalizedImages = Array.isArray(data.images)
                ? data.images.map(img => this.normalizeImageInput(img))
                : [];

            this.form = {
                title: data.title || '',
                slug: data.slug || '',
                sku: data.sku || '',
                brand: data.brand || '',
                short_description: data.short_description || '',
                description: data.description || '',
                thumbnail: normalizedThumbnail,
                images: normalizedImages,
                price: data.price || null,
                discount_percent: data.discount_percent || 0,
                discounted_price: data.discounted_price || null,
                price_range: data.price_range || '',
                show_price: data.show_price !== false,
                availability: data.availability || 'in_stock',
                rating: data.rating || 0,
                rating_count: data.rating_count || 0,
                features: Array.isArray(data.features) ? [...data.features] : [],
                category_ids: (data.categories || []).map(c => c.id),
                tag_names: (data.tags || []).map(t => t.name),
                published: data.published || false,
                featured: data.featured || false,
                stock: data.stock || null,
                order: data.order || 0,
                meta_title: data.meta_title || '',
                meta_description: data.meta_description || '',
                meta_keywords: data.meta_keywords || '',
                og_image: data.og_image || ''
            };

            // Reset image URL inputs (existing images are in form.images)
            this.imageUrlInputs = [];

            // Parse specifications (excluding special fields)
            this.specificationsList = [];
            if (data.specifications && typeof data.specifications === 'object') {
                Object.keys(data.specifications).forEach(key => {
                    // Skip special fields stored in specifications
                    // Also skip pure numeric keys (likely from corrupted array data)
                    if (!key.startsWith('_') && !/^\d+$/.test(String(key))) {
                        this.specificationsList.push({
                            key: key,
                            value: data.specifications[key]
                        });
                    }
                });
            }

            // Parse features (from key_features or specifications._key_features)
            if (data.key_features && Array.isArray(data.key_features)) {
                this.featuresList = [...data.key_features];
            } else if (data.specifications?._key_features && Array.isArray(data.specifications._key_features)) {
                this.featuresList = [...data.specifications._key_features];
            } else {
                this.featuresList = [];
            }

            // Parse downloads
            this.downloadsList = Array.isArray(data.downloads) ? [...data.downloads] : [];

            // Parse FAQs (from faqs or specifications._faqs)
            if (data.faqs && Array.isArray(data.faqs)) {
                this.faqsList = [...data.faqs];
            } else if (data.specifications?._faqs && Array.isArray(data.specifications._faqs)) {
                this.faqsList = [...data.specifications._faqs];
            } else {
                this.faqsList = [];
            }

            // Parse warranty info (from warranty_info or specifications._warranty_info)
            if (data.warranty_info && typeof data.warranty_info === 'object') {
                this.warrantyInfo = {
                    period: data.warranty_info.period || '2 Years',
                    coverage: Array.isArray(data.warranty_info.coverage) ? [...data.warranty_info.coverage] : [],
                    terms: data.warranty_info.terms || ''
                };
            } else if (data.specifications?._warranty_info && typeof data.specifications._warranty_info === 'object') {
                this.warrantyInfo = {
                    period: data.specifications._warranty_info.period || '2 Years',
                    coverage: Array.isArray(data.specifications._warranty_info.coverage) ? [...data.specifications._warranty_info.coverage] : [],
                    terms: data.specifications._warranty_info.terms || ''
                };
            } else {
                this.warrantyInfo = {
                    period: '2 Years',
                    coverage: [],
                    terms: ''
                };
            }

            // Reset form tab to basic after loading
            this.formTab = 'basic';
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

.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

.border {
    border: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
