import {createApp,} from 'vue'
import App from "./App";
import router from "./router";
import {createPinia} from "pinia";
import {useConfigStore} from "./store/config";

const app = createApp(App)
const pinia = createPinia()

app.use(router)
app.use(pinia)
app.mount('#app')


const settings = useConfigStore()

settings.setTimezone(document.querySelector('meta[name="timezone"]').content)