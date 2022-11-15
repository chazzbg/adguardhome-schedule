<template>
  <div v-if="id" class="flex-shrink-0" v-html="atob(icon)" :title="name"></div>
</template>
<script>
export default {
  props: {
    id: String,
    name: String,
    icon: String
  },
  methods: {
    atob: (a) => window.atob(a)
  }
}
</script>

<style type="text/css" scoped>
div {
  width: 20px;
  height: 20px;
}
</style>