<script setup>

import Navbar from './components/Navbar'
import {onMounted} from "vue";
import {useServiceStore} from "./store/service";

onMounted(() => {
  useServiceStore().loadServices()
})
</script>

<template>
  <Navbar/>
  <div class="container ">
    <router-view></router-view>
  </div>
</template>