<template>
    <v-row>
        <v-col cols="12">
            <div class="text-subtitle-1 font-weight-bold mb-4">Testimonials Section Content</div>
        </v-col>
        <v-col cols="12" md="4">
            <v-text-field v-model="settings.testimonials_overline.value" label="Overline" variant="outlined"
                density="comfortable" color="primary" hint="e.g., TESTIMONIALS" persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
            <v-text-field v-model="settings.testimonials_title.value" label="Title" variant="outlined"
                density="comfortable" color="primary" hint="e.g., Client Success Stories"
                persistent-hint></v-text-field>
        </v-col>
        <v-col cols="12">
            <v-textarea v-model="settings.testimonials_subtitle.value" label="Subtitle" variant="outlined"
                density="comfortable" color="primary" persistent-hint rows="2" auto-grow></v-textarea>
        </v-col>
        <v-col cols="12">
            <v-divider class="my-4"></v-divider>
            <div class="d-flex justify-space-between align-center mb-4">
                <div class="text-subtitle-1 font-weight-bold">Testimonials</div>
                <v-btn color="primary" prepend-icon="mdi-plus" @click="addTestimonial" size="small">
                    Add Testimonial
                </v-btn>
            </div>
            <div v-if="testimonials.length === 0" class="text-center py-8 text-medium-emphasis">
                No testimonials added. Click "Add Testimonial" to get started.
            </div>
            <v-card v-for="(testimonial, index) in testimonials" :key="index" class="mb-4" variant="outlined">
                <v-card-text>
                    <div class="d-flex justify-space-between align-start mb-2">
                        <div class="text-subtitle-2 font-weight-bold">Testimonial {{ index + 1 }}</div>
                        <v-btn icon="mdi-delete" variant="text" color="error" size="small"
                            @click="removeTestimonial(index)"></v-btn>
                    </div>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="testimonial.name" label="Name" variant="outlined"
                                density="comfortable" color="primary" hint="e.g., Robert Fox" persistent-hint
                                @input="updateTestimonials"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field v-model="testimonial.role" label="Role/Position" variant="outlined"
                                density="comfortable" color="primary" hint="e.g., Plant Manager, InduTech"
                                persistent-hint @input="updateTestimonials"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-textarea v-model="testimonial.text" label="Testimonial Text" variant="outlined"
                                density="comfortable" color="primary" persistent-hint rows="3" auto-grow
                                hint="The testimonial quote or review text" @input="updateTestimonials"></v-textarea>
                        </v-col>
                        <v-col cols="12">
                            <v-divider class="my-2"></v-divider>
                            <div class="text-caption text-medium-emphasis mb-2">Avatar Image</div>
                            <!-- Avatar Preview -->
                            <div v-if="testimonial.avatar" class="mb-3 text-center">
                                <v-avatar size="80" class="mb-2">
                                    <v-img :src="testimonial.avatar" cover>
                                        <template v-slot:placeholder>
                                            <div
                                                class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                                <v-progress-circular indeterminate
                                                    color="primary"></v-progress-circular>
                                            </div>
                                        </template>
                                    </v-img>
                                </v-avatar>
                            </div>
                            <!-- File Upload -->
                            <v-file-input v-model="testimonial.file" label="Upload Avatar Image" variant="outlined"
                                density="comfortable" color="primary" accept="image/*" prepend-icon="mdi-image"
                                hint="Upload an avatar image (JPG, PNG, GIF, WebP - Max 5MB). Recommended size: 200x200px or 300x300px (square format)" persistent-hint show-size
                                @update:model-value="handleAvatarChange(index)" class="mb-3">
                                <template v-slot:append-inner v-if="testimonial.uploading">
                                    <v-progress-circular indeterminate size="20" color="primary"></v-progress-circular>
                                </template>
                            </v-file-input>
                            <!-- Or Enter URL -->
                            <v-text-field v-model="testimonial.avatar" label="Or Enter Avatar URL" variant="outlined"
                                density="comfortable" color="primary" hint="Enter a direct URL to the avatar image"
                                persistent-hint prepend-inner-icon="mdi-link" @input="updateTestimonials">
                                <template v-slot:append-inner v-if="testimonial.avatar && !testimonial.file">
                                    <v-btn icon="mdi-open-in-new" variant="text" size="small"
                                        @click="window.open(testimonial.avatar, '_blank')"></v-btn>
                                </template>
                            </v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TestimonialsSectionSettings',
    props: {
        settings: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            testimonials: []
        };
    },
    mounted() {
        this.initializeTestimonials();
    },
    methods: {
        initializeTestimonials() {
            const testimonialsJson = this.settings.testimonials_data.value;
            if (testimonialsJson) {
                try {
                    const parsed = typeof testimonialsJson === 'string' ? JSON.parse(testimonialsJson) : testimonialsJson;
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        this.testimonials = parsed.map(testimonial => ({
                            text: testimonial.text || '',
                            name: testimonial.name || '',
                            role: testimonial.role || '',
                            avatar: testimonial.avatar || '',
                            file: null,
                            uploading: false
                        }));
                    } else {
                        this.testimonials = this.getDefaultTestimonials();
                        this.updateTestimonials(); // Save defaults
                    }
                } catch (e) {
                    console.warn('Error parsing testimonials:', e);
                    this.testimonials = this.getDefaultTestimonials();
                    this.updateTestimonials(); // Save defaults
                }
            } else {
                this.testimonials = this.getDefaultTestimonials();
                this.updateTestimonials(); // Save defaults
            }
        },
        getDefaultTestimonials() {
            return [
                {
                    text: "Their UPS installation saved our manufacturing line during a critical power outage. Highly recommended for industrial needs.",
                    name: "Robert Fox",
                    role: "Plant Manager, InduTech",
                    avatar: "https://i.pravatar.cc/150?img=12",
                    file: null,
                    uploading: false
                },
                {
                    text: "Excellent home backup solution. The installation was professional, and the system works seamlessly.",
                    name: "Sarah Jenkins",
                    role: "Homeowner",
                    avatar: "https://i.pravatar.cc/150?img=5",
                    file: null,
                    uploading: false
                },
                {
                    text: "We rely on them for all our battery maintenance. Responsive, knowledgeable, and efficient team.",
                    name: "David Miller",
                    role: "IT Director, NetCorp",
                    avatar: "https://i.pravatar.cc/150?img=3",
                    file: null,
                    uploading: false
                }
            ];
        },
        addTestimonial() {
            this.testimonials.push({
                text: '',
                name: '',
                role: '',
                avatar: '',
                file: null,
                uploading: false
            });
            this.updateTestimonials();
        },
        removeTestimonial(index) {
            this.testimonials.splice(index, 1);
            this.updateTestimonials();
        },
        async handleAvatarChange(index) {
            const testimonial = this.testimonials[index];
            if (!testimonial.file) {
                this.updateTestimonials();
                return;
            }

            const fileToUpload = Array.isArray(testimonial.file) ? testimonial.file[0] : testimonial.file;
            if (!fileToUpload) {
                this.updateTestimonials();
                return;
            }

            testimonial.uploading = true;
            try {
                const formData = new FormData();
                formData.append('image', fileToUpload);
                formData.append('folder', 'testimonials');

                const token = localStorage.getItem('admin_token');
                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    testimonial.avatar = response.data.url;
                    testimonial.file = null;
                    this.updateTestimonials();
                    this.showSuccess('Avatar uploaded successfully');
                } else {
                    throw new Error(response.data.message || 'Failed to upload avatar');
                }
            } catch (error) {
                console.error('Error uploading avatar:', error);
                this.showError(error.response?.data?.message || error.message || 'Failed to upload avatar');
                testimonial.file = null;
            } finally {
                testimonial.uploading = false;
            }
        },
        updateTestimonials() {
            // Ensure all testimonials have required fields
            const testimonialsData = this.testimonials.map(testimonial => ({
                text: testimonial.text || '',
                name: testimonial.name || '',
                role: testimonial.role || '',
                avatar: testimonial.avatar || ''
            })).filter(testimonial => testimonial.text || testimonial.name); // Only include testimonials with at least text or name

            this.settings.testimonials_data.value = JSON.stringify(testimonialsData);
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
