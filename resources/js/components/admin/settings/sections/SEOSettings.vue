<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">SEO Settings</h2>
            <p class="text-body-2 text-medium-emphasis">Optimize your site for search engines</p>
        </div>

        <v-row>
            <v-col cols="12">
                <v-text-field v-model="settings.meta_title.value"
                    label="Default Meta Title" variant="outlined" density="comfortable"
                    color="primary" hint="Default meta title for pages"
                    persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12">
                <v-textarea v-model="settings.meta_description.value"
                    label="Default Meta Description" variant="outlined"
                    density="comfortable" color="primary"
                    hint="Default meta description for pages" persistent-hint rows="3"
                    auto-grow></v-textarea>
            </v-col>
            <v-col cols="12">
                <v-text-field v-model="settings.meta_keywords.value"
                    label="Meta Keywords" variant="outlined" density="comfortable"
                    color="primary" hint="Default meta keywords (comma-separated)"
                    persistent-hint></v-text-field>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'SEOSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

