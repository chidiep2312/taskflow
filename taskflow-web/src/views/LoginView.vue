<template>
  <div class="auth-page">
    <div class="auth-card">
      <h2 class="auth-title">Welcome back ✦</h2>
      <p class="page-subtitle mb-4">
        Login to manage your projects beautifully.
      </p>

      <div v-if="auth.error" class="alert alert-danger">
        {{ auth.error }}
      </div>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label>Email</label>
          <input v-model="form.email" type="email" class="form-control" required />
        </div>

        <div class="mb-4">
          <label>Password</label>
          <input v-model="form.password" type="password" class="form-control" required />
        </div>

        <button class="btn btn-primary w-100" :disabled="auth.loading">
          {{ auth.loading ? 'Loading...' : 'Login' }}
        </button>
      </form>

      <p class="mt-4 mb-0 text-center">
        No account?
        <router-link to="/register" class="text-danger fw-bold">
          Register
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { useAuthStore } from '../stores/authStore'

const auth = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

function submit() {
  auth.login(form)
}
</script>