<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Featured Products Section Content</div>
        </v-col>
        <v-col cols="12" md="4">
            <v-text-field v-model="settings.products_overline.value" label="Overline" variant="outlined"
                density="comfortable" color="primary" hint="e.g., OUR PRODUCTS"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
            <v-text-field v-model="settings.products_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Featured Power Systems"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.products_button_text.value" label="Button Text"
                variant="outlined" density="comfortable" color="primary" hint="e.g., View All Products"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.products_button_link.value" label="Button Link"
                variant="outlined" density="comfortable" color="primary" hint="e.g., /products"
                persistent-hint></v-text-field>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'ProductsSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

