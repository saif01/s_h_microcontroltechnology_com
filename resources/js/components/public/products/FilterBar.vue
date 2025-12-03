<template>
    <div class="filter-shell mb-4 mb-md-5">
        <v-card class="filter-card rounded-xl elevation-2" :class="{ 'sticky-filter': isSticky }">
            <div class="filter-top">
                <div class="filter-meta">
                    <span class="eyebrow">Product filters</span>
                    <div class="title-row">
                        <div class="icon-pill">
                            <v-icon icon="mdi-filter-variant" color="primary" size="18" />
                        </div>
                        <div>
                            <div class="title-text">Filter & Search</div>
                            <div class="subtitle">Shape the catalog in a couple taps</div>
                        </div>
                    </div>
                </div>
                <div class="top-actions">
                    <v-chip v-if="resultsCount" size="small" color="primary" variant="tonal" class="metric-chip">
                        {{ resultsCount }} results
                    </v-chip>
                    <v-chip v-if="hasActiveFilters" size="small" color="primary" variant="flat" class="metric-chip">
                        {{ activeFiltersCount }} active
                    </v-chip>
                    <v-btn variant="text" color="grey-darken-1" size="small" prepend-icon="mdi-close-circle"
                        class="text-capitalize clear-btn" :disabled="!hasActiveFilters" @click="$emit('clear-filters')">
                        Clear filters
                    </v-btn>
                </div>
            </div>

            <div class="filter-grid">
                <div class="panel search-panel">
                    <div class="panel-header">
                        <span class="panel-title">Search</span>
                        <span class="panel-hint">Names, tags or SKU</span>
                    </div>
                    <v-text-field :model-value="searchQuery" density="compact" variant="solo-filled"
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

                <div class="panel sort-panel">
                    <div class="panel-header">
                        <span class="panel-title">Sort & compare</span>
                        <span class="panel-hint">Tune order & shortlist</span>
                    </div>
                    <div class="sort-actions">
                        <v-menu location="bottom end" :close-on-content-click="true">
                            <template #activator="{ props: menuProps }">
                                <v-btn v-bind="menuProps" variant="outlined" color="grey-darken-2"
                                    class="sort-btn bg-white text-grey-darken-2 font-weight-medium"
                                    append-icon="mdi-chevron-down" size="small" density="comfortable">
                                    <v-icon icon="mdi-sort" size="16" class="mr-1 d-none d-sm-inline" />
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

                        <v-btn v-if="comparisonCount > 0" color="primary" variant="flat"
                            prepend-icon="mdi-scale-balance" class="comparison-btn text-white font-weight-bold"
                            size="small" density="comfortable" @click="$emit('open-comparison')">
                            <span class="d-none d-sm-inline mr-1">Compare</span>
                            <v-chip size="x-small" color="white" variant="flat" class="comparison-badge ml-1">
                                {{ comparisonCount }}
                            </v-chip>
                        </v-btn>
                    </div>
                </div>
            </div>

            <div class="category-block">
                <div class="category-heading">
                    <div>
                        <span class="eyebrow">Categories</span>
                        <div class="subcopy">
                            <v-icon icon="mdi-gesture-swipe-horizontal" size="12" class="mr-1" />
                            Scroll to explore
                        </div>
                    </div>
                    <v-chip v-if="hasActiveFilters" size="x-small" color="primary" variant="tonal"
                        class="d-none d-sm-inline-flex">
                        Active filter set
                    </v-chip>
                </div>
                <div class="category-filter-wrapper">
                    <!-- Scroll indicators -->
                    <div v-if="showLeftArrow" class="scroll-indicator left-indicator" @click="scrollCategories('left')">
                        <v-icon icon="mdi-chevron-left" color="primary" size="small" />
                    </div>
                    <div v-if="showRightArrow" class="scroll-indicator right-indicator"
                        @click="scrollCategories('right')">
                        <v-icon icon="mdi-chevron-right" color="primary" size="small" />
                    </div>

                    <div ref="categoryScroll" class="category-filter" @scroll="checkScroll">
                        <v-btn v-for="category in categories" :key="category.id" variant="flat"
                            :color="activeCategory === category.id ? 'primary' : 'grey-lighten-3'"
                            class="category-btn font-weight-bold text-capitalize rounded-pill flex-shrink-0" :class="{
                                'category-btn-active': activeCategory === category.id,
                                'category-btn-inactive': activeCategory !== category.id
                            }" size="small" density="comfortable" @click="$emit('update:activeCategory', category.id)"
                            :aria-pressed="activeCategory === category.id">
                            <v-icon v-if="category.icon" :icon="category.icon" size="14" class="mr-1" />
                            <span class="category-text">{{ category.name }}</span>
                        </v-btn>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters Section -->
            <div class="advanced-filters-block">
                <div v-if="!mobile" class="filter-section-header">
                    <span class="eyebrow">Advanced Filters</span>
                    <v-btn variant="text" size="x-small" color="primary" class="text-capitalize"
                        @click="toggleAdvancedFilters">
                        {{ showAdvancedFilters ? 'Hide' : 'Show' }}
                        <v-icon :icon="showAdvancedFilters ? 'mdi-chevron-up' : 'mdi-chevron-down'" size="small"
                            class="ml-1" />
                    </v-btn>
                </div>

                <!-- Mobile Accordion View -->
                <v-expansion-panels v-if="mobile" variant="accordion" class="mobile-expansion-panels">
                    <v-expansion-panel elevation="0" bg-color="transparent">
                        <v-expansion-panel-title>
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-currency-usd" size="18" color="primary" class="mr-2" />
                                <span class="filter-label mb-0">Price Range</span>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <div class="price-inputs">
                                <v-text-field v-model="localPriceRange[0]" type="number" density="compact"
                                    variant="outlined" label="Min" prefix="$" hide-details class="price-field"
                                    @update:model-value="handlePriceChange" />
                                <span class="price-separator">—</span>
                                <v-text-field v-model="localPriceRange[1]" type="number" density="compact"
                                    variant="outlined" label="Max" prefix="$" hide-details class="price-field"
                                    @update:model-value="handlePriceChange" />
                            </div>
                            <v-range-slider v-model="localPriceRange" :min="0" :max="10000" :step="50" color="primary"
                                class="mt-4" thumb-label="always" hide-details @update:model-value="handlePriceChange">
                                <template #thumb-label="{ modelValue }">
                                    ${{ modelValue }}
                                </template>
                            </v-range-slider>
                        </v-expansion-panel-text>
                    </v-expansion-panel>

                    <v-expansion-panel elevation="0" bg-color="transparent">
                        <v-expansion-panel-title>
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-package-variant-closed" size="18" color="primary" class="mr-2" />
                                <span class="filter-label mb-0">Availability</span>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <v-chip-group v-model="localAvailability" column multiple
                                @update:model-value="$emit('update:availability', localAvailability)">
                                <v-chip v-for="option in availabilityOptions" :key="option.value" :value="option.value"
                                    filter variant="outlined" color="primary" size="small">
                                    <v-icon :icon="option.icon" size="14" class="mr-1" />
                                    {{ option.label }}
                                </v-chip>
                            </v-chip-group>
                        </v-expansion-panel-text>
                    </v-expansion-panel>

                    <v-expansion-panel elevation="0" bg-color="transparent">
                        <v-expansion-panel-title>
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-tag-multiple" size="18" color="primary" class="mr-2" />
                                <span class="filter-label mb-0">Brand</span>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <v-select v-model="localBrands" :items="brands" multiple chips closable-chips
                                density="compact" variant="outlined" placeholder="Select brands" hide-details
                                class="brand-select" @update:model-value="$emit('update:brands', localBrands)">
                                <template #chip="{ item, props: chipProps }">
                                    <v-chip v-bind="chipProps" size="small" color="primary" variant="flat">
                                        {{ item.title }}
                                    </v-chip>
                                </template>
                            </v-select>
                        </v-expansion-panel-text>
                    </v-expansion-panel>

                    <v-expansion-panel elevation="0" bg-color="transparent">
                        <v-expansion-panel-title>
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-star" size="18" color="primary" class="mr-2" />
                                <span class="filter-label mb-0">Minimum Rating</span>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <v-chip-group v-model="localMinRating" mandatory
                                @update:model-value="$emit('update:minRating', localMinRating)">
                                <v-chip v-for="rating in [5, 4, 3, 2, 1]" :key="rating" :value="rating" filter
                                    variant="outlined" color="primary" size="small">
                                    {{ rating }}
                                    <v-icon icon="mdi-star" size="12" class="ml-1" color="amber" />
                                    & up
                                </v-chip>
                            </v-chip-group>
                        </v-expansion-panel-text>
                    </v-expansion-panel>

                    <v-expansion-panel elevation="0" bg-color="transparent">
                        <v-expansion-panel-title>
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-feature-search" size="18" color="primary" class="mr-2" />
                                <span class="filter-label mb-0">Features</span>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <div class="features-grid">
                                <v-checkbox v-for="feature in features" :key="feature.value"
                                    :model-value="localFeatures.includes(feature.value)" :label="feature.label"
                                    :value="feature.value" density="compact" hide-details color="primary"
                                    class="feature-checkbox" @update:model-value="toggleFeature(feature.value)">
                                    <template #label>
                                        <div class="d-flex align-center">
                                            <v-icon :icon="feature.icon" size="14" class="mr-1" />
                                            <span class="text-body-2">{{ feature.label }}</span>
                                        </div>
                                    </template>
                                </v-checkbox>
                            </div>
                        </v-expansion-panel-text>
                    </v-expansion-panel>

                    <v-expansion-panel elevation="0" bg-color="transparent">
                        <v-expansion-panel-title>
                            <div class="d-flex align-center">
                                <v-icon icon="mdi-sale" size="18" color="primary" class="mr-2" />
                                <span class="filter-label mb-0">Discounts & Offers</span>
                            </div>
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                            <v-chip-group v-model="localDiscount"
                                @update:model-value="$emit('update:discount', localDiscount)">
                                <v-chip v-for="option in discountOptions" :key="option.value" :value="option.value"
                                    filter variant="outlined" color="primary" size="small">
                                    {{ option.label }}
                                </v-chip>
                            </v-chip-group>
                        </v-expansion-panel-text>
                    </v-expansion-panel>

                </v-expansion-panels>

                <v-expand-transition v-else>
                    <div v-show="showAdvancedFilters" class="filters-content">
                        <!-- Price Range Filter -->
                        <div class="filter-item">
                            <div class="filter-item-header">
                                <v-icon icon="mdi-currency-usd" size="18" color="primary" class="mr-2" />
                                <span class="filter-label">Price Range</span>
                            </div>
                            <div class="price-inputs">
                                <v-text-field v-model="localPriceRange[0]" type="number" density="compact"
                                    variant="outlined" label="Min" prefix="$" hide-details class="price-field"
                                    @update:model-value="handlePriceChange" />
                                <span class="price-separator">—</span>
                                <v-text-field v-model="localPriceRange[1]" type="number" density="compact"
                                    variant="outlined" label="Max" prefix="$" hide-details class="price-field"
                                    @update:model-value="handlePriceChange" />
                            </div>
                            <v-range-slider v-model="localPriceRange" :min="0" :max="10000" :step="50" color="primary"
                                class="mt-4" thumb-label="always" hide-details @update:model-value="handlePriceChange">
                                <template #thumb-label="{ modelValue }">
                                    ${{ modelValue }}
                                </template>
                            </v-range-slider>
                        </div>

                        <!-- Availability Filter -->
                        <div class="filter-item">
                            <div class="filter-item-header">
                                <v-icon icon="mdi-package-variant-closed" size="18" color="primary" class="mr-2" />
                                <span class="filter-label">Availability</span>
                            </div>
                            <v-chip-group v-model="localAvailability" column multiple
                                @update:model-value="$emit('update:availability', localAvailability)">
                                <v-chip v-for="option in availabilityOptions" :key="option.value" :value="option.value"
                                    filter variant="outlined" color="primary" size="small">
                                    <v-icon :icon="option.icon" size="14" class="mr-1" />
                                    {{ option.label }}
                                </v-chip>
                            </v-chip-group>
                        </div>

                        <!-- Brand Filter -->
                        <div class="filter-item">
                            <div class="filter-item-header">
                                <v-icon icon="mdi-tag-multiple" size="18" color="primary" class="mr-2" />
                                <span class="filter-label">Brand</span>
                            </div>
                            <v-select v-model="localBrands" :items="brands" multiple chips closable-chips
                                density="compact" variant="outlined" placeholder="Select brands" hide-details
                                class="brand-select" @update:model-value="$emit('update:brands', localBrands)">
                                <template #chip="{ item, props: chipProps }">
                                    <v-chip v-bind="chipProps" size="small" color="primary" variant="flat">
                                        {{ item.title }}
                                    </v-chip>
                                </template>
                            </v-select>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-item">
                            <div class="filter-item-header">
                                <v-icon icon="mdi-star" size="18" color="primary" class="mr-2" />
                                <span class="filter-label">Minimum Rating</span>
                            </div>
                            <v-chip-group v-model="localMinRating" mandatory
                                @update:model-value="$emit('update:minRating', localMinRating)">
                                <v-chip v-for="rating in [5, 4, 3, 2, 1]" :key="rating" :value="rating" filter
                                    variant="outlined" color="primary" size="small">
                                    {{ rating }}
                                    <v-icon icon="mdi-star" size="12" class="ml-1" color="amber" />
                                    & up
                                </v-chip>
                            </v-chip-group>
                        </div>

                        <!-- Features Filter -->
                        <div class="filter-item">
                            <div class="filter-item-header">
                                <v-icon icon="mdi-feature-search" size="18" color="primary" class="mr-2" />
                                <span class="filter-label">Features</span>
                            </div>
                            <div class="features-grid">
                                <v-checkbox v-for="feature in features" :key="feature.value"
                                    :model-value="localFeatures.includes(feature.value)" :label="feature.label"
                                    :value="feature.value" density="compact" hide-details color="primary"
                                    class="feature-checkbox" @update:model-value="toggleFeature(feature.value)">
                                    <template #label>
                                        <div class="d-flex align-center">
                                            <v-icon :icon="feature.icon" size="14" class="mr-1" />
                                            <span class="text-body-2">{{ feature.label }}</span>
                                        </div>
                                    </template>
                                </v-checkbox>
                            </div>
                        </div>

                        <!-- Discount Filter -->
                        <div class="filter-item">
                            <div class="filter-item-header">
                                <v-icon icon="mdi-sale" size="18" color="primary" class="mr-2" />
                                <span class="filter-label">Discounts & Offers</span>
                            </div>
                            <v-chip-group v-model="localDiscount"
                                @update:model-value="$emit('update:discount', localDiscount)">
                                <v-chip v-for="option in discountOptions" :key="option.value" :value="option.value"
                                    filter variant="outlined" color="primary" size="small">
                                    {{ option.label }}
                                </v-chip>
                            </v-chip-group>
                        </div>
                    </div>
                </v-expand-transition>
            </div>
        </v-card>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
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
    },
    priceRange: {
        type: Array,
        default: () => [0, 10000]
    },
    availability: {
        type: Array,
        default: () => []
    },
    brands: {
        type: Array,
        default: () => []
    },
    selectedBrands: {
        type: Array,
        default: () => []
    },
    minRating: {
        type: Number,
        default: 0
    },
    features: {
        type: Array,
        default: () => []
    },
    selectedFeatures: {
        type: Array,
        default: () => []
    },
    discount: {
        type: String,
        default: null
    },
    mobile: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits([
    'update:activeCategory',
    'update:searchQuery',
    'update:sortBy',
    'update:priceRange',
    'update:availability',
    'update:brands',
    'update:minRating',
    'update:features',
    'update:discount',
    'clear-filters',
    'open-comparison'
]);

