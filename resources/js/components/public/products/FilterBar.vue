<template>
    <div class="filter-section mb-4 mb-md-5">
        <v-card class="filter-card rounded-lg elevation-2" :class="{ 'sticky-filter': isSticky }">
            <!-- Header Section -->
            <div class="filter-header d-flex align-center justify-space-between pa-3 pa-md-3 pb-2">
                <div class="d-flex align-center gap-1">
                    <v-icon icon="mdi-filter-variant" color="primary" size="18" class="filter-icon d-none d-sm-flex" />
                    <h2 class="text-body-2 text-sm-subtitle-2 font-weight-bold text-grey-darken-3 mb-0">
                        <span class="d-none d-sm-inline">Filter & Search</span>
                        <span class="d-sm-none">Filters</span>
                    </h2>
                    <v-chip v-if="hasActiveFilters" size="x-small" color="primary" variant="flat" class="ml-1">
                        {{ activeFiltersCount }}
                    </v-chip>
                </div>
                <v-btn v-if="hasActiveFilters" variant="text" color="grey-darken-1" size="x-small"
                    prepend-icon="mdi-close-circle" class="text-capitalize d-none d-sm-flex"
                    @click="$emit('clear-filters')">
                    Clear All
                </v-btn>
                <v-btn v-if="hasActiveFilters" icon="mdi-close-circle" variant="text" color="grey-darken-1"
                    size="x-small" class="d-sm-none" @click="$emit('clear-filters')" />
            </div>

            <!-- Category Filter Section -->
            <div class="category-section pa-3 pa-md-3 pt-0 pb-2">
                <div
                    class="text-caption font-weight-bold text-grey-darken-1 mb-1 mb-md-2 text-uppercase tracking-wide d-none d-md-block">
                    Categories
                </div>
                <div class="category-filter-wrapper">
                    <div class="category-filter d-flex align-center overflow-x-auto w-100 pb-1 gap-1">
                        <v-btn v-for="category in categories" :key="category.id" variant="flat"
                            :color="activeCategory === category.id ? 'primary' : 'grey-lighten-3'"
                            class="category-btn font-weight-bold text-capitalize rounded-lg flex-shrink-0" :class="{
                                'category-btn-active': activeCategory === category.id,
                                'category-btn-inactive': activeCategory !== category.id
                            }" size="x-small" density="compact" @click="$emit('update:activeCategory', category.id)">
                            <v-icon v-if="category.icon" :icon="category.icon" size="x-small" class="mr-1" />
                            <span class="category-text">{{ category.name }}</span>
                        </v-btn>
                    </div>
                </div>
            </div>

            <!-- Search & Sort Section -->
            <div class="search-sort-section pa-3 pa-md-3 pt-0">
                <div class="search-sort-row d-flex align-center gap-1">
                    <!-- Search Field -->
                    <div class="search-wrapper flex-grow-1">
                        <v-text-field :model-value="searchQuery" density="compact" variant="outlined"
                            placeholder="Search products..." prepend-inner-icon="mdi-magnify" hide-details="auto"
                            bg-color="white" class="search-field rounded-lg" clearable
                            @update:model-value="handleSearchInput" @click:clear="$emit('update:searchQuery', '')">
                            <template v-if="searchQuery" #append-inner>
                                <v-chip size="x-small" color="primary" variant="flat" class="results-chip mr-1">
                                    {{ resultsCount }}
                                </v-chip>
                            </template>
                        </v-text-field>
                    </div>

                    <!-- Sort & Actions -->
                    <div class="sort-actions-wrapper d-flex align-center gap-1 flex-shrink-0">
                        <!-- Sort Menu -->
                        <v-menu location="bottom end" :close-on-content-click="true" class="sort-menu-mobile">
                            <template #activator="{ props: menuProps }">
                                <v-btn v-bind="menuProps" variant="outlined" color="grey-darken-2"
                                    class="sort-btn bg-white text-grey-darken-2 font-weight-medium"
                                    append-icon="mdi-chevron-down" size="x-small" density="compact">
                                    <v-icon icon="mdi-sort" size="x-small" class="mr-1 d-none d-sm-inline" />
                                    <span class="d-none d-sm-inline">{{ sortByLabel }}</span>
                                    <span class="d-sm-none">Sort</span>
                                </v-btn>
                            </template>
                            <v-list class="sort-menu-list" density="compact">
                                <v-list-item v-for="item in sortOptions" :key="item.value"
                                    :class="{ 'sort-option-active': sortBy === item.value }" class="sort-option"
                                    @click="$emit('update:sortBy', item.value)">
                                    <template #prepend>
                                        <v-icon v-if="sortBy === item.value" icon="mdi-check" color="primary"
                                            size="small" class="mr-2" />
                                        <div v-else class="mr-2" style="width: 20px;" />
                                    </template>
                                    <v-list-item-title class="font-weight-medium text-body-2">
                                        {{ item.label }}
                                    </v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>

                        <!-- Comparison Button -->
                        <v-btn v-if="comparisonCount > 0" color="primary" variant="flat"
                            prepend-icon="mdi-scale-balance" class="comparison-btn text-white font-weight-bold"
                            size="x-small" density="compact" @click="$emit('open-comparison')">
                            <span class="d-none d-sm-inline mr-1">Compare</span>
                            <v-chip size="x-small" color="white" variant="flat" class="comparison-badge ml-1">
                                {{ comparisonCount }}
                            </v-chip>
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-card>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { debounce } from '../../../utils/debounce';

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    activeCategory: {
        type: [String, Number],
        default: 'all'
    },
    searchQuery: {
        type: String,
        default: ''
    },
    sortBy: {
        type: String,
        default: 'newest'
    },
    sortOptions: {
        type: Array,
        default: () => []
    },
    sortByLabel: {
        type: String,
        default: 'Newest First'
    },
    hasActiveFilters: {
        type: Boolean,
        default: false
    },
    activeFiltersCount: {
        type: Number,
        default: 0
    },
    comparisonCount: {
        type: Number,
        default: 0
    },
    resultsCount: {
        type: Number,
        default: 0
    }
});

