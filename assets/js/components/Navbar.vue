<template>
  <nav class="navbar navbar-expand-lg bg-light">
    <div class="container">
      <router-link class="navbar-brand" :to="{name: 'home'}">Scheduler <sup>for AdGuard Home</sup></router-link>

      <router-link :to="{name: 'history'}" class="btn btn-outline-success ms-auto">
        <i class="bi-journal-text"></i>
        <span class="d-none d-sm-inline"> History</span>
      </router-link>

    </div>
  </nav>
</template>