// Sticky filter on scroll
const isSticky = ref(false);

// Advanced filters state
const showAdvancedFilters = ref(true);

// Local filter states
const localPriceRange = ref([...props.priceRange]);
const localAvailability = ref([...props.availability]);
const localBrands = ref([...props.selectedBrands]);
const localMinRating = ref(props.minRating);
const localFeatures = ref([...props.selectedFeatures]);
const localDiscount = ref(props.discount);

// Filter options
const availabilityOptions = [
    { value: 'in_stock', label: 'In Stock', icon: 'mdi-check-circle' },
    { value: 'out_of_stock', label: 'Out of Stock', icon: 'mdi-close-circle' },
    { value: 'pre_order', label: 'Pre-Order', icon: 'mdi-clock-outline' },
    { value: 'coming_soon', label: 'Coming Soon', icon: 'mdi-new-box' }
];

const discountOptions = [
    { value: 'any', label: 'Any Discount' },
    { value: '10', label: '10% or more' },
    { value: '25', label: '25% or more' },
    { value: '50', label: '50% or more' }
];

// Watch for prop changes and update local states
watch(() => props.priceRange, (newVal) => {
    localPriceRange.value = [...newVal];
}, { deep: true });

watch(() => props.availability, (newVal) => {
    localAvailability.value = [...newVal];
}, { deep: true });