const emit = defineEmits([
    'update:activeCategory',
    'update:searchQuery',
    'update:sortBy',
    'clear-filters',
    'open-comparison'
]);

// Sticky filter on scroll
const isSticky = ref(false);

const handleScroll = () => {
    isSticky.value = window.scrollY > 200;
};

// Debounced search input handler
const handleSearchInput = debounce((value) => {
    emit('update:searchQuery', value || '');
}, 300);

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.filter-section {
    position: relative;
    margin-bottom: 1rem;
}

.filter-card {
    background: linear-gradient(to bottom, #ffffff 0%, #fafbfc 100%) !important;
    backdrop-filter: blur(12px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.08), 0 1px 2px -1px rgba(0, 0, 0, 0.04) !important;
}

.filter-card.sticky-filter {
    position: sticky;
    top: 60px;
    z-index: 100;
    box-shadow: 0 6px 15px -3px rgba(0, 0, 0, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.08) !important;
    border-color: rgba(var(--v-theme-primary), 0.2);
}

.filter-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.category-section {
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.filter-icon {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.7;
    }
}

.category-filter-wrapper {
    position: relative;
    width: 100%;
}

.category-filter {
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
    /* Show scrollbar on mobile for better UX */
}

/* Hide scrollbar on desktop, show on mobile */
@media (min-width: 960px) {
    .category-filter {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .category-filter::-webkit-scrollbar {
        display: none;
    }
}

@media (max-width: 959px) {
    .category-filter {
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    }

    .category-filter::-webkit-scrollbar {
        height: 4px;
        display: block;
    }

    .category-filter::-webkit-scrollbar-track {
        background: transparent;
    }

    .category-filter::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 2px;
    }
}

.category-btn {
    white-space: nowrap;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: none !important;
    letter-spacing: 0.1px;
    font-size: 0.75rem;
    min-height: 28px;
    padding: 4px 10px !important;
    flex-shrink: 0;
    min-width: auto;
}

.category-text {
    font-size: inherit;
}

.category-btn-active {
    box-shadow: 0 3px 8px rgba(var(--v-theme-primary), 0.25) !important;
    transform: translateY(-1px);
}

.category-btn-inactive {
    color: #64748b !important;
}

.category-btn-inactive:hover {
    background-color: #e2e8f0 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
}

.search-sort-row {
    width: 100%;
    align-items: stretch;
}

.search-wrapper {
    position: relative;
    min-width: 0;
    flex: 1 1 auto;
}

.search-field {
    transition: all 0.3s ease;
    width: 100%;
}

.search-field :deep(.v-field) {
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    min-height: 28px;
    /* Match sort button height */
    height: 28px;
}

.search-field :deep(.v-field--focused) {
    box-shadow: 0 3px 10px rgba(var(--v-theme-primary), 0.12) !important;
}

.search-field :deep(.v-field__input) {
    font-size: 0.8125rem;
    padding: 6px 10px;
    line-height: 20px;
}

.search-field :deep(.v-field__prepend-inner) {
    padding-inline-start: 10px;
}

.search-field :deep(.v-field__prepend-inner .v-icon) {
    font-size: 18px;
}

.results-chip {
    font-weight: 600;
    font-size: 0.7rem;
}

.sort-actions-wrapper {
    flex-shrink: 0;
    min-width: 0;
    gap: 4px;
}

.sort-btn {
    transition: all 0.3s ease;
    border-radius: 8px;
    white-space: nowrap;
    min-width: auto;
    min-height: 28px;
    font-size: 0.75rem;
    padding: 4px 8px !important;
}

.sort-btn:hover {
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.sort-btn .v-icon {
    font-size: 16px;
}

.sort-menu-list {
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    min-width: 200px;
    max-width: 90vw;
}

.sort-option {
    transition: all 0.2s ease;
    border-radius: 6px;
    margin: 1px 4px;
    min-height: 32px;
}

.sort-option:hover {
    background-color: rgba(var(--v-theme-primary), 0.08);
}

.sort-option-active {
    background-color: rgba(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary));
}

.comparison-btn {
    border-radius: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(var(--v-theme-primary), 0.2) !important;
    position: relative;
    overflow: visible;
    white-space: nowrap;
    min-height: 28px;
    height: 28px;
    font-size: 0.75rem;
    min-width: auto;
    padding: 4px 8px !important;
}

.comparison-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(var(--v-theme-primary), 0.35) !important;
}

