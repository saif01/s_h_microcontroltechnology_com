<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">Contact Page Settings</h2>
            <p class="text-body-2 text-medium-emphasis">Customize the content of your Contact Us page</p>
        </div>

        <v-row>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.contact_email.value"
                    label="Contact Email" type="email" variant="outlined"
                    density="comfortable" color="primary" prepend-inner-icon="mdi-email"
                    hint="Email displayed on Contact Page" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.contact_phone.value"
                    label="Contact Phone" variant="outlined" density="comfortable"
                    color="primary" prepend-inner-icon="mdi-phone"
                    hint="Phone displayed on Contact Page" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12">
                <v-textarea v-model="settings.contact_address.value"
                    label="Physical Address" variant="outlined" density="comfortable"
                    color="primary" prepend-inner-icon="mdi-map-marker"
                    hint="Address displayed on Contact Page" persistent-hint rows="3"
                    auto-grow></v-textarea>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.contact_hero_title.value"
                    label="Hero Title" variant="outlined" density="comfortable"
                    color="primary" hint="Title for the hero section"
                    persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-textarea v-model="settings.contact_hero_subtitle.value"
                    label="Hero Subtitle" variant="outlined" density="comfortable"
                    color="primary" hint="Subtitle for the hero section" persistent-hint
                    rows="2" auto-grow></v-textarea>
            </v-col>
            <v-col cols="12">
                <v-textarea v-model="settings.contact_map_url.value"
                    label="Google Maps Embed URL" variant="outlined" density="comfortable"
                    color="primary" prepend-inner-icon="mdi-map"
                    hint="Paste the 'src' URL from the Google Maps embed code"
                    persistent-hint rows="3"></v-textarea>

                <div v-if="settings.contact_map_url.value" class="mt-4">
                    <div class="text-subtitle-2 mb-2">Map Preview:</div>
                    <iframe :src="settings.contact_map_url.value" width="100%"
                        height="300" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'ContactPageSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

