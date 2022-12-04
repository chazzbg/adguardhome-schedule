<template>
  <div class="row">
    <div class="col-12 col-md-9 mx-auto">
      <h1 v-if="ruleId">Edit Rule</h1>
      <h1 v-else>Add new Rule</h1>
      <div class="card">
        <div class="card-body">
          <div class=" mb-3">
            <label for="clients">Enabled</label>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="rule_enabled"
                     v-model="rule.enabled">
              <label class="form-check-label" for="rule_enabled">
                Enable this rule
              </label>
            </div>

          </div>
          <div class=" mb-3">

            <label for="name">Time</label>
            <datepicker v-model="time" timePicker :class="{'is-invalid': errors.time}">
              <template #action-preview="{ value }">
                {{ config.getTimezone() }}
              </template>
            </datepicker>


            <div class="form-text">
              Select the time of the day at which the rule will be applied
            </div>
            <div class="invalid-feedback">
              {{ errors.time }}
            </div>
          </div>
          <div class="mb-3">
            <label for="action">Action</label>
            <div>
              <div class="btn-group" role="group" aria-label="Rule Action" >
                <input type="radio" class="btn-check" name="rule_action" id="rule_action_block" autocomplete="off" value="block" v-model="rule.act" >
                <label class="btn btn-outline-primary" for="rule_action_block" :class="{'border border-danger': errors.act}">Block</label>

                <input type="radio" class="btn-check" name="rule_action" id="rule_action_unblock" autocomplete="off" value="unblock" v-model="rule.act" >
                <label class="btn btn-outline-primary" for="rule_action_unblock" :class="{'border border-danger': errors.act}">Unblock</label>

              </div>
            </div>

            <div class="form-text">
              Select the action which the rule will apply.
            </div>
            <div class="text-danger" v-if="errors.act">
              {{ errors.act }}
            </div>
          </div>
          <div class=" mb-3">
            <label for="days">Days</label>
            <div class="d-flex flex-row justify-content-between btn-group">

              <template v-for="d in days" :key="d.long" class=" flex-fill px-1">
                <input type="checkbox" class="btn-check" :id="'day-check-'+d.long" autocomplete="off"
                       v-model="rule[d.long]">
                <label class="btn btn-outline-primary d-block " :for="'day-check-'+d.long" :class="{'border border-danger': errors[d.long]}">{{
                    d.short
                  }}</label>
              </template>
            </div>
            <div class="form-text">
              Select days on which rule will be applied
            </div>
            <div class="text-danger" v-if="errors.monday">
              {{ errors.monday }}
            </div>
          </div>
          <div class=" mb-3">
            <label for="clients">Clients</label>
            <div class="row">
              <div class="col-4" v-for="(client, k) in clients.clients" :key="client.name">
                <div class="mb-1">
                  <input type="checkbox" class="btn-check" :id="'client-'+k" autocomplete="off"
                         v-model="rule.clients" :value="client">
                  <label class="btn btn-outline-primary d-block text-start" :for="'client-'+k">
                    {{ client.name }}<br/>
                    <small class="text-muted"> {{ client.ids.join(', ') }}</small>
                  </label>
                </div>
              </div>
            </div>

            <div class="form-text">
              Select clients on which should rule be applied. Do not select clients to apply the rule globaly.
            </div>
          </div>
          <div class=" mb-3">
            <label for="services">Services</label>

            <div class="row mb-2">
              <div class="col-6">
                <button class="btn btn-outline-secondary d-block w-100 btn-sm" @click="selectAll">Select all</button>
              </div>
              <div class="col-6">
                <button class="btn btn-outline-secondary d-block w-100 btn-sm" @click="unselectAll">Unselect all
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-4" v-for="service in services.services" :key="service.id">
                <div class="mb-1">
                  <input type="checkbox" class="btn-check" :id="'service-check-'+service.id" autocomplete="off"
                         v-model="rule.services" :value="service.id">
                  <label class="btn btn-outline-primary d-block text-start" :for="'service-check-'+service.id" :class="{'border border-danger': errors.services}">
                    <service-icon v-bind="decorateService(service.id)" class="d-inline-block me-1"/>
                    {{ service.name }}
                  </label>
                </div>

              </div>
            </div>

            <div class="form-text">
              Select services which should be disabled by the rule. Unselect all services if you want to enable all services.
            </div>
            <div class="text-danger" v-if="errors.services">
              {{errors.services}}
            </div>
          </div>
          <div class="mb-3">
            <label for="servers">Servers</label>
            <div class="row">
              <div class="col-6" v-for="server in servers.servers" :key="server.id">
                <div class="mb-1 " >
                  <input type="checkbox" class="btn-check" :id="'server-check-'+server.id" autocomplete="off"
                         v-model="rule.servers" :value="{id :server.id}" >
                  <label class="btn btn-outline-primary d-block text-start " :class="{'border-danger': errors.servers}" :for="'server-check-'+server.id">
                    {{ server.name }}
                    <small class="text-muted">
                      {{ server.host }}
                    </small>
                  </label>
                </div>

              </div>
            </div>

            <div class="form-text">
              Select on wich server rule should be applied.
            </div>
            <div class="text-danger">
              {{ errors.servers }}
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-6 ms-auto d-flex ">
              <router-link :to="{name: 'home'}" class="flex-fill btn btn-secondary ms-0 ms-md-2">Cancel</router-link>
              <button :disabled="running" class="flex-fill btn btn-primary ms-0 ms-md-2" @click="submit"> Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import {computed, onMounted, reactive, ref} from "vue";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import dayjs from "dayjs";
