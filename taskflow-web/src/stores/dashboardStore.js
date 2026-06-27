import { defineStore } from 'pinia'
import api from '../api/axios'

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    statistics: null,
    loading: false,
    error: null,
    activities: []
  }),

  actions: {
    async fetchDashboard() {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('/dashboard')
        this.statistics = response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot load dashboard'
      } finally {
        this.loading = false
      }
    },
    async fetchActivities() {
      try {
        const response = await api.get('/activities')
        this.activities = response.data.data
      } catch (error) {
        console.log(error)
      }
    }
  }
})