<template>
    <v-dialog v-model="dialog" max-width="1400" scrollable persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between bg-primary text-white pa-4">
                <span class="text-h5 font-weight-bold">Edit About Page Content</span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="closeDialog"
                    :disabled="saving || loading"></v-btn>
            </v-card-title>

            <v-card-text class="pa-0">
                <!-- Loading State -->
                <div v-if="loading" class="d-flex align-center justify-center pa-12">
                    <div class="text-center">
                        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                        <p class="text-body-1 text-medium-emphasis mt-4">Loading about content...</p>
                    </div>
                </div>

                <!-- Form Content -->
                <v-form ref="formRef" v-else>
                    <v-tabs v-model="activeTab" bg-color="grey-lighten-4">
                        <v-tab value="hero">Hero Section</v-tab>
                        <v-tab value="story">Our Story</v-tab>
                        <v-tab value="values">Core Values</v-tab>
                        <v-tab value="team">Team</v-tab>
                    </v-tabs>

                    <v-window v-model="activeTab">
                        <!-- Hero Section Tab -->
                        <v-window-item value="hero">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.hero.overline" label="Hero Overline"
                                            variant="outlined" hint="Small text above the title (e.g., 'WHO WE ARE')"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.hero.title" label="Hero Title *"
                                            variant="outlined" :rules="[v => !!v || 'Title is required']"
                                            hint="Main heading for the hero section" persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.hero.subtitle" label="Hero Subtitle *"
                                            variant="outlined" rows="3"
                                            :rules="[v => !!v || 'Subtitle is required']"
                                            hint="Subtitle text below the main title" persistent-hint></v-textarea>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Our Story Tab -->
                        <v-window-item value="story">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.story.overline" label="Story Overline"
                                            variant="outlined" hint="Small text above the story title"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.story.title" label="Story Title"
                                            variant="outlined" hint="Main heading for the story section"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Story Description</v-label>
                                        <div class="rich-text-editor-wrapper">
                                            <div ref="storyEditorContainer" class="rich-text-editor"></div>
                                        </div>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label class="mb-2">Story Image</v-label>
                                        <v-card variant="outlined" class="pa-4">
                                            <div class="d-flex flex-column flex-md-row align-start">
                                                <div v-if="storyImagePreview || form.story.image" class="mr-md-4 mb-4 mb-md-0">
                                                    <v-img :src="storyImagePreview || resolveImageUrl(form.story.image)"
                                                        max-width="300" max-height="300" class="rounded elevation-2" cover></v-img>
                                                    <div class="mt-2">
                                                        <v-btn color="error" size="small" variant="text"
                                                            @click="removeStoryImage" prepend-icon="mdi-delete">
                                                            Remove Image
                                                        </v-btn>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <v-file-input v-model="storyImageFile" accept="image/*"
                                                        label="Select Story Image" variant="outlined"
                                                        prepend-icon="" prepend-inner-icon="mdi-image" show-size
                                                        clearable hint="Recommended size: 800x500px. Max file size: 5MB"
                                                        persistent-hint @update:model-value="handleStoryImageSelect">
                                                        <template v-slot:append>
                                                            <v-progress-circular v-if="uploadingStoryImage" indeterminate
                                                                color="primary" size="24"></v-progress-circular>
                                                        </template>
                                                    </v-file-input>
                                                </div>
                                            </div>
                                        </v-card>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Statistics</h3>
                                        <div v-for="(stat, index) in form.story.stats" :key="index" class="mb-4">
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex justify-space-between align-center mb-2">
                                                    <strong>Stat {{ index + 1 }}</strong>
                                                    <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                        @click="removeStat(index)"></v-btn>
                                                </div>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="stat.value" label="Value"
                                                            variant="outlined" hint="e.g., 15+, 500+" persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="stat.label" label="Label"
                                                            variant="outlined" hint="e.g., Years Experience" persistent-hint></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-card>
                                        </div>
                                        <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                            @click="addStat">Add Statistic</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Core Values Tab -->
                        <v-window-item value="values">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.valuesTitle" label="Values Section Title"
                                            variant="outlined" hint="Main heading for the values section"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.valuesSubtitle" label="Values Section Subtitle"
                                            variant="outlined" rows="2"
                                            hint="Subtitle text for the values section" persistent-hint></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Core Values</h3>
                                        <div v-for="(value, index) in form.values" :key="index" class="mb-4">
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex justify-space-between align-center mb-2">
                                                    <strong>Value {{ index + 1 }}</strong>
                                                    <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                        @click="removeValue(index)"></v-btn>
                                                </div>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="value.title" label="Title *"
                                                            variant="outlined" :rules="[v => !!v || 'Title is required']"
                                                            hint="Value title" persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="value.icon" label="Icon"
                                                            variant="outlined"
                                                            hint="Material Design Icon name (e.g., mdi-shield-check)"
                                                            persistent-hint prepend-inner-icon="mdi-information-outline">
                                                            <template v-slot:append>
                                                                <v-icon v-if="value.icon" :icon="value.icon" size="small"></v-icon>
                                                            </template>
                                                        </v-text-field>
                                                    </v-col>
                                                    <v-col cols="12">
                                                        <v-textarea v-model="value.description" label="Description *"
                                                            variant="outlined" rows="3"
                                                            :rules="[v => !!v || 'Description is required']"
                                                            hint="Value description" persistent-hint></v-textarea>
                                                    </v-col>
                                                </v-row>
                                            </v-card>
                                        </div>
                                        <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                            @click="addValue">Add Value</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>

                        <!-- Team Tab -->
                        <v-window-item value="team">
                            <div class="pa-6">
                                <v-row>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.teamOverline" label="Team Section Overline"
                                            variant="outlined" hint="Small text above the team title"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.teamTitle" label="Team Section Title"
                                            variant="outlined" hint="Main heading for the team section"
                                            persistent-hint></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-divider class="my-4"></v-divider>
                                        <h3 class="text-h6 font-weight-bold mb-4">Team Members</h3>
                                        <div v-for="(member, index) in form.team" :key="index" class="mb-4">
                                            <v-card variant="outlined" class="pa-4">
                                                <div class="d-flex justify-space-between align-center mb-2">
                                                    <strong>Team Member {{ index + 1 }}</strong>
                                                    <v-btn icon="mdi-delete" size="small" variant="text" color="error"
                                                        @click="removeTeamMember(index)"></v-btn>
                                                </div>
                                                <v-row>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.name" label="Name *"
                                                            variant="outlined" :rules="[v => !!v || 'Name is required']"
                                                            hint="Team member name" persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.role" label="Role *"
                                                            variant="outlined" :rules="[v => !!v || 'Role is required']"
                                                            hint="Team member role" persistent-hint></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12">
                                                        <v-label class="mb-2">Member Image</v-label>
                                                        <v-card variant="outlined" class="pa-4">
                                                            <div class="d-flex flex-column flex-md-row align-start">
                                                                <div v-if="member.imagePreview || member.image" class="mr-md-4 mb-4 mb-md-0">
                                                                    <v-img :src="member.imagePreview || resolveImageUrl(member.image)"
                                                                        max-width="150" max-height="150" class="rounded elevation-2" cover></v-img>
                                                                    <div class="mt-2">
                                                                        <v-btn color="error" size="small" variant="text"
                                                                            @click="removeTeamMemberImage(index)" prepend-icon="mdi-delete">
                                                                            Remove Image
                                                                        </v-btn>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <v-file-input :ref="`teamImageFile_${index}`"
                                                                        @update:model-value="(file) => handleTeamMemberImageSelect(file, index)"
                                                                        accept="image/*" label="Select Team Member Image"
                                                                        variant="outlined" prepend-icon="" prepend-inner-icon="mdi-image"
                                                                        show-size clearable
                                                                        hint="Recommended: Square image. Max file size: 5MB"
                                                                        persistent-hint></v-file-input>
                                                                </div>
                                                            </div>
                                                        </v-card>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.linkedin" label="LinkedIn URL"
                                                            variant="outlined" hint="Optional LinkedIn profile URL"
                                                            persistent-hint prepend-inner-icon="mdi-linkedin"></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" md="6">
                                                        <v-text-field v-model="member.twitter" label="Twitter URL"
                                                            variant="outlined" hint="Optional Twitter profile URL"
                                                            persistent-hint prepend-inner-icon="mdi-twitter"></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-card>
                                        </div>
                                        <v-btn color="primary" variant="outlined" prepend-icon="mdi-plus"
                                            @click="addTeamMember">Add Team Member</v-btn>
                                    </v-col>
                                </v-row>
                            </div>
                        </v-window-item>
                    </v-window>
                </v-form>
            </v-card-text>

            <v-card-actions class="pa-4 bg-grey-lighten-4">
                <v-spacer></v-spacer>
                <v-btn variant="text" @click="closeDialog" :disabled="saving">Cancel</v-btn>
                <v-btn color="primary" variant="flat" @click="saveAbout" :loading="saving">
                    Save About Content
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';
import adminPaginationMixin from '../../../mixins/adminPaginationMixin';
import Quill from 'quill';
import { normalizeUploadPath, resolveUploadUrl } from '../../../utils/uploads';
import 'quill/dist/quill.snow.css';

