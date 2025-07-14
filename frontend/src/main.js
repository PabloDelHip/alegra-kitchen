import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import Toast from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import App from './App.vue'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(Toast)
app.mount('#app')
