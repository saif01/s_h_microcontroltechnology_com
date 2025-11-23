<template>
    <div>
        <div class="d-flex justify-space-between align-center mb-6">
            <h1 class="text-h4">Products Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="showDialog = true">Create Product</v-btn>
        </div>

        <!-- Search and Filter -->
        <v-card class="mb-4">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search products" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadProducts"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="publishedFilter" :items="publishedOptions" label="Filter by Status"
                            prepend-inner-icon="mdi-filter" variant="outlined" density="compact" clearable
                            @update:model-value="loadProducts"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-select v-model="perPage" :items="perPageOptions" label="Items per page"
                            prepend-inner-icon="mdi-format-list-numbered" variant="outlined" density="compact"
                            @update:model-value="onPerPageChange"></v-select>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Products Table -->
        <v-card>
            <v-card-title>Products</v-card-title>
            <v-card-text>
                <v-table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products" :key="product.id">
                            <td>{{ product.title }}</td>
                            <td>
                                <v-chip size="small" variant="outlined">{{ product.sku || '-' }}</v-chip>
                            </td>
                            <td>{{ product.price ? '$' + product.price : '-' }}</td>
                            <td>
                                <v-chip :color="product.published ? 'success' : 'error'" size="small">
                                    {{ product.published ? 'Published' : 'Draft' }}
                                </v-chip>
                            </td>
                            <td>
                                <v-btn size="small" icon="mdi-pencil" @click="editProduct(product)"
                                    variant="text"></v-btn>
                                <v-btn size="small" icon="mdi-delete" @click="deleteProduct(product.id)" variant="text"
                                    color="error"></v-btn>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="5" class="text-center py-4">No products found</td>
                        </tr>
                    </tbody>
                </v-table>

                <!-- Pagination -->
                <v-pagination v-if="pagination.last_page > 1" v-model="currentPage" :length="pagination.last_page"
                    @update:model-value="loadProducts" class="mt-4"></v-pagination>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            products: [],
            showDialog: false,
            search: '',
            publishedFilter: null,
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ],
            currentPage: 1,
            perPage: 10,
            perPageOptions: [10, 25, 50, 100, 500],
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: 0
            }
        };
    },
    async mounted() {
        await this.loadProducts();
    },
    methods: {
        async loadProducts() {
            try {
                const token = localStorage.getItem('admin_token');
                const params = {
                    page: this.currentPage,
                    per_page: this.perPage
                };

                if (this.search) {
                    params.search = this.search;
                }

                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                const response = await axios.get('/api/v1/products', {
                    params,
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.products = response.data.data || [];
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    per_page: response.data.per_page,
                    total: response.data.total
                };
            } catch (error) {
                console.error('Error loading products:', error);
                this.showError('Failed to load products');
            }
        },
        editProduct(product) {
            // TODO: Implement edit functionality
            alert('Edit functionality coming soon');
        },
        async deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                try {
                    const token = localStorage.getItem('admin_token');
                    await axios.delete(`/api/v1/products/${id}`, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    this.showSuccess('Product deleted successfully');
                    await this.loadProducts();
                } catch (error) {
                    this.showError('Error deleting product');
                }
            }
        },
        showSuccess(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: message
                });
            } else {
                alert(message);
            }
        },
        showError(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'error',
                    title: message
                });
            } else {
                alert(message);
            }
        },
        onPerPageChange() {
            this.currentPage = 1;
            this.loadProducts();
        }
    }
};
</script>
