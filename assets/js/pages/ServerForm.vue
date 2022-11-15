<template>
  <div class="row">
    <div class="col-12 col-md-6 mx-auto">
      <h1 v-if="serverId">Edit Server</h1>
      <h1 v-else>Add new Server</h1>
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col">
              <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-sm" id="name" placeholder="Server Name"
                       :disabled="running" v-model="server.name" :class="{'is-invalid': errors.name}">
                <label for="name">Server Name</label>


                <div class="form-text">
                  Short name to recognise your server
                </div>
                <div class="invalid-feedback">
                  {{ errors.name }}
                </div>
              </div>
              <div class=" form-floating mb-3">
                <input type="text" class="form-control form-control-sm" id="host" placeholder="Server Host"
                       :disabled="running" v-model="server.host" :class="{'is-invalid': errors.host}">
                <label for="host">Server Host</label>
                <div class="form-text">
                  Hostame like &lt;adguard.local&gt; or ip and port like &lt;localhost:8080&gt;
                </div>
                <div class="invalid-feedback">
                  {{ errors.host }}
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="" id="schema" :disabled="running"
                           v-model="server.schema" :class="{'is-invalid': errors.schema}">
                    <label class="form-check-label" for="schema">
                      Use secure connection
                    </label>
                    <div class="form-text">
                      Check this if you are using self-signed certificates
                    </div>
                    <div class="invalid-feedback">
                      {{ errors.schema }}
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="" id="skipSSlVerify" :disabled="running"
                           v-model="server.skip_ssl_verify" :class="{'is-invalid': errors.skip_ssl_verify}">
                    <label class="form-check-label" for="skipSSlVerify">
                      Skip certificate verification
                    </label>
                    <div class="form-text">
                      Check this if you are using self-signed certificates
                    </div>
                    <div class="invalid-feedback">
                      {{ errors.skip_ssl_verify }}
                    </div>
                  </div>

                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control form-control-sm" id="username" placeholder="Username"
                           :disabled="running" v-model="server.username" :class="{'is-invalid': errors.username}">
                    <label for="username">Username</label>

                    <div class="invalid-feedback">
                      {{ errors.username }}
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control form-control-sm" id="password" placeholder="Password"
                           :disabled="running" v-model="server.password" :class="{'is-invalid': errors.password}">
                    <label for="password">Password</label>
                    <div class="invalid-feedback">
                      {{ errors.password }}
                    </div>
                  </div>
                </div>
              </div>
              <div v-if="errors.general" class="text-danger">
                {{ errors.general }}
              </div>
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
import {onMounted, reactive, ref} from "vue";
import {api} from "../api/api";
import router from "../router";
import {each} from "lodash";
import {map} from "lodash/collection";
import {useRoute} from "vue-router";

const route = useRoute()
const running = ref(false)
const serverId = ref(null)
const server = reactive({
  name: null,
  schema: null,
  host: null,
  skip_ssl_verify: false,
  username: null,
  password: null
})

let errors = ref({
  name: null,
  schema: null,
  host: null,
  skip_ssl_verify: null,
  username: null,
  password: null,
  general: null
})

let submit = async () => {
  running.value = true
  errors.value = map(errors, (v, k) => {
    return null;
  })
  try {
    server.schema = server.schema ? 'https' : 'http'
    if(serverId.value){
      await api.servers.update(serverId.value, server)
    } else {
      await api.servers.save(server)
    }
    router.push({name: 'home'})
  } catch (e) {
    let _errors = {}
    each(e.response.data.violations, (v, k) => {
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

let loadServer = async (id) => {
    running.value = true
    try {
      let remoteServer = await api.servers.show(id, true)
      serverId.value = remoteServer.id
      server.name = remoteServer.name
      server.host = remoteServer.host
      server.host = remoteServer.host
      server.schema = remoteServer.schema === 'https'
      server.skip_ssl_verify = remoteServer.skipSslVerify
      server.username = remoteServer.username
      server.password = remoteServer.password
    } catch (e){
      console.log(e)
    } finally {
      running.value  = false
    }
}


onMounted(() => {
    if(route.params && route.params.id){
      loadServer(route.params.id)
    }
})
</script>
