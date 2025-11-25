<template>
    <section class="py-12 overflow-hidden bg-grey-lighten-5">
        <v-container fluid class="px-0">
            <div class="text-center mb-8">
                <div class="text-overline text-medium-emphasis tracking-widest">TRUSTED BY INDUSTRY LEADERS</div>
            </div>
            <div class="marquee-container">
                <div class="marquee-track">
                    <div v-for="(client, i) in [...clients, ...clients]" :key="i" class="marquee-item mx-8">
                        <v-img :src="client.logo" width="120" height="60"
                            class="client-logo grayscale hover-color transition-all"></v-img>
                    </div>
                </div>
            </div>
        </v-container>
    </section>
</template>

<script>
export default {
    name: 'TrustedBySection',
    props: {
        clients: {
            type: Array,
            default: () => [
                { logo: 'https://via.placeholder.com/120x60?text=Client+1' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+2' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+3' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+4' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+5' },
                { logo: 'https://via.placeholder.com/120x60?text=Client+6' },
            ]
        }
    }
};
</script>

<style scoped>
.marquee-container {
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
}

.marquee-track {
    display: inline-flex;
    animation: scroll 30s linear infinite;
}

.marquee-item {
    flex-shrink: 0;
}

.client-logo {
    opacity: 0.5;
    transition: all 0.3s ease;
}

.client-logo.grayscale {
    filter: grayscale(100%);
}

.client-logo:hover {
    opacity: 1;
    filter: grayscale(0%);
    transform: scale(1.1);
}

@keyframes scroll {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-50%);
    }
}
</style>

