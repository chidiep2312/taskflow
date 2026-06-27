import { defineStore } from 'pinia'
import api from '../api/axios'

export const useTaskStore = defineStore('task', {
  state: () => ({
    tasks: [],
    meta: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchTasks(projectId, params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/projects/${projectId}/tasks`, { params })
        this.tasks = response.data.data
        this.meta = response.data.meta
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot load tasks'
      } finally {
        this.loading = false
      }
    },

    async createTask(projectId, form) {
      this.loading = true
      this.error = null

      try {
        await api.post(`/projects/${projectId}/tasks`, form)
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot create task'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateTask(taskId, form) {
      this.loading = true
      this.error = null

      try {
        await api.put(`/tasks/${taskId}`, form)
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot update task'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteTask(taskId) {
      this.loading = true
      this.error = null

      try {
        await api.delete(`/tasks/${taskId}`)
        this.tasks = this.tasks.filter(task => task.id !== taskId)
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot delete task'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})