watch(() => props.selectedBrands, (newVal) => {
    localBrands.value = [...newVal];
}, { deep: true });

watch(() => props.minRating, (newVal) => {
    localMinRating.value = newVal;
});

watch(() => props.selectedFeatures, (newVal) => {
    localFeatures.value = [...newVal];
}, { deep: true });

watch(() => props.discount, (newVal) => {
    localDiscount.value = newVal;
});

// Watch categories to update scroll indicators
watch(() => props.categories, () => {
    setTimeout(() => {
        checkScroll();
    }, 100);
}, { deep: true });

const handleScroll = () => {
    isSticky.value = window.scrollY > 200;
};

// Debounced search input handler
const handleSearchInput = debounce((value) => {
    emit('update:searchQuery', value || '');
}, 300);

// Debounced price change handler
const handlePriceChange = debounce(() => {
    emit('update:priceRange', localPriceRange.value);
}, 500);

// Toggle advanced filters
const toggleAdvancedFilters = () => {
    showAdvancedFilters.value = !showAdvancedFilters.value;
};

// Toggle feature selection
const toggleFeature = (featureValue) => {
    const index = localFeatures.value.indexOf(featureValue);
    if (index > -1) {
        localFeatures.value.splice(index, 1);
    } else {
        localFeatures.value.push(featureValue);
    }
    emit('update:features', localFeatures.value);
};

