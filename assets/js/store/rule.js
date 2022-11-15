import {defineStore} from "pinia";
import {computed, ref} from "vue";
import {api} from "../api/api";
import {map} from "lodash/collection";
import dayjs from "dayjs";
import weekday from "dayjs/plugin/weekday";
dayjs.extend(weekday)
export const useRuleStore = defineStore('rule', () => {
    const rules = ref([])

    const loadRules = async () => {
        rules.value = await api.rules.index()
    }

    const formattedRules = computed(() => {
            return map(rules.value, (rule) => {

                let days = [];

                for (let i = 0; i < 7; i++ ) {
                    let weekday = dayjs().weekday(i).format('dddd').toLowerCase()
                    let weekdayShort = dayjs().weekday(i).format('ddd')
                    days[i] = {
                        'short' : weekdayShort,
                        'checked': !!rule[weekday]
                    }
                }
                rule.days = days;
                return rule;
            })
    })
    return {rules, formattedRules, loadRules}
})