import {defineStore} from "pinia";
import {ref} from "vue";
import {api} from "../api/api";
import {find} from "lodash/collection";

export const useServiceStore = defineStore('service', () => {
    const services = ref([])

    let loadServices = async () => {
        services.value = await api.services.index()
    }

    let getService = (service) => {
        return find(services.value, (s) => s.id === service)

    }

    return {services, getService, loadServices}
})