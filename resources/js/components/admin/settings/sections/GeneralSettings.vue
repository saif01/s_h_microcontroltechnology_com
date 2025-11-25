<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">General Settings</h2>
            <p class="text-body-2 text-medium-emphasis">Basic information about your website</p>
        </div>

        <v-row>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.site_name.value" label="Site Name"
                    variant="outlined" density="comfortable" color="primary"
                    hint="The name of your website" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.site_tagline.value"
                    label="Site Tagline" variant="outlined" density="comfortable"
                    color="primary" hint="A short tagline for your website"
                    persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.contact_email.value"
                    label="Contact Email" type="email" variant="outlined"
                    density="comfortable" color="primary" prepend-inner-icon="mdi-email"
                    hint="Main contact email address" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.contact_phone.value"
                    label="Contact Phone" variant="outlined" density="comfortable"
                    color="primary" prepend-inner-icon="mdi-phone"
                    hint="Main contact phone number" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12">
                <v-textarea v-model="settings.address.value" label="Address"
                    variant="outlined" density="comfortable" color="primary"
                    prepend-inner-icon="mdi-map-marker" hint="Business address"
                    persistent-hint rows="3" auto-grow></v-textarea>
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

