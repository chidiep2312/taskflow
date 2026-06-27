import { defineStore } from 'pinia'
import api from '../api/axios'

export const useProjectStore = defineStore('project', {
  state: () => ({
    projects: [],
    project: null,
    meta: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchProjects(params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('/projects', { params })
        this.projects = response.data.data
        this.meta = response.data.meta
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot load projects'
      } finally {
        this.loading = false
      }
    },

    async fetchProject(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/projects/${id}`)
        this.project = response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot load project'
      } finally {
        this.loading = false
      }
    },

    async createProject(form) {
      this.loading = true
      this.error = null

      try {
        await api.post('/projects', form)
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot create project'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateProject(id, form) {
      this.loading = true
      this.error = null

      try {
        await api.put(`/projects/${id}`, form)
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot update project'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteProject(id) {
      this.loading = true
      this.error = null

      try {
        await api.delete(`/projects/${id}`)
        this.projects = this.projects.filter(project => project.id !== id)
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot delete project'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})