// Category scroll functionality
const categoryScroll = ref(null);
const showLeftArrow = ref(false);
const showRightArrow = ref(false);

const checkScroll = () => {
    if (!categoryScroll.value) return;

    const element = categoryScroll.value;
    showLeftArrow.value = element.scrollLeft > 10;
    showRightArrow.value = element.scrollLeft < (element.scrollWidth - element.clientWidth - 10);
};

const scrollCategories = (direction) => {
    if (!categoryScroll.value) return;

    const scrollAmount = 200;
    const element = categoryScroll.value;

    if (direction === 'left') {
        element.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
        element.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }

    setTimeout(checkScroll, 100);
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);

    // Check scroll indicators on mount and when categories change
    setTimeout(() => {
        checkScroll();
    }, 100);

    // Add resize listener to update scroll indicators
    window.addEventListener('resize', checkScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', checkScroll);
});
</script>

<style scoped>
.mobile-expansion-panels {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.06);
    background: white;
}

.mobile-expansion-panels :deep(.v-expansion-panel-title) {
    padding: 12px 16px;
    font-size: 0.9rem;
    min-height: 48px;
}

.mobile-expansion-panels :deep(.v-expansion-panel-text__wrapper) {
    padding: 12px 16px 16px;
}

.filter-shell {
    position: relative;
}