import weekday from "dayjs/plugin/weekday";

import ServiceIcon from "../components/ServiceIcon";

import {useConfigStore} from "../store/config";
import {useClientStore} from "../store/client";
import {useServiceStore} from "../store/service";
import {useServerStore} from "../store/server";
import {map} from "lodash/collection";
import {each} from "lodash";
import {api} from "../api/api";
import router from "../router";
import {useRoute} from "vue-router";

const route = useRoute()

const config = useConfigStore()
const clients = useClientStore()
const services = useServiceStore()
const servers = useServerStore()

const running = ref(false)
const ruleId = ref(null)
dayjs.extend(weekday)

const time = ref({
  hours: new Date().getHours(),
  minutes: 0
});

const rule = reactive({
  act: null,
  monday: false,
  tuesday: false,
  wednesday: false,
  thursday: false,
  friday: false,
  saturday: false,
  sunday: false,
  clients: [],
  services: [],
  servers: [],
  enabled: true

})

let errors = ref({
  act: null,
  time: null,
  monday: null,
  tuesday: null,
  wednesday: null,
  thursday: null,
  friday: null,
  saturday: null,
  sunday: null,
  clients: null,
  services: null,
  servers: null,
  enabled: null
})


let days = computed(() => {
  let days = [];

  for (let i = 0; i < 7; i++) {
    let weekday = dayjs().weekday(i).format('dddd').toLowerCase()
    let weekdayShort = dayjs().weekday(i).format('ddd')
    days[i] = {
      'long': weekday,
      'short': weekdayShort
    }
  }
  return days
})
let decorateService = (service) => {
  let stored = services.getService(service)

  if (!stored) {
    return null;
  }

  return {
    id: stored.id,
    name: stored.name,
    icon: stored.icon_svg
  };
}

let selectAll = () => {
  rule.services = services.services.map(s => s.id)
}
let unselectAll = () => {
  rule.services = []
}

let submit = async () => {
  running.value = true
  errors.value = map(errors, () => {
    return null;
  })

  try {
    let data = {
      time: `${time.value.hours}:${String(time.value.minutes).padStart(2, '0')}`,
      ...rule
    }

    if(ruleId.value){
      await api.rules.update(ruleId.value, data)
    } else {
      await api.rules.save(data)
    }

    router.push({name: 'home'})
  } catch (e) {
    let _errors = {}
    each(e.response.data.violations, (v) => {
      _errors[v.propertyPath] = v.title
    })
    errors.value = {
      ...errors.value,
      ..._errors
    }

  } finally {
    running.value = false
  }
}



let loadRoute = async (id) => {
  running.value = true
  try {
    let remoteRule = await api.rules.show(id)

    ruleId.value = remoteRule.id;

    console.log(remoteRule);
    each(remoteRule, (value, key) => {
      console.log(key,value);
      if(rule.hasOwnProperty(key)){
        rule[key] = value
      }
    })

    rule.servers = remoteRule.servers.map( s => { return { id: s.id } })

    let remoteTime  = dayjs(remoteRule.time)
    time.value = {
      hours: remoteTime.hour(),
      minutes: remoteTime.minute()
    }
  } catch (e){
    console.log(e)
  } finally {
    running.value  = false
  }
}



onMounted(() => {
  if(route.params && route.params.id){
    loadRoute(route.params.id)
  }  running.value = true
  Promise.all([clients.loadClients(), services.loadServices(), servers.loadServers()]).then(() => {
    running.value = false
  });
})
</script>

<style scoped>

</style>