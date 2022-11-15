<template>
  <div class="d-flex flex-row align-items-center">
    <h1 class="">Servers</h1>
    <router-link to="/server/add" class="btn btn-outline-primary btn-sm ms-4">Add server</router-link>
  </div>
  <div v-if="state.running" >
    Loading servers...
  </div>
  <div v-if="state.error" class="alert alert-danger">
    {{state.error}}
  </div>
  <div class="card mb-2"  v-for=" (server, k) in store.servers" :key="server.host">
    <div class="card-body d-flex flex-column flex-md-row align-items-top align-items-md-center justify-content-start justify-content-md-between" >
      <div class="flex-fill">
        <h5 class="card-title">{{server.name}}</h5>
        <h6 class="card-subtitle mb-2 text-muted">{{server.host}}</h6>
      </div>
      <div class="d-flex  mb-2 mb-md-0 flex-row flex-md-column">
        <router-link :to="{name: 'server_edit', params: {id: server.id}}" class="btn btn-sm btn-primary me-2 flex-fill mb-0 mb-md-2">
          <i class="bi-pencil-square"></i> Edit
        </router-link>
        <button  class="btn btn-sm btn-danger me-2 flex-fill" @click="confirmDelete(server.id)">
          <i class="bi-trash-fill"></i> Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, reactive} from "vue";

import {useServerStore} from "../store/server";
import {api} from "../api/api";
import {useRuleStore} from "../store/rule";

const store = useServerStore()

let state = reactive({
  running: true,
  servers: [],
  error: ""
})

let confirmDelete = (id) =>  {
  if(window.confirm("Are you sure?")){
    deleteServer(id)
  }
}
let deleteServer = async (id) => {
  await api.servers.delete(id)
  store.loadServers()
  useRuleStore().loadRules()
}
onMounted(async  () => {
  try {
    await store.loadServers()
  } catch (e) {
    state.error = 'Problem with fetching servers'
  } finally {
    state.running = false
  }
})

</script>
