import {defineStore} from "pinia";
import {ref} from "vue";
import {api} from "../api/api";

export const useServerStore = defineStore('server', () => {
    const servers = ref([])

    const loadServers = async () => {
        servers.value = await api.servers.index()
    }

    return {servers, loadServers}
})