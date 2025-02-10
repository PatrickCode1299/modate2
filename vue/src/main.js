import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import store from './store/index.js'
import  'bootstrap/dist/css/bootstrap.css';
import '@fortawesome/fontawesome-free/css/all.css'
import '@fortawesome/fontawesome-free/js/all.js'
import 'flag-icon-css/css/flag-icons.min.css';
import router from './router/index.js'
import './index.css'
import VueCookies from 'vue-cookies';




createApp(App)
.use(store)
.use(router)
.use(VueCookies,{expires:'365'})
.mount('#app')
