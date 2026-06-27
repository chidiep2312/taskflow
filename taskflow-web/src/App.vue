<template>
  <div>
    <nav class="navbar navbar-expand-lg px-4">
      <router-link class="navbar-brand" to="/dashboard">
        ✿ TaskFlow
      </router-link>

      <div class="ms-auto">
        <router-link
          v-if="!auth.token"
          class="btn btn-outline-light me-2"
          to="/login"
        >
          Login
        </router-link>

        <router-link
          v-if="!auth.token"
          class="btn btn-light"
          to="/register"
        >
          Register
        </router-link>

        <button
          v-if="auth.token"
          class="btn btn-light"
          @click="auth.logout"
        >
          Logout
        </button>
      </div>
    </nav>

    <router-view />
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/authStore'

const auth = useAuthStore()
</script>