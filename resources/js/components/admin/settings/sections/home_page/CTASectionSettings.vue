<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">CTA Section Content</div>
        </v-col>
        <v-col cols="12">
            <v-text-field v-model="settings.cta_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Secure Your Power Today"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-textarea v-model="settings.cta_subtitle.value" label="Subtitle" variant="outlined"
                density="comfortable" color="primary" persistent-hint rows="2" auto-grow></v-textarea>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.cta_primary_button_text.value" label="Primary Button Text"
                variant="outlined" density="comfortable" color="primary" hint="e.g., Get Started"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.cta_primary_button_link.value"
                label="Primary Button Link (JSON or URL)" variant="outlined" density="comfortable"
                color="primary" hint='e.g., {"name": "Contact"} or /contact' persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.cta_secondary_button_text.value" label="Secondary Button Text"
                variant="outlined" density="comfortable" color="primary" hint="e.g., Contact Support"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.cta_secondary_button_link.value"
                label="Secondary Button Link (JSON or URL)" variant="outlined" density="comfortable"
                color="primary" hint='e.g., {"name": "Contact"} or /contact' persistent-hint></v-text-field>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'CTASectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

