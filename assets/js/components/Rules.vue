<template>
  <div class="d-flex flex-row align-items-center">
    <h1 class="">Rules</h1>
    <router-link to="/rule/add" class="btn btn-outline-primary btn-sm ms-4">Add rule</router-link>
  </div>

  <div class="row" v-if="store.rules">
    <div class="col-12" v-for=" (rule, k) in store.formattedRules" :key="rule.id">
      <div class="card">
        <div
            class="card-body d-flex flex-column flex-md-row align-items-center justify-content-center  justify-content-md-start">
          <div class="d-flex flex-column align-items-center">
            <small class="fw-semibold text-muted">Id</small>
            <span>{{ rule.id }}</span>
          </div>

          <div class="d-flex flex-column align-items-center">
            <small class="fw-semibold text-muted">Time</small>
            <span>{{ rule.time }}</span>
          </div>

          <div class="d-flex flex-row">
            <div class="d-flex flex-column align-items-center" v-for="(day, k) in rule.days" :key="k">
              <small class="fw-semibold text-muted">{{day.short}}</small>
              <span :class="{'text-success' : day.checked, 'text-danger' : !day.checked}">{{
                  day.checked ? 'X' : '*'
                }}</span>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, reactive} from "vue";
import {useRuleStore} from "../store/rule";

const store = useRuleStore()

let state = reactive({
  running: true,
  error: ""
})

let ruleWeekdays = function (rule) {

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
