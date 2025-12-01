<template>
    <section class="trusted-section position-relative overflow-hidden">
        <div class="trusted-bg"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>

        <v-container fluid class="trusted-container px-0 px-md-8 position-relative">
            <div class="text-center mb-6">
                <div class="glass-pill-sm d-inline-flex align-center px-4 py-2 rounded-pill mb-3">
                    <span class="text-caption font-weight-bold text-primary tracking-widest">PARTNERS & CLIENTS</span>
                </div>
                <h3 class="text-h5 text-md-h4 font-weight-bold text-grey-darken-3">
                    {{ title || 'Trusted by Industry Leaders' }}
                </h3>
                <p class="text-body-2 text-medium-emphasis mt-2">
                    Real brands, real uptime—logo lockups displayed as provided.
                </p>
            </div>

            <div class="logo-belt">
                <div class="belt-track belt-track-1">
                    <div v-for="(client, i) in doubledClients" :key="'a-' + i" class="logo-chip elevation-2">
                        <v-img :src="client.logo ? resolveImageUrl(client.logo) : defaultLogo"
                            :alt="client.name || 'Client logo'" cover class="client-logo"></v-img>
                    </div>
                </div>
                <div class="belt-track belt-track-2">
                    <div v-for="(client, i) in doubledClients" :key="'b-' + i" class="logo-chip elevation-2">
                        <v-img :src="client.logo ? resolveImageUrl(client.logo) : defaultLogo"
                            :alt="client.name || 'Client logo'" cover class="client-logo"></v-img>
                    </div>
                </div>
            </div>
        </v-container>
    </section>
</template>

<script>
import { resolveUploadUrl } from '../../../../utils/uploads';

export default {
    name: 'TrustedBySection',
    props: {
        title: {
            type: String,
            default: 'TRUSTED BY INDUSTRY LEADERS'
        },
        clients: {
            type: Array,
            default: () => [
                { logo: 'https://via.placeholder.com/200x80?text=Client+1' },
                { logo: 'https://via.placeholder.com/200x80?text=Client+2' },
                { logo: 'https://via.placeholder.com/200x80?text=Client+3' },
                { logo: 'https://via.placeholder.com/200x80?text=Client+4' },
                { logo: 'https://via.placeholder.com/200x80?text=Client+5' },
                { logo: 'https://via.placeholder.com/200x80?text=Client+6' },
            ]
        }
    },
    computed: {
        safeClients() {
            if (Array.isArray(this.clients) && this.clients.length) {
                return this.clients;
            }
            return this.$options.props.clients.default();
        },
        doubledClients() {
            return [...this.safeClients, ...this.safeClients];
        },
        defaultLogo() {
            return '/assets/img/default.jpg';
        }
    },
    methods: {
        resolveImageUrl(url) {
            return resolveUploadUrl(url);
        }
    }
};
</script>

<style scoped>
.trusted-section {
    padding: 64px 0;
}

.trusted-bg {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 10% 20%, #e0f2fe, transparent 35%),
        radial-gradient(circle at 90% 20%, #fef3c7, transparent 35%),
        linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    z-index: 0;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.45;
    z-index: 0;
}

.orb-1 {
    width: 360px;
    height: 360px;
    background: #c7d2fe;
    top: -120px;
    left: -80px;
}

.orb-2 {
    width: 420px;
    height: 420px;
    background: #fed7aa;
    bottom: -120px;
    right: -60px;
}

.trusted-container {
    z-index: 1;
}

.logo-belt {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.82);
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
    padding: 18px 0;
    border: 1px solid rgba(37, 99, 235, 0.06);
}

.belt-track {
    display: flex;
    width: max-content;
    align-items: center;
    gap: 28px;
    padding: 8px 0;
}

.belt-track-1 {
    animation: belt 28s linear infinite;
}

.belt-track-2 {
    animation: belt 30s linear infinite reverse;
}

.logo-chip {
    background: white;
    border-radius: 50%;
    padding: 12px;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(0, 0, 0, 0.04);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease, filter 0.3s ease;
    aspect-ratio: 1;
    position: relative;
    overflow: hidden;
}

.logo-chip::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(135deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.3) 25%,
            rgba(255, 255, 255, 0.5) 50%,
            rgba(255, 255, 255, 0.3) 75%,
            rgba(255, 255, 255, 0) 100%);
    transform: rotate(45deg);
    animation: shine 3s ease-in-out infinite;
    pointer-events: none;
    z-index: 1;
}

.logo-chip:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 14px 40px rgba(15, 23, 42, 0.12);
    border-color: rgba(37, 99, 235, 0.18);
}

.client-logo {
    opacity: 1;
    filter: drop-shadow(0 6px 14px rgba(15, 23, 42, 0.08)) brightness(1.05) contrast(1.1);
    transition: transform 0.3s ease, filter 0.3s ease;
    border-radius: 50%;
    width: 100%;
    height: 100%;
    aspect-ratio: 1;
    position: relative;
    z-index: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
}

.logo-chip:hover .client-logo {
    transform: scale(1.03);
    filter: drop-shadow(0 12px 26px rgba(37, 99, 235, 0.16)) brightness(1.1) contrast(1.15);
}

.logo-chip:hover::before {
    animation: shine 1.5s ease-in-out infinite;
}

@keyframes belt {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-50%);
    }
}

@keyframes shine {
    0% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
    }

    50% {
        transform: translateX(100%) translateY(100%) rotate(45deg);
    }

    100% {
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
    }
}

@media (max-width: 960px) {
    .trusted-section {
        padding: 48px 0;
    }

    .logo-chip {
        width: 70px;
        height: 70px;
    }
}

@media (max-width: 600px) {
    .trusted-section {
        padding: 40px 0;
    }

    .belt-track {
        gap: 18px;
    }

    .logo-chip {
        width: 60px;
        height: 60px;
        padding: 10px;
    }
}
</style>
