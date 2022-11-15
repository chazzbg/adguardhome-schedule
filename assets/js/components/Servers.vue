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
  <div class="row">
    <div class="col-3" v-for=" (server, k) in store.servers" :key="server.host">
    <div class="card" >
      <div class="card-body" >
        {{server.host}}
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive} from "vue";

import {useServerStore} from "../store/server";
const store = useServerStore()

let state = reactive({
  running: true,
  servers: [],
  error: ""
})

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
