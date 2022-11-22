import {defineStore} from "pinia";
import {ref} from "vue";

export const useConfigStore = defineStore('config', () => {
    const timezone = ref('UTC')

    let setTimezone = function (tz){
        timezone.value = tz
    }

    let getTimezone = function (){
        return timezone.value;
    }

    return {
       timezone, setTimezone, getTimezone
    }
})