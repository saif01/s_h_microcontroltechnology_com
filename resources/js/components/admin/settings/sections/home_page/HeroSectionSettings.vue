<template>
    <v-row>
        <v-col cols="12">
            <v-text-field v-model="settings.home_hero_title.value" label="Hero Title" variant="outlined"
                density="comfortable" color="primary" hint="Main headline displayed in the hero section"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-textarea v-model="settings.home_hero_subtitle.value" label="Hero Subtitle" variant="outlined"
                density="comfortable" color="primary"
                hint="Subtitle or description displayed below the hero title" persistent-hint rows="3"
                auto-grow></v-textarea>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'HeroSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

