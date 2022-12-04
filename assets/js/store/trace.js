import {defineStore} from "pinia";
import {ref} from "vue";
import {api} from "../api/api";
import {find, sortBy} from "lodash/collection";

export const userTraceStore = defineStore('trace', () => {
    const traces = ref([])
    const total = ref(0)

    let load = (page) => {
        let localTotal, all;
        Promise.all([
            api.traces.total(),
            api.traces.index(page)
        ]).then(values => {
            [localTotal, all] = values

            total.value = localTotal.total;

            if(!!page && page > 1){
                traces.value.push(...all)
            } else {
                traces.value = all;
            }
        })
    }

    return {traces, total, load}
})