.comparison-btn .v-icon {
    font-size: 16px;
}

.comparison-badge {
    color: rgb(var(--v-theme-primary)) !important;
    font-weight: 700;
    min-width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: bounce 0.5s ease;
    font-size: 0.65rem;
}

@keyframes bounce {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.2);
    }
}

/* hide-scrollbar moved to app.css */

/* Utility classes moved to app.css */

/* Responsive */
@media (max-width: 960px) {
    .filter-card.sticky-filter {
        top: 50px;
    }

    .filter-header,
    .category-section,
    .search-sort-section {
        padding: 12px !important;
    }

    .category-section,
    .filter-header {
        padding-bottom: 8px !important;
    }

    .category-section,
    .search-sort-section {
        padding-top: 0 !important;
    }
}

@media (max-width: 600px) {
    .filter-card.sticky-filter {
        top: 45px;
    }

    .filter-card {
        border-radius: 10px !important;
    }

    .filter-header,
    .category-section,
    .search-sort-section {
        padding: 10px !important;
    }

    .filter-header,
    .category-section {
        padding-bottom: 8px !important;
    }

    .category-section,
    .search-sort-section {
        padding-top: 0 !important;
    }

    .filter-header h2 {
        font-size: 0.8125rem !important;
    }

    .category-filter-wrapper {
        margin: 0 -10px;
        padding: 0 10px;
    }

    .category-filter {
        padding-bottom: 4px;
    }

    .category-btn {
        font-size: 0.7rem !important;
        padding: 4px 8px !important;
        min-height: 28px;
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .category-btn .v-icon {
        font-size: 12px !important;
    }

    .category-text {
        display: inline-block !important;
        visibility: visible !important;
    }

    /* Search & Sort Row - Keep side by side with compact height */
    .search-sort-row {
        gap: 6px !important;
        align-items: stretch;
    }

    /* Search Field Mobile Styles - Compact like category buttons */
    .search-wrapper {
        flex: 1 1 auto;
        min-width: 0;
    }

    .search-field {
        width: 100%;
    }

    .search-field :deep(.v-field) {
        min-height: 28px !important;
        height: 28px !important;
        /* Match sort button height exactly */
        border-radius: 8px;
    }

    .search-field :deep(.v-field__input) {
        font-size: 0.75rem !important;
        /* Compact text */
        padding: 4px 8px !important;
        min-height: 28px !important;
        line-height: 20px !important;
    }

    .search-field :deep(.v-field__prepend-inner) {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        align-items: center;
    }

    .search-field :deep(.v-field__prepend-inner) {
        padding-inline-start: 8px;
    }

    .search-field :deep(.v-field__prepend-inner .v-icon) {
        font-size: 16px !important;
    }

    .results-chip {
        font-size: 0.65rem !important;
        padding: 2px 4px !important;
        height: 16px !important;
        min-width: 16px !important;
    }

    /* Sort & Actions Mobile Styles - Compact */
    .sort-actions-wrapper {
        gap: 4px !important;
        flex-shrink: 0;
    }

    .sort-btn,
    .comparison-btn {
        font-size: 0.7rem !important;
        padding: 4px 8px !important;
        min-height: 28px !important;
        height: 28px !important;
        /* Match search field height exactly */
        min-width: auto;
    }

    .sort-btn .v-icon,
    .comparison-btn .v-icon {
        font-size: 14px !important;
    }

    .sort-btn .v-btn__append .v-icon {
        font-size: 14px !important;
    }

    .comparison-badge {
        min-width: 16px !important;
        height: 16px !important;
        font-size: 0.6rem !important;
        font-weight: 700 !important;
        margin-left: 2px !important;
    }

    .sort-menu-list {
        min-width: 200px;
        max-width: calc(100vw - 40px);
    }

    .sort-option {
        min-height: 40px;
        /* Touch target */
        padding: 8px 12px !important;
    }
}

@media (max-width: 400px) {
    .filter-header {
        flex-wrap: wrap;
        gap: 6px !important;
    }

    .category-filter-wrapper {
        margin: 0 -10px;
        padding: 0 10px;
    }

    .category-btn {
        font-size: 0.65rem !important;
        padding: 3px 8px !important;
        min-height: 26px;
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Keep search and sort side by side even on extra small screens */
    .search-sort-row {
        gap: 4px !important;
    }

    .search-wrapper {
        flex: 1 1 auto;
        min-width: 0;
    }

    .sort-actions-wrapper {
        gap: 4px !important;
        flex-shrink: 0;
    }

    .search-field :deep(.v-field) {
        min-height: 26px !important;
        /* Match sort button height */
        height: 26px !important;
    }

    .search-field :deep(.v-field__input) {
        font-size: 0.7rem !important;
        padding: 3px 6px !important;
        min-height: 26px !important;
    }

    .search-field :deep(.v-field__prepend-inner) {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .search-field :deep(.v-field__prepend-inner .v-icon) {
        font-size: 14px !important;
    }

    .sort-btn,
    .comparison-btn {
        font-size: 0.65rem !important;
        padding: 3px 6px !important;
        min-height: 26px !important;
        height: 26px !important;
        /* Match search field height exactly */
    }

    .sort-btn .v-icon,
    .comparison-btn .v-icon {
        font-size: 12px !important;
    }

    .sort-btn .v-btn__append .v-icon {
        font-size: 12px !important;
    }

    .comparison-badge {
        min-width: 14px !important;
        height: 14px !important;
        font-size: 0.55rem !important;
    }

    .sort-menu-list {
        min-width: 200px;
        max-width: calc(100vw - 20px);
    }
}
</style>
