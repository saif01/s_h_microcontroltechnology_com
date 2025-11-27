<template>
    <a v-if="whatsappUrl" :href="whatsappUrl" target="_blank"
        class="whatsapp-float elevation-6 d-flex align-center justify-center text-decoration-none">
        <v-icon icon="mdi-whatsapp" color="white" size="32"></v-icon>
    </a>
</template>

<script>

export default {
    name: 'WhatsAppFloat',
    data() {
        return {
            whatsappUrl: '',
            settings: {
                whatsapp_number: '',
                contact_phone: ''
            }
        };
    },
    async mounted() {
        await this.loadSettings();
        this.updateWhatsAppUrl();
    },
    methods: {
        async loadSettings() {
            try {
                const response = await this.$axios.get('/api/openapi/settings');
                const data = response.data;

                // Update settings
                if (data.whatsapp_number !== undefined) {
                    this.settings.whatsapp_number = data.whatsapp_number;
                }
                if (data.contact_phone !== undefined) {
                    this.settings.contact_phone = data.contact_phone;
                }
            } catch (error) {
                console.error('Error loading WhatsApp settings:', error);
            }
        },
        updateWhatsAppUrl() {
            // Use whatsapp_number if available, otherwise use contact_phone
            const phone = this.settings.whatsapp_number || this.settings.contact_phone || '';
            if (!phone) {
                this.whatsappUrl = '';
                return;
            }
            // Remove any non-digit characters except + for WhatsApp URL
            const cleanPhone = phone.replace(/[^\d+]/g, '');
            this.whatsappUrl = `https://wa.me/${cleanPhone}`;
        }
    },
    watch: {
        'settings.whatsapp_number'() {
            this.updateWhatsAppUrl();
        },
        'settings.contact_phone'() {
            this.updateWhatsAppUrl();
        }
    }
};
</script>

<style scoped>
/* WhatsApp Float */
.whatsapp-float {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background-color: #25d366;
    border-radius: 50%;
    z-index: 100;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    animation: pulse-green 2s infinite;
}

.whatsapp-float:hover {
    transform: scale(1.1) rotate(10deg);
    box-shadow: 0 10px 20px rgba(37, 211, 102, 0.4) !important;
}

@keyframes pulse-green {
    0% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
    }

    70% {
        box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
    }
}

/* Responsive Styles */
@media (max-width: 600px) {
    .whatsapp-float {
        width: 56px;
        height: 56px;
        bottom: 20px;
        right: 20px;
    }

    .whatsapp-float .v-icon {
        font-size: 28px !important;
    }
}
</style>
