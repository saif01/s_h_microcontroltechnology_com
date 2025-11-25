<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">General Settings</h2>
            <p class="text-body-2 text-medium-emphasis">Basic information about your website</p>
        </div>

        <v-row>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.site_name.value" label="Site Name" variant="outlined"
                    density="comfortable" color="primary" hint="The name of your website"
                    persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.site_tagline.value" label="Site Tagline" variant="outlined"
                    density="comfortable" color="primary" hint="A short tagline for your website"
                    persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.whatsapp_number.value" label="WhatsApp Number" variant="outlined"
                    density="comfortable" color="primary" prepend-inner-icon="mdi-whatsapp"
                    hint="WhatsApp number for floating button (e.g., +8801712258689)" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12">
                <v-textarea v-model="settings.newsletter_description.value" label="Newsletter Description" variant="outlined"
                    density="comfortable" color="primary" rows="2"
                    hint="Description text shown in the newsletter section of the footer" persistent-hint></v-textarea>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'GeneralSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>
