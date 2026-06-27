import { defineStore } from 'pinia'
import api from '../api/axios'

export const useAttachmentStore = defineStore('attachment', {
  state: () => ({
    attachments: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchAttachments(taskId) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/tasks/${taskId}/attachments`)
        this.attachments = response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot load attachments'
      } finally {
        this.loading = false
      }
    },

    async uploadAttachment(taskId, file) {
      const formData = new FormData()
      formData.append('file', file)

      await api.post(`/tasks/${taskId}/attachments`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
    },

    async deleteAttachment(id) {
      await api.delete(`/task-attachments/${id}`)
      this.attachments = this.attachments.filter(item => item.id !== id)
    }
  }
})