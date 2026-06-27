import { defineStore } from 'pinia'
import api from '../api/axios'

export const useMemberStore = defineStore('member', {
  state: () => ({
    members: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchMembers(projectId) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/projects/${projectId}/members`)
        this.members = response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot load members'
      } finally {
        this.loading = false
      }
    },

    async addMember(projectId, form) {
      await api.post(`/projects/${projectId}/members`, form)
    },

    async updateRole(projectId, memberId, role) {
      await api.put(`/projects/${projectId}/members/${memberId}`, {
        role
      })
    },

    async removeMember(projectId, memberId) {
      await api.delete(`/projects/${projectId}/members/${memberId}`)
      this.members = this.members.filter(member => member.id !== memberId)
    }
  }
})