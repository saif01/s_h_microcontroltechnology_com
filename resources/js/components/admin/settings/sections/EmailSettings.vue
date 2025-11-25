<template>
    <div>
        <div class="mb-6">
            <h2 class="text-h5 font-weight-bold mb-1">Email Settings</h2>
            <p class="text-body-2 text-medium-emphasis">Configure SMTP for email delivery</p>
        </div>

        <v-row>
            <v-col cols="12" md="8">
                <v-text-field v-model="settings.smtp_host.value" label="SMTP Host"
                    variant="outlined" density="comfortable" color="primary"
                    prepend-inner-icon="mdi-server" hint="SMTP server hostname"
                    persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field v-model="settings.smtp_port.value" label="SMTP Port"
                    type="number" variant="outlined" density="comfortable" color="primary"
                    hint="Port (e.g. 587, 465)" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.smtp_username.value"
                    label="SMTP Username" variant="outlined" density="comfortable"
                    color="primary" prepend-inner-icon="mdi-account"
                    hint="SMTP authentication username" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-text-field v-model="settings.smtp_password.value"
                    label="SMTP Password" type="password" variant="outlined"
                    density="comfortable" color="primary" prepend-inner-icon="mdi-lock"
                    hint="SMTP authentication password" persistent-hint></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
                <v-select v-model="settings.smtp_encryption.value"
                    :items="['none', 'tls', 'ssl']" label="SMTP Encryption"
                    variant="outlined" density="comfortable" color="primary"
                    prepend-inner-icon="mdi-shield-check" hint="Encryption method"
                    persistent-hint></v-select>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'EmailSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    }
};
</script>