export default {
    name: 'AboutFormDialog',
    mixins: [adminPaginationMixin],
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },
        aboutData: {
            type: Object,
            default: null
        }
    },
    emits: ['update:modelValue', 'saved'],
    data() {
        return {
            activeTab: 'hero',
            loading: false,
            saving: false,
            storyImageFile: null,
            storyImagePreview: null,
            uploadingStoryImage: false,
            storyQuillEditor: null,
            form: {
                hero: {
                    overline: 'WHO WE ARE',
                    title: 'Empowering Your World',
                    subtitle: 'We are dedicated to providing reliable, efficient, and sustainable power solutions for businesses and homes across the globe.'
                },
                story: {
                    overline: 'OUR STORY',
                    title: 'Decades of Excellence in Power Solutions',
                    description: '',
                    image: '',
                    stats: []
                },
                valuesTitle: 'Our Core Values',
                valuesSubtitle: 'The principles that guide every project we undertake.',
                values: [],
                teamOverline: 'OUR TEAM',
                teamTitle: 'Meet the Experts',
                team: []
            }
        };
    },
    computed: {
        dialog: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    },
    watch: {
        modelValue(newVal) {
            if (newVal) {
                this.activeTab = 'hero';
                if (this.aboutData) {
                    this.loadAboutForEdit();
                } else {
                    this.resetForm();
                }
            }
        },
        aboutData: {
            handler(newVal) {
                if (newVal && this.dialog) {
                    this.loadAboutForEdit();
                } else if (!newVal && this.dialog) {
                    this.resetForm();
                }
            },
            immediate: false
        },
        activeTab(newTab) {
            if (newTab === 'story' && this.dialog && !this.loading) {
                setTimeout(() => {
                    this.initStoryEditor();
                }, 300);
            } else if (newTab !== 'story' && this.storyQuillEditor) {
                this.destroyStoryEditor();
            }
        }
    },
    methods: {
        resetForm() {
            if (this.storyQuillEditor) {
                this.destroyStoryEditor();
            }

            this.form = {
                hero: {
                    overline: 'WHO WE ARE',
                    title: 'Empowering Your World',
                    subtitle: 'We are dedicated to providing reliable, efficient, and sustainable power solutions for businesses and homes across the globe.'
                },
                story: {
                    overline: 'OUR STORY',
                    title: 'Decades of Excellence in Power Solutions',
                    description: '',
                    image: '',
                    stats: []
                },
                valuesTitle: 'Our Core Values',
                valuesSubtitle: 'The principles that guide every project we undertake.',
                values: [],
                teamOverline: 'OUR TEAM',
                teamTitle: 'Meet the Experts',
                team: []
            };

            this.storyImageFile = null;
            this.storyImagePreview = null;
        },
        loadAboutForEdit() {
            if (!this.aboutData) {
                this.resetForm();
                return;
            }

            this.loading = true;

            try {
                // Load hero data
                if (this.aboutData.hero) {
                    this.form.hero = { ...this.aboutData.hero };
                }

                // Load story data
                if (this.aboutData.story) {
                    this.form.story = {
                        overline: this.aboutData.story.overline || '',
                        title: this.aboutData.story.title || '',
                        description: this.aboutData.story.description || '',
                        image: this.aboutData.story.image || '',
                        stats: this.aboutData.story.stats ? [...this.aboutData.story.stats] : []
                    };
                }

                // Load values data
                this.form.valuesTitle = this.aboutData.valuesTitle || 'Our Core Values';
                this.form.valuesSubtitle = this.aboutData.valuesSubtitle || '';
                this.form.values = this.aboutData.values ? this.aboutData.values.map(v => ({
                    title: v.title || '',
                    description: v.description || v.desc || '',
                    icon: v.icon || ''
                })) : [];

                // Load team data
                this.form.teamOverline = this.aboutData.teamOverline || 'OUR TEAM';
                this.form.teamTitle = this.aboutData.teamTitle || 'Meet the Experts';
                this.form.team = this.aboutData.team ? this.aboutData.team.map(m => ({
                    name: m.name || '',
                    role: m.role || '',
                    image: m.image || '',
                    linkedin: m.linkedin || '',
                    twitter: m.twitter || '',
                    imagePreview: null
                })) : [];

                // Initialize editor with content
                this.$nextTick(() => {
                    if (this.activeTab === 'story') {
                        setTimeout(() => {
                            this.initStoryEditor();
                        }, 300);
                    }
                });
            } catch (error) {
                console.error('Error loading about data:', error);
                this.handleApiError(error, 'Failed to load about content');
            } finally {
                this.loading = false;
            }
        },
        initStoryEditor() {
            if (this.storyQuillEditor) {
                this.destroyStoryEditor();
            }

            const container = this.$refs.storyEditorContainer;
            if (!container) return;

            this.storyQuillEditor = new Quill(container, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            if (this.form.story.description) {
                this.storyQuillEditor.root.innerHTML = this.form.story.description;
            }

            this.storyQuillEditor.on('text-change', () => {
                this.form.story.description = this.storyQuillEditor.root.innerHTML;
            });
        },
        destroyStoryEditor() {
            if (this.storyQuillEditor) {
                this.storyQuillEditor = null;
            }
        },
        async handleStoryImageSelect(file) {
            if (!file || !file[0]) return;

            const selectedFile = file[0];
            if (selectedFile.size > 5242880) {
                this.showError('File size is larger than 5MB. Please choose a smaller image.');
                return;
            }

            this.storyImageFile = selectedFile;
            this.uploadingStoryImage = true;

            try {
                const formData = new FormData();
                formData.append('image', selectedFile);

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data && response.data.path) {
                    this.form.story.image = normalizeUploadPath(response.data.path);
                    this.storyImagePreview = resolveUploadUrl(response.data.path);
                    this.showSuccess('Image uploaded successfully');
                }
            } catch (error) {
                this.handleApiError(error, 'Failed to upload image');
            } finally {
                this.uploadingStoryImage = false;
            }
        },
        removeStoryImage() {
            this.form.story.image = '';
            this.storyImagePreview = null;
            this.storyImageFile = null;
        },
        addStat() {
            this.form.story.stats.push({ value: '', label: '' });
        },
        removeStat(index) {
            this.form.story.stats.splice(index, 1);
        },
        addValue() {
            this.form.values.push({ title: '', description: '', icon: 'mdi-check-circle' });
        },
        removeValue(index) {
            this.form.values.splice(index, 1);
        },
        addTeamMember() {
            this.form.team.push({
                name: '',
                role: '',
                image: '',
                linkedin: '',
                twitter: '',
                imagePreview: null
            });
        },
        removeTeamMember(index) {
            this.form.team.splice(index, 1);
        },
        async handleTeamMemberImageSelect(file, index) {
            if (!file || !file[0]) return;

            const selectedFile = file[0];
            if (selectedFile.size > 5242880) {
                this.showError('File size is larger than 5MB. Please choose a smaller image.');
                return;
            }

            this.form.team[index].imageFile = selectedFile;
            this.form.team[index].uploadingImage = true;

            try {
                const formData = new FormData();
                formData.append('image', selectedFile);

                const response = await axios.post('/api/v1/upload/image', formData, {
                    headers: {
                        ...this.getAuthHeaders(),
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data && response.data.path) {
                    this.form.team[index].image = normalizeUploadPath(response.data.path);
                    this.form.team[index].imagePreview = resolveUploadUrl(response.data.path);
                    this.showSuccess('Image uploaded successfully');
                }
            } catch (error) {
                this.handleApiError(error, 'Failed to upload image');
            } finally {
                this.form.team[index].uploadingImage = false;
            }
        },
        removeTeamMemberImage(index) {
            this.form.team[index].image = '';
            this.form.team[index].imagePreview = null;
            this.form.team[index].imageFile = null;
        },
        async saveAbout() {
            // Validate form
            if (!this.$refs.formRef) {
                this.showError('Form reference not found');
                return;
            }

            const { valid } = await this.$refs.formRef.validate();
            if (!valid) {
                this.showError('Please fill in all required fields');
                return;
            }

            // Save editor content
            if (this.storyQuillEditor) {
                this.form.story.description = this.storyQuillEditor.root.innerHTML;
            }

            this.saving = true;

            try {
                const payload = { ...this.form };

                await axios.put('/api/v1/about', payload, {
                    headers: this.getAuthHeaders()
                });

                this.showSuccess('About content saved successfully');
                this.$emit('saved');
                this.closeDialog();
            } catch (error) {
                // If 404, try POST instead
                if (error.response?.status === 404) {
                    try {
                        await axios.post('/api/v1/about', payload, {
                            headers: this.getAuthHeaders()
                        });

                        this.showSuccess('About content created successfully');
                        this.$emit('saved');
                        this.closeDialog();
                    } catch (postError) {
                        this.handleApiError(postError, 'Failed to save about content');
                    }
                } else {
                    this.handleApiError(error, 'Failed to save about content');
                }
            } finally {
                this.saving = false;
            }
        },
        closeDialog() {
            if (this.storyQuillEditor) {
                this.destroyStoryEditor();
            }
            this.dialog = false;
        },
        resolveImageUrl(imageValue) {
            return resolveUploadUrl(imageValue);
        }
    },
    beforeUnmount() {
        if (this.storyQuillEditor) {
            this.destroyStoryEditor();
        }
    }
};
</script>

<style scoped>
.rich-text-editor-wrapper {
    border: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 4px;
    overflow: hidden;
}

.rich-text-editor {
    min-height: 200px;
}
</style>

