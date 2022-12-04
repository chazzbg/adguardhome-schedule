<template>
  <div class="row">
    <div class="col-12 col-md-8 mx-auto">
      <div v-for="(traces, day) in grouped" class="mt-2">
        <h5 class="ps-1">{{ day }}</h5>
        <ul class="list-group">
          <li class="list-group-item d-flex" v-for="trace in traces" >
            <span>{{ formatTime(trace ) }}</span>
            <div>{{trace}}</div>
          </li>
        </ul>
      </div>
      <div class="row mt-2">
        <div class="col-3 mx-auto">

          <button v-if="store.traces.length < store.total" class="btn btn-primary w-100" :disabled="running" @click="loadMore">Load more</button>
          <button v-else-if="!running" class="btn btn-light  w-100" disabled>No more entries</button>
          <button v-else class="btn btn-light  w-100" disabled>Loading...</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import {computed, onMounted, ref} from "vue";
import {userTraceStore} from "../store/trace";
import {each} from "lodash";
import dayjs from "dayjs";
import localizedFormat from 'dayjs/plugin/localizedFormat'

dayjs.extend(localizedFormat)

const running = ref(false)
const page = ref(1)
const store = userTraceStore();

const grouped = computed(() => {
  let traces = {}

  each(store.traces, function (trace, k) {
    let date = dayjs(trace.createdAt)
    let day = date.format('LL');

    if (!traces[day]) {
      traces[day] = [trace]
    } else {
      traces[day].push(trace)
    }
  });

  return traces;

})



let loadMore = () => {
  running.value = true;
  store.load(page.value)
  page.value++

  running.value = false

}
let formatTime = (trace) => {
  return dayjs(trace.createdAt).format('HH:mm')
}
onMounted(() => {
  running.value = true
  store.load(page.value)
  page.value ++;
  running.value = false
})
</script>
