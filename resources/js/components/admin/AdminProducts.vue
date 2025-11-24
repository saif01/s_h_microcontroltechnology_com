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
                            @update:model-value="loadProducts"></v-text-field>
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
                        <tr v-for="product in products" :key="product.id">
                            <td>
                                <v-avatar size="40" v-if="product.thumbnail">
                                    <v-img :src="product.thumbnail" cover></v-img>
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
                            <td>{{ product.price ? '$' + product.price : (product.price_range || '-') }}</td>
                            <td>
                                <v-chip :color="product.published ? 'success' : 'error'" size="small">
                                    {{ product.published ? 'Published' : 'Draft' }}
                                </v-chip>
                            </td>
                            <td>
                                <v-btn size="small" icon="mdi-pencil" @click="editProduct(product)" variant="text"
                                    color="primary"></v-btn>
                                <v-btn size="small" icon="mdi-delete" @click="deleteProduct(product.id)" variant="text"
                                    color="error"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="7" class="text-center py-4">No products found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <div class="d-flex justify-space-between align-center mt-4">
                    <div class="text-caption text-grey">
                        <span v-if="products.length > 0">
                            Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage,
                                pagination.total) }} of {{ pagination.total }} records
                        </span>
                        <span v-else>No records found</span>
                    </div>
                    <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                        @update:model-value="loadProducts"></v-pagination>
                </div>
            </v-card-text>
        </v-card>

        <!-- Product Form Dialog -->
        <v-dialog v-model="showDialog" max-width="1400" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">
                        {{ editingProduct ? 'Edit Product' : 'Create New Product' }}
                    </span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0">
                    <v-tabs v-model="formTab" color="primary" bg-color="grey-lighten-4">
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

                    <v-window v-model="formTab" class="pa-6">
                        <!-- Basic Info Tab -->
                        <v-window-item value="basic">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="form.title" label="Product Title *" variant="outlined"
                                        :rules="[v => !!v || 'Title is required']"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="form.slug" label="Slug *" variant="outlined"
                                        hint="URL-friendly version of title" :rules="[v => !!v || 'Slug is required']">
                                        <template v-slot:append>
                                            <v-btn icon="mdi-refresh" size="small" variant="text"
                                                @click="generateSlug"></v-btn>
                                        </template>
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="form.sku" label="SKU" variant="outlined"
                                        hint="Stock Keeping Unit"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="form.short_description" label="Short Description"
                                        variant="outlined" rows="2" hint="Brief description for listings"></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="form.description" label="Full Description" variant="outlined"
                                        rows="6" hint="Detailed product description (supports HTML)"></v-textarea>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Media Tab -->
                        <v-window-item value="media">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="form.thumbnail" label="Thumbnail URL" variant="outlined"
                                        prepend-inner-icon="mdi-image" hint="Main product image URL"></v-text-field>
                                    <div v-if="form.thumbnail" class="mt-2">
                                        <v-img :src="form.thumbnail" max-height="200" contain class="rounded"></v-img>
                                    </div>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">Product Images (Gallery)</div>
                                    <div v-for="(img, index) in form.images" :key="index" class="mb-3">
                                        <v-text-field v-model="form.images[index]" :label="`Image ${index + 1} URL`"
                                            variant="outlined" prepend-inner-icon="mdi-image">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                    @click="removeImage(index)"></v-btn>
                                            </template>
                                        </v-text-field>
                                        <div v-if="form.images[index]" class="mt-2">
                                            <v-img :src="form.images[index]" max-height="150" contain
                                                class="rounded"></v-img>
                                        </div>
                                    </div>
                                    <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                        @click="addImage">Add Image</v-btn>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Pricing Tab -->
                        <v-window-item value="pricing">
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="form.price" label="Price" variant="outlined"
                                        type="number" step="0.01" prepend-inner-icon="mdi-currency-usd"
                                        hint="Numeric price value"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model="form.price_range" label="Price Range" variant="outlined"
                                        hint="e.g., '$50 - $100' or 'Contact for Price'"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-switch v-model="form.show_price" label="Show Price on Website"
                                        color="primary"></v-switch>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Categories & Tags Tab -->
                        <v-window-item value="categories">
                            <v-row>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">Categories</div>
                                    <v-select v-model="form.category_ids" :items="availableCategories" item-title="name"
                                        item-value="id" label="Select Categories" variant="outlined" multiple chips
                                        :loading="loadingCategories"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-1 font-weight-bold mb-2">Tags</div>
                                    <v-combobox v-model="form.tag_names" :items="availableTags" label="Tags"
                                        variant="outlined" multiple chips closable-chips
                                        hint="Type to add new tags"></v-combobox>
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
                                            placeholder="e.g., Capacity, Input Voltage"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="spec.value" label="Value" variant="outlined"
                                            placeholder="e.g., 2000VA / 1800W"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="1">
                                        <v-btn icon="mdi-delete" color="error" variant="text"
                                            @click="removeSpecification(index)"></v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                            <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                @click="addSpecification">Add
                                Specification</v-btn>
                        </v-window-item>

                        <!-- Features Tab -->
                        <v-window-item value="features">
                            <div class="text-subtitle-1 font-weight-bold mb-4">Key Features</div>
                            <div v-for="(feature, index) in featuresList" :key="index" class="mb-3">
                                <v-text-field v-model="featuresList[index]" :label="`Feature ${index + 1}`"
                                    variant="outlined">
                                    <template v-slot:append>
                                        <v-btn icon="mdi-delete" color="error" variant="text"
                                            @click="removeFeature(index)"></v-btn>
                                    </template>
                                </v-text-field>
                            </div>
                            <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus" @click="addFeature">Add
                                Feature</v-btn>
                        </v-window-item>

                        <!-- Downloads Tab -->
                        <v-window-item value="downloads">
                            <div class="text-subtitle-1 font-weight-bold mb-4">Product Downloads</div>
                            <div v-for="(download, index) in downloadsList" :key="index"
                                class="mb-4 pa-4 border rounded">
                                <v-row>
                                    <v-col cols="12" md="4">
                                        <v-text-field v-model="download.title" label="Title" variant="outlined"
                                            placeholder="e.g., Product Datasheet"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-select v-model="download.type" :items="downloadTypes" label="Type"
                                            variant="outlined"></v-select>
                                    </v-col>
                                    <v-col cols="12" md="3">
                                        <v-text-field v-model="download.size" label="Size" variant="outlined"
                                            placeholder="e.g., 1.2 MB"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="2">
                                        <v-btn icon="mdi-delete" color="error" variant="text"
                                            @click="removeDownload(index)"></v-btn>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="download.url" label="File URL" variant="outlined"
                                            placeholder="https://..."></v-text-field>
                                    </v-col>
                                </v-row>
                            </div>
                            <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus" @click="addDownload">Add
                                Download</v-btn>
                        </v-window-item>

                        <!-- FAQs Tab -->
                        <v-window-item value="faq">
                            <div class="text-subtitle-1 font-weight-bold mb-4">Frequently Asked Questions</div>
                            <div v-for="(faq, index) in faqsList" :key="index" class="mb-4 pa-4 border rounded">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="faq.question" label="Question" variant="outlined"
                                            placeholder="What is the warranty period?"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="faq.answer" label="Answer" variant="outlined" rows="3"
                                            placeholder="This product comes with..."></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-btn color="error" variant="outlined" prepend-icon="mdi-delete"
                                            @click="removeFAQ(index)">Remove
                                            FAQ</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                            <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus" @click="addFAQ">Add
                                FAQ</v-btn>
                        </v-window-item>

                        <!-- Warranty & Service Tab -->
                        <v-window-item value="warranty">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="warrantyInfo.period" label="Warranty Period"
                                        variant="outlined" placeholder="e.g., 2 Years"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <div class="text-subtitle-2 font-weight-bold mb-2">What's Covered</div>
                                    <div v-for="(item, index) in warrantyInfo.coverage" :key="index" class="mb-2">
                                        <v-text-field v-model="warrantyInfo.coverage[index]" variant="outlined"
                                            :label="`Coverage Item ${index + 1}`">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-delete" size="small" color="error" variant="text"
                                                    @click="removeCoverageItem(index)"></v-btn>
                                            </template>
                                        </v-text-field>
                                    </div>
                                    <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                        @click="addCoverageItem">Add Coverage
                                        Item</v-btn>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="warrantyInfo.terms" label="Terms & Conditions"
                                        variant="outlined" rows="4"></v-textarea>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- SEO Tab -->
                        <v-window-item value="seo">
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field v-model="form.meta_title" label="Meta Title" variant="outlined"
                                        hint="SEO title (50-60 characters recommended)"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-textarea v-model="form.meta_description" label="Meta Description"
                                        variant="outlined" rows="3"
                                        hint="SEO description (150-160 characters recommended)"></v-textarea>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field v-model="form.meta_keywords" label="Meta Keywords" variant="outlined"
                                        hint="Comma-separated keywords"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field v-model="form.og_image" label="Open Graph Image URL"
                                        variant="outlined" hint="Image for social media sharing"></v-text-field>
                                </v-col>
                            </v-row>
                        </v-window-item>

                        <!-- Settings Tab -->
                        <v-window-item value="settings">
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-switch v-model="form.published" label="Published" color="success"></v-switch>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-switch v-model="form.featured" label="Featured Product" color="amber"></v-switch>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="form.stock" label="Stock Quantity" variant="outlined"
                                        type="number"></v-text-field>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="form.order" label="Display Order" variant="outlined"
                                        type="number" hint="Lower numbers appear first"></v-text-field>
                                </v-col>
                            </v-row>
                        </v-window-item>
                    </v-window>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="closeDialog">Cancel</v-btn>
                    <v-btn color="primary" variant="flat" @click="saveProduct" :loading="saving">
                        {{ editingProduct ? 'Update' : 'Create' }} Product
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../mixins/adminPaginationMixin';

