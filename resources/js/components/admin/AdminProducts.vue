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
                                <v-btn size="small" icon="mdi-eye" @click="viewProduct(product)" variant="text"
                                    color="info" class="mr-1"></v-btn>
                                <v-btn size="small" icon="mdi-pencil" @click="editProduct(product)" variant="text"
                                    color="primary" class="mr-1"></v-btn>
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

        <!-- Product Details Dialog -->
        <v-dialog v-model="showDetailsDialog" max-width="1400" scrollable>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-info text-white pa-4">
                    <div class="d-flex align-center">
                        <v-icon icon="mdi-eye" class="mr-3"></v-icon>
                        <span class="text-h5 font-weight-bold">Product Details</span>
                    </div>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDetailsDialog"
                        :disabled="loadingProductDetails"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0">
                    <!-- Loading State -->
                    <div v-if="loadingProductDetails" class="d-flex align-center justify-center pa-12">
                        <div class="text-center">
                            <v-progress-circular indeterminate color="info" size="64"></v-progress-circular>
                            <p class="text-body-1 text-medium-emphasis mt-4">Loading product details...</p>
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div v-else-if="productDetails">
                        <v-tabs v-model="detailsTab" color="info" bg-color="grey-lighten-4">
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

                        <v-window v-model="detailsTab" class="pa-6">
                            <!-- Basic Info Tab -->
                            <v-window-item value="basic">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Product Title</div>
                                        <div class="text-h6 font-weight-bold mb-4">{{ productDetails.title || '-' }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Slug</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.slug || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">SKU</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.sku || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Short Description</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.short_description || '-' }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Full Description</div>
                                        <div class="text-body-1" v-html="productDetails.description || '-'"></div>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- Media Tab -->
                            <v-window-item value="media">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Thumbnail</div>
                                        <div v-if="productDetails.thumbnail" class="mb-4">
                                            <v-img :src="productDetails.thumbnail" max-height="300" contain
                                                class="rounded elevation-2"></v-img>
                                            <div class="text-caption text-medium-emphasis mt-2">{{
                                                productDetails.thumbnail }}</div>
                                        </div>
                                        <div v-else class="text-body-1 text-medium-emphasis">No thumbnail</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Product Images (Gallery)
                                        </div>
                                        <div v-if="productDetails.images && productDetails.images.length > 0"
                                            class="d-flex flex-wrap gap-3">
                                            <v-card v-for="(img, index) in productDetails.images" :key="index"
                                                class="elevation-2" style="max-width: 200px;">
                                                <v-img :src="img" height="150" cover></v-img>
                                                <v-card-text class="pa-2">
                                                    <div class="text-caption text-truncate">{{ img }}</div>
                                                </v-card-text>
                                            </v-card>
                                        </div>
                                        <div v-else class="text-body-1 text-medium-emphasis">No images</div>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- Pricing Tab -->
                            <v-window-item value="pricing">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Price</div>
                                        <div class="text-h6 font-weight-bold mb-4">
                                            {{ productDetails.price ? '$' + productDetails.price : '-' }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Price Range</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.price_range || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Show Price on Website
                                        </div>
                                        <v-chip :color="productDetails.show_price ? 'success' : 'error'" size="small">
                                            {{ productDetails.show_price ? 'Yes' : 'No' }}
                                        </v-chip>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- Categories & Tags Tab -->
                            <v-window-item value="categories">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Categories</div>
                                        <div v-if="productDetails.categories && productDetails.categories.length > 0"
                                            class="d-flex flex-wrap gap-2 mb-4">
                                            <v-chip v-for="cat in productDetails.categories" :key="cat.id"
                                                color="primary" variant="tonal">
                                                {{ cat.name }}
                                            </v-chip>
                                        </div>
                                        <div v-else class="text-body-1 text-medium-emphasis mb-4">No categories</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Tags</div>
                                        <div v-if="productDetails.tags && productDetails.tags.length > 0"
                                            class="d-flex flex-wrap gap-2">
                                            <v-chip v-for="tag in productDetails.tags" :key="tag.id" color="info"
                                                variant="outlined">
                                                {{ tag.name }}
                                            </v-chip>
                                        </div>
                                        <div v-else class="text-body-1 text-medium-emphasis">No tags</div>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- Specifications Tab -->
                            <v-window-item value="specs">
                                <div v-if="detailsSpecificationsList.length > 0">
                                    <v-table>
                                        <thead>
                                            <tr>
                                                <th>Specification</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(spec, index) in detailsSpecificationsList" :key="index">
                                                <td class="font-weight-medium">{{ spec.key }}</td>
                                                <td>{{ spec.value }}</td>
                                            </tr>
                                        </tbody>
                                    </v-table>
                                </div>
                                <div v-else class="text-center py-8">
                                    <v-icon icon="mdi-cog" size="48" color="grey-lighten-1" class="mb-4"></v-icon>
                                    <p class="text-body-1 text-medium-emphasis">No specifications available</p>
                                </div>
                            </v-window-item>

                            <!-- Features Tab -->
                            <v-window-item value="features">
                                <div v-if="detailsFeaturesList.length > 0">
                                    <v-list>
                                        <v-list-item v-for="(feature, index) in detailsFeaturesList" :key="index"
                                            prepend-icon="mdi-check-circle" color="success">
                                            <v-list-item-title>{{ feature }}</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </div>
                                <div v-else class="text-center py-8">
                                    <v-icon icon="mdi-star-outline" size="48" color="grey-lighten-1"
                                        class="mb-4"></v-icon>
                                    <p class="text-body-1 text-medium-emphasis">No features available</p>
                                </div>
                            </v-window-item>

                            <!-- Downloads Tab -->
                            <v-window-item value="downloads">
                                <div v-if="detailsDownloadsList.length > 0">
                                    <v-list>
                                        <v-list-item v-for="(download, index) in detailsDownloadsList" :key="index"
                                            :title="download.title"
                                            :subtitle="`${download.type} - ${download.size || 'N/A'}`"
                                            prepend-icon="mdi-download" append-icon="mdi-open-in-new">
                                            <template v-slot:append>
                                                <v-btn icon="mdi-open-in-new" variant="text" size="small"
                                                    @click="window.open(download.url, '_blank')"></v-btn>
                                            </template>
                                        </v-list-item>
                                    </v-list>
                                </div>
                                <div v-else class="text-center py-8">
                                    <v-icon icon="mdi-download-off" size="48" color="grey-lighten-1"
                                        class="mb-4"></v-icon>
                                    <p class="text-body-1 text-medium-emphasis">No downloads available</p>
                                </div>
                            </v-window-item>

                            <!-- FAQs Tab -->
                            <v-window-item value="faq">
                                <div v-if="detailsFaqsList.length > 0">
                                    <v-expansion-panels>
                                        <v-expansion-panel v-for="(faq, index) in detailsFaqsList" :key="index">
                                            <v-expansion-panel-title>
                                                <div class="font-weight-medium">{{ faq.question }}</div>
                                            </v-expansion-panel-title>
                                            <v-expansion-panel-text>
                                                <div class="text-body-1">{{ faq.answer }}</div>
                                            </v-expansion-panel-text>
                                        </v-expansion-panel>
                                    </v-expansion-panels>
                                </div>
                                <div v-else class="text-center py-8">
                                    <v-icon icon="mdi-help-circle-outline" size="48" color="grey-lighten-1"
                                        class="mb-4"></v-icon>
                                    <p class="text-body-1 text-medium-emphasis">No FAQs available</p>
                                </div>
                            </v-window-item>

                            <!-- Warranty & Service Tab -->
                            <v-window-item value="warranty">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Warranty Period</div>
                                        <div class="text-h6 font-weight-bold mb-4">
                                            {{ detailsWarrantyInfo.period || '-' }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">What's Covered</div>
                                        <div
                                            v-if="detailsWarrantyInfo.coverage && detailsWarrantyInfo.coverage.length > 0">
                                            <v-list>
                                                <v-list-item v-for="(item, index) in detailsWarrantyInfo.coverage"
                                                    :key="index" prepend-icon="mdi-check" color="success">
                                                    <v-list-item-title>{{ item }}</v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </div>
                                        <div v-else class="text-body-1 text-medium-emphasis mb-4">No coverage items
                                        </div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Terms & Conditions</div>
                                        <div class="text-body-1">{{ detailsWarrantyInfo.terms || '-' }}</div>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- SEO Tab -->
                            <v-window-item value="seo">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Title</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.meta_title || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Description</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.meta_description || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Meta Keywords</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.meta_keywords || '-' }}</div>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Open Graph Image</div>
                                        <div v-if="productDetails.og_image" class="mb-2">
                                            <v-img :src="productDetails.og_image" max-height="200" contain
                                                class="rounded"></v-img>
                                        </div>
                                        <div class="text-body-1">{{ productDetails.og_image || '-' }}</div>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- Settings Tab -->
                            <v-window-item value="settings">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Published Status</div>
                                        <v-chip :color="productDetails.published ? 'success' : 'error'" size="default"
                                            class="mb-4">
                                            {{ productDetails.published ? 'Published' : 'Draft' }}
                                        </v-chip>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Featured Product</div>
                                        <v-chip :color="productDetails.featured ? 'amber' : 'grey'" size="default"
                                            class="mb-4">
                                            {{ productDetails.featured ? 'Featured' : 'Not Featured' }}
                                        </v-chip>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Stock Quantity</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.stock !== null ?
                                            productDetails.stock : '-' }}</div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Display Order</div>
                                        <div class="text-body-1 mb-4">{{ productDetails.order || 0 }}</div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Created At</div>
                                        <div class="text-body-1 mb-4">
                                            {{ productDetails.created_at ? new
                                                Date(productDetails.created_at).toLocaleString() : '-' }}
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <div class="text-subtitle-1 text-medium-emphasis mb-2">Updated At</div>
                                        <div class="text-body-1 mb-4">
                                            {{ productDetails.updated_at ? new
                                                Date(productDetails.updated_at).toLocaleString() : '-' }}
                                        </div>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                        </v-window>
                    </div>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4" v-if="!loadingProductDetails && productDetails">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="closeDetailsDialog">Close</v-btn>
                    <v-btn color="primary" variant="flat" @click="editProductFromDetails">
                        <v-icon icon="mdi-pencil" class="mr-2"></v-icon>
                        Edit Product
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Product Form Dialog -->
        <v-dialog v-model="showDialog" max-width="1400" scrollable persistent>
            <v-card>
                <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                    <span class="text-h5 font-weight-bold">
                        {{ editingProduct ? 'Edit Product' : 'Create New Product' }}
                    </span>
                    <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                        :disabled="loadingProduct"></v-btn>
                </v-card-title>

                <v-card-text class="pa-0">
                    <!-- Loading State -->
                    <div v-if="loadingProduct" class="d-flex align-center justify-center pa-12">
                        <div class="text-center">
                            <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                            <p class="text-body-1 text-medium-emphasis mt-4">Loading product data...</p>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div v-else>
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
                                            hint="URL-friendly version of title"
                                            :rules="[v => !!v || 'Slug is required']">
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
                                            variant="outlined" rows="2"
                                            hint="Brief description for listings"></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.description" label="Full Description"
                                            variant="outlined" rows="6"
                                            hint="Detailed product description (supports HTML)"></v-textarea>
                                    </v-col>
                                </v-row>
                            </v-window-item>

                            <!-- Media Tab -->
                            <v-window-item value="media">
                                <v-row>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 font-weight-bold mb-2">Thumbnail Image</div>
                                        <v-file-input v-model="thumbnailFile" label="Select Thumbnail Image"
                                            accept="image/*" prepend-icon="mdi-camera" variant="outlined"
                                            @update:model-value="previewThumbnail" clearable show-size
                                            hint="Select an image file (will upload on save)"></v-file-input>

                                        <!-- Preview for selected file -->
                                        <div v-if="thumbnailFile && thumbnailPreview" class="mt-3 mb-3">
                                            <v-card class="elevation-2" style="max-width: 300px;">
                                                <v-img :src="thumbnailPreview" max-height="200" contain
                                                    class="rounded"></v-img>
                                                <v-card-text class="pa-2">
                                                    <div class="text-caption text-medium-emphasis">
                                                        {{ thumbnailFile.name || 'Selected file' }}
                                                        <span v-if="thumbnailFile.size">
                                                            ({{ formatFileSize(thumbnailFile.size) }})
                                                        </span>
                                                    </div>
                                                </v-card-text>
                                            </v-card>
                                        </div>

                                        <!-- Preview for existing/uploaded thumbnail -->
                                        <div v-else-if="form.thumbnail" class="mt-2 mb-3">
                                            <v-card class="elevation-2" style="max-width: 300px;">
                                                <v-img :src="form.thumbnail" max-height="200" contain
                                                    class="rounded"></v-img>
                                                <v-card-text class="pa-2">
                                                    <div class="text-caption text-medium-emphasis">Current thumbnail
                                                    </div>
                                                </v-card-text>
                                            </v-card>
                                        </div>

                                        <v-text-field v-model="form.thumbnail" label="Or Enter Image URL"
                                            variant="outlined" prepend-inner-icon="mdi-link"
                                            hint="Alternative: Paste image URL" class="mt-2"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <div class="text-subtitle-1 font-weight-bold mb-2">Product Images (Gallery)
                                        </div>
                                        <v-file-input v-model="galleryFiles" label="Select Gallery Images"
                                            accept="image/*" prepend-icon="mdi-image-multiple" variant="outlined"
                                            multiple @update:model-value="previewGalleryImages" clearable show-size
                                            hint="Select multiple image files (will upload on save)"></v-file-input>

                                        <!-- Preview for selected files -->
                                        <div v-if="galleryPreviews.length > 0" class="mt-3 mb-4">
                                            <div class="text-subtitle-2 text-medium-emphasis mb-2">Selected Images ({{
                                                galleryPreviews.length }})</div>
                                            <div class="d-flex flex-wrap gap-3">
                                                <v-card v-for="(preview, index) in galleryPreviews" :key="index"
                                                    class="elevation-2" style="max-width: 200px; position: relative;">
                                                    <v-img :src="preview.url" height="150" cover></v-img>
                                                    <v-card-text class="pa-2">
                                                        <div class="text-caption text-truncate">{{ preview.name }}</div>
                                                        <div class="text-caption text-medium-emphasis"
                                                            v-if="preview.size">
                                                            {{ formatFileSize(preview.size) }}
                                                        </div>
                                                    </v-card-text>
                                                    <v-btn icon="mdi-close" size="x-small" color="error" variant="flat"
                                                        class="position-absolute" style="top: 4px; right: 4px;"
                                                        @click="removeGalleryPreview(index)"></v-btn>
                                                </v-card>
                                            </div>
                                        </div>

                                        <v-divider class="my-4"></v-divider>
                                        <div class="text-subtitle-2 text-medium-emphasis mb-2">Or Add Image URLs</div>
                                        <div v-for="(img, index) in form.images" :key="index" class="mb-3">
                                            <v-text-field v-model="form.images[index]" :label="`Image ${index + 1} URL`"
                                                variant="outlined" prepend-inner-icon="mdi-link">
                                                <template v-slot:append>
                                                    <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                        @click="removeImage(index)"></v-btn>
                                                </template>
                                            </v-text-field>
                                            <div v-if="form.images[index]" class="mt-2">
                                                <v-img :src="form.images[index]" max-height="150" contain
                                                    class="rounded elevation-1"></v-img>
                                            </div>
                                        </div>
                                        <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                            @click="addImage">Add
                                            Image URL</v-btn>
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
                                        <v-select v-model="form.category_ids" :items="availableCategories"
                                            item-title="name" item-value="id" label="Select Categories"
                                            variant="outlined" multiple chips :loading="loadingCategories"></v-select>
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
                                            <v-text-field v-model="spec.key" label="Specification Name"
                                                variant="outlined"
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
                                <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                    @click="addFeature">Add
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
                                                placeholder="e.g., 1.2 MB" :disabled="!!download.file"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="2">
                                            <v-btn icon="mdi-delete" color="error" variant="text"
                                                @click="removeDownload(index)"></v-btn>
                                        </v-col>
                                        <v-col cols="12">
                                            <div class="text-subtitle-2 font-weight-bold mb-2">Upload File</div>
                                            <v-file-input v-model="download.file"
                                                :label="download.file ? 'File Selected' : 'Select File to Upload'"
                                                accept=".pdf,.zip,.doc,.docx,.xls,.xlsx" prepend-icon="mdi-file-upload"
                                                variant="outlined" show-size clearable
                                                @update:model-value="handleDownloadFileChange(index)"
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
                                                                {{ formatFileSize(download.file.size) }}
                                                                <span v-if="download.uploading" class="ml-2">
                                                                    <v-progress-circular indeterminate size="16"
                                                                        color="primary"></v-progress-circular>
                                                                    Uploading...
                                                                </span>
                                                                <span v-else-if="download.uploaded"
                                                                    class="ml-2 text-success">
                                                                    <v-icon icon="mdi-check-circle"
                                                                        size="small"></v-icon>
                                                                    Uploaded
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <v-btn icon="mdi-close" size="small" variant="text"
                                                            color="error" @click="clearDownloadFile(index)"></v-btn>
                                                    </div>
                                                </v-card>
                                            </div>

                                            <v-divider class="my-3"></v-divider>
                                            <div class="text-subtitle-2 text-medium-emphasis mb-2">Or Enter File URL
                                            </div>
                                            <v-text-field v-model="download.url" label="File URL" variant="outlined"
                                                placeholder="https://..." :disabled="!!download.file"></v-text-field>
                                        </v-col>
                                    </v-row>
                                </div>
                                <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                    @click="addDownload">Add
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
                                        <v-text-field v-model="form.meta_keywords" label="Meta Keywords"
                                            variant="outlined" hint="Comma-separated keywords"></v-text-field>
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
                                        <v-switch v-model="form.featured" label="Featured Product"
                                            color="amber"></v-switch>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model.number="form.stock" label="Stock Quantity"
                                            variant="outlined" type="number"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model.number="form.order" label="Display Order"
                                            variant="outlined" type="number"
                                            hint="Lower numbers appear first"></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                        </v-window>
                    </div>
                </v-card-text>

                <v-card-actions class="pa-4 bg-grey-lighten-4" v-if="!loadingProduct">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="closeDialog" :disabled="saving">Cancel</v-btn>
                    <v-btn color="primary" variant="flat" @click="saveProduct" :loading="saving"
                        :disabled="loadingProduct">
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
            galleryPreviews: []
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
                // Use product.id for the API call
                const response = await axios.get(`/api/v1/products/${product.id}`, {
                    headers: this.getAuthHeaders()
                });

                // Handle different response structures
                const data = response.data?.data || response.data;

                if (!data) {
                    throw new Error('No product data received from server');
                }

                console.log('Loaded product data:', data);

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

                // Reset form tab to basic after loading
                this.formTab = 'basic';
            } catch (error) {
                console.error('Error loading product:', error);
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to load product data';
                this.showError(errorMessage);
                // Close dialog on error
                this.closeDialog();
                throw error;
            } finally {
                this.loadingProduct = false;
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
            this.galleryFiles = [];
            this.galleryPreviews = [];
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
        previewThumbnail(file) {
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

            // Create preview URL
            const reader = new FileReader();
            reader.onload = (e) => {
                this.thumbnailPreview = e.target.result;
            };
            reader.readAsDataURL(fileToPreview);
        },
        previewGalleryImages(files) {
            if (!files) {
                this.galleryPreviews = [];
                return;
            }

            // Handle both single file and array
            const filesToPreview = Array.isArray(files) ? files : [files];
            if (filesToPreview.length === 0) {
                this.galleryPreviews = [];
                return;
            }

            // Create previews for all selected files
            this.galleryPreviews = [];
            filesToPreview.forEach((file, index) => {
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.galleryPreviews.push({
                            url: e.target.result,
                            name: file.name,
                            size: file.size,
                            file: file
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        },
        removeGalleryPreview(index) {
            this.galleryPreviews.splice(index, 1);
            // Also remove from galleryFiles array
            if (Array.isArray(this.galleryFiles)) {
                this.galleryFiles.splice(index, 1);
            } else {
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

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    return response.data.url;
                } else {
                    throw new Error(response.data.message || 'Failed to upload thumbnail');
                }
            } catch (error) {
                console.error('Error uploading thumbnail:', error);
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to upload thumbnail';
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

                const response = await axios.post('/api/v1/upload/images', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success && response.data.images && response.data.images.length > 0) {
                    uploadedUrls.push(...response.data.images.map(img => img.url));
                } else {
                    throw new Error(response.data.message || 'Failed to upload images');
                }

                return uploadedUrls;
            } catch (error) {
                console.error('Error uploading gallery images:', error);
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to upload images';
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

            download.uploading = true;
            try {
                const formData = new FormData();
                formData.append('file', download.file);
                formData.append('folder', 'downloads');
                // Add product title as prefix if available
                if (this.form.title) {
                    formData.append('prefix', this.form.title);
                }

                const response = await axios.post('/api/v1/upload/file', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    download.url = response.data.url;
                    download.size = this.formatFileSize(response.data.size);
                    download.uploaded = true;
                    return response.data.url;
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
                let thumbnailUrl = this.form.thumbnail;
                if (this.thumbnailFile) {
                    try {
                        thumbnailUrl = await this.uploadThumbnailFile();
                        if (thumbnailUrl) {
                            this.form.thumbnail = thumbnailUrl;
                        }
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload thumbnail');
                        return;
                    }
                }

                // Upload gallery images if new files are selected
                let galleryUrls = [...this.form.images.filter(img => img)];
                if (this.galleryPreviews && this.galleryPreviews.length > 0) {
                    try {
                        const uploadedUrls = await this.uploadGalleryFiles();
                        galleryUrls = [...galleryUrls, ...uploadedUrls];
                    } catch (error) {
                        this.showError(error.message || 'Failed to upload gallery images');
                        return;
                    }
                }

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
                const specifications = {};
                this.specificationsList.forEach(spec => {
                    if (spec.key && spec.value) {
                        specifications[spec.key] = spec.value;
                    }
                });

                // Prepare form data - ensure all fields are included
                const formData = {
                    title: this.form.title,
                    slug: this.form.slug,
                    sku: this.form.sku || null,
                    short_description: this.form.short_description || null,
                    description: this.form.description || null,
                    thumbnail: thumbnailUrl || null,
                    images: galleryUrls,
                    price: this.form.price ? parseFloat(this.form.price) : null,
                    price_range: this.form.price_range || null,
                    show_price: this.form.show_price !== false,
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
                    og_image: this.form.og_image || null,
                    published: this.form.published || false,
                    featured: this.form.featured || false,
                    stock: this.form.stock ? parseInt(this.form.stock) : null,
                    order: this.form.order ? parseInt(this.form.order) : 0,
                    category_ids: this.form.category_ids || [],
                    tag_names: this.form.tag_names || []
                };

                if (this.editingProduct) {
                    const response = await axios.put(`/api/v1/products/${this.editingProduct.id}`, formData, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Product updated successfully');
                } else {
                    const response = await axios.post('/api/v1/products', formData, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Product created successfully');
                }

                // Clear file inputs and previews
                this.thumbnailFile = null;
                this.thumbnailPreview = null;
                this.galleryFiles = [];
                this.galleryPreviews = [];

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
                const response = await axios.get(`/api/v1/products/${product.id}`, {
                    headers: this.getAuthHeaders()
                });

                const data = response.data?.data || response.data;

                if (!data) {
                    throw new Error('No product data received');
                }

                this.productDetails = data;

                // Parse specifications
                this.detailsSpecificationsList = [];
                if (data.specifications && typeof data.specifications === 'object') {
                    Object.keys(data.specifications).forEach(key => {
                        if (!key.startsWith('_')) {
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
            } catch (error) {
                console.error('Error loading product details:', error);
                const errorMessage = error.response?.data?.message ||
                    error.response?.data?.error ||
                    'Failed to load product details';
                this.showError(errorMessage);
                this.closeDetailsDialog();
            } finally {
                this.loadingProductDetails = false;
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
                        this.form = {
                            title: productData.title || '',
                            slug: productData.slug || '',
                            sku: productData.sku || '',
                            short_description: productData.short_description || '',
                            description: productData.description || '',
                            thumbnail: productData.thumbnail || '',
                            images: Array.isArray(productData.images) ? [...productData.images] : [],
                            price: productData.price || null,
                            price_range: productData.price_range || '',
                            show_price: productData.show_price !== false,
                            category_ids: (productData.categories || []).map(c => c.id),
                            tag_names: (productData.tags || []).map(t => t.name),
                            published: productData.published || false,
                            featured: productData.featured || false,
                            stock: productData.stock || null,
                            order: productData.order || 0,
                            meta_title: productData.meta_title || '',
                            meta_description: productData.meta_description || '',
                            meta_keywords: productData.meta_keywords || '',
                            og_image: productData.og_image || ''
                        };

                        // Parse specifications
                        this.specificationsList = [];
                        if (productData.specifications && typeof productData.specifications === 'object') {
                            Object.keys(productData.specifications).forEach(key => {
                                if (!key.startsWith('_')) {
                                    this.specificationsList.push({
                                        key: key,
                                        value: productData.specifications[key]
                                    });
                                }
                            });
                        }

                        // Parse features
                        if (productData.key_features && Array.isArray(productData.key_features)) {
                            this.featuresList = [...productData.key_features];
                        } else if (productData.specifications?._key_features && Array.isArray(productData.specifications._key_features)) {
                            this.featuresList = [...productData.specifications._key_features];
                        } else {
                            this.featuresList = [];
                        }

                        // Parse downloads
                        this.downloadsList = Array.isArray(productData.downloads) ? [...productData.downloads] : [];

                        // Parse FAQs
                        if (productData.faqs && Array.isArray(productData.faqs)) {
                            this.faqsList = [...productData.faqs];
                        } else if (productData.specifications?._faqs && Array.isArray(productData.specifications._faqs)) {
                            this.faqsList = [...productData.specifications._faqs];
                        } else {
                            this.faqsList = [];
                        }

                        // Parse warranty info
                        if (productData.warranty_info && typeof productData.warranty_info === 'object') {
                            this.warrantyInfo = {
                                period: productData.warranty_info.period || '2 Years',
                                coverage: Array.isArray(productData.warranty_info.coverage) ? [...productData.warranty_info.coverage] : [],
                                terms: productData.warranty_info.terms || ''
                            };
                        } else if (productData.specifications?._warranty_info && typeof productData.specifications._warranty_info === 'object') {
                            this.warrantyInfo = {
                                period: productData.specifications._warranty_info.period || '2 Years',
                                coverage: Array.isArray(productData.specifications._warranty_info.coverage) ? [...productData.specifications._warranty_info.coverage] : [],
                                terms: productData.specifications._warranty_info.terms || ''
                            };
                        } else {
                            this.warrantyInfo = {
                                period: '2 Years',
                                coverage: [],
                                terms: ''
                            };
                        }

                        this.formTab = 'basic';
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
            if (this.loadingProduct || this.saving) {
                return; // Prevent closing while loading or saving
            }
            this.showDialog = false;
            this.editingProduct = null;
            this.loadingProduct = false;
            // Clear file inputs and previews
            this.thumbnailFile = null;
            this.thumbnailPreview = null;
            this.galleryFiles = [];
            this.galleryPreviews = [];
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