import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import store from './store/index.js'
import  'bootstrap/dist/css/bootstrap.css';
import router from './router/index.js'
import './index.css'


createApp(App)
.use(store)
.use(router)
.mount('#app')
