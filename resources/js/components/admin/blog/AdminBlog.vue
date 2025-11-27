<template>
    <div>
        <div class="page-header">
            <h1 class="text-h4 page-title">Blog Management</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog()" class="add-button">Create
                Post</v-btn>
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
                            @update:model-value="loadPosts"></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field v-model="search" label="Search posts" prepend-inner-icon="mdi-magnify"
                            variant="outlined" density="compact" clearable
                            @update:model-value="loadPosts"></v-text-field>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Blog Posts Table -->
        <v-card>
            <v-card-title class="d-flex justify-space-between align-center">
                <span>Blog Posts</span>
                <span class="text-caption text-grey">
                    Total Records: <strong>{{ pagination.total || 0 }}</strong>
                    <span v-if="posts.length > 0">
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
                            <th class="sortable" @click="onSort('published_at')">
                                <div class="d-flex align-center">
                                    Published Date
                                    <v-icon :icon="getSortIcon('published_at')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Author</th>
                            <th class="sortable" @click="onSort('published')">
                                <div class="d-flex align-center">
                                    Status
                                    <v-icon :icon="getSortIcon('published')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th class="sortable" @click="onSort('views')">
                                <div class="d-flex align-center">
                                    Views
                                    <v-icon :icon="getSortIcon('views')" size="small" class="ml-1"></v-icon>
                                </div>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Skeleton Loaders: Show loading placeholders while fetching data -->
                        <tr v-if="loading" v-for="n in 5" :key="`skeleton-${n}`">
                            <td>
                                <!-- Skeleton for post title column with image placeholder -->
                                <div class="d-flex align-center">
                                    <!-- Avatar skeleton matching the 50x50px featured image size -->
                                    <v-skeleton-loader type="avatar" width="50" height="50"
                                        class="mr-3"></v-skeleton-loader>
                                    <div class="flex-grow-1">
                                        <!-- Title skeleton -->
                                        <v-skeleton-loader type="text" width="200" class="mb-1"></v-skeleton-loader>
                                        <!-- Slug skeleton -->
                                        <v-skeleton-loader type="text" width="150"></v-skeleton-loader>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="120"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="100"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="chip" width="80" height="24"></v-skeleton-loader>
                            </td>
                            <td>
                                <v-skeleton-loader type="text" width="60"></v-skeleton-loader>
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
                        <!-- Actual Post Data -->
                        <template v-else>
                            <tr v-for="post in posts" :key="post.id">
                                <td>
                                    <!-- Post Title with Featured Image -->
                                    <!-- Using v-avatar wrapper to ensure fixed 50x50px image size with proper aspect ratio handling -->
                                    <div class="d-flex align-center">
                                        <!-- Featured image avatar: Fixed size container ensures consistent image dimensions -->
                                        <v-avatar size="50" v-if="post.featured_image" class="mr-3">
                                            <v-img :src="post.featured_image" cover></v-img>
                                        </v-avatar>
                                        <!-- Fallback placeholder when no featured image is available -->
                                        <v-avatar size="50" v-else color="grey-lighten-2" class="mr-3">
                                            <v-icon icon="mdi-file-image"></v-icon>
                                        </v-avatar>
                                        <div>
                                            <div class="font-weight-medium">{{ post.title }}</div>
                                            <div class="text-caption text-grey">{{ post.slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="post.published_at">
                                        {{ formatDate(post.published_at) }}
                                    </div>
                                    <span v-else class="text-grey">Not published</span>
                                </td>
                                <td>
                                    <div v-if="post.author">
                                        {{ post.author.name || post.author.email }}
                                    </div>
                                    <span v-else class="text-grey">Unknown</span>
                                </td>
                                <td>
                                    <v-chip :color="post.published ? 'success' : 'error'" size="small">
                                        {{ post.published ? 'Published' : 'Draft' }}
                                    </v-chip>
                                </td>
                                <td>{{ post.views || 0 }}</td>
                                <td>
                                    <v-btn size="small" icon="mdi-eye" @click="viewPost(post)" variant="text"
                                        color="info" title="View Post"></v-btn>
                                    <v-btn size="small" icon="mdi-pencil" @click="editPost(post)" variant="text"
                                        title="Edit Post"></v-btn>
                                    <v-btn size="small" icon="mdi-delete" @click="deletePost(post.id)" variant="text"
                                        color="error" title="Delete Post"></v-btn>
                                </td>
                            </tr>
                            <!-- Empty state: Show message when no posts are found -->
                            <tr v-if="posts.length === 0">
                                <td colspan="6" class="text-center py-4">No blog posts found</td>
                            </tr>
                        </template>
                    </tbody>
                </v-table>

                <!-- Pagination and Records Info -->
                <!-- Display pagination controls and current page information -->
                <div
                    class="d-flex flex-column flex-md-row justify-space-between align-center align-md-start gap-3 mt-4">
                    <!-- Records count and current page information -->
                    <div class="text-caption text-grey">
                        <span v-if="posts.length > 0 && pagination.total > 0">
                            Showing <strong>{{ ((currentPage - 1) * perPage) + 1 }}</strong> to
                            <strong>{{ Math.min(currentPage * perPage, pagination.total) }}</strong> of
                            <strong>{{ pagination.total.toLocaleString() }}</strong> records
                            <span v-if="pagination.last_page > 1" class="ml-2">
                                (Page {{ currentPage }} of {{ pagination.last_page }})
                            </span>
                        </span>
                        <span v-else>
                            No records found
                        </span>
                    </div>
                    <!-- Pagination controls: Only show if there's more than one page -->
                    <div v-if="pagination.last_page > 1" class="d-flex align-center gap-2">
                        <v-pagination v-model="currentPage" :length="pagination.last_page" :total-visible="7"
                            density="comfortable" @update:model-value="loadPosts">
                        </v-pagination>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- Blog Post Form Dialog -->
        <BlogFormDialog v-model="showDialog" :editing-post="editingPost" @saved="handlePostSaved" />
    </div>
</template>

<script>
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import BlogFormDialog from './BlogFormDialog.vue';

/**
 * AdminBlog Component
 * Manages the blog posts listing, filtering, sorting, and CRUD operations
 * Uses adminPaginationMixin for pagination, sorting, and search functionality
 */
export default {
    components: {
        BlogFormDialog
    },
    mixins: [adminPaginationMixin],
    data() {
        return {
            // Array of blog post objects loaded from API
            posts: [],
            // Controls visibility of the blog post form dialog
            showDialog: false,
            // Currently editing post object, null when creating new post
            editingPost: null,
            // Current filter value for published status (null = all posts)
            publishedFilter: null,
            // Options for published status filter dropdown
            publishedOptions: [
                { title: 'Published', value: true },
                { title: 'Draft', value: false }
            ]
        };
    },
    /**
     * Lifecycle hook: Load posts when component is mounted
     */
    async mounted() {
        await this.loadPosts();
    },
    methods: {
        /**
         * Load blog posts from API with pagination, search, and filter parameters
         * Updates the posts array and pagination state
         */
        async loadPosts() {
            try {
                this.loading = true;
                const params = this.buildPaginationParams();

                // Add search parameter if search query exists
                if (this.search) {
                    params.search = this.search;
                }

                // Add published filter parameter if filter is selected
                if (this.publishedFilter !== null) {
                    params.published = this.publishedFilter;
                }

                const response = await this.$axios.get('/api/v1/blog-posts', {
                    params,
                    headers: this.getAuthHeaders()
                });

                this.posts = response.data.data || [];
                this.updatePagination(response.data);
            } catch (error) {
                this.handleApiError(error, 'Failed to load blog posts');
            } finally {
                this.loading = false;
            }
        },
        /**
         * Open the blog post form dialog
         * @param {Object|null} post - Post object to edit, or null to create new post
         */
        openDialog(post = null) {
            this.editingPost = post;
            this.showDialog = true;
        },
        /**
         * Open dialog in edit mode for the given post
         * @param {Object} post - Post object to edit
         */
        editPost(post) {
            this.openDialog(post);
        },
        /**
         * View post details (currently opens in edit mode)
         * TODO: Can add a view-only dialog later for read-only viewing
         * @param {Object} post - Post object to view
         */
        viewPost(post) {
            this.openDialog(post);
        },
        /**
         * Handle post saved event from BlogFormDialog
         * Reloads the posts list to reflect changes
         */
        handlePostSaved() {
            this.loadPosts();
        },
        /**
         * Delete a blog post by ID
         * Shows confirmation dialog before deletion
         * @param {Number} id - ID of the post to delete
         */
        async deletePost(id) {
            if (confirm('Are you sure you want to delete this blog post?')) {
                try {
                    await this.$axios.delete(`/api/v1/blog-posts/${id}`, {
                        headers: this.getAuthHeaders()
                    });
                    this.showSuccess('Blog post deleted successfully');
                    await this.loadPosts();
                } catch (error) {
                    this.handleApiError(error, 'Error deleting blog post');
                }
            }
        },
        /**
         * Handle items per page change
         * Resets pagination to first page and reloads posts
         */
        onPerPageChange() {
            this.resetPagination();
            this.loadPosts();
        },
        /**
         * Handle column sorting
         * @param {String} field - Field name to sort by
         */
        onSort(field) {
            this.handleSort(field);
            this.loadPosts();
        },
        /**
         * Format date to readable string format
         * @param {String|Date} date - Date to format
         * @returns {String} Formatted date string or empty string if date is invalid
         */
        formatDate(date) {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }
};
</script>

<style scoped>
/* Page header layout: Title and action button */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

/* Sortable table headers: Indicate interactive columns */
.sortable {
    cursor: pointer;
    user-select: none;
}

/* Hover effect for sortable headers */
.sortable:hover {
    background-color: rgba(0, 0, 0, 0.04);
}
</style>