.filter-card {
    background: radial-gradient(120% 120% at 10% 10%, rgba(var(--v-theme-primary), 0.06), transparent),
        linear-gradient(180deg, #ffffff 0%, #f7f9fb 100%) !important;
    backdrop-filter: blur(12px);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 12px 40px -24px rgba(15, 23, 42, 0.55) !important;
    padding: 14px 14px 16px;
}

/* Mobile drawer specific styles */
@media (max-width: 1279px) {
    .filter-shell {
        margin-bottom: 0 !important;
    }

    .filter-card {
        border-radius: 12px;
        box-shadow: none !important;
        border: none !important;
        padding: 8px !important;
        background: transparent !important;
    }

    .filter-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding-bottom: 10px;
    }

    .top-actions {
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 6px;
    }

    .filter-grid {
        gap: 10px;
        padding: 10px 0 4px;
    }

    .panel {
        padding: 8px;
    }

    .category-block {
        margin-top: 10px;
        padding-top: 10px;
    }

    .advanced-filters-block {
        margin-top: 10px;
        padding-top: 10px;
    }

    .filter-item {
        padding: 10px;
    }
}

.filter-card.sticky-filter {
    /* Sticky behavior handled by parent container in sidebar layout */
    box-shadow: 0 18px 45px -20px rgba(0, 0, 0, 0.25) !important;
    border-color: rgba(var(--v-theme-primary), 0.18);
}

