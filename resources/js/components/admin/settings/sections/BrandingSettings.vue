<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">Branding</h2>
            <p class="text-body-2 text-medium-emphasis">Customize the look and feel of your site</p>
        </div>

        <v-row>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.logo.value" label="Logo URL"
                    variant="outlined" density="comfortable" color="primary"
                    prepend-inner-icon="mdi-image" hint="URL to your logo image"
                    persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.favicon.value" label="Favicon URL"
                    variant="outlined" density="comfortable" color="primary"
                    prepend-inner-icon="mdi-image-size-select-actual"
                    hint="URL to your favicon" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.primary_color.value"
                    label="Primary Color" type="color" variant="outlined"
                    density="comfortable" style="height: 60px" hint="Primary brand color"
                    persistent-hint></v-text-field>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'BrandingSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

