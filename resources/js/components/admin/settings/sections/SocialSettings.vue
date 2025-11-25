<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">Social Media Links</h2>
            <p class="text-body-2 text-medium-emphasis">Connect your social profiles</p>
        </div>

        <v-row>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.facebook_url.value"
                    label="Facebook URL" type="url" variant="outlined" density="comfortable"
                    color="primary" hint="Your Facebook page URL" persistent-hint
                    prepend-inner-icon="mdi-facebook"></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.twitter_url.value"
                    label="Twitter URL" type="url" variant="outlined" density="comfortable"
                    color="primary" hint="Your Twitter profile URL" persistent-hint
                    prepend-inner-icon="mdi-twitter"></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.linkedin_url.value"
                    label="LinkedIn URL" type="url" variant="outlined" density="comfortable"
                    color="primary" hint="Your LinkedIn company page URL" persistent-hint
                    prepend-inner-icon="mdi-linkedin"></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.instagram_url.value"
                    label="Instagram URL" type="url" variant="outlined"
                    density="comfortable" color="primary" hint="Your Instagram profile URL"
                    persistent-hint prepend-inner-icon="mdi-instagram"></v-text-field>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'SocialSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

