import './bootstrap';
import { createApp } from 'vue';
import router from './routes';

// Utilities and Plugins
import { setupAxios } from './utils/axios.config';
import { createVuetifyInstance } from './plugins/vuetify';
import VueProgressBar, { progressBarOptions, setupProgressBarHelper } from './plugins/progressBar';
import VueSweetalert2, { setupSweetAlert } from './plugins/sweetalert';

// Components
import App from './components/app.vue';

// Initialize axios configuration
setupAxios();

// Initialize SweetAlert
setupSweetAlert();

// Create Vue app instance
const app = createApp(App);

// Register plugins
app.use(router);
app.use(createVuetifyInstance());
app.use(VueProgressBar, progressBarOptions);
app.use(VueSweetalert2);

// Setup progress bar helper for router hooks
setupProgressBarHelper(app, router);

// Mount the app
app.mount('#app');