export default {
    mixins: [adminPaginationMixin],
    data() {
        return {
            products: [],
            showDialog: false,
            formTab: 'basic',
            editingProduct: null,
            saving: false,
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
                short_description: '',
                description: '',
                thumbnail: '',
                images: [],
                price: null,
                price_range: '',
                show_price: true,
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
            downloadTypes: ['PDF', 'ZIP', 'DOC', 'DOCX', 'XLS', 'XLSX', 'Other']
        };
    },
    async mounted() {
        await Promise.all([
            this.loadProducts(),
            this.loadCategories(),
            this.loadTags()
        ]);
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

                const response = await axios.get('/api/v1/products', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.products = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load products');
            } finally {
                this.loading = false;
            }
        },
        async loadCategories() {
            try {
                this.loadingCategories = true;
                // Try multiple possible endpoints
                let response = await axios.get('/api/v1/categories?type=product', {
                    headers: this.getAuthHeaders()
                }).catch(() => null);

                if (!response) {
                    // Try without type filter
                    response = await axios.get('/api/v1/categories', {
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
                let response = await axios.get('/api/v1/tags?type=product', {
                    headers: this.getAuthHeaders()
                }).catch(() => null);

                if (!response) {
                    // Try without type filter
                    response = await axios.get('/api/v1/tags', {
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
        openDialog(product = null) {
            this.editingProduct = product;
            if (product) {
                this.loadProductForEdit(product);
            } else {
                this.resetForm();
            }
            this.showDialog = true;
        },
        async loadProductForEdit(product) {
            try {
                const response = await axios.get(`/api/v1/products/${product.id}`, {
                    headers: this.getAuthHeaders()
                });
                const data = response.data;

                this.form = {
                    title: data.title || '',
                    slug: data.slug || '',
                    sku: data.sku || '',
                    short_description: data.short_description || '',
                    description: data.description || '',
                    thumbnail: data.thumbnail || '',
                    images: Array.isArray(data.images) ? data.images : [],
                    price: data.price || null,
                    price_range: data.price_range || '',
                    show_price: data.show_price !== false,
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

                // Parse specifications (excluding special fields)
                this.specificationsList = [];
                if (data.specifications && typeof data.specifications === 'object') {
                    Object.keys(data.specifications).forEach(key => {
                        // Skip special fields stored in specifications
                        if (!key.startsWith('_')) {
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
            } catch (error) {
                this.handleApiError(error, 'Failed to load product');
            }
        },
        resetForm() {
            this.form = {
                title: '',
                slug: '',
                sku: '',
                short_description: '',
                description: '',
                thumbnail: '',
                images: [],
                price: null,
                price_range: '',
                show_price: true,
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
            this.downloadsList = [];
            this.faqsList = [];
            this.warrantyInfo = {
                period: '2 Years',
                coverage: [],
                terms: ''
            };
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
        addImage() {
            this.form.images.push('');
        },
        removeImage(index) {
            this.form.images.splice(index, 1);
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
            this.downloadsList.push({ title: '', type: 'PDF', size: '', url: '' });
        },
        removeDownload(index) {
            this.downloadsList.splice(index, 1);
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

                // Build specifications object
                const specifications = {};
                this.specificationsList.forEach(spec => {
                    if (spec.key && spec.value) {
                        specifications[spec.key] = spec.value;
                    }
                });

                // Prepare form data
                const formData = {
                    ...this.form,
                    images: this.form.images.filter(img => img),
                    specifications: Object.keys(specifications).length > 0 ? specifications : null,
                    key_features: this.featuresList.filter(f => f),
                    downloads: this.downloadsList.filter(d => d.title && d.url),
                    faqs: this.faqsList.filter(f => f.question && f.answer),
                    warranty_info: this.warrantyInfo
                };

                // Handle tags - create if they don't exist
                if (this.form.tag_names && this.form.tag_names.length > 0) {
                    // For now, we'll send tag names and let backend handle creation
                    formData.tag_ids = [];
                    // Backend should handle tag creation by name
                }

                if (this.editingProduct) {
                    await axios.put(`/api/v1/products/${this.editingProduct.id}`, formData, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Product updated successfully');
                } else {
                    await axios.post('/api/v1/products', formData, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Product created successfully');
                }

                this.closeDialog();
                await this.loadProducts();
            } catch (error) {
                this.handleApiError(error, 'Failed to save product');
            } finally {
                this.saving = false;
            }
        },
        editProduct(product) {
            this.openDialog(product);
        },
        async deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                try {
                    await axios.delete(`/api/v1/products/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Product deleted successfully');
                    await this.loadProducts();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting product');
                }
            }
        },
        closeDialog() {
            this.showDialog = false;
            this.editingProduct = null;
            this.resetForm();
        },
        onPerPageChange() {
            this.resetPagination();
            this.loadProducts();
        },
        onSort(field) {
            this.handleSort(field);
            this.loadProducts();
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