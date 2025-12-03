<template>
    <div class="product-rating" :class="{ 'rating-clickable': clickable }">
        <!-- Star Display -->
        <div class="rating-stars d-flex align-center" :class="sizeClass">
            <v-rating
                :model-value="rating"
                :length="5"
                :size="starSize"
                :density="density"
                active-color="amber"
                color="grey-lighten-1"
                half-increments
                readonly
                :class="{ 'rating-hover': clickable }"
                @click="handleClick"
            ></v-rating>

            <!-- Rating Value Text -->
            <span v-if="showValue" class="rating-value ml-2" :class="valueClass">
                {{ formattedRating }}
            </span>

            <!-- Rating Count -->
            <span v-if="showCount && ratingCount > 0" class="rating-count ml-1 text-medium-emphasis" :class="countClass">
                ({{ formatCount(ratingCount) }})
            </span>
        </div>

        <!-- Rating Distribution (optional) -->
        <div v-if="showDistribution && stats" class="rating-distribution mt-3">
            <div
                v-for="star in [5, 4, 3, 2, 1]"
                :key="star"
                class="distribution-row d-flex align-center mb-1"
            >
                <span class="star-label text-caption">{{ star }} ★</span>
                <v-progress-linear
                    :model-value="stats.percentage_distribution[star]"
                    height="6"
                    color="amber"
                    class="mx-2 flex-grow-1"
                    rounded
                ></v-progress-linear>
                <span class="distribution-count text-caption text-medium-emphasis">
                    {{ stats.distribution[star] }}
                </span>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="rating === 0 && showEmpty" class="rating-empty">
            <span class="text-caption text-medium-emphasis">
                {{ emptyText }}
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ProductRating',
    props: {
        rating: {
            type: Number,
            default: 0
        },
        ratingCount: {
            type: Number,
            default: 0
        },
        stats: {
            type: Object,
            default: null
        },
        size: {
            type: String,
            default: 'medium', // small, medium, large
            validator: (value) => ['small', 'medium', 'large', 'x-large'].includes(value)
        },
        showValue: {
            type: Boolean,
            default: true
        },
        showCount: {
            type: Boolean,
            default: true
        },
        showDistribution: {
            type: Boolean,
            default: false
        },
        showEmpty: {
            type: Boolean,
            default: true
        },
        emptyText: {
            type: String,
            default: 'No ratings yet'
        },
        clickable: {
            type: Boolean,
            default: false
        },
        density: {
            type: String,
            default: 'default'
        }
    },
    emits: ['click'],
    computed: {
        formattedRating() {
            return this.rating > 0 ? this.rating.toFixed(1) : '0.0';
        },
        sizeClass() {
            return `rating-${this.size}`;
        },
        starSize() {
            const sizes = {
                small: 16,
                medium: 20,
                large: 28,
                'x-large': 32
            };
            return sizes[this.size] || 20;
        },
        valueClass() {
            const classes = {
                small: 'text-caption',
                medium: 'text-body-2',
                large: 'text-body-1',
                'x-large': 'text-h6'
            };
            return classes[this.size] || 'text-body-2';
        },
        countClass() {
            const classes = {
                small: 'text-caption',
                medium: 'text-caption',
                large: 'text-body-2',
                'x-large': 'text-body-1'
            };
            return classes[this.size] || 'text-caption';
        }
    },
    methods: {
        formatCount(count) {
            if (count >= 1000000) {
                return (count / 1000000).toFixed(1) + 'M';
            } else if (count >= 1000) {
                return (count / 1000).toFixed(1) + 'K';
            }
            return count.toString();
        },
        handleClick() {
            if (this.clickable) {
                this.$emit('click');
            }
        }
    }
};
</script>

<style scoped>
.product-rating {
    display: inline-block;
}

.rating-stars {
    line-height: 1;
}

.rating-value {
    font-weight: 600;
    color: rgb(var(--v-theme-on-surface));
}

.rating-count {
    font-size: 0.875rem;
}

.rating-clickable {
    cursor: pointer;
}

.rating-hover:hover {
    opacity: 0.8;
    transition: opacity 0.2s;
}

.rating-empty {
    display: inline-block;
}

.distribution-row {
    min-width: 200px;
}

.star-label {
    width: 30px;
    text-align: right;
    font-weight: 500;
}

.distribution-count {
    width: 35px;
    text-align: right;
}
</style>