.filter-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.filter-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 700;
}

.title-row {
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.icon-pill {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: rgba(var(--v-theme-primary), 0.12);
    box-shadow: 0 8px 18px -10px rgba(var(--v-theme-primary), 0.45);
}

.title-text {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
}

.subtitle {
    font-size: 0.85rem;
    color: #6b7280;
}

.top-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.metric-chip {
    font-weight: 700;
    letter-spacing: 0.01em;
}

.clear-btn {
    font-weight: 700;
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    padding: 12px 0 4px;
}

.panel {
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(0, 0, 0, 0.04);
    border-radius: 14px;
    padding: 10px;
    box-shadow: 0 6px 18px -14px rgba(15, 23, 42, 0.7);
}

.panel-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 6px;
    gap: 6px;
}

.panel-title {
    font-weight: 700;
    color: #0f172a;
}

.panel-hint {
    font-size: 0.8rem;
    color: #6b7280;
}

.search-field {
    width: 100%;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
}

.search-field :deep(.v-field) {
    border-radius: 10px;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9), 0 8px 20px -14px rgba(15, 23, 42, 0.5);
}

.search-field :deep(.v-field--focused) {
    box-shadow: 0 10px 30px -16px rgba(var(--v-theme-primary), 0.6) !important;
    transform: translateY(-1px);
}

.search-field :deep(.v-field__input) {
    font-size: 0.9rem;
    padding: 10px 12px;
}

.search-field :deep(.v-field__prepend-inner .v-icon) {
    font-size: 18px;
}

.results-chip {
    font-weight: 700;
    font-size: 0.75rem;
}

.sort-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: space-between;
    flex-wrap: wrap;
}

.sort-btn {
    transition: all 0.25s ease;
    border-radius: 12px;
    min-width: 0;
    font-weight: 700;
    min-height: 42px;
}

.sort-btn:hover {
    box-shadow: 0 10px 30px -16px rgba(0, 0, 0, 0.2);
    transform: translateY(-1px);
}

.sort-menu-list {
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2) !important;
    min-width: 220px;
    max-width: 92vw;
}

.sort-option {
    transition: all 0.2s ease;
    border-radius: 8px;
    margin: 2px 6px;
    min-height: 38px;
}

.sort-option:hover {
    background-color: rgba(var(--v-theme-primary), 0.08);
}

.sort-option-active {
    background-color: rgba(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary));
}

.comparison-btn {
    border-radius: 12px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.2) !important;
    min-width: 0;
    font-weight: 800;
    min-height: 42px;
}

.comparison-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(var(--v-theme-primary), 0.3) !important;
}

.comparison-badge {
    color: rgb(var(--v-theme-primary)) !important;
    font-weight: 800;
    min-width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: bounce 0.5s ease;
    font-size: 0.68rem;
}

@keyframes bounce {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.15);
    }
}

.category-block {
    border-top: 1px dashed rgba(0, 0, 0, 0.06);
    margin-top: 2px;
    padding-top: 12px;
}

.category-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    gap: 10px;
}

