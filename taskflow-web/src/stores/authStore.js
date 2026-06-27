import { defineStore } from 'pinia'
import api from '../api/axios'
import router from '../router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),

  actions: {
    async register(form) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/register', form)

        this.user = response.data.data.user
        this.token = response.data.data.token

        localStorage.setItem('user', JSON.stringify(this.user))
        localStorage.setItem('token', this.token)

        router.push('/dashboard')
      } catch (error) {
        this.error = error.response?.data?.message || 'Register failed'
      } finally {
        this.loading = false
      }
    },

    async login(form) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/login', form)

        this.user = response.data.data.user
        this.token = response.data.data.token

        localStorage.setItem('user', JSON.stringify(this.user))
        localStorage.setItem('token', this.token)

        router.push('/dashboard')
      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        await api.post('/logout')
      } catch (error) {
        console.log(error)
      }

      this.user = null
      this.token = null
      localStorage.removeItem('user')
      localStorage.removeItem('token')

      router.push('/login')
    }
  }
})