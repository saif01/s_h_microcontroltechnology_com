<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Trusted By Section Content</div>
        </v-col>
        <v-col cols="12">
            <v-text-field v-model="settings.trusted_by_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., TRUSTED BY INDUSTRY LEADERS"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-divider class="my-4"></v-divider>
            <div class="d-flex justify-space-between align-center mb-4">
                <div class="text-subtitle-1 font-weight-bold">Client Logos</div>
                <v-btn color="primary" prepend-icon="mdi-plus" @click="addTrustedByClient" size="small">
                    Add Client Logo
                </v-btn>
            </div>
            <div v-if="trustedByClients.length === 0" class="text-center py-8 text-medium-emphasis">
                No client logos added. Click "Add Client Logo" to get started.
            </div>
            <v-row>
                <v-col v-for="(client, index) in trustedByClients" :key="index" cols="12" md="6" lg="4">
                    <v-card variant="outlined" class="h-100">
                        <v-card-text>
                            <div class="d-flex justify-space-between align-start mb-3">
                                <div class="text-subtitle-2 font-weight-bold">
                                    Client {{ index + 1 }}</div>
                                <v-btn icon="mdi-delete" variant="text" color="error" size="small"
                                    @click="removeTrustedByClient(index)"></v-btn>
                            </div>

                            <!-- Logo Preview -->
                            <div v-if="client.logo" class="mb-3 text-center">
                                <v-img :src="client.logo" max-height="120" max-width="200"
                                    class="mx-auto mb-2" contain>
                                    <template v-slot:placeholder>
                                        <div
                                            class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                            <v-progress-circular indeterminate
                                                color="primary"></v-progress-circular>
                                        </div>
                                    </template>
                                </v-img>
                            </div>

                            <!-- File Upload -->
                            <v-file-input v-model="client.file" label="Upload Logo" variant="outlined"
                                density="comfortable" color="primary" accept="image/*"
                                prepend-icon="mdi-image"
                                hint="Upload a logo image (JPG, PNG, GIF, WebP - Max 5MB)" persistent-hint
                                show-size @update:model-value="handleClientLogoChange(index)">
                                <template v-slot:append-inner v-if="client.uploading">
                                    <v-progress-circular indeterminate size="20"
                                        color="primary"></v-progress-circular>
                                </template>
                            </v-file-input>

                            <!-- Or Enter URL -->
                            <v-text-field v-model="client.logo" label="Or Enter Logo URL" variant="outlined"
                                density="comfortable" color="primary"
                                hint="Enter a direct URL to the logo image" persistent-hint
                                prepend-inner-icon="mdi-link" @input="updateTrustedByClients">
                                <template v-slot:append-inner v-if="client.logo && !client.file">
                                    <v-btn icon="mdi-open-in-new" variant="text" size="small"
                                        @click="window.open(client.logo, '_blank')"></v-btn>
                                </template>
                            </v-text-field>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TrustedBySectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            trustedByClients: []
        };
    },
    mounted() {
        this.initializeTrustedByClients();
    },
    methods: {
        initializeTrustedByClients() {
            const clientsJson = this.settings.trusted_by_clients.value;
            if (clientsJson) {
                try {
                    const parsed = typeof clientsJson === 'string' ? JSON.parse(clientsJson) : clientsJson;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.trustedByClients = parsed.map(client => ({
                            logo: client.logo || '',
                            file: null,
                            uploading: false
                        }));
                    } else {
                        this.trustedByClients = [];
                    }
                } catch (e) {
                    console.warn('Error parsing trusted_by_clients:', e);
                    this.trustedByClients = [];
                }
            } else {
                this.trustedByClients = [];
            }
        },
        addTrustedByClient() {
            this.trustedByClients.push({
                logo: '',
                file: null,
                uploading: false
            });
        },
        removeTrustedByClient(index) {
            this.trustedByClients.splice(index, 1);
            this.updateTrustedByClients();
        },
        async handleClientLogoChange(index) {
            const client = this.trustedByClients[index];
            if (!client.file) {
                this.updateTrustedByClients();
                return;
            }

            const fileToUpload = Array.isArray(client.file) ? client.file[0] : client.file;
            if (!fileToUpload) {
                this.updateTrustedByClients();
                return;
            }

            client.uploading = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'clients');

                const token = localStorage.getItem('admin_token');
                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    client.logo = response.data.url;
                    client.file = null;
                    this.updateTrustedByClients();
                    this.showSuccess('Logo uploaded successfully');
                } else {
                    throw new Error(response.data.message || 'Failed to upload logo');
                }
            } catch (error) {
                console.error('Error uploading logo:', error);
                this.showError(error.response?.data?.message || error.message || 'Failed to upload logo');
                client.file = null;
            } finally {
                client.uploading = false;
            }
        },
        updateTrustedByClients() {
            const clientsData = this.trustedByClients.map(client => ({
                logo: client.logo || ''
            })).filter(client => client.logo);
            this.settings.trusted_by_clients.value = JSON.stringify(clientsData);
        },
        showSuccess(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: message
                });
            } else {
                alert(message);
            }
        },
        showError(message) {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'error',
                    title: message
                });
            } else {
                alert(message);
            }
        }
    }
};
</script>

