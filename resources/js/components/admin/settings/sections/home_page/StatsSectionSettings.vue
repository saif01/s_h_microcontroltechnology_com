<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Statistics Section</div>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_1_value.value" label="Stat 1 - Value" variant="outlined"
                density="comfortable" color="primary" hint="e.g., 500+" persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_1_label.value" label="Stat 1 - Label" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Systems Installed"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_2_value.value" label="Stat 2 - Value" variant="outlined"
                density="comfortable" color="primary" hint="e.g., 99.9%" persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_2_label.value" label="Stat 2 - Label" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Power Uptime"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_3_value.value" label="Stat 3 - Value" variant="outlined"
                density="comfortable" color="primary" hint="e.g., 24/7" persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_3_label.value" label="Stat 3 - Label" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Support" persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_4_value.value" label="Stat 4 - Value" variant="outlined"
                density="comfortable" color="primary" hint="e.g., 15+" persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
            <v-text-field v-model="settings.stat_4_label.value" label="Stat 4 - Label" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Years Experience"
                persistent-hint></v-text-field>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'StatsSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