.subcopy {
    font-size: 0.82rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    margin-top: 2px;
}

.category-filter-wrapper {
    position: relative;
    width: 100%;
    padding: 0 4px;
}

.category-filter {
    display: flex;
    align-items: center;
    gap: 8px;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
    padding: 8px 0 10px;
    scrollbar-width: thin;
    scrollbar-color: rgba(var(--v-theme-primary), 0.3) rgba(0, 0, 0, 0.05);
}

.category-filter::-webkit-scrollbar {
    height: 6px;
    display: block;
}

.category-filter::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.04);
    border-radius: 10px;
    margin: 0 4px;
}

.category-filter::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, rgba(var(--v-theme-primary), 0.4), rgba(var(--v-theme-primary), 0.6));
    border-radius: 10px;
    transition: background 0.3s ease;
}

.category-filter::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(90deg, rgba(var(--v-theme-primary), 0.6), rgba(var(--v-theme-primary), 0.8));
}

/* Scroll Indicators */
.scroll-indicator {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 2;
    transition: all 0.3s ease;
    backdrop-filter: blur(8px);
}

.scroll-indicator:hover {
    background: white;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    transform: translateY(-50%) scale(1.1);
}

.left-indicator {
    left: -8px;
}

.right-indicator {
    right: -8px;
}

@media (max-width: 1280px) {
    .scroll-indicator {
        display: none;
    }
}

.category-btn {
    white-space: nowrap;
    transition: all 0.25s ease;
    text-transform: none !important;
    letter-spacing: 0.1px;
    font-size: 0.8rem;
    min-height: 32px;
    padding: 7px 12px !important;
    flex-shrink: 0;
    min-width: auto;
}

.category-text {
    font-size: inherit;
}

.category-btn-active {
    box-shadow: 0 10px 20px -12px rgba(var(--v-theme-primary), 0.6) !important;
    transform: translateY(-1px);
}

.category-btn-inactive {
    color: #475569 !important;
}

.category-btn-inactive:hover {
    background-color: #e2e8f0 !important;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px -12px rgba(0, 0, 0, 0.15) !important;
}

/* Advanced Filters Section */
.advanced-filters-block {
    border-top: 1px dashed rgba(0, 0, 0, 0.06);
    margin-top: 12px;
    padding-top: 12px;
}

.filter-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.filters-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding-top: 12px;
}

.filter-item {
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(0, 0, 0, 0.04);
    border-radius: 12px;
    padding: 14px;
    transition: all 0.25s ease;
}

.filter-item:hover {
    background: rgba(255, 255, 255, 0.85);
    box-shadow: 0 6px 18px -12px rgba(0, 0, 0, 0.15);
}

.filter-item-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.filter-label {
    font-weight: 700;
    font-size: 0.9rem;
    color: #0f172a;
}

/* Price Range Filter */
.price-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.price-field {
    flex: 1;
}

.price-separator {
    font-weight: 600;
    color: #6b7280;
    padding: 0 4px;
}

/* Brand Select */
.brand-select :deep(.v-field) {
    border-radius: 8px;
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}

.feature-checkbox :deep(.v-label) {
    font-size: 0.875rem;
}

/* Chip Groups */
.filter-item :deep(.v-chip-group) {
    gap: 8px;
}

.filter-item :deep(.v-chip) {
    font-weight: 600;
    font-size: 0.8rem;
}

@media (max-width: 1024px) {
    .panel {
        padding: 9px;
    }

    .filter-card {
        padding: 12px;
    }
}

@media (max-width: 720px) {
    .filter-card.sticky-filter {
        top: 56px;
    }

    .filter-top {
        flex-direction: column;
        align-items: flex-start;
    }

    .top-actions {
        justify-content: flex-start;
    }

    .category-heading {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .filter-card {
        border-radius: 14px !important;
    }

    .title-text {
        font-size: 0.98rem;
    }

    .panel-hint,
    .subtitle {
        font-size: 0.78rem;
    }

    .sort-actions {
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 8px;
    }

    .sort-btn,
    .comparison-btn {
        width: 100%;
        justify-content: center;
        min-height: 40px;
    }
}
</style>
