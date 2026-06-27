import { defineStore } from 'pinia'
import api from '../api/axios'

export const useCommentStore = defineStore('comment', {
  state: () => ({
    comments: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchComments(taskId) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/tasks/${taskId}/comments`)
        this.comments = response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Cannot load comments'
      } finally {
        this.loading = false
      }
    },

    async createComment(taskId, form) {
      await api.post(`/tasks/${taskId}/comments`, form)
    },

    async deleteComment(commentId) {
      await api.delete(`/task-comments/${commentId}`)
      this.comments = this.comments.filter(comment => comment.id !== commentId)
    }
  }
})