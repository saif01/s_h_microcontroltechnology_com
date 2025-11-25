<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Testimonials Section Content</div>
        </v-col>
        <v-col cols="12" md="4">
            <v-text-field v-model="settings.testimonials_overline.value" label="Overline" variant="outlined"
                density="comfortable" color="primary" hint="e.g., TESTIMONIALS"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
            <v-text-field v-model="settings.testimonials_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Client Success Stories"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-textarea v-model="settings.testimonials_subtitle.value" label="Subtitle" variant="outlined"
                density="comfortable" color="primary" persistent-hint rows="2" auto-grow></v-textarea>
        </v-col>
        <v-col cols="12">
            <v-textarea v-model="settings.testimonials_data.value" label="Testimonials (JSON)"
                variant="outlined" density="comfortable" color="primary"
                hint='Format: [{"text": "...", "name": "...", "role": "...", "avatar": "..."}]'
                persistent-hint rows="8" auto-grow></v-textarea>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'TestimonialsSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

