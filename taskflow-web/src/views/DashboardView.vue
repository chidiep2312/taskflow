<template>
  <div class="container mt-4">
    <div class="hero-panel">
      <h1 class="page-title">
        Hello, {{ auth.user?.name }} ✿
      </h1>

      <p class="page-subtitle">
        Your soft but powerful project management space.
      </p>

      <router-link to="/projects" class="btn btn-primary mt-3">
        Manage Projects
      </router-link>
    </div>

    <div v-if="dashboard.loading" class="alert alert-info">
      Loading dashboard...
    </div>

    <div v-if="dashboard.error" class="alert alert-danger">
      {{ dashboard.error }}
    </div>

    <div v-if="stats" class="row">
      <div class="col-md-3 mb-3">
        <div class="card p-4 dashboard-card">
          <span class="dashboard-label">Projects</span>
          <h2>{{ stats.total_projects }}</h2>
          <p class="page-subtitle mb-0">Total projects</p>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="card p-4 dashboard-card">
          <span class="dashboard-label">Tasks</span>
          <h2>{{ stats.total_tasks }}</h2>
          <p class="page-subtitle mb-0">Total tasks</p>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="card p-4 dashboard-card">
          <span class="dashboard-label">Done</span>
          <h2>{{ stats.status.done }}</h2>
          <p class="page-subtitle mb-0">Completed tasks</p>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <div class="card p-4 dashboard-card warning">
          <span class="dashboard-label">Overdue</span>
          <h2>{{ stats.overdue_tasks }}</h2>
          <p class="page-subtitle mb-0">Need attention</p>
        </div>
      </div>
    </div>

    <div v-if="stats" class="row mt-3">
  <div class="col-md-6 mb-3">
    <div class="card p-4">
      <h5 class="mb-3">Task Status</h5>

      <div class="stat-row">
        <span>Todo</span>
        <strong>{{ stats.status.todo }}</strong>
      </div>

      <div class="stat-row">
        <span>In Progress</span>
        <strong>{{ stats.status.in_progress }}</strong>
      </div>

      <div class="stat-row">
        <span>Done</span>
        <strong>{{ stats.status.done }}</strong>
      </div>
    </div>
  </div>

  <div class="col-md-6 mb-3">
    <div class="card p-4">
      <h5 class="mb-3">Task Priority</h5>

      <div class="stat-row">
        <span>High</span>
        <strong>{{ stats.priority.high }}</strong>
      </div>

      <div class="stat-row">
        <span>Medium</span>
        <strong>{{ stats.priority.medium }}</strong>
      </div>

      <div class="stat-row">
        <span>Low</span>
        <strong>{{ stats.priority.low }}</strong>
      </div>
    </div>
  </div>
</div>

<div class="card p-4 mt-3 mb-4">
  <h5 class="mb-3">Recent Activities ✦</h5>

  <div v-if="dashboard.activities.length === 0" class="page-subtitle">
    No activities yet.
  </div>

  <div
    v-for="activity in dashboard.activities"
    :key="activity.id"
    class="activity-item"
  >
    <strong>{{ formatAction(activity.action) }}</strong>
    <div class="page-subtitle small">
      {{ activity.created_at }}
    </div>
  </div>
</div>
      
  

  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/authStore'
import { useDashboardStore } from '../stores/dashboardStore'

const auth = useAuthStore()
const dashboard = useDashboardStore()

const stats = computed(() => dashboard.statistics)

onMounted(() => {
  dashboard.fetchDashboard()
  dashboard.fetchActivities()
})
function formatAction(action) {
  return action.replaceAll('_', ' ')
}
</script>