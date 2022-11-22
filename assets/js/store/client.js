import {defineStore} from "pinia";
import {ref} from "vue";
import {api} from "../api/api";
import {find, sortBy} from "lodash/collection";

export const useClientStore = defineStore('client', () => {
    const clients = ref([])

    let loadClients = async () => {
        let clientsAll = await api.clients.index();

        clients.value = sortBy(clientsAll, (c) => Number(c.ids[0].split(".").map((num) => String(num).padStart(3,'0') ).join("")))
    }

    let getClient = (client) => {
        return find(clients.value, (s) => s.name === client)

    }

    return {clients, getClient, loadClients}
})