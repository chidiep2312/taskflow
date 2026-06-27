<template>
  <div class="auth-page">
    <div class="auth-card">
      <h2 class="auth-title">Create account ✿</h2>
      <p class="page-subtitle mb-4">
        Start your pink productivity workspace.
      </p>

      <div v-if="auth.error" class="alert alert-danger">
        {{ auth.error }}
      </div>

      <form @submit.prevent="submit">
        <div class="mb-3">
          <label>Name</label>
          <input v-model="form.name" class="form-control" required />
        </div>

        <div class="mb-3">
          <label>Email</label>
          <input v-model="form.email" type="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label>Password</label>
          <input v-model="form.password" type="password" class="form-control" required />
        </div>

        <div class="mb-4">
          <label>Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" class="form-control" required />
        </div>

        <button class="btn btn-primary w-100" :disabled="auth.loading">
          {{ auth.loading ? 'Loading...' : 'Register' }}
        </button>
      </form>

      <p class="mt-4 mb-0 text-center">
        Already have an account?
        <router-link to="/login" class="text-danger fw-bold">
          Login
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
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

function submit() {
  auth.register(form)
}
</script>