<template>
  <div class="d-flex flex-row align-items-center">
    <h1 class="">Rules</h1>
    <router-link to="/rule/add" class="btn btn-outline-primary btn-sm ms-4">Add rule</router-link>
  </div>

  <div class="row" v-if="store.rules">
    <div class="col-12" v-for=" (rule, k) in store.formattedRules" :key="rule.id">
      <div class="card mb-2">
        <div  class="card-body d-flex flex-column flex-md-row align-items-top justify-content-start  justify-content-md-between">
          <div class="d-flex flex-column align-items-start align-items-md-center mb-2 mb-md-0">
            <small class="fw-semibold text-muted">Time</small>
            <span>{{ rule.time }}</span>
          </div>

          <div class="d-flex flex-row justify-content-start justify-content-md-center  mb-2 mb-md-0">
            <div class="d-flex flex-column align-items-center px-1" v-for="(day, k) in rule.days" :key="k">
              <small class="fw-semibold text-muted">{{day.short}}</small>
              <span v-if="day.checked" class="text-success">
                <i class="bi-check"></i>
              </span>
              <span v-else class="text-danger">
                <i class="bi-x"></i>
              </span>

            </div>
          </div>
          <div class="d-flex flex-column align-items-start align-items-md-center  mb-2 mb-md-0">
            <small class="fw-semibold text-muted">Blocked</small>

            <span v-if="!rule.services" >
              Unblock all
            </span>
            <div  v-else class="row g-0">
              <div class="col" v-for="service in rule.services" :key="service">
                  <service-icon  v-bind="decorateService(service)" />
              </div>
            </div>

          </div>

          <div class="d-flex flex-column align-items-start align-items-md-center flex-shrink-0 mb-2 mb-md-0">
            <small class="fw-semibold text-muted">Clients</small>
            <div v-if="!rule.clients || !rule.clients.length">
              Global
            </div>
            <div v-else>
              <div v-for="client in rule.clients">
                {{client}}
              </div>
            </div>
          </div>
          <div class="d-flex flex-column align-items-start align-items-md-center flex-shrink-0 mb-2 mb-md-0">
            <small class="fw-semibold text-muted">Servers</small>
            <div v-for="server in rule.servers">
                {{server.name}}
            </div>
          </div>
          <div class="d-flex  mb-2 mb-md-0 flex-row flex-md-column">
            <router-link :to="{name: 'rule_edit', params: {id: rule.id}}" class="btn btn-sm btn-primary me-2 flex-fill mb-0 mb-md-2">
              <i class="bi-pencil-square"></i> Edit
            </router-link>
            <button  class="btn btn-sm btn-danger me-2 flex-fill">
              <i class="bi-trash-fill"></i> Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, reactive} from "vue";
import {useRuleStore} from "../store/rule";
import {useServiceStore} from "../store/service";
import ServiceIcon from "./ServiceIcon";

const store = useRuleStore()
const serviceStore = useServiceStore();

let state = reactive({
  running: true,
  error: ""
})

let decorateService = (service) => {
  let stored = serviceStore.getService(service)

  if (!stored) {
    return null;
  }

  return  {
    id: stored.id,
    name: stored.name,
    icon: stored.icon_svg
  };
}

onMounted(async () => {
  try {
    await store.loadRules()
  } catch (e) {
    state.error = 'Problem with fetching rules'
  } finally {
    state.running = false
  }
})

</script>
